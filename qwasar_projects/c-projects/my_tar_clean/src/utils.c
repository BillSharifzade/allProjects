#include "../includes/my_tar.h"

int print_error(char *message, char *path) {
    write(2, "my_tar: ", 8);
    if (path) {
        write(2, path, my_strlen(path));
        write(2, ": ", 2);
    }
    write(2, message, my_strlen(message));
    write(2, "\n", 1);
    return 1;
}

int my_strlen(const char *s) {
    int i = 0;
    while (s && s[i]) i++;
    return i;
}

int my_strcmp(const char *s1, const char *s2) {
    while (*s1 && (*s1 == *s2)) {
        s1++;
        s2++;
    }
    return *(const unsigned char*)s1 - *(const unsigned char*)s2;
}

int my_strncmp(const char *s1, const char *s2, int n) {
    while (n-- > 0 && *s1 && (*s1 == *s2)) {
        if (*s1 == '\0') return 0;
        s1++;
        s2++;
    }
    if (n < 0) return 0;
    return *(const unsigned char*)s1 - *(const unsigned char*)s2;
}

char *my_strcpy(char *dest, const char *src) {
    int i = 0;
    while (src[i]) {
        dest[i] = src[i];
        i++;
    }
    dest[i] = '\0';
    return dest;
}

void my_bzero(void *s, int n) {
    char *ptr = (char *)s;
    for (int i = 0; i < n; i++) {
        ptr[i] = 0;
    }
}

long my_oct_to_dec(char *oct) {
    long dec = 0;
    int i = 0;
    while (oct[i] && (oct[i] >= '0' && oct[i] <= '7')) {
        dec = dec * 8 + (oct[i] - '0');
        i++;
    }
    return dec;
}

void my_dec_to_oct(long dec, char *oct, int size) {
    my_bzero(oct, size);
    oct[size - 1] = '\0';
    for (int i = size - 2; i >= 0; i--) {
        oct[i] = (dec % 8) + '0';
        dec /= 8;
    }
}