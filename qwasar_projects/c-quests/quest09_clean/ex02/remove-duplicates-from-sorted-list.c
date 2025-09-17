#include <stdlib.h>
#ifndef STRUCT_LISTNODE
#define STRUCT_LISTNODE
typedef struct s_listnode
{
    int val;
    struct s_listnode* next;
} listnode;
#endif


listnode* remove_duplicates_from_sorted_list(listnode* param_1)
{
    listnode* current_node = param_1;

    while (current_node != NULL && current_node->next != NULL) {
        if (current_node->val == current_node->next->val) {
            current_node->next = current_node->next->next;
        } else {
            current_node = current_node->next;
        }
    }
    return param_1;
}