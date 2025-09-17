char* my_strncpy(char* param_1, char* param_2, int param_3) {
    char* prikol = param_1;
    while (param_3 > 0 && *param_2) {
        *param_1 = *param_2;
        param_1++;
        param_2++;
        param_3--;
    }
    while (param_3 > 0) {
        *param_1 = '\0';
        param_1++;
        param_3--;
    }   
    return prikol;
}