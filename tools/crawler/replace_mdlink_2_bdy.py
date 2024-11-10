import os
import sys
import re
import pandas as pd
import pypinyin
def chinese_to_pinyin(text):
    return pypinyin.slug(text, separator='')

def load_csv(root_dir):
    combined_csv = pd.DataFrame()
    for file in os.listdir(root_dir):
        if file.endswith(".csv"):
            # 提取科目和年份
            subject, year = file.replace('.csv', '').split('_')
            
            # 读取 CSV 文件
            df = pd.read_csv(os.path.join(root_dir, file))
            
            # 增加科目和年份列
            df['科目'] = subject
            df['年份'] = year
            
            # 合并数据
            combined_csv = pd.concat([combined_csv, df], ignore_index=True)

    # 保存合并后的 CSV 文件
    combined_csv.to_excel("combined.xlsx", index=False)

root_dir = "/Users/xiangyuwang/Downloads"
load_csv(root_dir)
subject = '生物中文版'
subject_pinyin = chinese_to_pinyin(subject)
# Step 1: Load the Markdown file
md_file_path = f'/Users/xiangyuwang/Code/perlite/ChangEduHome/DSE/Notebook/updated_markdown_file.md'
target = "/Users/xiangyuwang/Code/perlite/ChangEduHome/DSE/Notebook"
with open(md_file_path, 'r', encoding='utf-8') as file:
    md_content = file.read()
# Step 2: Find all Markdown links in the custom format
md_links = re.findall(r'\[\[/?DSE/Attachments/DSE Papers/' + re.escape(subject) + r'/(.*?)\\?\|([^\]]+)\]\]', md_content)

# Print all found Markdown links
for link in md_links:
    print(f"Filename: {link[0]}, Text: {link[1]}")

# Step 3: Load the Excel file
excel_file_path = 'combined.xlsx'
excel_data = pd.read_excel(excel_file_path)

# Step 4: Create a dictionary mapping filenames to URLs from Excel
url_mapping = {}
for i in range(len(excel_data)):
    filename = excel_data.iloc[i]['文件名']
    link = excel_data.iloc[i]['链接']
    link_sub = excel_data.iloc[i]["科目"]
    if link_sub != subject_pinyin:
        continue
    year = excel_data.iloc[i]["年份"]
    url_mapping[f"{year}/{filename}"] = link

# Step 5: Replace Markdown links with new URLs based on the mapping
def replace_md_links(md_text, url_mapping):
    def replace_link(match):
        filename = match.group(1)
        if filename in url_mapping:
            return f"[{match.group(2)}]({url_mapping[filename]})"
        else:
            print(f"No URL found for filename: {filename}")
        return match.group(0)

    # Use regex to find Markdown links
    pattern = re.compile(r'\[\[/?DSE/Attachments/DSE Papers/' + re.escape(subject) + r'/(.*?)\\?\|([^\]]+)\]\]')
    updated_md_content = pattern.sub(replace_link, md_text)
    return updated_md_content

# Apply the function to replace the links
updated_md_content = replace_md_links(md_content, url_mapping)

# Step 6: Save the updated Markdown content back to a file
updated_md_file_path = os.path.join(target, 'updated_markdown_file.md')
with open(updated_md_file_path, 'w', encoding='utf-8') as file:
    file.write(updated_md_content)

print("Markdown links replaced successfully!")
