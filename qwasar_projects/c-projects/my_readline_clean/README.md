# Welcome to My Readline
***

## Task
The task is to implement a function in C, my_readline, that reads a single line from a file descriptor, ending at a newline character (\n) or the end of the file.
The primary challenge is to build this functionality using only the low-level read system call. Unlike higher-level functions, read fetches a fixed number of bytes, which may not align with line breaks. This requires a robust solution for:
Buffering: Storing parts of a line read in one call to be used in the next.
State Management: Remembering the "leftover" data from a previous read when processing the next line.
Dynamic Memory: Allocating the exact amount of memory for each line and ensuring there are no memory leaks.
Constraints: The implementation is restricted to using a maximum of two global variables and a limited set of authorized functions (malloc, free, read).

## Description
This project solves the problem by using a static global variable, g_storage, to act as a persistent buffer that maintains state across multiple calls to my_readline.
The logic is as follows:
When my_readline is called, it first checks if g_storage already contains a complete line (i.e., includes a \n).
If not, it enters a loop that calls read() to fetch a new chunk of data from the file descriptor. The size of this chunk is determined by the global variable READLINE_READ_SIZE.
The newly read data is appended to g_storage. This is handled by a helper function that allocates a new, larger memory block, copies the old and new data, and frees the old g_storage pointer to prevent memory leaks.
Once a newline character is found, the function isolates the complete line from g_storage. It allocates the precise amount of memory required for this line, copies the characters, and prepares it for return.
Any characters remaining in g_storage after the newline are preserved for the next my_readline call.
If the end of the file is reached, any remaining data in g_storage is returned as the final line.
An additional function, init_my_readline, is provided to free any memory held by g_storage and reset its state. This is crucial for handling multiple files or for cleaning up after an error.


## Installation
The project consists of a single C file and does not require complex installation. You only need a C compiler like gcc.
To compile the program, navigate to the project directory and run the following command:
gcc -Wall -Wextra -Werror my_readline.c -o my_readline
Use code with caution.
This will create an executable file named my_readline.
For convenience, you can use a Makefile with the following commands:
make: Compiles the project.
make clean: Removes temporary object files.
make fclean: Removes the executable and object files.
make re: Cleans and recompiles the project from scratch.

## Usage
The program can read from a file provided as a command-line argument or from standard input if no argument is given.
Reading from a File
To read the contents of a file, pass its name as an argument to the program.
Example:
Generated bash
# First, create a sample file
echo "This is the first line.\nAnd this is the second." > test.txt

# Run the program with the file as an argument
./my_readline test.txt
Use code with caution.
Bash
Output:
Generated code
This is the first line.
And this is the second.
Use code with caution.
Reading from Standard Input
If you run the program without any arguments, it will read from standard input (your keyboard).
Example:
Generated bash
./my_readline
Use code with caution.
Bash
The program will now wait for you to type. Enter a line and press Enter, and the program will print it back to you. To signal the end of the input and exit the program, press Ctrl+D.
Generated bash
./my_readline
Hello, this is a test.
Hello, this is a test.
It works!
It works!
(Press Ctrl+D here)
```
./my_project argument1 argument2
```

### The Core Team


<span><i>Made at <a href='https://qwasar.io'>Qwasar SV -- Software Engineering School</a></i></span>
<span><img alt='Qwasar SV -- Software Engineering School's Logo' src='https://storage.googleapis.com/qwasar-public/qwasar-logo_50x50.png' width='20px' /></span>
