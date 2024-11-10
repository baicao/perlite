import os
root_path = "/Users/xiangyuwang/Library/Mobile Documents/com~apple~CloudDocs/Documents/畅知享/DSE Papers/"

subject_2_header = {
    "英國語文": {"header":"|Years|卷一 閱讀能力|卷二 寫作能力|卷三 聆聽考核|卷四 說話能力|參考答案|考生表現|",  "paper_size":5},
    "中國語文": {"header":"|Years|卷一 閱讀能力|卷二 寫作能力|卷三 聆聽考核|卷四 說話能力|參考答案|考生表現|",  "paper_size":4},
    "數學中文版": {"header":"|Years|Paper 1|Paper 2 MC|參考答案|", "paper_size":2},
    "數學英文版": {"header":"|Years|Paper 1|Paper 2 MC|參考答案|", "paper_size":2},
    "M1": {"header":"|Years|Question Paper |參考答案|考生表現|", "paper_size":1},
    "M2": {"header":"|Years|Question Paper |參考答案|考生表現|", "paper_size":1},
    "歷史中文版": {"header":"|Years|Paper 1|Paper 2|Sample 1|Sample 2|參考答案|考生表現|", "paper_size":4},
    "歷史英文版": {"header":"|Years|Paper 1|Paper 2|Sample 1|Sample 2|參考答案|考生表現|", "paper_size":4},
    "ICT中文版": {"header":"|Years|卷一|卷二甲 數據庫|卷二乙 數據通訊及建網|卷二丙 多媒體製作及網站建構|卷二丁 軟件開發|參考答案|考生表現|", "paper_size":5},
    "ICT英文版": {"header":"|Years|卷一|卷二甲 數據庫|卷二乙 數據通訊及建網|卷二丙 多媒體製作及網站建構|卷二丁 軟件開發|參考答案|考生表現|", "paper_size":5},
    "物理英文版": {"header":"|Years|Paper1A MC|Paper1B LQ|Paper2 Electives|參考答案|考生表現|", "paper_size":3},
    "物理中文版": {"header":"|Years|Paper1A MC|Paper1B LQ|Paper2 Electives|參考答案|考生表現|", "paper_size":3},
    "化學中文版": {"header":"|Years|卷一|卷二|參考答案|考生表現|", "paper_size":2},
    "化學英文版": {"header":"|Years|卷一|卷二|參考答案|考生表現|", "paper_size":2},
    "生物中文版": {"header":"|Years|卷一|卷二|參考答案|考生表現|", "paper_size":2},
    "生物英文版": {"header":"|Years|卷一|卷二|參考答案|考生表現|", "paper_size":2},
}
subject = "生物中文版"
header = subject_2_header[subject]["header"]
paper_size = subject_2_header[subject]["paper_size"]
fields = header[1:-1].split("|")
fields_size = len(fields)
md_content = ""
md_content += header + "\r\n"
md_content += "|"+'|'.join([":--"] * fields_size) + "|\r\n"
years_list = ['Practice<br>Paper', 'Sample<br>Paper'] + list(range(2012, 2024))
for year in years_list:
    if year == 'Practice<br>Paper':
        year_path = 'pp'
    elif year == 'Sample<br>Paper':
        year_path = 'sp'
    else:
        year_path = str(year)
    md_content_temp = "|" + str(year) 
    for i in range(paper_size):
        if paper_size == 1:
            i = 0
            paper_name = "pp.pdf"
        elif fields[i+1] == "Sample 1":
            paper_name = f"sample1.pdf"
        elif fields[i+1] == "Sample 2":
            paper_name = f"sample2.pdf"
        elif fields[i+1].startswith("Paper1A MC"):
            paper_name = f"p1a.pdf"
        elif fields[i+1].startswith("Paper1B LQ"):
            paper_name = f"p1b.pdf"
        elif fields[i+1].startswith("Paper2 Electives"):
            paper_name = f"p2.pdf"
        elif fields[i+1].startswith("卷二甲"):
            paper_name = f"p2a.pdf"
        elif fields[i+1].startswith("卷二乙"):
            paper_name = f"p2b.pdf"
        elif fields[i+1].startswith("卷二丙"):
            paper_name = f"p2c.pdf"
        elif fields[i+1].startswith("卷二丁"):
            paper_name = f"p2d.pdf"
        else:
            paper_name = f"p{i+1}.pdf"
        real_paper_name = f"{subject}/{year_path}/{paper_name}"
        paper_path = f"/DSE/Attachments/DSE Papers/{subject}/{year_path}/{paper_name}"
        if  os.path.exists(root_path + real_paper_name):
            name = f"[[{paper_path}\|{fields[i+1]}]]"
            md_content_temp += "|" +name 
        else:
            md_content_temp += "|"
    md_content += md_content_temp
    real_ans_file = f"{subject}/{year_path}/ans.pdf"
    real_per_file = f"{subject}/{year_path}/per.pdf"
    ans_file = f"/DSE/Attachments/DSE Papers/{subject}/{year_path}/ans.pdf"
    per_file = f"/DSE/Attachments/DSE Papers/{subject}/{year_path}/per.pdf"
    i = i + 1
    if  os.path.exists(root_path + real_ans_file):
        md_content += f"|[[{ans_file}\|{fields[i+1]}]]"
    else:
        md_content += "|"

    if fields[-1] == "考生表現":
        if os.path.exists(root_path + real_per_file):
            md_content += f"|[[{per_file}\|{fields[-1]}]]|"
        else:
            md_content += "|"
    md_content += "\r\n"
print(md_content)
