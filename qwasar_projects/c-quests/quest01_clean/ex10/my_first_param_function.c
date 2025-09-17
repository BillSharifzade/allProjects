#include <stdio.h>

void detonation_in(int seconds) {
    printf("detonation in... %d seconds.\n", seconds);
}
int main() {
    int timer = 10;

    while (timer > 0) {
        detonation_in(timer);
        timer--;
    }

    return 0;
}
