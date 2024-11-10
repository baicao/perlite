import os
import requests
from bs4 import BeautifulSoup
import urllib.request
from urllib.error import ContentTooShortError


# 下载文件的函数，带有重试机制
def download_pdf(pdf_url, pdf_path):
    retries = 3  # 设置最大重试次数
    for attempt in range(retries):
        try:
            print(f"开始下载 {pdf_path} (第 {attempt+1} 次尝试)")
            # 下载PDF文件，每次下载 1MB (1024*1024 字节)
            with urllib.request.urlopen(pdf_url) as response, open(pdf_path, 'wb') as out_file:
                data_chunk = response.read(1024 * 1024)
                while data_chunk:
                    out_file.write(data_chunk)
                    data_chunk = response.read(1024 * 1024)
            print(f"下载成功: {pdf_path}")
            break  # 下载成功，跳出循环
        except (ContentTooShortError, urllib.error.URLError) as e:
            print(f"下载失败: {e}，重试中...")
            if attempt == retries - 1:
                print(f"下载失败 {pdf_path}，达到最大重试次数")

# 定义要抓取页面的URL
url = "https://dse.life/ppindex/bio/"

# 发送请求获取页面内容
response = requests.get(url)
response.raise_for_status()  # 检查请求是否成功

# 使用 BeautifulSoup 解析页面
soup = BeautifulSoup(response.text, 'html.parser')

# 创建一个文件夹来保存下载的PDF
download_dir = 'pdf_downloads'
if not os.path.exists(download_dir):
    os.makedirs(download_dir)

# 找到所有的PDF链接
pdf_links = soup.find_all('a', href=True)  # 查找所有<a>标签
pdf_files = [link['href'] for link in pdf_links if link['href'].endswith('.pdf') ]  # 筛选出以.pdf结尾的链接
pdf_files = [link for link in pdf_files if link.find("/dse/")!=-1 ]
print("pdf_files", pdf_files)
# 下载所有的PDF文件
for pdf_file in pdf_files:
    pdf_url = pdf_file if pdf_file.startswith('http') else f'https://dse.life{pdf_file}'
    pdf_fields = pdf_url.split('/')
    print("pdf_fields", pdf_fields)
    if pdf_url.find("chi")!=-1:
        current_download_dir = os.path.join(download_dir, "中文版")
    elif pdf_url.find("eng")!=-1:
        current_download_dir = os.path.join(download_dir, "英文版")
    else:
        current_download_dir = download_dir
    if not os.path.exists(current_download_dir):
        os.makedirs(current_download_dir)
    year = pdf_fields[-2]
    year_folder = os.path.join(current_download_dir, year)
    if not os.path.exists(year_folder):
        os.makedirs(year_folder)
    pdf_name = pdf_fields[-1]  # 从URL中提取文件名
    pdf_path = os.path.join(year_folder, pdf_name)
    
    print(f"正在下载: {pdf_name}")
    download_pdf(pdf_url, pdf_path)


print("所有PDF文件下载完成！")
