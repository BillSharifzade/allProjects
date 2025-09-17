#include <stdbool.h>
#ifndef STRUCT_INTEGER_ARRAY
#define STRUCT_INTEGER_ARRAY
typedef struct s_integer_array
{
    int size;
    int* array;
} integer_array;
#endif
bool my_is_sort(integer_array* param_1) {
    if (param_1->size <= 1) {
        return true;
    }
    bool is_ascending = true;
    bool is_descending = true;
    for (int i = 0; i < param_1->size - 1; i++) {
        if (param_1->array[i] > param_1->array[i + 1]) {
            is_ascending = false;
        }
        if (param_1->array[i] < param_1->array[i + 1]) {
            is_descending = false;
        }
    }
    return is_ascending || is_descending;
}