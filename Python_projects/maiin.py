from database import Database
from post import Post

class Main:
    def __init__(self):
        self.db = Database('443','root','root','posts')
    
    def run(self):
        if self.db.conn():
            new_post = Post(self.db,title = 'New Post', content = "Content")
            new_post.create_post()
    def delete_post(self,params):
        if self.db.conn():
            self.connection.cursor()
            del_post = "DELETE FROM posts WHERE column1 = %s"
            cursor.close()
    def edit_post(self,title,params):
        if self.db.conn():
            self.connection.cursor()
            edit_post = "UPDATE posts SET column1 = %s WHERE column2 = %s"
            cursor.close
    def show_post(self,title):
        if self.db.conn():
             for index, posts in enumerate(self.posts):
                 print(f"{index + 1}: {post}")

if __name__ == "__main__":
    main = Main()
    main.run()