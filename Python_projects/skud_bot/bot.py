from flask import Flask, request
from telegram import Update, Bot
from telegram.ext import ApplicationBuilder, CommandHandler, MessageHandler, filters
from config import TELEGRAM_TOKEN, WEBHOOK_URL
from handlers import start, register, verify_code
import asyncio

app = Flask(__name__)

# Настройка бота
application = None  # Переменная для хранения экземпляра приложения Telegram
bot = None  # Переменная для объекта бота

async def setup_telegram():
    global application, bot
    # Инициализация приложения и бота
    application = ApplicationBuilder().token(TELEGRAM_TOKEN).build()
    await application.initialize()  # Инициализация приложения
    bot = application.bot  # Получаем объект бота

# Настройка обработки обновлений
async def handle_update(update: Update):
    await application.process_update(update)

# Обработчик вебхуков
@app.route("/webhook", methods=["POST"])
def webhook():
    if bot is None:
        # Проверяем, что бот инициализирован
        return "Bot is not initialized", 500
    
    update_data = request.get_json()  # Получаем данные от Telegram
    update = Update.de_json(update_data, bot)  # Создаем объект Update
    return "ok"

if __name__ == "__main__":
    # Запускаем инициализацию Telegram бота
    asyncio.run(setup_telegram())

    # Добавляем обработчики команд
    application.add_handler(CommandHandler('start', start))

    # Установка вебхука
    bot.set_webhook(url=WEBHOOK_URL)

    # Запуск Flask сервера
    app.run(port=6000)
