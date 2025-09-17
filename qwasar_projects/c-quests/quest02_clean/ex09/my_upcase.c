#include <stddef.h>
char* my_upcase(char* param_1)
{
    if (param_1 == NULL) return NULL; // ETO NUZHNO ATO OSHOBKA POSTOYANNO   
    char* ptr = param_1;   
    while (*ptr != '\0') {
        if (*ptr >= 'a' && *ptr <= 'z') {
            *ptr -= 'a' - 'A';
        }
        ptr++;
    }   
    return param_1;
}