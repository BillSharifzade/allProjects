#include "my_bsq.h"
#include <unistd.h>

static int min(int a, int b, int c) {
    int m = a < b ? a : b;
    return m < c ? m : c;
}

Square find_largest_square(const Map *map) {
    Square best = {0, 0, 0};
    int *prev_row = calloc(map->cols, sizeof(int));
    int *curr_row = calloc(map->cols, sizeof(int));
    if (!prev_row || !curr_row) return best;

    for (int i = 0; i < map->rows; i++) {
        for (int j = 0; j < map->cols; j++) {
            if (map->grid[i][j] == OBSTACLE_CHAR) {
                curr_row[j] = 0;
            } else {
                if (i == 0 || j == 0) curr_row[j] = 1;
                else curr_row[j] = 1 + min(prev_row[j], curr_row[j-1], prev_row[j-1]);
                if (curr_row[j] > best.size) {
                    best.size = curr_row[j];
                    best.row = i;
                    best.col = j;
                }
            }
        }
        int *tmp = prev_row; prev_row = curr_row; curr_row = tmp;
    }
    free(prev_row);
    free(curr_row);
    return best;
}

void fill_square(Map *map, Square square) {
    int start_row = square.row - square.size + 1;
    int start_col = square.col - square.size + 1;

    for (int i = start_row; i <= square.row; i++) {
        for (int j = start_col; j <= square.col; j++) {
            map->grid[i][j] = SQUARE_CHAR;
        }
    }
}