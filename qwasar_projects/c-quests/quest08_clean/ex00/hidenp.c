int hidenp(char* param_1, char* param_2) {
    while (*param_1 != '\0' && *param_2 != '\0') {
        if (*param_1 == *param_2) {
            param_1++;
        }
        param_2++;
    }

    if (*param_1 == '\0') {
        return 1;
    } else {
        return 0;
    }
}