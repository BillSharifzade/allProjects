#ifndef MY_BSQ_H
#define MY_BSQ_H

#include <stdlib.h>

#define EMPTY_CHAR '.'
#define OBSTACLE_CHAR 'o'
#define SQUARE_CHAR 'x'

typedef struct {
    char **grid;
    int rows;
    int cols;
} Map;

typedef struct {
    int row;
    int col;
    int size;
} Square;

Map* load_map(const char *filepath);
void print_map(const Map *map);
void free_map(Map *map);

Square find_largest_square(const Map *map);
void fill_square(Map *map, Square square);

int my_bsq(int argc, char *argv[]);

#endif