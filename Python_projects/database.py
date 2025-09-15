import mysql.connector
from mysql.connector import Error

class Database:
    def __init__(self, host,user,password,database):
        self.host = host
        self.user = user
        self.password = password
        self.database = database
        self.connection = None

    def conn(self):
        """Connect to database"""
        try:
            self.connection = mysql.connector.connect(
                host=self.host,
                user=self.user,
                password=self.password,
                database=self.database
            )
            if self.connection.is_connected():
                print("Connection is sex")
        except Error as e:
            print(f"There is some error occured: {e}")
            return False
        return True

    def disconn(self):
        """Close connection with MYSQL"""
        if self.connection.is_connected():
            self.connect.close()
            print("Connection is closed")
    def execute_query(self,query, params=None ):
        """Send request to MYSQL"""
        cursor = self.connection.cursor()
        try:
            cursor.execute(query,params)
            self.connection.commit()
            print("Query executed successfully")
        except Error as e:
            print(f"Theres some error: {e}")
        finally:
            cursor.close()
    def fetch_query(self,query,params=None):
        """Get data from MYSQL"""
        cursor = self.connection.cursor(dictionary=True)
        try:
            cursor.execute(query, params)
            result = cursor.fetchall()
            return result
        except Error as e:
            print("There is some error: {e}")
        finally:
            cursor.close()