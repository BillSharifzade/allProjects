#include <stdio.h>

int my_strlen(char* param_1)
{
    int length = 0;
    while (*param_1 != '\0') {
        length++;
        param_1++;
    }
    return length;
}
