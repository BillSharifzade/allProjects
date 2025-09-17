# Welcome to My Printf
***

## Task
Reimplement the standard C `printf` function with support for a subset of format specifiers. The challenge is to handle formatted output, variable arguments, and different data types without using the standard library's printf.

## Description
This project provides a custom implementation of the `printf` function, named `my_printf`, written in C. It supports formatted output for various data types, including integers, unsigned integers, octal, hexadecimal, pointers, strings, and characters. The implementation uses variadic arguments and manual string formatting to mimic the behavior of the standard `printf` function.

### Supported Format Specifiers
- `%d` : Signed decimal integer
- `%u` : Unsigned decimal integer
- `%o` : Unsigned octal
- `%x` : Unsigned hexadecimal (lowercase)
- `%p` : Pointer address
- `%s` : String
- `%c` : Character
- `%%` : Literal percent sign

## Installation
No installation is required. Simply compile the `my_printf.c` file with your C compiler:

```
gcc -o my_printf my_printf.c
```

## Usage
You can use the `my_printf` function in your C programs. Here is an example of how to use it:

```c
#include "my_printf.c"

int main() {
    my_printf("Hello, %s! Number: %d, Hex: %x, Char: %c, Pointer: %p, Percent: %%\n", "world", 42, 42, 'A', (void*)main);
    return 0;
}
```

To compile and run the example:

```
gcc -o my_printf my_printf.c
./my_printf
```

### The Core Team

<span><i>Made at <a href='https://qwasar.io'>Qwasar SV -- Software Engineering School</a></i></span>
<span><img alt='Qwasar SV -- Software Engineering School's Logo' src='https://storage.googleapis.com/qwasar-public/qwasar-logo_50x50.png' width='20px' /></span>
