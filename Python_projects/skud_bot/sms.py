# sms.py

import random
import hashlib
import requests
from config import SMS_USER, SMS_SALT, SMS_URL
import asyncio

def generate_verification_code():
    """Генерация 6-значного кода."""
    return ''.join([str(random.randint(0, 9)) for _ in range(6)])

async def send_sms(phone, verification_code):
    """Асинхронная отправка SMS с кодом."""
    try:
        full_phone = "992" + phone
        token = hashlib.md5((SMS_SALT + SMS_USER + full_phone).encode('utf-8')).hexdigest()
        print(token)

        url = f"{SMS_URL}?user={SMS_USER}&phone={full_phone}&msg={verification_code}&token={token}"
        response = requests.get(url)
        print(response)

        if response.status_code == 200:
            print(f"SMS с кодом {verification_code} успешно отправлено на номер {full_phone}.")
        else:
            print(f"Ошибка при отправке SMS: {response.status_code}, {response.text}")
    except Exception as e:
        print(f"Ошибка при отправке SMS: {e}")
