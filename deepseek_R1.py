import requests
import json
import pandas as pd

API_KEY = "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"
MODEL = "deepseek/deepseek-r1"

def process_content(content):
    return content.replace('<think>', '').replace('</think>', '')

def read_xlsx(file_path):
    try:
        df = pd.read_excel(file_path)
        return df.to_csv(index=False)
    except Exception as e:
        print(f"Ошибка чтения файла: {e}")
        return None

def chat_stream(prompt):
    headers = {
        "Authorization": f"Bearer {API_KEY}",
        "Content-Type": "application/json"
    }
    
    data = {
        "model": MODEL,
        "messages": [{"role": "user", "content": prompt}],
        "stream": True
    }

    with requests.post(
        "https://openrouter.ai/api/v1/chat/completions",
        headers=headers,
        json=data,
        stream=True
    ) as response:
        if response.status_code != 200:
            print("Ошибка API:", response.status_code)
            return ""

        full_response = []
        
        for chunk in response.iter_lines():
            if chunk:
                chunk_str = chunk.decode('utf-8').replace('data: ', '')
                try:
                    chunk_json = json.loads(chunk_str)
                    if "choices" in chunk_json:
                        content = chunk_json["choices"][0]["delta"].get("content", "")
                        if content:
                            cleaned = process_content(content)
                            print(cleaned, end='', flush=True)
                            full_response.append(cleaned)
                except:
                    pass

        print()
        return ''.join(full_response)

def main():
    print("Чат с DeepSeek-R1 (by QuaZZZar)\nВведите 'file: путь_к_файлу.xlsx' для отправки файла.\nДля выхода введите 'exit'\n")

    while True:
        user_input = input("Вы: ")
        
        if user_input.lower() == 'exit':
            print("Завершение работы...")
            break
        
        if user_input.startswith("file: "):
            file_path = user_input.split("file: ")[1]
            file_content = read_xlsx(file_path)
            if file_content:
                print("DeepSeek-R1:", end=' ', flush=True)
                chat_stream(f"Вот содержимое файла:\n{file_content}")
        else:
            print("DeepSeek-R1:", end=' ', flush=True)
            chat_stream(user_input)

if __name__ == "__main__":
    main()