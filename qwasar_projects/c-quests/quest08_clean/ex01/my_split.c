#include <stdlib.h>
#include "my_split.h"

static int count_segments_internal(const char* str, char sep) {
    int count = 0;
    int i = 0;
    int in_segment = 0;
    while (str[i] != '\0') {
        if (str[i] != sep) {
            if (in_segment == 0) {
                count++;
                in_segment = 1;
            }
        } else {
            in_segment = 0;
        }
        i++;
    }
    return count;
}

string_array* my_split(char* param_1, char* param_2) {
    if (param_1 == NULL || param_2 == NULL || param_2[0] == '\0') {
        string_array* res_empty = (string_array*)malloc(sizeof(string_array));
        if (res_empty == NULL) return NULL;
        res_empty->size = 0;
        res_empty->array = (char**)malloc(0);
        return res_empty;
    }

    if (param_1[0] == '\0') {
        string_array* res_empty = (string_array*)malloc(sizeof(string_array));
        if (res_empty == NULL) return NULL;
        res_empty->size = 0;
        res_empty->array = (char**)malloc(0);
        return res_empty;
    }

    char separator_char = param_2[0];
    int num_segments = count_segments_internal(param_1, separator_char);

    string_array* result = (string_array*)malloc(sizeof(string_array));
    if (result == NULL) return NULL;

    result->size = num_segments;

    if (num_segments == 0) {
        result->array = (char**)malloc(0);
        return result;
    }

    result->array = (char**)malloc(num_segments * sizeof(char*));
    if (result->array == NULL) {
        free(result);
        return NULL;
    }

    int current_pos_in_param1 = 0;
    int segment_array_idx = 0;

    while (segment_array_idx < num_segments && param_1[current_pos_in_param1] != '\0') {
        while (param_1[current_pos_in_param1] != '\0' && param_1[current_pos_in_param1] == separator_char) {
            current_pos_in_param1++;
        }

        if (param_1[current_pos_in_param1] == '\0') {
            break;
        }

        int segment_start_pos = current_pos_in_param1;

        while (param_1[current_pos_in_param1] != '\0' && param_1[current_pos_in_param1] != separator_char) {
            current_pos_in_param1++;
        }
        int segment_len = current_pos_in_param1 - segment_start_pos;

        char* segment_str = (char*)malloc(segment_len + 1);
        if (segment_str == NULL) {
            for (int k = 0; k < segment_array_idx; k++) {
                free(result->array[k]);
            }
            free(result->array);
            free(result);
            return NULL;
        }

        for (int k = 0; k < segment_len; k++) {
            segment_str[k] = param_1[segment_start_pos + k];
        }
        segment_str[segment_len] = '\0';

        result->array[segment_array_idx++] = segment_str;
    }
    return result;
}