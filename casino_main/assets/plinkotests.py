import pyautogui
import time
import keyboard

time.sleep(2)

while True:
    if keyboard.is_pressed('q'):
        break
    pyautogui.click()