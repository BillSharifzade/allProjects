#include <stdio.h>
#include <string.h>
char* my_spaceship(char* param_1) {
    static char result[100];
    int x = 0, y = 0;
    int direction = 0;
    char directions[4][5] = {"up", "right", "down", "left"};
    for (int i = 0; param_1[i] != '\0'; i++) {
        if (param_1[i] == 'R') {
            direction = (direction + 1) % 4;
        } else if (param_1[i] == 'L') {
            direction = (direction + 3) % 4;
        } else if (param_1[i] == 'A') {
            if (direction == 0) { y--; }
            else if (direction == 1) { x++; }
            else if (direction == 2) { y++; }
            else if (direction == 3) { x--; }
        }
    } 
    sprintf(result, "{x: %d, y: %d, direction: '%s'}", x, y, directions[direction]);
    return result;
}