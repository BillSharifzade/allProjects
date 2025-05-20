import mysql.connector
from mysql.connector import Error

db_config={
    'user':'root',
    'password':'root',
    'host':'localhost',
    'database': 'task_manager'
}

def get_connection():
    try:
        conn = mysql.connector.connect(**db_config)
        if conn.is_connected():
            print('Connection to db is successful')
            return conn
    except Error as e:
        print(f'Error: {e}')
        return None
from Task import Task

class toDoList:
    def __init__(self):
        self.tasks = []

    def add_task(self, title, description):
        new_task = Task(title,description)
        self.tasks.append(new_task)

    def complete_task(self,index):
        index = int(index) - 1
        if 0 <= index < len(self.tasks):
            self.tasks[index].complete()
        else:
            print("Index out of range")
        
    def show_task(self):
        if not self.tasks:
            print("No task available")
        for index, task in enumerate(self.tasks):
            print(f"{index + 1}: {task}")
    def delete_task(self,index):
            index = int(input(f"Enter index of task: ")) - 1
            self.tasks.pop(index)


         

