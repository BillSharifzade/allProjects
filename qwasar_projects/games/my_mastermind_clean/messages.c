#include <stdio.h>
#include "messages.h"


void print_start() {
    printf("Will you find the secret code?\n");
    printf("Please enter a valid guess\n");
    printf("---\n");
}

void print_guess_round(int round) {
    printf("Round %d\n", round);
}

void print_guess_feedback(int well_placed, int misplaced) {
    printf("Well placed pieces: %d\n", well_placed);
    printf("Misplaced pieces: %d\n", misplaced);
    printf("---\n");
}

void print_congrats() {
    printf("Congratz! You did it!\n");
}

void print_wrong_input_error() {
    printf("Wrong input!\n");
}