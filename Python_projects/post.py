from database import Database

class Post:
    def __init__(self,db, title=None,content=None, post_id=None):
        self.db = db
        self.post_id = post_id
        self.title = title
        self.content = content 

    def create_post(self):
        """Create post function"""
        query = "INSERT INTO posts (title,content) VALUES(%s,%s)"
        params = (self.title, self.content)
        self.db.execute_query(query,params)