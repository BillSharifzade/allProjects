# handlers.py

from telegram import Update
from telegram.ext import ContextTypes
from db import get_user_by_chat_id, create_user, update_user_verification, get_verification_code_by_chat_id
from sms import generate_verification_code, send_sms

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE):
    chat_id = str(update.message.chat_id)
    user = await get_user_by_chat_id(chat_id)
    
    if user:
        is_verified, name = user
        if is_verified:
            await update.message.reply_text(f"Привет, {name}! Вы уже зарегистрированы.")
        else:
            await update.message.reply_text("Ваш номер телефона еще не подтвержден. Пожалуйста, введите код подтверждения.")
    else:
        await update.message.reply_text("Привет! Вы не зарегистрированы. Пожалуйста, введите свой номер телефона для регистрации.")
