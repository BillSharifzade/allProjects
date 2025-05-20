import requests

response = requests.get(f"https://api.telegram.org/bot7935976824:AAFIfUkAEdcBdw01yQy90zZ9JHsgxJv__mo/setWebhook?url=https://8e4e-62-122-140-133.ngrok-free.app/webhook")
print(response.json())


# response = requests.post("http://127.0.0.1:5000/webhook", json={"test": "data"})
# print(response.text)