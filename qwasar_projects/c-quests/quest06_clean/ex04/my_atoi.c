int my_atoi(char* param_1)
{
    int result = 0;
    int sign = 1;
    int i = 0;
    if (param_1[0] == '-') {
        sign = -1;
        i++;
    }
    else if (param_1[0] == '+') {
        i++;
    }
    while (param_1[i] >= '0' && param_1[i] <= '9') {
        result = result * 10 + (param_1[i] - '0');
        i++;
    }
    return result * sign;
}