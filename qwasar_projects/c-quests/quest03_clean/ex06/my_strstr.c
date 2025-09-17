#include <stddef.h>
char* my_strstr(char* param_1, char* param_2) {
    if (*param_2 == '\0') {
        return param_1;
    }
    while (*param_1 != '\0') {
        char* biba = param_1;
        char* boba = param_2;
        while (*biba && *boba && (*biba == *boba)) {
            biba++;
            boba++;
        }
        if (*boba == '\0') {
            return param_1;
        }
        param_1++;
    }
    return NULL;
}