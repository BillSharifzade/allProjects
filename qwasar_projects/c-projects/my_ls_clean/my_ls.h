#ifndef MY_LS_H
# define MY_LS_H

# include <sys/stat.h>
# include <time.h>

typedef struct s_options
{
	int	a;
	int	t;
}	t_options;

typedef struct s_file_entry
{
	char				*name;
	char				*path; 
	struct timespec		mtime;
	struct s_file_entry	*next;
}	t_file_entry;

void			parse_args(int argc, char **argv, t_options *opts, t_file_entry **operands);
void			process_operands(t_file_entry *operands, t_options *opts, int operand_count);
t_file_entry	*sort_list(t_file_entry *list, t_options *opts);
void			free_list(t_file_entry *head);

#endif