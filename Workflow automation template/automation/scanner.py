from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager
import time

driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()))
driver.get("https://store.creality.com/collections/scanners")
time.sleep(3)

product_elements = driver.find_elements(By.CSS_SELECTOR, ".product-item a.product-item__title")
product_links = [element.get_attribute("href") for element in product_elements]

with open("scanner_links.txt", "w") as f:
    for link in product_links:
        f.write(link + "\n")

driver.quit()