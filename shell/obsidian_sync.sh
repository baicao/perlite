#!/bin/bash

# 定义变量
REPO_URL="https://baicao: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@github.com/baicao/ChangEduHome.git"
LOCAL_DIR="/www/wwwroot/perlite/ChangEduHome"  # 替换为你本地仓库的路径

# 打印当前时间
echo "=== $(date) ==="

echo "本地目录已存在，正在更新仓库..."
cd "$LOCAL_DIR" || exit
git fetch --all
git reset --hard origin/main

echo "更新完成。"