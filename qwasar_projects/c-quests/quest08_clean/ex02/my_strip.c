#include <stdlib.h>

char* my_strip(char* param_1) {
    if (param_1 == NULL) {
        char* empty_res = (char*)malloc(1);
        if (empty_res == NULL) {
            return NULL;
        }
        empty_res[0] = '\0';
        return empty_res;
    }

    long total_word_chars = 0;
    int word_count = 0;
    int i = 0;
    int in_word = 0;

    while (param_1[i] != '\0') {
        if (param_1[i] != ' ' && param_1[i] != '\t') {
            total_word_chars++;
            if (!in_word) {
                word_count++;
                in_word = 1;
            }
        } else {
            in_word = 0;
        }
        i++;
    }

    long alloc_size;
    if (word_count == 0) {
        alloc_size = 1;
    } else {
        long num_spaces_needed = word_count - 1;
        alloc_size = total_word_chars + num_spaces_needed + 1;
    }

    char* result = (char*)malloc(alloc_size);
    if (result == NULL) {
        return NULL;
    }

    i = 0;
    long j = 0;
    in_word = 0;

    while (param_1[i] != '\0') {
        if (param_1[i] != ' ' && param_1[i] != '\t') {
            if (!in_word) {
                if (j > 0) {
                    result[j++] = ' ';
                }
                in_word = 1;
            }
            result[j++] = param_1[i];
        } else {
            in_word = 0;
        }
        i++;
    }

    result[j] = '\0';

    return result;
}