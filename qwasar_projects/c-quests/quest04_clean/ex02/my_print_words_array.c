#include <unistd.h>
#ifndef STRUCT_STRING_ARRAY
#define STRUCT_STRING_ARRAY
typedef struct s_string_array
{
    int size;
    char** array;
} string_array;
#endif
void my_putstr(const char *str)
{
    while (*str)
    {
        write(1, str, 1);
        str++;
    }
} // Ne smog bez putstr
void my_print_words_array(string_array* param_1)
{
    if (param_1 == NULL)
        return;

    for (int i = 0; i < param_1->size; i++)
    {
        my_putstr(param_1->array[i]);
        write(1, "\n", 1);
    }
}