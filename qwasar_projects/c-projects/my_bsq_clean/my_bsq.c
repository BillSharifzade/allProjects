#include <unistd.h>
#include "my_bsq.h"

static int error_msg(const char *msg) {
    int len = 0;
    while (msg[len]) {
        len++;
    }
    write(2, msg, len);
    return 1;
}

int my_bsq(int argc, char *argv[]) {
    if (argc != 2) {
        return error_msg("Usage: ./my_bsq map_file\n");
    }

    Map *map = load_map(argv[1]);
    if (!map) {
        return error_msg("Map Error\n");
    }

    Square largest_square = find_largest_square(map);
    if (largest_square.size > 0) {
        fill_square(map, largest_square);
    }

    print_map(map);
    free_map(map);

    return 0;
}