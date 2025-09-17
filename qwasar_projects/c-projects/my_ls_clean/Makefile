CC		=	gcc
CFLAGS	=	-Wall -Wextra -Werror
NAME	=	my_ls
SRCS	=	my_ls.c
OBJS	=	$(SRCS:.c=.o)

all: $(NAME)

$(NAME): $(OBJS)
	$(CC) $(CFLAGS) -o $(NAME) $(OBJS)

clean:
	rm -f $(OBJS)

fclean: clean
	rm -f $(NAME)

re: fclean all

debug: CFLAGS += -g3 -fsanitize=address
debug: re