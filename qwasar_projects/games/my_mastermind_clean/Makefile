# Compiler and flags
CC = gcc
CFLAGS = -Wall -Wextra -Werror

# Target executable
TARGET = my_mastermind

# Source files 
SOURCES = my_mastermind.c messages.c

# Convert all .c to .o
OBJECTS = $(SOURCES:.c=.o)

# Default target
all: $(TARGET)

# Link object files to create the final executable
$(TARGET): $(OBJECTS)
	$(CC) $(CFLAGS) $(OBJECTS) -o $(TARGET)

# Compile .c to .o (generic rule)
%.o: %.c
	$(CC) $(CFLAGS) -c $< -o $@

# Header dependencies
%.o: messages.h

# Clean object files
clean:
	rm -f $(OBJECTS)

# Clean everything
fclean: clean
	rm -f $(TARGET)

# Rebuild everything
re: fclean all

.PHONY: all clean fclean re