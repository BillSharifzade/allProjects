#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdbool.h>
#include <time.h>
#include <unistd.h>
#include "messages.h"

#define CODE_LENGTH 4
#define DEFAULT_ATTEMPTS 10
#define MAX_PIECES 9

typedef struct
{
    char code[CODE_LENGTH + 1];
    int attemps;
    bool is_code_provided;
} GameConfig;

typedef struct 
{
    int well_placed;
    int misplaced;
} Feedback;  

// Custom strlen implementation
int my_strlen(const char *str) {
    int len = 0;
    if (!str) return 0;
    while (str[len] != '\0') {
        len++;
    }
    return len;
}

// Custom strchr implementation
char* my_strchr(const char *str, char c) {
    if (!str) return NULL;
    while (*str != '\0') {
        if (*str == c) {
            return (char*)str;
        }
        str++;
    }
    return NULL;
}

// Custom strncpy implementation
char* my_strncpy(char *dest, const char *src, int n) {
    if (!dest || !src) return dest;
    int i = 0;
    while (i < n && src[i] != '\0') {
        dest[i] = src[i];
        i++;
    }
    while (i < n) {
        dest[i] = '\0';
        i++;
    }
    return dest;
}

bool is_valide_code(char *code) {
    if (!code) {
        return false;
    }
    int len = my_strlen(code);
    if (len == 0 || len > 4) {
        return false;
    }

    for(int i = 0; i < CODE_LENGTH; i++) {
        if (code[i] < '0' || code[i] > '8') { // ASCII 48 = '0' to 56 = '8'
            return false;
        }
        //Check duplicates
        if(my_strchr(code + i +1, code[i]) != NULL) {
            return false;
        }
    }
    
    return true;
}

bool parse_arguments(int args, char *argv[], GameConfig *game_config) {
     if (!argv || !game_config) {
         return false;
     }

    game_config->attemps = DEFAULT_ATTEMPTS;
    game_config->code[0] = '\0';  
    game_config->is_code_provided = false;

    for (int i =  1; i < args; i++) {
        if (i % 2 == 1 && strcmp(argv[i], "-c") == 0) {
            if (i + 1 >= args ) {
                printf("The options -c need a code argument\n");
                return false;
            }

            if (!is_valide_code(argv[i + 1])) {
                printf("Wrong input! Code must be only non duplicate digits (0-8)\n");
                return false;
                
            } 
            
            // Process with copying
            my_strncpy(game_config->code, argv[i + 1], CODE_LENGTH);
            game_config->code[CODE_LENGTH] = '\0';
            game_config->is_code_provided = true;
            i++; // Skip to the next options

        } else if (i % 2 == 1 && strcmp(argv[i], "-t") == 0) {
            if (i + 1 >= args) {
                printf("The options -t require an attempts argument\n");
                return false;
            }

            int attemps = atoi(argv[i + 1]);
            if (attemps < 1 || attemps > 10) {
                printf("Invalide attemps! Please entre a numbre between 1 and 10\n");
                return false;
            }
                
            game_config->attemps = attemps;
            i++; // Skip to the next options

        } else {
            printf("Invalide options\n");
            printf("Usage: %s -c [ CODE ] -t [ ATTEMPTS ]\n", argv[0]);
            return false;
        }
    }

    return true;
}

void generate_random_cade(char *code) {
    if (!code) {
        return;
    }

    srand(time(NULL));

    int num_index = 0;
    while (num_index < CODE_LENGTH) {
        int generated = rand() % MAX_PIECES; // Get a random between 0 - 8 (rand() % (max - min + 1)) + min.
        char digit = '0' + generated;
        if (my_strchr(code, digit) == NULL) {
            code[num_index] = digit;
            num_index++;
        }
    }

    code[CODE_LENGTH] = '\0';
}

bool read_user_guess(char *guess_code) {
    char c;
    int index = 0;

    printf(">");
    fflush(stdout); // real time output

    while (index < CODE_LENGTH + 1) {  // +1 for potential newline
        ssize_t bytes_read = read(0, &c, 1);
        
        if (bytes_read == 0) {  // EOF (Ctrl+D)
            return false;
        }
        
        if (bytes_read == -1) {  // Error
            return false;
        }
        
        if (c == '\n') {  // End of input (Entre)
            break;
        }
        
        if (index < CODE_LENGTH) {
            guess_code[index] = c;
            index++;
        }
    }
    
    guess_code[index] = '\0';
    return true;
}

Feedback calculte_guess_result(const char *secret_code, const char *guess_code) {
     Feedback feedback = {0, 0};

    // First pass: count well-placed pieces
    for (int i = 0; i < CODE_LENGTH; i++) {
        if (secret_code[i] == guess_code[i]) {
            feedback.well_placed++;
        }
    }
    
    // Second pass: count misplaced pieces
    for (int i = 0; i < CODE_LENGTH; i++) {
        if (secret_code[i] != guess_code[i]) { 
            for (int j = 0; j < CODE_LENGTH; j++) {
                if (i != j && secret_code[j] == guess_code[i] && secret_code[j] != guess_code[j]) {
                    feedback.misplaced++;
                    break;
                }
            }
        }
    }

    return feedback;
}

/**
 * Main game loop
 */
void play_game(const GameConfig *game_config) {
    char guess[CODE_LENGTH + 2]; // +2 for \n (entre) and null terminator
    int attempts = 0;
    
    print_start();
    
    while (attempts < game_config->attemps) {
        print_guess_round(attempts);
        attempts++;
        
        if (!read_user_guess(guess)) {
            printf("Error reading input. Exiting.\n");
            break;
        }
        
        if (!is_valide_code(guess)) {
            print_wrong_input_error();
            attempts--; // Don't count invalid attempts
            continue;
        }
        
        // Check if player won
        if (strcmp(game_config->code, guess) == 0) {
            print_congrats();
            return;
        }
        
        // Calculate and display feedback
        Feedback feedback = calculte_guess_result(game_config->code, guess);
        print_guess_feedback(feedback.well_placed, feedback.misplaced);
    }
    
}

int main(int args, char *argv[]) {
    GameConfig game;

    if (!parse_arguments(args, argv, &game)) {
        return 1;
    }

    if (!game.is_code_provided) {
        generate_random_cade(game.code);
    }

     // Start the game
    play_game(&game);

    return 0;
}