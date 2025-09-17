#include <stdarg.h>
#include <unistd.h>
#include <stdlib.h>

void clear_buffer(char *buffer, int size)
{
    for (int i = 0; i < size; i++)
    {
        buffer[i] = '\0';
    }
}

int len_buffer(char *buffer)
{
    int len = 0;
    while (buffer[len])
        len++;
    return len;
}

void print_str(char *str, int len)
{
    write(1, str, len);
}

void int_to_str(int n, char *buffer)
{
    int i = 0;
    int is_negative = 0;

    // Handle special case of INT_MIN
    if (n == -2147483648)
    {
        buffer[0] = '-';
        buffer[1] = '2';
        buffer[2] = '1';
        buffer[3] = '4';
        buffer[4] = '7';
        buffer[5] = '4';
        buffer[6] = '8';
        buffer[7] = '3';
        buffer[8] = '6';
        buffer[9] = '4';
        buffer[10] = '8';
        buffer[11] = '\0';
        return;
    }

    if (n == 0)
    {
        buffer[i++] = '0';
        buffer[i] = '\0';
        return;
    }

    if (n < 0)
    {
        is_negative = 1;
        n = -n;
    }

    while (n > 0)
    {
        buffer[i++] = (n % 10) + '0';
        n /= 10;
    }

    if (is_negative)
    {
        buffer[i++] = '-';
    }

    buffer[i] = '\0';

    // Reverse the string
    for (int j = 0, k = i - 1; j < k; j++, k--)
    {
        char temp = buffer[j];
        buffer[j] = buffer[k];
        buffer[k] = temp;
    }
}

void uint_to_str_base(unsigned int n, char *buffer, int base)
{
    int i = 0;
    char digits[] = "0123456789ABCDEF"; // Changed to uppercase for hex

    if (n == 0)
    {
        buffer[i++] = '0';
        buffer[i] = '\0';
        return;
    }

    while (n > 0)
    {
        buffer[i++] = digits[n % base];
        n /= base;
    }

    buffer[i] = '\0';

    // Reverse the string
    for (int j = 0, k = i - 1; j < k; j++, k--)
    {
        char temp = buffer[j];
        buffer[j] = buffer[k];
        buffer[k] = temp;
    }
}

void ptr_to_str(void *ptr, char *buffer)
{
    unsigned long addr = (unsigned long)ptr;
    int i = 0;
    char digits[] = "0123456789abcdef"; // Keep lowercase for pointers

    // Add "0x" prefix
    buffer[i++] = '0';
    buffer[i++] = 'x';

    if (addr == 0)
    {
        buffer[i++] = '0';
        buffer[i] = '\0';
        return;
    }

    // Convert to hex (we'll reverse it later)
    int start = i;
    while (addr > 0)
    {
        buffer[i++] = digits[addr % 16];
        addr /= 16;
    }

    buffer[i] = '\0';

    // Reverse only the hex digits part (after "0x")
    for (int j = start, k = i - 1; j < k; j++, k--)
    {
        char temp = buffer[j];
        buffer[j] = buffer[k];
        buffer[k] = temp;
    }
}

void print_number(int number)
{
    char buffer[32];
    int_to_str(number, buffer);
    write(1, buffer, len_buffer(buffer));
}

void print_unsigned(unsigned int number, int base)
{
    char buffer[32];
    uint_to_str_base(number, buffer, base);
    write(1, buffer, len_buffer(buffer));
}

void print_pointer(void *ptr)
{
    char buffer[32];
    ptr_to_str(ptr, buffer);
    write(1, buffer, len_buffer(buffer));
}

int my_printf(char *restrict format, ...)
{
    va_list args;
    va_start(args, format);

    int len = len_buffer(format);
    char buffer[1024];
    clear_buffer(buffer, 1024);

    int i = 0;
    int buffer_index = 0;
    int chars_written = 0;

    while (i < len)
    {
        if (format[i] == '%' && i + 1 < len)
        {
            // Print accumulated buffer first
            if (buffer_index > 0)
            {
                print_str(buffer, buffer_index);
                chars_written += buffer_index;
                clear_buffer(buffer, 1024);
                buffer_index = 0;
            }

            char special_character = format[i + 1];
            i += 2; // Skip both '%' and the format specifier

            switch (special_character)
            {
            case 'd':
            {
                int d = va_arg(args, int);
                print_number(d);
                // Count characters written for this number
                char temp_buffer[32];
                int_to_str(d, temp_buffer);
                chars_written += len_buffer(temp_buffer);
            }
            break;

            case 'u':
            {
                unsigned int u = va_arg(args, unsigned int);
                print_unsigned(u, 10);
                // Count characters written for this number
                char temp_buffer[32];
                uint_to_str_base(u, temp_buffer, 10);
                chars_written += len_buffer(temp_buffer);
            }
            break;

            case 'o':
            {
                unsigned int o = va_arg(args, unsigned int);
                print_unsigned(o, 8);
                // Count characters written for this number
                char temp_buffer[32];
                uint_to_str_base(o, temp_buffer, 8);
                chars_written += len_buffer(temp_buffer);
            }
            break;

            case 'x':
            {
                unsigned int x = va_arg(args, unsigned int);
                print_unsigned(x, 16);
                // Count characters written for this number
                char temp_buffer[32];
                uint_to_str_base(x, temp_buffer, 16);
                chars_written += len_buffer(temp_buffer);
            }
            break;

            case 'p':
            {
                void *p = va_arg(args, void *);
                print_pointer(p);
                // Count characters written for this pointer
                char temp_buffer[32];
                ptr_to_str(p, temp_buffer);
                chars_written += len_buffer(temp_buffer);
            }
            break;

            case 's':
            {
                char *s = va_arg(args, char *);
                if (s != NULL)
                {
                    int s_len = len_buffer(s);
                    print_str(s, s_len);
                    chars_written += s_len;
                }
                else
                {
                    print_str("(null)", 6);
                    chars_written += 6;
                }
            }
            break;

            case 'c':
            {
                char c = (char)va_arg(args, int);
                write(1, &c, 1);
                chars_written += 1;
            }
            break;

            case '%':
            {
                char percent = '%';
                write(1, &percent, 1);
                chars_written += 1;
            }
            break;

            default:
                // For unknown format specifiers, print them as-is
                {
                    char percent = '%';
                    write(1, &percent, 1);
                    write(1, &special_character, 1);
                    chars_written += 2;
                }
                break;
            }
        }
        else
        {
            // Add character to buffer
            buffer[buffer_index++] = format[i];
            i++;
        }
    }

    // Print any remaining buffer content
    if (buffer_index > 0)
    {
        print_str(buffer, buffer_index);
        chars_written += buffer_index;
    }

    va_end(args);
    return chars_written;
}