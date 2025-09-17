#ifndef MY_TAR_H
#define MY_TAR_H

#include <sys/stat.h>
#include <unistd.h>
#include <sys/types.h>

#define BLOCK_SIZE 512
#define NAME_SIZE 100
#define MAGIC_USTR "ustar"
#define VERSION_USTR "00"


typedef struct posix_header {
    char name[100];
    char mode[8];
    char uid[8];
    char gid[8];
    char size[12];
    char mtime[12];
    char chksum[8];
    char typeflag;
    char linkname[100];
    char magic[6];
    char version[2];
    char uname[32];
    char gname[32];
    char devmajor[8];
    char devminor[8];
    char prefix[155];
    char padding[12];
} tar_header;

typedef struct tar_options {
    int create;
    int list;
    int extract;
    int append;
    int update;
    int file_provided;
    char *archive_path;
    char **input_files;
    int input_files_count;
} tar_options;


int print_error(char *message, char *path);
int my_strlen(const char *s);
int my_strcmp(const char *s1, const char *s2);
int my_strncmp(const char *s1, const char *s2, int n);
char *my_strcpy(char *dest, const char *src);
void my_bzero(void *s, int n);
long my_oct_to_dec(char *oct);
void my_dec_to_oct(long dec, char *oct, int size);


int fill_header(const char *filename, tar_header *header);
unsigned int calculate_checksum(tar_header *header);
int is_end_of_archive(tar_header *header);


int open_archive(tar_options *opts, int flags);
int find_end_of_data(int archive_fd);
void write_entries_to_archive(int archive_fd, char **files, int count); 

int write_file_to_archive(int archive_fd, const char *path, long size);

int create_archive(tar_options *opts);
int list_archive(tar_options *opts);
int extract_archive(tar_options *opts);
int append_archive(tar_options *opts);
int update_archive(tar_options *opts);

#endif 