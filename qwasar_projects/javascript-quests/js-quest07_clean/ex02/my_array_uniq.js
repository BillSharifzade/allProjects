/*
**
** QWASAR.IO -- my_array_uniq
**
**
** @param {Integer[]} param_1
** @return {integer[]}
**
*/

function my_array_uniq(param_1) {
    return [...new Set(param_1)];
}