class Task:
    def __init__(self, title, description):
        self.title = title
        self.description = description
        self.completed = False 
        

    def complete(self):
        self.complete = True

    def __str__(self):
        status = "+" if self.complete == True else "x"
        return f"{status} {self.title}: {self.description}"
        
        