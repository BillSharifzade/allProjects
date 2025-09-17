#ifndef MY_SPLIT_H_QWASAR 
#define MY_SPLIT_H_QWASAR

#ifndef STRUCT_STRING_ARRAY 
#define STRUCT_STRING_ARRAY
typedef struct s_string_array {
    int size;
    char** array;
} string_array;
#endif 

string_array* my_split(char* param_1, char* param_2);

#endif 