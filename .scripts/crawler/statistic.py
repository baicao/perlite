import os
import fitz  # PyMuPDF
import pandas as pd

def count_pdf_pages(directory):
    data = []

    # 遍历目录及其子目录
    for root, _, files in os.walk(directory):
        for file in files:
            if file.endswith('.pdf'):
                file_path = os.path.join(root, file)
                try:
                    # 打开PDF文件并获取页面数
                    pdf_document = fitz.open(file_path)
                    num_pages = pdf_document.page_count
                    pdf_document.close()

                    # 获取父目录名称
                    parent_dir = os.path.basename(root)
                    # 获取父目录的父目录名称
                    grandparent_dir = os.path.basename(os.path.dirname(root))

                    # 将结果添加到数据列表中
                    data.append({
                        'File': file,
                        'Parent Directory': parent_dir,
                        'Grandparent Directory': grandparent_dir,
                        'Page Count': num_pages
                    })
                except Exception as e:
                    print(f"Error reading {file_path}: {e}")

    # 创建DataFrame
    df = pd.DataFrame(data)
    return df

# 使用示例
directory = "/Users/xiangyuwang/Downloads/英國語文"  # 替换为你的目录路径
df = count_pdf_pages(directory)
print(df)

# 计算总页面数
total_pages = df['Page Count'].sum()
print(f"Total number of pages: {total_pages}")
