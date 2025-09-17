#include <fcntl.h>
#include "../includes/my_tar.h"

int open_archive(tar_options *opts, int flags) {
    int fd;
    if (opts->file_provided) {
        fd = open(opts->archive_path, flags, 0644);
        if (fd < 0) {
            print_error("Cannot open", opts->archive_path);
        }
    } else {
        fd = (flags & O_WRONLY) ? STDOUT_FILENO : STDIN_FILENO;
    }
    return fd;
}

int find_end_of_data(int archive_fd) {
    tar_header header;
    lseek(archive_fd, 0, SEEK_SET);

    while (read(archive_fd, &header, BLOCK_SIZE) == BLOCK_SIZE) {
        if (is_end_of_archive(&header)) {
            lseek(archive_fd, -BLOCK_SIZE, SEEK_CUR);
            return 0;
        }
        if (my_strncmp(header.magic, MAGIC_USTR, 5) != 0) {
            return print_error("This does not look like a tar archive", NULL);
        }

        long file_size = my_oct_to_dec(header.size);
        long blocks_to_skip = (file_size + BLOCK_SIZE - 1) / BLOCK_SIZE;
        lseek(archive_fd, blocks_to_skip * BLOCK_SIZE, SEEK_CUR);
    }
    lseek(archive_fd, 0, SEEK_END);
    return 0;
}


void write_entries_to_archive(int archive_fd, char **files, int count) {
    for (int i = 0; i < count; i++) {
        tar_header header;
        if (fill_header(files[i], &header) != 0) {
            continue;
        }
        write(archive_fd, &header, BLOCK_SIZE);
        if (header.typeflag == '0' || header.typeflag == '\0') {
            long size = my_oct_to_dec(header.size);
            write_file_to_archive(archive_fd, files[i], size);
        }
    }
}