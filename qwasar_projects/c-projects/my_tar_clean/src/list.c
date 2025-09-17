#include <fcntl.h>
#include <stdlib.h>
#include "../includes/my_tar.h"

int list_archive(tar_options *opts) {
    int archive_fd = open_archive(opts, O_RDONLY);
    if (archive_fd < 0) return 1;

    tar_header header;
    while (read(archive_fd, &header, BLOCK_SIZE) == BLOCK_SIZE) {
        if (is_end_of_archive(&header)) break;

        if (my_strncmp(header.magic, MAGIC_USTR, 5) != 0) {
            print_error("This does not look like a tar archive", opts->archive_path);
            if (archive_fd != STDIN_FILENO) close(archive_fd);
            return 1;
        }

        write(1, header.name, my_strlen(header.name));
        write(1, "\n", 1);

        long file_size = my_oct_to_dec(header.size);
        long blocks_to_skip = (file_size + BLOCK_SIZE - 1) / BLOCK_SIZE;
        lseek(archive_fd, blocks_to_skip * BLOCK_SIZE, SEEK_CUR);
    }

    if (archive_fd != STDIN_FILENO) close(archive_fd);
    return 0;
}