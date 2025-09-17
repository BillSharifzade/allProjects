#include <stdlib.h>
#ifndef STRUCT_LISTNODE
#define STRUCT_LISTNODE
typedef struct s_listnode
{
    int val;
    struct s_listnode* next;
} listnode;
#endif

#ifndef STRUCT_LISTNODE_ARRAY
#define STRUCT_LISTNODE_ARRAY
typedef struct s_listnode_array
{
    int size;
    listnode **array;
} listnode_array;
#endif

static listnode* mergeTwoLists(listnode* l1, listnode* l2) {
    if (l1 == NULL) {
        return l2;
    }
    if (l2 == NULL) {
        return l1;
    }

    listnode dummy_head;
    listnode* tail = &dummy_head;

    while (l1 != NULL && l2 != NULL) {
        if (l1->val <= l2->val) {
            tail->next = l1;
            l1 = l1->next;
        } else {
            tail->next = l2;
            l2 = l2->next;
        }
        tail = tail->next;
    }

    if (l1 != NULL) {
        tail->next = l1;
    } else {
        tail->next = l2;
    }

    return dummy_head.next;
}

listnode* merge_k_sorted_lists(listnode_array* param_1) {
    if (param_1 == NULL || param_1->array == NULL || param_1->size == 0) {
        return NULL;
    }
    
    int num_lists = param_1->size;
    listnode** lists = param_1->array;

    if (num_lists == 1) {
        return lists[0];
    }

    int interval = 1;
    while (interval < num_lists) {
        for (int i = 0; i < num_lists - interval; i += interval * 2) {
            lists[i] = mergeTwoLists(lists[i], lists[i + interval]);
        }
        interval *= 2;
    }

    return lists[0];
}