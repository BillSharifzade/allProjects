#include <unistd.h>
#include <fcntl.h>
#include <stdio.h>
void my_cat(int argc, char *argv[]) {
    char buffer[1024];
    int fd, bytesRead;
    if (argc < 2) {
        write(STDOUT_FILENO, "Usage: my_cat <file1> <file2> ...\n", 33);
        return;
    }
    for (int i = 1; i < argc; i++) {
        fd = open(argv[i], O_RDONLY);
        if (fd == -1) {
            perror(argv[i]);
            continue;
        }
        while ((bytesRead = read(fd, buffer, sizeof(buffer))) > 0) {
            write(STDOUT_FILENO, buffer, bytesRead);
        }
        close(fd);
    }
}
int main(int argc, char *argv[]) {
    my_cat(argc, argv);
    return 0;
}