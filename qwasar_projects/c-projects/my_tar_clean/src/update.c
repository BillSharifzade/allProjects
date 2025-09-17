#include <fcntl.h>
#include <stdlib.h>
#include "../includes/my_tar.h"

typedef struct file_entry {
    char name[NAME_SIZE + 1];
    long mtime;
    struct file_entry *next;
} file_entry;

static void free_file_entries(file_entry *head) {
    while (head) {
        file_entry *current = head;
        head = head->next;
        free(current);
    }
}

static file_entry* read_archive_entries(int archive_fd) {
    file_entry *head = NULL, *current = NULL;
    tar_header header;
    lseek(archive_fd, 0, SEEK_SET);

    while (read(archive_fd, &header, BLOCK_SIZE) == BLOCK_SIZE && !is_end_of_archive(&header)) {
        file_entry *new_entry = malloc(sizeof(file_entry));
        if (!new_entry) { print_error("Malloc failed", NULL); return NULL; }
        my_strcpy(new_entry->name, header.name);
        new_entry->mtime = my_oct_to_dec(header.mtime);
        new_entry->next = NULL;

        if (!head) head = new_entry;
        else current->next = new_entry;
        current = new_entry;

        long size = my_oct_to_dec(header.size);
        lseek(archive_fd, (size + BLOCK_SIZE - 1) / BLOCK_SIZE * BLOCK_SIZE, SEEK_CUR);
    }
    return head;
}

static char** get_files_to_update(tar_options *opts, file_entry *head, int *count_to_add) {
    char **files_to_add = malloc(sizeof(char*) * opts->input_files_count);
    if (!files_to_add) { print_error("Malloc failed", NULL); return NULL; }
    *count_to_add = 0;

    for (int i = 0; i < opts->input_files_count; i++) {
        struct stat st;
        if (lstat(opts->input_files[i], &st) != 0) continue;

        int found = 0;
        for (file_entry *current = head; current; current = current->next) {
            if (my_strcmp(opts->input_files[i], current->name) == 0) {
                found = 1;
                if (st.st_mtime > current->mtime) {
                    files_to_add[(*count_to_add)++] = opts->input_files[i];
                }
                break;
            }
        }
        if (!found) {
            files_to_add[(*count_to_add)++] = opts->input_files[i];
        }
    }
    return files_to_add;
}

int update_archive(tar_options *opts) {
    int archive_fd = open_archive(opts, O_RDWR | O_CREAT);
    if (archive_fd < 0) return 1;

    file_entry *head = read_archive_entries(archive_fd);
    int count_to_add = 0;
    char **files_to_add = get_files_to_update(opts, head, &count_to_add);

    if (files_to_add && count_to_add > 0) {
        find_end_of_data(archive_fd);

        write_entries_to_archive(archive_fd, files_to_add, count_to_add);

        char end_block[BLOCK_SIZE * 2];
        my_bzero(end_block, BLOCK_SIZE * 2);
        write(archive_fd, end_block, BLOCK_SIZE * 2);
    }

    free(files_to_add);
    free_file_entries(head);
    close(archive_fd);
    return 0;
}