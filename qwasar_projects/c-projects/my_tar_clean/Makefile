CC = gcc
CFLAGS = -Wall -Wextra -Werror -I./includes
LDFLAGS =

NAME = my_tar

SRCS =	src/main.c \
		src/utils.c \
		src/header.c \
		src/archive_operations.c \
		src/list.c \
		src/extract.c \
		src/create.c \
		src/append.c \
		src/update.c

OBJS = $(SRCS:.c=.o)

INC_DIR = includes

all: $(NAME)

$(NAME): $(OBJS)
	$(CC) $(LDFLAGS) -o $(NAME) $(OBJS)

%.o: %.c
	$(CC) $(CFLAGS) -c $< -o $@

clean:
	rm -f $(OBJS)

fclean: clean
	rm -f $(NAME)

re: fclean all

.PHONY: all clean fclean re