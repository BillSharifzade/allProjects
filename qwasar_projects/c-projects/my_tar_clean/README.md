# Welcome to My Tar
***

## Task
To create a GNU like archive software, which was pretty cool expirience

## Description
Its lightweight, robust and well structured software you can run everywhere

## Installation
Installation is the process of compiling the C source code into a single executable program.
Requirements
To compile the program, you will need a standard development environment for C, which includes:
gcc (the GNU C Compiler)
make (a tool to automate compilation)
These are standard on most Linux and macOS systems. If you are on a Debian-based Linux (like Ubuntu), you can install them with: sudo apt install build-essential.
Installation Steps
Navigate to the Project Directory
Open your terminal and use the cd command to go to the root directory of your my_tar project (the folder that contains the Makefile).
Generated bash
# Example:
cd /path/to/your/project
Use code with caution.
Bash
Compile the Program
Once you are in the correct directory, simply run the make command. This will read the Makefile, compile all the .c files, and link them together to create the final program.
Generated bash
make
Use code with caution.
Bash
You will see the compiler commands being executed. If there are no errors, you're done!
Verify the Installation
After make finishes, use the ls -l command to check that the executable file my_tar has been created.
Generated bash
ls -l my_tar
Use code with caution.
Bash
You should see an output like -rwxr-xr-x 1 user group 23480 Dec 1 12:30 my_tar. The x means it is executable.
You have now successfully "installed" my_tar. The program is ready to be used.

## Usage
Here is how to use the different features of my_tar.
Basic Syntax:
./my_tar [OPTIONS] [ARCHIVE_NAME] [FILE_1] [FILE_2] ...
1. Creating an Archive (-c)
This is the most common use case. The -c flag creates a completely new archive file. The -f flag is used to specify the filename of the archive.
Example:
Let's create two test files, then archive them into archive.tar.
Generated bash
# Create some test files
echo "Hello World" > file1.txt
echo "This is a test" > file2.txt

# Create the archive containing these two files
./my_tar -cf archive.tar file1.txt file2.txt
Use code with caution.
Bash
This command creates a new file named archive.tar containing file1.txt and file2.txt.
2. Listing Archive Contents (-t)
The -t flag shows a table of contents, listing all the files inside an archive without extracting them.
Example:
Let's inspect the archive.tar we just created.
Generated bash
./my_tar -tf archive.tar
Use code with caution.
Bash
Expected Output:
Generated code
file1.txt
file2.txt
Use code with caution.
3. Extracting an Archive (-x)
The -x flag extracts all the files from an archive into your current directory.
Example:
First, let's delete our original files to prove the extraction works. Then, we will extract them from the archive.
Generated bash
# Remove the original files
rm file1.txt file2.txt

# Now, extract the files from the archive
./my_tar -xf archive.tar

# Verify that the files are back
ls
Use code with caution.
Bash
You will see archive.tar, file1.txt, and file2.txt in your directory again.
4. Appending to an Archive (-r)
The -r flag appends new files to the rear of an existing archive without deleting its current contents. The -f option is required.
Example:
Let's create a new file and add it to our existing archive.tar.
Generated bash
# Create a new file
echo "A new file" > file3.log

# Append it to the archive
./my_tar -rf archive.tar file3.log

# Now, list the contents to see the result
./my_tar -tf archive.tar
Use code with caution.
Bash
Expected Output:
Generated code
file1.txt
file2.txt
file3.log
Use code with caution.
5. Updating an Archive (-u)
The -u flag is a smarter version of append. It only adds files that are updated (i.e., have a newer modification date on your disk than the version inside the archive) or files that don't exist in the archive at all. The -f option is required.
Example:
Let's modify file1.txt and create a brand new file, file4.c.
Generated bash
# Modify file1.txt (this updates its modification time)
echo "New content" > file1.txt

# Create a brand new file
echo "int main() {}" > file4.c

# Run the update command
# It will check file1.txt, file2.txt, and file4.c
./my_tar -uf archive.tar file1.txt file2.txt file4.c
Use code with caution.
Bash
file1.txt will be added because it is newer.
file2.txt will be skipped because it is not newer.
file4.c will be added because it is not in the archive.
Quick Reference
Option	Name	Description	Requires -f?
-c	Create	Creates a new archive. Overwrites if it exists.	Yes
-t	List	Lists the contents of an archive.	Yes
-x	Extract	Extracts files from an archive.	Yes
-r	Append	Adds specified files to the end of an archive.	Yes
-u	Update	Adds files that are new or have been modified.	Yes

```
./my_project argument1 argument2
```

### The Core Team


<span><i>Made at <a href='https://qwasar.io'>Qwasar SV -- Software Engineering School</a></i></span>
<span><img alt='Qwasar SV -- Software Engineering School's Logo' src='https://storage.googleapis.com/qwasar-public/qwasar-logo_50x50.png' width='20px' /></span>
