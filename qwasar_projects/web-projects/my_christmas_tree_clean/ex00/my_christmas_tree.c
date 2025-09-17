#include <unistd.h>
#include <stdlib.h>

void	ft_putchar(char c)
{
	write(1, &c, 1);
}

int	get_max_width(int size)
{
	int	segment;
	int	start_width;
	int	end_width;

	if (size <= 0)
		return (0);
	segment = 1;
	start_width = 1;
	end_width = 1;
	while (segment <= size)
	{
		int	rows_in_segment = segment + 3;
		int	reduction;

		end_width = start_width + (rows_in_segment - 1) * 2;
		if (segment < size)
		{
			reduction = 2 * ((segment + 1) / 2);
			start_width = end_width - reduction;
		}
		segment++;
	}
	return (end_width);
}

void	draw_leaves(int size, int max_width)
{
	int	segment;
	int	row;
	int	segment_start_width;

	segment = 1;
	segment_start_width = 1;
	while (segment <= size)
	{
		int	rows_in_segment = segment + 3;
		row = 1;
		while (row <= rows_in_segment)
		{
			int	current_stars = segment_start_width + (row - 1) * 2;
			int	spaces = (max_width - current_stars) / 2;
			int s = 0;
			while (s < spaces)
			{
				ft_putchar(' ');
				s++;
			}
			int i = 0;
			while (i < current_stars)
			{
				ft_putchar('*');
				i++;
			}
			ft_putchar('\n');
			row++;
		}
		int segment_end_width = segment_start_width + (rows_in_segment - 1) * 2;
		int reduction = 2 * ((segment + 1) / 2);
		segment_start_width = segment_end_width - reduction;
		segment++;
	}
}

void	draw_trunk(int size, int max_width)
{
	int	i;
	int	trunk_width;
	int	spaces;

	trunk_width = size;
	spaces = (max_width - 1) / 2 - (trunk_width - 1) / 2;
	i = 0;
	while (i < size)
	{
		int	j = 0;
		while (j < spaces)
		{
			ft_putchar(' ');
			j++;
		}
		j = 0;
		while (j < trunk_width)
		{
			ft_putchar('|');
			j++;
		}
		ft_putchar('\n');
		i++;
	}
}

int	main(int argc, char **argv)
{
	int	size;
	int	max_width;

	if (argc != 2)
	{
		return (0);
	}
	size = atoi(argv[1]);
	if (size <= 0)
	{
		return (0);
	}
	max_width = get_max_width(size);
	draw_leaves(size, max_width);
	draw_trunk(size, max_width);
	return (0);
}