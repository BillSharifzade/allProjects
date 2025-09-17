function my_spaceship(flightPath) {
    let x = 0, y = 0;
    let directions = ['up', 'right', 'down', 'left'];
    let currentDirectionIndex = 0;

    for (let command of flightPath) {
        if (command === 'R') currentDirectionIndex = (currentDirectionIndex + 1) % 4;
        else if (command === 'L') currentDirectionIndex = (currentDirectionIndex + 3) % 4;
        else if (command === 'A') {
            if (directions[currentDirectionIndex] === 'up') y -= 1;
            if (directions[currentDirectionIndex] === 'down') y += 1;
            if (directions[currentDirectionIndex] === 'left') x -= 1;
            if (directions[currentDirectionIndex] === 'right') x += 1;
        }
    }

    return `{x: ${x}, y: ${y}, direction: '${directions[currentDirectionIndex]}'}`;
}