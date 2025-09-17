#include <stdlib.h>
#include "../includes/my_tar.h"

static int validate_options(tar_options *opts, int ac) {
    if (opts->create + opts->append + opts->list + opts->update + opts->extract != 1) {
        return print_error("You must specify exactly one of '-c', '-r', '-t', '-u', or '-x'", NULL);
    }
    if (opts->file_provided && ac < 3) {
        return print_error("Option -f requires an archive name", NULL);
    }
    if (!opts->file_provided && (opts->append || opts->update)) {
        return print_error("Options -r and -u require the -f option", NULL);
    }
    if ((opts->create || opts->append || opts->update) && opts->input_files_count == 0) {
        return print_error("Cowardly refusing to create an empty archive", NULL);
    }
    return 0;
}

static int parse_options(int ac, char **av, tar_options *opts) {
    int i = 1;

    if (ac < 2) {
        return print_error("You must specify one of the '-c', '-r', '-t', '-u', or '-x' options", NULL);
    }

    for (int j = 0; av[1][j]; j++) {
        switch (av[1][j]) {
            case 'c': opts->create = 1; break;
            case 'r': opts->append = 1; break;
            case 't': opts->list = 1; break;
            case 'u': opts->update = 1; break;
            case 'x': opts->extract = 1; break;
            case 'f': opts->file_provided = 1; break;
        }
    }

    if (opts->file_provided) {
        opts->archive_path = av[2];
        i = 3;
    }

    if (i < ac) {
        opts->input_files = &av[i];
        opts->input_files_count = ac - i;
    } else {
        opts->input_files = NULL;
        opts->input_files_count = 0;
    }

    return validate_options(opts, ac);
}

int main(int ac, char **av) {
    tar_options opts;
    my_bzero(&opts, sizeof(tar_options));

    if (parse_options(ac, av, &opts) != 0) {
        return 1;
    }

    if (opts.create) return create_archive(&opts);
    if (opts.list) return list_archive(&opts);
    if (opts.extract) return extract_archive(&opts);
    if (opts.append) return append_archive(&opts);
    if (opts.update) return update_archive(&opts);

    return 1;
}