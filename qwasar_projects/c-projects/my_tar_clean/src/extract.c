#include <fcntl.h>
#include <stdlib.h>
#include <utime.h>
#include <time.h>
#include "../includes/my_tar.h"

static int write_extracted_file(int out_fd, int archive_fd, long size) {
    char buffer[BLOCK_SIZE];
    long bytes_left = size;
    int to_read, bytes_written;

    while (bytes_left > 0) {
        to_read = (bytes_left < BLOCK_SIZE) ? bytes_left : BLOCK_SIZE;
        int bytes_read = read(archive_fd, buffer, to_read);
        if (bytes_read <= 0) return 1;

        bytes_written = write(out_fd, buffer, bytes_read);
        if (bytes_written != bytes_read) return 1;

        bytes_left -= bytes_read;
    }
    return 0;
}

static void set_file_metadata(tar_header *header) {
    struct utimbuf times;
    times.actime = time(NULL);
    times.modtime = my_oct_to_dec(header->mtime);
    utime(header->name, &times);
}

static int extract_file(int archive_fd, tar_header *header) {
    long size = my_oct_to_dec(header->size);
    mode_t mode = my_oct_to_dec(header->mode);
    int out_fd = open(header->name, O_WRONLY | O_CREAT | O_TRUNC, mode);
    if (out_fd < 0) return print_error("Cannot create file", header->name);

    if (write_extracted_file(out_fd, archive_fd, size) != 0) {
        close(out_fd);
        return 1;
    }
    close(out_fd);
    set_file_metadata(header);

    long padding_to_skip = (BLOCK_SIZE - (size % BLOCK_SIZE)) % BLOCK_SIZE;
    if (padding_to_skip > 0) {
        lseek(archive_fd, padding_to_skip, SEEK_CUR);
    }
    return 0;
}

static int extract_dir(tar_header *header) {
    mode_t mode = my_oct_to_dec(header->mode);
    mkdir(header->name, mode);
    return 0;
}

static int extract_symlink(tar_header *header) {
    unlink(header->name);
    if (symlink(header->linkname, header->name) != 0) {
        return print_error("Cannot create symlink", header->name);
    }
    return 0;
}

int extract_archive(tar_options *opts) {
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

        if (header.typeflag == '5') extract_dir(&header);
        else if (header.typeflag == '2') extract_symlink(&header);
        else extract_file(archive_fd, &header);
    }

    if (archive_fd != STDIN_FILENO) close(archive_fd);
    return 0;
}