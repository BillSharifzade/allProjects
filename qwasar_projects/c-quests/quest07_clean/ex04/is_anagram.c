int is_anagram(char* param_1, char* param_2)
{
    int char_count[95] = {0};
    
    int i = 0;
    while (param_1[i] != '\0') {
        char_count[param_1[i] - 32]++;
        i++;
    }
    
    i = 0;
    while (param_2[i] != '\0') {
        char_count[param_2[i] - 32]--;
        i++;
    }
    
    for (i = 0; i < 95; i++) {
        if (char_count[i] != 0) {
            return 0;
        }
    }
    
    return 1;
}