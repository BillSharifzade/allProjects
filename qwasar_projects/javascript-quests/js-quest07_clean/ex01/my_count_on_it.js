/*
**
** QWASAR.IO -- my_count_on_it
**
**
** @param {String[]} param_1
** @return {integer[]}
**
*/

function my_count_on_it(param_1) {
    return param_1.map(function(item) {
        return item.length;
    });
}