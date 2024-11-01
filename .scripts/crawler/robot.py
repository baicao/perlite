import pyautogui
from PIL import Image
import pytesseract
import time
import os
import sys
import subprocess
import pyperclip
import pypinyin 


def chinese_to_pinyin(text):
    return pypinyin.slug(text, separator='')

def find_text_click(target_text):
    down_scroll = False
    up_scroll = False
    # # 截图百度网盘窗口的指定区域 (x, y, width, height)
    # time.sleep(2)  # 给用户一些时间切换到百度网盘界面
    start_point = (600, 260)
    height = 50
    width = 100
    index = 0
    while not down_scroll or not up_scroll:
        position = (start_point[0], start_point[1]  + index * height, width, height)
        screenshot = pyautogui.screenshot(region=position)  # 替换为实际的百度网盘窗口区域

        # # 保存截图到文件（可选）
        screenshot.save(f"temp/line_{index}.png")
        # # 使用 Tesseract OCR 识别文字
        custom_config = r'--oem 3 --psm 6'
        text = pytesseract.image_to_string(screenshot, lang='chi_tra+chi_sim+eng', config=custom_config)  # 识别中文
        text = text.strip()
        text = text.replace("MI", "M1")
        print(f"text {index}:",text, target_text, text == target_text)
        if text == target_text:
            click_point = (start_point[0] + 10, start_point[1] + index * height + 25)
            print("click_point", click_point)
            pyautogui.click(x=start_point[0] + 10, y=start_point[1] + index * height + 25)
            break
        index += 1
        if index == 14:
            if not down_scroll:
                pyautogui.scroll(-500)
                down_scroll = True
            else:
                pyautogui.scroll(500)
                up_scroll = True
            index = 0


CLICK_WAITE_TIME = 3
# 打开百度网盘应用
try:
    subprocess.run(["open", "-a", "/Applications/BaiduNetdisk.app"], check=True)
except subprocess.CalledProcessError as e:
    print(f"Error opening BaiduNetdisk: {e}")

# 等待应用程序加载
time.sleep(CLICK_WAITE_TIME)

root_dir = "/Users/xiangyuwang/Downloads"
# paper_kinds = ["英國語文", "中國語文", "英國語文",  "M1", "M2","歷史中文版","中國歷史","ICT中文版", "ICT英文版", "數學中文版"]
paper_kinds = [ "生物英文版", "生物中文版" ]
years = ["2012", "2013", "2014", "2015", "2016", "2017", "2018", "2019", "2020", "2021", "2022", "2023", "pp", "sp"]
# years = ["pp"]
# find_text_click("DSE真题")
# time.sleep(CLICK_WAITE_TIME)
for paper_kind in paper_kinds:
    find_text_click(paper_kind)
    time.sleep(CLICK_WAITE_TIME)

    text_pinyin = chinese_to_pinyin(paper_kind)
    for year in years:
        file_name = f"{root_dir}/{text_pinyin}_{year}.csv"
        print(f"file {file_name} exists")
        if os.path.exists(file_name):
            continue
        find_text_click(year)
        time.sleep(CLICK_WAITE_TIME)

        pyautogui.click(x=560, y=234)
        time.sleep(CLICK_WAITE_TIME)
        pyautogui.click(x=666, y=145)
        time.sleep(CLICK_WAITE_TIME)
        pyautogui.click(x=668, y=347)
        time.sleep(CLICK_WAITE_TIME)
        pyautogui.click(x=829, y=389)
        pyautogui.click(x=832, y=641)
        time.sleep(CLICK_WAITE_TIME)
        pyautogui.click(x=931, y=611)
        time.sleep(CLICK_WAITE_TIME)
        pyautogui.press('backspace')
        time.sleep(CLICK_WAITE_TIME)
        pyautogui.write(f'{text_pinyin}_{year}', interval=0.2)
        time.sleep(CLICK_WAITE_TIME)
        pyautogui.press('enter')
        time.sleep(CLICK_WAITE_TIME)
        pyautogui.click(x=1333, y=567)
        time.sleep(CLICK_WAITE_TIME)
        pyautogui.click(x=551, y=194)
        time.sleep(CLICK_WAITE_TIME)
    pyautogui.click(x=551, y=194)
    time.sleep(CLICK_WAITE_TIME)