import time
import pyautogui
import pyperclip
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager

# Funkcja do odczytu URL
def read_current_url():
    pyautogui.hotkey('ctrl', 'l')  # Wybieramy pasek adresu
    pyautogui.hotkey('ctrl', 'c')  # Kopiujemy URL
    time.sleep(1)
    return pyperclip.paste()  # Zwracamy skopiowany URL

# Funkcja do sprawdzenia, czy na stronie są ".9"
def check_for_numbers(driver):
    # Pobieramy całą zawartość strony
    page_content = driver.page_source
    
    # Sprawdzamy, czy na stronie jest ".9"
    if ".9" in page_content:
        print("Znaleziono '.9' lub '.10'.")
        return True
    else:
        print("Nie znaleziono '.9' ani '.10'.")
        return False

# Funkcja do odświeżenia strony
def refresh_page(driver):
    driver.refresh()  # Odświeżenie strony
    print("Strona została odświeżona.")

# Główna funkcja
def main():
    # Ustawienia sterownika przeglądarki
    options = webdriver.ChromeOptions()
    # Usuwamy opcję headless, aby przeglądarka była widoczna
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)

    # URL strony
    url = 'http://localhost/kasyno/pages/poker.php'
    driver.get(url)

    # Sprawdzamy zawartość strony
    while not check_for_numbers(driver):
        # Jeśli na stronie nie znaleziono ".9" ani ".10", odświeżamy
        refresh_page(driver)

    # Możesz dodać pauzę, żeby strona pozostała otwarta przez jakiś czas
    time.sleep(10)  # Na przykład 10 sekund przed zakończeniem skryptu

    # Przeglądarka pozostanie otwarta

# Uruchomienie głównej funkcji
if __name__ == "__main__":
    main()