#!/bin/bash

# 定义变量
REPO_URL="https://github.com/baicao/ChangEduHome.git"
LOCAL_DIR="/www/wwwroot/perlite/ChangEduHome"  # 替换为你本地仓库的路径

# 打印当前时间
echo "=== $(date) ==="

echo "本地目录已存在，正在更新仓库..."
cd "$LOCAL_DIR" || exit
git fetch --all
git reset --hard origin/main

echo "更新完成。"