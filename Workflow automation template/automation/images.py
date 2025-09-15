from PIL import Image
import os
import imagehash
from pathlib import Path

image_dir = "downloaded_images"
image_files = [f for f in os.listdir(image_dir) if f.endswith((".jpg", ".png"))]

hashes = {}
for file in image_files:
    img = Image.open(os.path.join(image_dir, file))
    hash_val = str(imagehash.average_hash(img))
    if hash_val in hashes:
        existing_img = Image.open(os.path.join(image_dir, hashes[hash_val]))
        if img.width > existing_img.width:
            os.remove(os.path.join(image_dir, hashes[hash_val]))
            hashes[hash_val] = file
        else:
            os.remove(os.path.join(image_dir, file))
    else:
        hashes[hash_val] = file

output_dir = "processed_images"
os.makedirs(output_dir, exist_ok=True)

for file in hashes.values():
    img = Image.open(os.path.join(image_dir, file))
    if img.width > 800:
        ratio = 800 / img.width
        new_height = int(img.height * ratio)
        img = img.resize((800, new_height), Image.LANCZOS)
    
    img.save(os.path.join(output_dir, file), "JPEG", quality=85, optimize=True)

print("Обработка завершена. Проверьте папку 'processed_images'.")