#include <sys/types.h>
#include <dirent.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include "my_ls.h"

/*UTILITY FUNCTIONS*/

char	*ft_strdup(const char *s)
{
	char	*new_str;
	size_t	i;

	i = 0;
	while (s[i])
		i++;
	new_str = malloc(sizeof(char) * (i + 1));
	if (!new_str)
		return (NULL);
	i = 0;
	while (s[i])
	{
		new_str[i] = s[i];
		i++;
	}
	new_str[i] = '\0';
	return (new_str);
}

t_file_entry	*create_node(const char *path, const char *name)
{
	t_file_entry	*node;

	node = malloc(sizeof(t_file_entry));
	if (!node)
		return (NULL);
	node->path = ft_strdup(path);
	node->name = ft_strdup(name);
	node->next = NULL;
	node->mtime.tv_sec = 0;
	node->mtime.tv_nsec = 0;
	return (node);
}

void	add_node_back(t_file_entry **head, t_file_entry *new_node)
{
	t_file_entry	*current;

	if (!head || !new_node)
		return ;
	if (!*head)
	{
		*head = new_node;
		return ;
	}
	current = *head;
	while (current->next)
		current = current->next;
	current->next = new_node;
}

void	free_list(t_file_entry *head)
{
	t_file_entry	*tmp;

	while (head)
	{
		tmp = head;
		head = head->next;
		free(tmp->name);
		free(tmp->path);
		free(tmp);
	}
}

/*SORTING LOGIC*/

int	compare_entries(t_file_entry *a, t_file_entry *b, t_options *opts)
{
	if (opts->t)
	{
		if (a->mtime.tv_sec != b->mtime.tv_sec)
			return (b->mtime.tv_sec - a->mtime.tv_sec > 0 ? 1 : -1);
		if (a->mtime.tv_nsec != b->mtime.tv_nsec)
			return (b->mtime.tv_nsec - a->mtime.tv_nsec > 0 ? 1 : -1);
	}
	int i = 0;
	while (a->name[i] && b->name[i] && a->name[i] == b->name[i])
		i++;
	return (a->name[i] - b->name[i]);
}

t_file_entry	*sorted_merge(t_file_entry *a, t_file_entry *b, t_options *opts)
{
	t_file_entry	*result;

	if (a == NULL)
		return (b);
	if (b == NULL)
		return (a);
	result = NULL;
	if (compare_entries(a, b, opts) <= 0)
	{
		result = a;
		result->next = sorted_merge(a->next, b, opts);
	}
	else
	{
		result = b;
		result->next = sorted_merge(a, b->next, opts);
	}
	return (result);
}

void	split_list(t_file_entry *source, t_file_entry **front, t_file_entry **back)
{
	t_file_entry	*fast;
	t_file_entry	*slow;

	slow = source;
	fast = source->next;
	while (fast != NULL)
	{
		fast = fast->next;
		if (fast != NULL)
		{
			slow = slow->next;
			fast = fast->next;
		}
	}
	*front = source;
	*back = slow->next;
	slow->next = NULL;
}

t_file_entry	*sort_list(t_file_entry *list, t_options *opts)
{
	t_file_entry	*head;
	t_file_entry	*a;
	t_file_entry	*b;

	head = list;
	if (head == NULL || head->next == NULL)
		return (head);
	split_list(head, &a, &b);
	a = sort_list(a, opts);
	b = sort_list(b, opts);
	return (sorted_merge(a, b, opts));
}


/*CORE LS LOGIC*/

void	fill_stats(t_file_entry *list, t_options *opts)
{
	struct stat	st;

	if (!opts->t) 
		return ;
	while (list)
	{
		if (lstat(list->path, &st) == 0)
			list->mtime = st.st_mtim;
		list = list->next;
	}
}

void	print_list(t_file_entry *head)
{
	while (head)
	{
		printf("%s\n", head->name);
		head = head->next;
	}
}

void	process_directory(const char *dir_name, t_options *opts)
{
	DIR				*dir_stream;
	struct dirent	*entry;
	t_file_entry	*contents;

	dir_stream = opendir(dir_name);
	if (!dir_stream)
	{
		write(2, "my_ls: cannot open directory '", 30);
		write(2, dir_name, strlen(dir_name));
		write(2, "'\n", 2);
		return ;
	}
	contents = NULL;
	while ((entry = readdir(dir_stream)) != NULL)
	{
		if (entry->d_name[0] == '.' && !opts->a)
			continue ;
		char path_buffer[1024]; 
		snprintf(path_buffer, sizeof(path_buffer), "%s/%s", dir_name, entry->d_name);
		add_node_back(&contents, create_node(path_buffer, entry->d_name));
	}
	closedir(dir_stream);
	fill_stats(contents, opts);
	contents = sort_list(contents, opts);
	print_list(contents);
	free_list(contents);
}

void	process_operands(t_file_entry *operands, t_options *opts, int operand_count)
{
	t_file_entry	*files;
	t_file_entry	*dirs;
	t_file_entry	*current;
	struct stat		st;

	files = NULL;
	dirs = NULL;
	current = operands;
	while (current)
	{
		if (lstat(current->path, &st) == -1)
		{
			write(2, "my_ls: cannot access '", 22);
			write(2, current->path, strlen(current->path));
			write(2, "': No such file or directory\n", 29);
		}
		else if (S_ISDIR(st.st_mode))
			add_node_back(&dirs, create_node(current->path, current->name));
		else
			add_node_back(&files, create_node(current->path, current->name));
		current = current->next;
	}
	fill_stats(files, opts);
	fill_stats(dirs, opts);
	files = sort_list(files, opts);
	dirs = sort_list(dirs, opts);
	print_list(files);
	current = dirs;
	if (files && dirs)
		printf("\n");
	while (current)
	{
		if (operand_count > 1)
			printf("%s:\n", current->name);
		process_directory(current->path, opts);
		if (current->next)
			printf("\n");
		current = current->next;
	}
	free_list(files);
	free_list(dirs);
}

/*MAIN FUNCTION*/

void	parse_args(int argc, char **argv, t_options *opts, t_file_entry **operands)
{
	int	i;
	int	j;

	opts->a = 0;
	opts->t = 0;
	for (i = 1; i < argc; i++)
	{
		if (argv[i][0] == '-')
		{
			for (j = 1; argv[i][j]; j++)
			{
				if (argv[i][j] == 'a')
					opts->a = 1;
				else if (argv[i][j] == 't')
					opts->t = 1;
			}
		}
		else
		{
			add_node_back(operands, create_node(argv[i], argv[i]));
		}
	}
}

int	main(int argc, char **argv)
{
	t_options		opts;
	t_file_entry	*operands;
	int				operand_count;

	operands = NULL;
	parse_args(argc, argv, &opts, &operands);
	operand_count = 0;
	t_file_entry *temp = operands;
	while(temp)
	{
		operand_count++;
		temp = temp->next;
	}
	if (operand_count == 0)
	{
		process_directory(".", &opts);
	}
	else
	{
		process_operands(operands, &opts, operand_count);
	}
	free_list(operands);
	return (0);
}