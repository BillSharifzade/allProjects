void my_putchar(char c) {
    write(1, &c, 1);
}

void my_print_alphabet() {
    char letter;

    for (letter = 'a'; letter <= 'z'; letter++) {
        my_putchar(letter);
    }

    my_putchar('\n');
}