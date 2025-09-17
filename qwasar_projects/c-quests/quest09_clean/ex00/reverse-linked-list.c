#include <stdlib.h>
#ifndef STRUCT_LISTNODE
#define STRUCT_LISTNODE
typedef struct s_listnode
{
    int val;
    struct s_listnode* next;
} listnode;
#endif

listnode* reverse_linked_list(listnode* param_1)
{
    listnode* previous_node = NULL;
    listnode* current_node = param_1;
    listnode* next_node_holder = NULL;

    while (current_node != NULL) {
        next_node_holder = current_node->next;
        current_node->next = previous_node;
        previous_node = current_node;
        current_node = next_node_holder;
    }
    return previous_node;
}