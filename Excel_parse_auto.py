import pandas as pd
from openpyxl import load_workbook

base_df = pd.read_excel('Base.xlsx') #Файл xlsx откуда нужно собрать данные
sorted_df = base_df.sort_values(by=['Медиа','inputDate','year', 'month', 'week', 'channel','Вещание', 'advertiser', 'brand', 'programme', 'programme_start', 'start_time', 'duration', 'time', 'quantity', 'clip', 'message', 'clip_type', 'break_type', 'language', 'type', 'number_of_advertise_block', 'spot_position', 'time15min', 'PRIME/OFF', 'minutes', 'rating', 'wgrp', 'grp', 'price (Сомони)','budget (сомон)', 'adex1', 'adex2', 'adex3', 'adex4', 'comments', 'budget $' ])  #Ключи переменных

# Загрузка xlsx как пути
krab_wb = load_workbook("КРАБ_ФИНТЕХ_1_8'_2024.xlsx")

# Сортировка путей (столбцы,ячейки, ряды)
for _, row in sorted_df.iterrows():
    sheet_name = row['Target Sheet']  # Имя листа
    cell_address = row['Target Cell']  #Адрес ячейки
    value = row['Value to Paste']  #Данные столбцов
    sheet = krab_wb[sheet_name] 
    sheet[cell_address] = value

# Save the updated file
krab_wb.save("КРАБ_ФИНТЕХ_1_8'_2024_UPDATED.xlsx")
### Key Steps:
#
#2. **Mapping**: Assumes `Base.xlsx` contains columns:
 #  - `Target Sheet`: Sheet name in КРАБ.
  # - `Target Cell`: Cell address (e.g., `C5`).
   #- `Value to Paste`: Data to write.
#3. **Write Data**: Iterates over rows, writing each value to the specified cell in КРАБ.
#4. **Save**: Outputs the modified workbook to a new file to avoid overwriting.

### Instructions:
#1. Replace column names (`Target Sheet`, `Target Cell`, `Value to Paste`) with actual names from your `Base.xlsx`.
#2. Adjust sorting columns (`by=['Priority', 'Date']`) to match your requirements.
#3. Test with copies of your files first.