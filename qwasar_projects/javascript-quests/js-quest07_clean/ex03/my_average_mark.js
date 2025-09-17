/*
**
** QWASAR.IO -- my_average_mark
**
**
** @param {Array} param_1 - An array of objects where each object contains "string" (name) and "integer" (grade).
** @return {float} - The average grade for the entire class.
**
*/

function my_average_mark(param_1) {
    let total = 0;
    
    for (let i = 0; i < param_1.length; i++) {
        total += param_1[i].integer;
    }

    let average = total / param_1.length;

    return parseFloat(average.toFixed(1));
}
