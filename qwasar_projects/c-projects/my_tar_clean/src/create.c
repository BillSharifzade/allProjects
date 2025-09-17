#include <fcntl.h>
#include <stdlib.h>
#include "../includes/my_tar.h"

static int write_padding(int archive_fd, long size) {
    long padding = (BLOCK_SIZE - (size % BLOCK_SIZE)) % BLOCK_SIZE;
    if (padding > 0) {
        char buffer[BLOCK_SIZE];
        my_bzero(buffer, BLOCK_SIZE);
        if (write(archive_fd, buffer, padding) != padding) {
            return 1;
        }
    }
    return 0;
}

static int copy_file_content(int archive_fd, int file_fd, long size) {
    char buffer[BLOCK_SIZE];
    int bytes_read;
    while (size > 0 && (bytes_read = read(file_fd, buffer, BLOCK_SIZE)) > 0) {
        if (write(archive_fd, buffer, bytes_read) != bytes_read) return 1;
        size -= bytes_read;
    }
    return size > 0;
}

int write_file_to_archive(int archive_fd, const char *path, long size) {
    int file_fd = open(path, O_RDONLY);
    if (file_fd < 0) {
        return print_error("Cannot open", (char*)path);
    }

    if (copy_file_content(archive_fd, file_fd, size) != 0) {
        close(file_fd);
        return 1;
    }
    close(file_fd);

    return write_padding(archive_fd, size);
}

static int write_end_of_archive(int archive_fd) {
    char buffer[BLOCK_SIZE * 2];
    my_bzero(buffer, BLOCK_SIZE * 2);
    write(archive_fd, buffer, BLOCK_SIZE * 2);
    return 0;
}

int create_archive(tar_options *opts) {
    int archive_fd = open_archive(opts, O_WRONLY | O_CREAT | O_TRUNC);
    if (archive_fd < 0) return 1;

    for (int i = 0; i < opts->input_files_count; i++) {
        tar_header header;
        if (fill_header(opts->input_files[i], &header) != 0) {
            continue;
        }

        write(archive_fd, &header, BLOCK_SIZE);

        if (header.typeflag == '0' || header.typeflag == '\0') {
            long size = my_oct_to_dec(header.size);
            write_file_to_archive(archive_fd, opts->input_files[i], size);
        }
    }

    write_end_of_archive(archive_fd);
    if (archive_fd != STDOUT_FILENO) close(archive_fd);
    return 0;
}