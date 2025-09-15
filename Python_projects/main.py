from toDoList import toDoList
def main():
    todo_list = toDoList()

    while True:
        print("\nTODO list Menu: ")
        print("1. Complete Task")
        print("2. Add task")
        print("3. Show Task")
        print("4. Delete Task")
        print("5. Exit")
        choice = input("Choose an option")
        if choice == "2":
            title = input("Enter task title")
            description = input("Enter task description")
            todo_list.add_task(title,description)
        elif choice == "1":
            index = input("Plz select task")
            todo_list.complete_task(index)
        elif choice == "3":
            todo_list.show_task()
        elif choice == "5":
            break
        elif choice == "4":
            todo_list.delete_task(index)


if __name__ == "__main__":
    main()