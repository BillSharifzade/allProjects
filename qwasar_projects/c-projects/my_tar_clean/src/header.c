#include <pwd.h>
#include <grp.h>
#include <time.h>
#include "../includes/my_tar.h"

static void fill_type_and_link(struct stat *st, tar_header *header, const char *filename) {
    if (S_ISREG(st->st_mode)) {
        header->typeflag = '0';
    } else if (S_ISDIR(st->st_mode)) {
        header->typeflag = '5';
    } else if (S_ISLNK(st->st_mode)) {
        header->typeflag = '2';
        readlink(filename, header->linkname, 100);
    } else {
        header->typeflag = '0';
    }
}

static void fill_user_group_info(struct stat *st, tar_header *header) {
    struct passwd *pwd = getpwuid(st->st_uid);
    if (pwd) my_strcpy(header->uname, pwd->pw_name);
    struct group *grp = getgrgid(st->st_gid);
    if (grp) my_strcpy(header->gname, grp->gr_name);
}

int fill_header(const char *filename, tar_header *header) {
    struct stat st;
    if (lstat(filename, &st) != 0) {
        return print_error("Cannot stat: No such file or directory", (char*)filename);
    }

    my_bzero(header, sizeof(tar_header));

    my_strcpy(header->name, filename);
    my_dec_to_oct(st.st_mode & 07777, header->mode, 8);
    my_dec_to_oct(st.st_uid, header->uid, 8);
    my_dec_to_oct(st.st_gid, header->gid, 8);
    my_dec_to_oct(S_ISDIR(st.st_mode) ? 0 : st.st_size, header->size, 12);
    my_dec_to_oct(st.st_mtime, header->mtime, 12);

    fill_type_and_link(&st, header, filename);
    my_strcpy(header->magic, MAGIC_USTR);
    my_strcpy(header->version, VERSION_USTR);
    fill_user_group_info(&st, header);

    my_dec_to_oct(calculate_checksum(header), header->chksum, 7);
    header->chksum[7] = ' ';

    return 0;
}

unsigned int calculate_checksum(tar_header *header) {
    unsigned int sum = 0;
    char *p = (char *)header;
    my_bzero(header->chksum, 8);
    for (int i = 0; i < BLOCK_SIZE; i++) {
        sum += (unsigned char)p[i];
    }
    sum += ' ' * 8; 
    return sum;
}

int is_end_of_archive(tar_header *header) {
    char *p = (char *)header;
    for (int i = 0; i < BLOCK_SIZE; i++) {
        if (p[i] != 0) return 0;
    }
    return 1;
}