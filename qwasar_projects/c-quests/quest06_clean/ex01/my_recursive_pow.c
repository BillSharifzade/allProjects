int my_recursive_pow(int param_1, int param_2)
{
    if (param_2 < 0)
        return 0;
    if (param_2 == 0)
        return 1;
    return param_1 * my_recursive_pow(param_1, param_2 - 1);
}