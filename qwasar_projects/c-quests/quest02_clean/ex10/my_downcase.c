#include <stddef.h>
char* my_downcase(char* param_1)
{
    if (param_1 == NULL) return NULL; // ETO NUZHNO ATO OSHOBKA POSTOYANNO   
    char* ptr = param_1;   
    while (*ptr != '\0') {
        if (*ptr >= 'A' && *ptr <= 'Z') {
            *ptr -= 'A' - 'a';
        }
        ptr++;
    }   
    return param_1;
}