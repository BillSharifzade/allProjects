#include <unistd.h>
#include <stdbool.h>

#define EVEN(N) ((N) % 2 == 0)
#define SUCCESS 0

#define EVEN_MSG "I have an even number of arguments."
#define ODD_MSG "I have an odd number of arguments."

typedef enum s_bool
{
    FALSE,
    TRUE
} t_bool;

void my_putchar(char c)
{
    write(1, &c, 1);
}

void my_putstr(char *str)
{
    int index;

    index = 0;
    while (str[index] != '\0') {
        my_putchar(str[index]);
        index++;
    }
}

t_bool my_is_even(int nbr)
{
    return ((EVEN(nbr)) ? TRUE : FALSE);
}

void my_define(int argc)
{
    if (my_is_even(argc) == TRUE) {
        my_putstr(EVEN_MSG);
        my_putchar('\n');
    }
    else {
        my_putstr(ODD_MSG);
        my_putchar('\n');
    }
}