int my_iterative_pow(int param_1, int param_2)
{
    if (param_2 < 0)
        return 0;
    
    if (param_2 == 0)
        return 1;
    
    int result = param_1;
    for (int i = 1; i < param_2; i++) {
        result *= param_1;
    }
    return result;
}