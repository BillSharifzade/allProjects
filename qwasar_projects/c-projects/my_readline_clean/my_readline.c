#include <stdlib.h>
#include <unistd.h>
#include <fcntl.h>
#include <stdio.h>

static char *g_storage = NULL;
int READLINE_READ_SIZE = 10;

static size_t my_strlen(const char *s)
{
    size_t i = 0;
    if (!s)
        return (0);
    while (s[i])
        i++;
    return (i);
}

static char *my_strchr(const char *s, int c)
{
    if (!s)
        return (NULL);
    while (*s)
    {
        if (*s == (char)c)
            return ((char *)s);
        s++;
    }
    if ((char)c == '\0')
        return ((char *)s);
    return (NULL);
}

static char *my_strjoin_and_free(char *s1, const char *s2)
{
    char   *new_str;
    size_t len1 = my_strlen(s1);
    size_t len2 = my_strlen(s2);
    size_t i = -1;
    size_t j = -1;

    new_str = malloc(sizeof(char) * (len1 + len2 + 1));
    if (!new_str)
    {
        free(s1);
        return (NULL);
    }
    while (++i < len1)
        new_str[i] = s1[i];
    while (++j < len2)
        new_str[i + j] = s2[j];
    new_str[i + j] = '\0';
    free(s1);
    return (new_str);
}

void init_my_readline()
{
    if (g_storage)
    {
        free(g_storage);
        g_storage = NULL;
    }
}

char *my_readline(int fd)
{
    char   *line;
    char   *remainder;
    char   *newline_pos;
    char   buffer[READLINE_READ_SIZE + 1];
    ssize_t bytes_read;

    if (fd < 0 || READLINE_READ_SIZE <= 0)
        return (NULL);

    bytes_read = 1;
    while (bytes_read > 0 && !my_strchr(g_storage, '\n'))
    {
        bytes_read = read(fd, buffer, READLINE_READ_SIZE);
        if (bytes_read < 0)
        {
            init_my_readline();
            return (NULL);
        }
        buffer[bytes_read] = '\0';
        g_storage = my_strjoin_and_free(g_storage, buffer);
        if (!g_storage)
            return (NULL);
    }

    newline_pos = my_strchr(g_storage, '\n');
    if (newline_pos)
    {
        *newline_pos = '\0';
        line = my_strjoin_and_free(NULL, g_storage);
        remainder = my_strjoin_and_free(NULL, newline_pos + 1);
        free(g_storage);
        g_storage = remainder;
        if (!line || !g_storage)
        {
            free(line);
            init_my_readline();
            return (NULL);
        }
    }
    else
    {
        if (!g_storage || *g_storage == '\0')
        {
            init_my_readline();
            return (NULL);
        }
        line = g_storage;
        g_storage = NULL;
    }
    return (line);
}

int main(int ac, char **av)
{
    char *str = NULL;
    int fd;

    if (ac != 2)
    {
        fd = 0;
    }
    else
    {
        fd = open(av[1], O_RDONLY);
        if (fd == -1)
        {
            perror("Error opening file");
            return (1);
        }
    }

    while ((str = my_readline(fd)) != NULL)
    {
        printf("%s\n", str);
        free(str);
    }

    init_my_readline();
    if (fd > 2)
        close(fd);

    return (0);
}