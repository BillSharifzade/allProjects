# My Mastermind: reproduce printf behavior

-----

## Task
Develop a simplified version of the classic **Mastermind** game using the C programming language. The program should implementing game logic.

1. Implement a playable **Mastermind** game in C.
2. The program should support the following command-line options:
    - `-c [CODE]` — Specifies the secret code. If omitted, a random code is generated.
    - `-t [ATTEMPTS]` — Specifies the number of attempts. Default is 10.
3. The program must gracefully handle **End-of-File** input (`Ctrl+D`).
4. Provide a `Makefile` for automated compilation.

## Description

The game accepts a secret code through the `-c` option. If not provided, a random numeric code will be generated. 
The user then attempts to guess the code within the specified number of attempts.

- **On Start**:
    Will you find the secret code?
    Please enter a valid guess

- **On Win**:
    Congratz! You did it!

- **After Each Guess**:
    Round X
    Well placed pieces: Y | Misplaced pieces: Z

- **Invalid Input**:
    Wrong input!

> Notes:
> - "Well placed pieces" refer to correct digits in the correct positions.
> - "Misplaced pieces" refer to correct digits but in the wrong positions.

----
## Installation

No external dependencies or packages are required. However, a standard Unix-like development environment is expected.

- **Operating System**: Linux or macOS
- **Shell**: Bash, Zsh, or any POSIX-compatible shell
- **Compiler**: GCC or Clang
- **Tools**: `git` for version control

## Usage

1.  **Clone the Repository**
    ```bash
    git clone git@git.us.qwasar.io:my_mastermind_184288_os_vtv/my_mastermind.git
    ```

2.  **Navigate to the Project Directory**

    ```bash
    cd my_mastermind
    ```
3. **Build the Project**
    ```bash
    make
    ```
4. **Clean the project**
    ```bash
    make clean
    ```

## Test

1.  **Execute the program**
    Run the Program
    ```bash
    ./my_mastermind -c "1234" -t 12
    ```
>Replace "1234" with your desired secret code (only numeric values).
>-t is optional; defaults to 10 if not provided.

2.  **Try to guess the code**
    Follow the hint from the program
    - Well placed pieces: mean correct numbre in thier place.
    - Misplaced pieces: mean Wrong number is the place.

> Note: Wining `Congratz! You did it!` will shown and the program exit.
3. **Exite**
To exit early, press Ctrl + D.
-----

### The Core Team

<span>Developed with ❣️ by MOksith.</span>
<span><i>Made at <a href='https://qwasar.io'>Qwasar SV -- Software Engineering School</a></i></span>
<span><img alt='Qwasar SV -- Software Engineering School's Logo' src='https://storage.googleapis.com/qwasar-public/qwasar-logo_50x50.png' width='20px' /></span>
