#include <stdlib.h>
#ifndef STRUCT_LISTNODE
#define STRUCT_LISTNODE
typedef struct s_listnode
{
    int val;
    struct s_listnode* next;
} listnode;
#endif

listnode* remove_nth_node_from_end_of_list(listnode* param_1, int param_2)
{
    listnode dummy_node;
    dummy_node.next = param_1;

    listnode* slow_ptr = &dummy_node;
    listnode* fast_ptr = &dummy_node;

    for (int i = 0; i <= param_2; ++i) {
        fast_ptr = fast_ptr->next;
    }

    while (fast_ptr != NULL) {
        slow_ptr = slow_ptr->next;
        fast_ptr = fast_ptr->next;
    }

    listnode* node_to_remove = slow_ptr->next;
    slow_ptr->next = node_to_remove->next;

    return dummy_node.next;
}