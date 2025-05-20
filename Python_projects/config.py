import os
from dotenv import load_dotenv

load_dotenv()
TELEGRAM_TOKEN = os.getenv("TELEGRAM_TOKEN")
WEBHOOK_URL = os.getenv("WEBHOOK_URL")
DB_CONFIG = {
    'host' : os.getenv("DB_HOST"),
    'user' : os.getenv("DB_USER"),
    'password' : os.getenv("DB_PASSWORD"),
    'database' : os.getenv("DB_NAME")
}

