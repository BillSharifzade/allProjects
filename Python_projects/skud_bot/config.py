# config.py

import os
from dotenv import load_dotenv

load_dotenv()  # Загрузка переменных окружения

# Токен Telegram API
TELEGRAM_TOKEN = os.getenv("TELEGRAM_TOKEN")

# URL для Telegram вебхуков
WEBHOOK_URL = os.getenv("WEBHOOK_URL")

# Настройки базы данных MySQL
DB_CONFIG = {
    'host': os.getenv("DB_HOST"),
    'user': os.getenv("DB_USER"),
    'port': os.getenv("DB_PORT"),
    'password': os.getenv("DB_PASSWORD"),
    'database': os.getenv("DB_NAME"),
    'unix_socket': '/Applications/MAMP/tmp/mysql/mysql.sock'

}

# Настройки для отправки SMS
SMS_USER = os.getenv("SMS_USER")
SMS_SALT = os.getenv("SMS_SALT")
SMS_URL = os.getenv("SMS_URL")
