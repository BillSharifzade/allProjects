int my_strcmp(char* param_1, char* param_2) {
    while (*param_1 && (*param_1 == *param_2)) {
        param_1++;
        param_2++;
    }
    return (unsigned char)*param_1 - (unsigned char)*param_2;
}