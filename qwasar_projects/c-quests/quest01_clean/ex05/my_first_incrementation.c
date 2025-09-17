#include <stdio.h>

int main() {
    int my_index = 0;

    my_index++;
    printf("%d\n", my_index);

    my_index--; 
    my_index--;
    printf("%d\n", my_index);

    my_index++;
    my_index++;
    my_index++;
    printf("%d\n", my_index);

    return 0;
}
