# Perlite - 知识管理系统

一个基于PHP的轻量级知识管理和文档展示系统，集成了伪代码编译器功能。

## 功能特性

- 📝 Markdown文档渲染和展示
- 🔍 文档搜索和导航
- 📊 知识图谱可视化
- 💻 集成伪代码编译器
- 👤 用户认证和权限管理
- 📱 响应式设计

## 技术栈

- **后端**: PHP 7.4+
- **前端**: HTML5, CSS3, JavaScript (jQuery)
- **编译器**: React.js (独立应用)
- **数据库**: MySQL
- **其他**: Composer, KaTeX, Vis.js

## 快速开始

### 环境要求

- PHP 7.4 或更高版本
- MySQL 5.7 或更高版本
- Node.js 14+ (用于伪代码编译器)
- Composer

### 安装步骤

1. 克隆项目
```bash
git clone <repository-url>
cd perlite
```

2. 安装PHP依赖
```bash
composer install
```

3. 配置数据库
- 复制 `config.php.example` 到 `config.php`
- 修改数据库连接配置

4. 启动PHP开发服务器
```bash
php -S localhost:8080
```

5. 启动伪代码编译器（可选）
```bash
cd pseudo_compiler
npm install
npm start
```

## 最近更新

### 2024年修复记录

#### iframe位置修复和编译器集成改进

**问题描述**:
- 伪代码编译器iframe覆盖整个页面，影响用户体验
- 点击"Pseudocode Compiler"无反应
- 导航功能在pseudocode.php页面异常

**修复内容**:

1. **iframe嵌入位置优化**
   - 将iframe从`body`改为嵌入到`mdContent`的父容器中
   - 移除绝对定位，改为相对定位在内容区域
   - 设置合适的高度(600px)和圆角样式

2. **编译器源路径修复**
   - 修复iframe源路径从`./pseudo_compiler/build/index.html`到`http://localhost:3000`
   - 确保使用React开发服务器URL

3. **导航功能修复**
   - 修复`getContent`函数中的强制重定向逻辑
   - 优化`showMainContent`函数的URL更新机制
   - 确保从编译器返回时正确显示主内容

**修改文件**:
- `.js/perlite.js` - 主要修复文件
  - `loadCompilerInIframe()` 函数优化
  - `showMainContent()` 函数改进
  - `getContent()` 函数导航逻辑修复

**技术细节**:
- 使用jQuery选择器优化DOM操作
- 改进iframe生命周期管理
- 优化URL状态管理和历史记录

## 项目结构

```
perlite/
├── .js/                    # JavaScript文件
├── .styles/               # CSS样式文件
├── pseudo_compiler/       # React伪代码编译器
├── handlers/             # PHP处理器
├── models/               # 数据模型
├── vendor/               # Composer依赖
├── index.php             # 主入口文件
├── helper.php            # 辅助函数
└── config.php            # 配置文件
```

## 开发说明

### 伪代码编译器开发

编译器是一个独立的React应用，位于`pseudo_compiler/`目录：

```bash
cd pseudo_compiler
npm start  # 开发模式，运行在 http://localhost:3000
npm run build  # 构建生产版本
```

### 主要组件

- **perlite.js**: 核心JavaScript功能
- **helper.php**: 导航和文件树生成
- **content.php**: 内容渲染逻辑

## 贡献指南

1. Fork 项目
2. 创建功能分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 开启 Pull Request

## 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情。

## 联系方式

如有问题或建议，请通过以下方式联系：
- 提交 Issue
- 发送邮件至项目维护者

---

*最后更新: 2024年*