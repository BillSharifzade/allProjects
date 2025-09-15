   # -*- coding: utf-8 -*-
FILENAME = "todo_list.txt"
import os
def welcome():
    print("Welcome")
    print("Add your tasks")
def save_task(tasks):
        with open(FILENAME, "w") as file:
            for task in tasks:
                file.write(task + "\n")
def add_task(tasks):
     task = input("Create the new task")
     if task.lower() == "q":
          return False
     tasks.append(task)
     return True 

def load_task():
    if not os.path.exists(FILENAME):
        return []
    with open (FILENAME,"r") as file:
        tasks = [line.strip() for line in file.reaslines()]
        return tasks
def display_tasks(tasks):
     if not tasks:
          print("To do list is empty")
     else:
          print("Your tasks: ")
          for i, task in enumerate(tasks, 1):
               print("{0}. {1}".format(i,task))
          
def edit_task(tasks):
    display_tasks(tasks)
    if not tasks:
        print("No tasks to edit!")
    else:
        print("Choose the task, you want to edit: ")
        with open(FILENAME, "e") as file:
            for task in tasks:
                file.edit (task + e)
def delete_task(tasks):
    display_tasks(tasks)
    try:
        task_num = input("Select task that you want to delete:")
        if task_num.lower() == "q":
            return False
        task_num = int(task_num)
        tasks.pop(task_num - 1)
    except(ValueError, IndexError):
        print("Wrong value or index")
        return True 

def main():
    welcome()
    tasks = load_task()
    while True:
        print("\n Choose the operation")
        print("1. See the task")
        print("2. Add task")
        print("3. Delete task")
        print("4. Edit task")
        print("q. Quit the programm")
        choice = input("Your choice:")
        if choice == "1":
            display_tasks(tasks)
        elif choice =="2":
            if not add_task(tasks):
                break 
            elif choice == "3":
                if not delete_task(tasks):
                    break
                elif choice == "4":
                    if not edit_task(tasks):
                        break
                elif isinstance(choice, str) and choice.lower() =="q":
                    break 
                else:
                    print("Incorrect choice, retry")
                    
                    save_task(tasks)
                   
if __name__ == "__main__":
     main()