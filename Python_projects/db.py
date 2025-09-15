import mysql.connector
from mysql.connector import Error
from config import DB_CONFIG
import asynco

async def connect_db():
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        if connection.is_connected():
            return connection
        except Error as e:
            print(f"DB ERROR {e}")
            return None
async def get_user_by_chat_id(chat_id):
    db = await connect_db()
    if db:
        try:
            cursor = db.cursor()
            cursor.execute("SELECT is_verified, name FROM telegram_bot WHERE chat_id = %s", (chat_id))