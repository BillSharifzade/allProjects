char* rcapitalize(char* param_1)
{
    if (!param_1)
        return param_1;
        
    int i = 0;
    int new_word = 1;
    
    while (param_1[i] != '\0') {
        int is_letter = (param_1[i] >= 'a' && param_1[i] <= 'z') || 
                        (param_1[i] >= 'A' && param_1[i] <= 'Z');
        
        int is_delimiter = (param_1[i] == ' ' || param_1[i] == '\t');
        
        if (is_letter) {
            if (new_word) {
                new_word = 0;
            }
            
            if (param_1[i] >= 'A' && param_1[i] <= 'Z') {
                param_1[i] = param_1[i] + 32;
            }
            
            if (param_1[i+1] == '\0' || param_1[i+1] == ' ' || param_1[i+1] == '\t') {
                if (param_1[i] >= 'a' && param_1[i] <= 'z') {
                    param_1[i] = param_1[i] - 32;
                }
            }
        } else if (is_delimiter) {
            new_word = 1;
        }
        
        i++;
    }
    
    return param_1;
}