# db.py

import mysql.connector
from mysql.connector import Error
from config import DB_CONFIG
import asyncio

async def connect_db():
    """Подключение к базе данных."""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        if connection.is_connected():
            return connection
    except Error as e:
        print(f"Ошибка подключения к базе данных: {e}")
        return None

async def get_user_by_chat_id(chat_id):
    db = await connect_db()
    if db:
        try:
            cursor = db.cursor()
            cursor.execute("SELECT is_verified, name FROM telegram_bot WHERE chat_id = %s", (chat_id,))
            user = cursor.fetchone()
            return user
        except Error as e:
            print(f"Ошибка получения пользователя: {e}")
        finally:
            cursor.close()
            db.close()

async def create_user(chat_id, phone, verification_code, name):
    db = await connect_db()
    if db:
        try:
            cursor = db.cursor()
            cursor.execute(
                "INSERT INTO telegram_bot (chat_id, phone, verification_code, name) VALUES (%s, %s, %s, %s)",
                (chat_id, phone, verification_code, name)
            )
            db.commit()
        except Error as e:
            print(f"Ошибка создания пользователя: {e}")
        finally:
            cursor.close()
            db.close()

async def update_user_verification(chat_id, verification_code=None, is_verified=False):
    db = await connect_db()
    if db:
        try:
            cursor = db.cursor()
            if verification_code:
                cursor.execute(
                    "UPDATE telegram_bot SET verification_code = %s WHERE chat_id = %s",
                    (verification_code, chat_id)
                )
            if is_verified:
                cursor.execute(
                    "UPDATE telegram_bot SET is_verified = 1 WHERE chat_id = %s",
                    (chat_id,)
                )
            db.commit()
        except Error as e:
            print(f"Ошибка обновления пользователя: {e}")
        finally:
            cursor.close()
            db.close()

async def get_verification_code_by_chat_id(chat_id):
    db = await connect_db()
    if db:
        try:
            cursor = db.cursor()
            cursor.execute("SELECT verification_code FROM telegram_bot WHERE chat_id = %s", (chat_id,))
            code = cursor.fetchone()
            return code
        except Error as e:
            print(f"Ошибка получения кода подтверждения: {e}")
        finally:
            cursor.close()
            db.close()
