#include <stdio.h>

struct Address {
    char city[20];
    int zip;
};

struct Student {
    char name[30];
    int age;
    struct Address addr;  //Integrated structure
};

int main() {
    struct Student s = {"Billie", 23, {"New York", 31415}};
    
    printf("%s Живёт в %s (%d)\n", s.name, s.addr.city, s.addr.zip);
    return 0;
}