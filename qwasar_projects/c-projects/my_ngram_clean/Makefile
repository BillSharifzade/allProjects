CC = gcc
CFLAGS = -Wall -Wextra -Werror
SRC = my_ngram.c
OBJ = my_ngram.o
TARGET = my_ngram

all: $(TARGET)
$(TARGET): $(OBJ)
	$(CC) $(CFLAGS) -o $(TARGET) $(OBJ)
$(OBJ): $(SRC)
	$(CC) $(CFLAGS) -c $(SRC)
clean:
	rm -f *.o
fclean: clean
	rm -f $(TARGET)
re: fclean all