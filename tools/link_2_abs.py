import os

# Define root_dir as a global variable
ROOT_DIR = "/Users/xiangyuwang/Code/ObsidianPlus/perlite/ChangEduHome"

def is_absolute_path(link, file_path):
    if "|Details" in link:
        link = link.split("|Details")[0]

    return (
        link.startswith("ASAL Computer Science/") or
        link.startswith("IGCSE Computer Science/") or
        link.startswith("AP Computer Science Principles/") or
        link.startswith("AP Computer Science A/") or
        link.startswith("http")
    )

def file_exists(path):
    return os.path.exists(path)

def convert_to_absolute_path(link, file_path):
    if "ASAL Computer Science" in file_path:
        primary_root_dir = "ASAL Computer Science"
    elif "IGCSE Computer Science" in file_path:
        primary_root_dir = "IGCSE Computer Science"
    elif "AP Computer Science Principles" in file_path:
        primary_root_dir = "AP Computer Science Principles"
    elif "AP Computer Science A" in file_path:
        primary_root_dir = "AP Computer Science A"
    else:
        primary_root_dir = "ASAL Computer Science"

    root_dirs = [
        "ASAL Computer Science",
        "IGCSE Computer Science",
        "AP Computer Science Principles",
        "AP Computer Science A"
    ]

    potential_paths = []
    if '.' in link:
        # Assume it's a file with an extension, look in Attachments
        potential_paths = [(os.path.join(ROOT_DIR, root_dir_name, "Attachments", link), f"{root_dir_name}/Attachments/{link}") for root_dir_name in root_dirs]
    else:
        # Assume it's a markdown file without an extension, look in Box and add .md for checking
        potential_paths = [(os.path.join(ROOT_DIR, root_dir_name, "Box", link + ".md"), f"{root_dir_name}/Box/{link}") for root_dir_name in root_dirs]

    for full_path, relative_path in potential_paths:
        print(f"Checking path: {full_path}, {file_exists(full_path)}")
        if file_exists(full_path):
            return relative_path, full_path

    return link, ""

def process_markdown_file(file_path):
    with open(file_path, 'r', encoding='utf-8') as file:
        lines = file.readlines()

    with open(file_path, 'w', encoding='utf-8') as file:
        for i, line in enumerate(lines):
            index = 0
            while index < len(line):
                start_embedded = line.find('![', index)
                start_normal = line.find('[[', index)
                
                if start_embedded != -1 and (start_embedded < start_normal or start_normal == -1):
                    start = start_embedded
                    is_embedded = True
                elif start_normal != -1:
                    start = start_normal
                    is_embedded = False
                else:
                    break

                end = line.find(']]', start)
                if end != -1:
                    link_content = line[start+3:end] if is_embedded else line[start+2:end]
                    print(f"Processing link: {link_content} {is_embedded} {start}")

                    # Split link, fragment identifier, and parameters
                    if '|' in link_content:
                        link_content, params = link_content.split('|', 1)
                    else:
                        params = ''

                    if '#' in link_content:
                        link, fragment = link_content.split('#', 1)
                        fragment = '#' + fragment
                    else:
                        link, fragment = link_content, ''

                    if "header" in link:
                        index = end + 2
                        continue

                    if not is_absolute_path(link, file_path):
                        abs_path, full_path = convert_to_absolute_path(link, file_path)
                        print(f"abs_path already: {abs_path}")
                        tail = line[end:]
                        if is_embedded:
                            line = line[:start+3] + abs_path + fragment + ('|' + params if params else '') 
                        else:
                            line = line[:start+2] + abs_path + fragment + ('|' + params if params else '')
                        if full_path.endswith(".md") and not "|" in link and "Pastpapers" not in full_path and "Markscheme" not in full_path:
                            line += "|Details" + tail
                        else:
                            line += tail
                    else:
                        # Add ROOT_DIR as prefix and check for .md
                        full_path = os.path.join(ROOT_DIR, link + ".md")
                        if not '.' in link and file_exists(full_path):
                            if "|Details" not in link and "Pastpapers" not in full_path and "Markscheme" not in full_path:
                                if is_embedded:
                                    line = line[:start+3] + f"{link}|Details" + line[end:]
                                else:
                                    line = line[:start+2] + f"{link}|Details" + line[end:]
                    print(f"Updated line: {line}")
                    index = end + 2
                else:
                    break

            # Check if the next line is a header and ensure exactly one newline before it
            if i + 1 < len(lines) and lines[i + 1].strip().startswith('#'):
                # Ensure there is exactly one empty line before the header
                if line == "\n":
                    pass
                elif not line.endswith('\n\n'):
                    line = line.rstrip('\n') + '\n\n'
            if line == "\n" and len(lines) > i + 1 and lines[i + 1] == "\n":
                continue
            file.write(line)

def process_all_markdown_files_in_directory(directory):
    for root, _, files in os.walk(directory):
        for file in files:
            if file.endswith('.md'):
                file_path = os.path.join(root, file)
                print(f"Processing file: {file_path}")
                process_markdown_file(file_path)

# Example usage
notebook_dir = os.path.join(ROOT_DIR, "ASAL Computer Science/Notebook")
box_dir = os.path.join(ROOT_DIR, "ASAL Computer Science/Box")
notebook_dir2 = os.path.join(ROOT_DIR, "IGCSE Computer Science/Notebook")
box_dir2 = os.path.join(ROOT_DIR, "IGCSE Computer Science/Box")
box_dir = os.path.join(ROOT_DIR, "ASAL Computer Science/Box")
for dir in [notebook_dir2, box_dir2]:
    process_all_markdown_files_in_directory(dir)