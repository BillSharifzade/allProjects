int my_fibonacci(int param_1)
{
    if (param_1 < 0)
        return -1;
    if (param_1 == 0)
        return 0;
    if (param_1 == 1 || param_1 == 2)
        return 1;
    return my_fibonacci(param_1 - 1) + my_fibonacci(param_1 - 2);
}