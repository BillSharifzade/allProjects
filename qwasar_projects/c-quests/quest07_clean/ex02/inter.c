#include <stdlib.h>

char* inter(char* param_1, char* param_2)
{
    int i, j, len1 = 0, len2 = 0;
    char *result;
    int seen1[128] = {0};
    int seen2[128] = {0};

    while (param_1[len1])
        len1++;
    while (param_2[len2])
        len2++;

    result = (char*)malloc(len1 + 1);
    if (!result)
        return NULL;

    for (i = 0; i < len2; i++)
        seen2[(unsigned char)param_2[i]] = 1;

    j = 0;
    for (i = 0; i < len1; i++) {
        if (seen2[(unsigned char)param_1[i]] && !seen1[(unsigned char)param_1[i]]) {
            seen1[(unsigned char)param_1[i]] = 1;
            result[j++] = param_1[i];
        }
    }

    result[j] = '\0';
    return result;
}