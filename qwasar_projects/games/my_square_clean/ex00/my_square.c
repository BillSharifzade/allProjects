#include <stdio.h>
#include <stdlib.h>
int main(int ac, char **av) {
    if (ac != 3) { // Tut nuzhno obratit vnimaniye chtob input ne bil uebishnim
        return 0;
    }
    int x = atoi(av[1]);
    int y = atoi(av[2]);
    if (x <= 0 || y <= 0) {
        return 0;
    }
    for (int i = 0; i < x; i++) {
        if (i == 0 || i == x - 1) {
            printf("o");
        } else if (x > 1) {
            printf("-");
        }
    }
    printf("\n");
    for (int i = 0; i < y - 2; i++) {
        printf("|");
        if (x > 2) {
            for (int j = 0; j < x - 2; j++) {
                printf(" ");
            }
        }
        if (x > 1) {
            printf("|");
        }
        printf("\n");
    }
    if (y > 1) {
        for (int i = 0; i < x; i++) {
            if (i == 0 || i == x - 1) {
                printf("o");
            } else if (x > 1) {
                printf("-");
            }
        }
        printf("\n");
    }
    return 0;
}
// Eba slozhhhhhhhhnnnnnnnooooo