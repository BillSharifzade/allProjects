#include <stdlib.h>
int* my_range(int param_1, int param_2)
{
    if (param_1 >= param_2)
    {
        return NULL;
    }
    int size = param_2 - param_1;
    int* range = (int*)malloc(size * sizeof(int));
    if (!range)
    {
        return NULL;
    }
    for (int i = 0; i < size; i++)
    {
        range[i] = param_1 + i;
    }
    return range;
}