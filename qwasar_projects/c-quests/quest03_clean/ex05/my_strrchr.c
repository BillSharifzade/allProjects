#include <stdio.h>
char* my_strrchr(char* param_1, char param_2) {
    char* border = NULL;
    while (*param_1 != '\0') {
        if (*param_1 == param_2) {
            border = param_1;
        }
        param_1++;
    }
    if (*param_1 == param_2) {
        border = param_1;
    }
    return border;
}