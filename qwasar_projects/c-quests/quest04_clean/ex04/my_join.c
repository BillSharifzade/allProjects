#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#ifndef STRUCT_STRING_ARRAY
#define STRUCT_STRING_ARRAY
typedef struct s_string_array
{
    int size;
    char** array;
} string_array;
#endif
char* my_join(string_array* param_1, char* param_2)
{
    if (param_1->size == 0) return NULL;
    int total_length = 0, sep_len = strlen(param_2);
    for (int i = 0; i < param_1->size; ++i) total_length += strlen(param_1->array[i]);
    total_length += (param_1->size - 1) * sep_len;
    char* result = (char*)malloc(total_length + 1);
    if (!result) return NULL;
    int pos = 0;
    for (int i = 0; i < param_1->size; ++i) {
        strcpy(result + pos, param_1->array[i]);
        pos += strlen(param_1->array[i]);
        if (i < param_1->size - 1) {
            strcpy(result + pos, param_2);
            pos += sep_len;
        }
    }
    return result;
}