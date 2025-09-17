CC = gcc
CFLAGS = -Wall -Wextra -Werror

NAME = my_bsq

SRCS =	main.c \
		my_bsq.c \
		map_handler.c \
		algorithm.c

OBJS = $(SRCS:.c=.o)

all: $(NAME)

$(NAME): $(OBJS)
	$(CC) $(CFLAGS) -o $(NAME) $(OBJS)

%.o: %.c my_bsq.h
	$(CC) $(CFLAGS) -c $< -o $@

clean:
	rm -f $(OBJS)

fclean: clean
	rm -f $(NAME)

re: fclean all

.PHONY: all clean fclean re