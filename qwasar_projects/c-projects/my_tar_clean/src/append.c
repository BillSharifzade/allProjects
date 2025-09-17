#include <fcntl.h>
#include <stdlib.h>
#include "../includes/my_tar.h"

int append_archive(tar_options *opts) {
    int archive_fd = open_archive(opts, O_RDWR | O_CREAT);
    if (archive_fd < 0) {
        return 1;
    }

    if (find_end_of_data(archive_fd) != 0) {
        close(archive_fd);
        return 1;
    }

    write_entries_to_archive(archive_fd, opts->input_files, opts->input_files_count);

    char end_block[BLOCK_SIZE * 2];
    my_bzero(end_block, BLOCK_SIZE * 2);
    write(archive_fd, end_block, BLOCK_SIZE * 2);

    close(archive_fd);
    return 0;
}