/*
**
** QWASAR.IO -- my_string_index
**
**
** @param {String} param_1
** @param {Character} param_2
** @return {integer}
**
*/

function my_string_index(param_1, param_2) {

    for (let i = 0; i < param_1.length; i++) {
        if (param_1[i] === param_2) {
            return i;
        }
    }
    return -1;
}