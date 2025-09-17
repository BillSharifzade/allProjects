
int my_iterative_factorial(int param_1)
{
    if (param_1 < 0)
        return 0;
    if (param_1 == 0)
        return 1;
    int result = 1;
    for (int i = 1; i <= param_1; i++) {
        result *= i;
    }
    return result;
}