#include <stdlib.h>

char* my_union(char* param_1, char* param_2)
{
    int i, j, len1 = 0, len2 = 0;
    char *result;
    int seen[128] = {0};

    while (param_1[len1])
        len1++;
    while (param_2[len2])
        len2++;

    result = (char*)malloc(len1 + len2 + 1);
    if (!result)
        return NULL;

    j = 0;
    for (i = 0; i < len1; i++) {
        if (!seen[(unsigned char)param_1[i]]) {
            seen[(unsigned char)param_1[i]] = 1;
            result[j++] = param_1[i];
        }
    }

    for (i = 0; i < len2; i++) {
        if (!seen[(unsigned char)param_2[i]]) {
            seen[(unsigned char)param_2[i]] = 1;
            result[j++] = param_2[i];
        }
    }

    result[j] = '\0';

    return result;
}