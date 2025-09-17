#include <stdio.h>

void my_swap(int* param_1, int* param_2)
{
    int boxik = *param_1; 
    *param_1 = *param_2; 
    *param_2 = boxik;
}
