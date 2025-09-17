int my_isalpha(char param_1)
{
    if ((param_1 >= 'A' && param_1 <= 'Z') || (param_1 >= 'a' && param_1 <= 'z')) //I HATE SKOBKI 
    {
        return 1;
    }
    else
    {
        return 0;
    }
}