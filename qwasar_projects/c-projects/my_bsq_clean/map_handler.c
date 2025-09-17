#include <fcntl.h>
#include <unistd.h>
#include "my_bsq.h"

static void free_partial_map(Map *map, int rows_allocated) {
    if (!map) return;
    if (map->grid) {
        for (int i = 0; i < rows_allocated; i++) {
            free(map->grid[i]);
        }
        free(map->grid);
    }
    free(map);
}

static int get_map_dimensions(const char *filepath, int *rows, int *cols) {
    int fd = open(filepath, 0);
    if (fd < 0) return 0;

    char c;
    *rows = 0;
    
    while (read(fd, &c, 1) > 0 && c != '\n') {
        if (c < '0' || c > '9') { close(fd); return 0; }
        *rows = *rows * 10 + (c - '0');
    }
    if (*rows == 0) { close(fd); return 0; }

    *cols = 0;
    while (read(fd, &c, 1) > 0 && c != '\n') {
        (*cols)++;
    }
    close(fd);
    return (*cols > 0);
}

static int fill_and_validate_map(int fd, Map *map) {
    char c;
    int actual_rows = 0;
    
    while (read(fd, &c, 1) > 0 && c != '\n');

    for (int i = 0; i < map->rows; i++) {
        map->grid[i] = malloc(map->cols * sizeof(char));
        if (!map->grid[i]) return 0;

        int bytes_read = read(fd, map->grid[i], map->cols);
        if (bytes_read != map->cols) return 0;

        for (int j = 0; j < map->cols; j++) {
            if (map->grid[i][j] != EMPTY_CHAR && map->grid[i][j] != OBSTACLE_CHAR) {
                return 0;
            }
        }

        if (read(fd, &c, 1) != 1 || c != '\n') return 0;
        actual_rows++;
    }
    if (read(fd, &c, 1) > 0) return 0;
    return (actual_rows == map->rows);
}

Map* load_map(const char *filepath) {
    int rows, cols;
    if (!get_map_dimensions(filepath, &rows, &cols)) return NULL;

    Map *map = malloc(sizeof(Map));
    if (!map) return NULL;
    map->rows = rows;
    map->cols = cols;
    map->grid = malloc(rows * sizeof(char*));
    if (!map->grid) {
        free(map);
        return NULL;
    }

    int fd = open(filepath, 0);
    if (fd < 0) {
        free_partial_map(map, 0);
        return NULL;
    }

    if (!fill_and_validate_map(fd, map)) {
        close(fd);
        int rows_allocated = 0;
        for(int i=0; i<rows; ++i) if(map->grid[i]) rows_allocated++; else break;
        free_partial_map(map, rows_allocated);
        return NULL;
    }

    close(fd);
    return map;
}

void print_map(const Map *map) {
    for (int i = 0; i < map->rows; i++) {
        write(1, map->grid[i], map->cols);
        write(1, "\n", 1);
    }
}

void free_map(Map *map) {
    if (!map) return;
    for (int i = 0; i < map->rows; i++) {
        free(map->grid[i]);
    }
    free(map->grid);
    free(map);
}