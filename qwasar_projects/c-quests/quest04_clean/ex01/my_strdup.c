#include <stdlib.h>
#include <string.h>
char* my_strdup(char* param_1) {
    size_t length = strlen(param_1) + 1;
    char* new_str = (char*)malloc(length);
    if (new_str == NULL) {
        return NULL;
    }
    strcpy(new_str, param_1);
    return new_str;
}