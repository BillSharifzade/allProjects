char* reverse_string(char* param_1) {
    int left = 0, right = 0;
    while (param_1[right] != '\0') right++;
    right--;
    while (left < right) {
        char boxik = param_1[left];
        param_1[left] = param_1[right];
        param_1[right] = boxik;
        left++; right--;
    }
    return param_1;
}