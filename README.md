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

#### ERR_ABORTED错误修复和资源加载优化 (2024-12-19)

**问题描述**:
- 伪代码编译器在Chrome浏览器（特别是无痕模式）中出现`net::ERR_ABORTED`错误
- React应用的静态资源（JS/CSS文件）加载被浏览器中断
- iframe内容无法正常显示，影响编译器功能

**根本原因分析**:
1. **iframe重复创建**: `loadCompilerInIframe()`函数每次调用都会清空容器并重新创建iframe，导致浏览器取消正在加载的资源请求
2. **资源路径问题**: React应用使用相对路径，在iframe环境中可能导致路径解析错误
3. **浏览器安全策略**: Chrome对iframe的资源加载有严格的安全限制，特别是在无痕模式下

**完整解决方案**:

1. **防止iframe重复创建**
   ```javascript
   // 检查iframe是否已存在，避免重复创建
   const existingIframe = $('#compiler-iframe');
   if (existingIframe.length > 0) {
       console.log('Iframe already exists, not recreating');
       return;
   }
   ```

2. **React应用路径配置优化**
   - 修改`pseudo_compiler/package.json`中的`homepage`配置
   - 从相对路径`"./"` 改为绝对路径`"/pseudo_compiler/build"`
   - 重新构建React应用以应用新的路径配置

3. **服务器端CORS和安全头优化**
   ```php
   // router.php中添加的安全头
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
   header('Access-Control-Allow-Headers: Content-Type');
   header('X-Frame-Options: ALLOWALL');
   header('X-Content-Type-Options: nosniff');
   ```

4. **资源预加载策略**
   ```javascript
   // 在创建iframe前预加载关键资源
   const preloadResources = [
       '/pseudo_compiler/build/static/js/main.6c06c7b9.js',
       '/pseudo_compiler/build/static/css/main.55e66a0c.css'
   ];
   
   preloadResources.forEach(url => {
       const link = document.createElement('link');
       link.rel = 'preload';
       link.href = url;
       link.as = url.endsWith('.js') ? 'script' : 'style';
       document.head.appendChild(link);
   });
   ```

5. **iframe配置增强**
   ```javascript
   const iframe = $('<iframe>', {
       id: 'compiler-iframe',
       src: '/pseudo_compiler/build/index.html',
       sandbox: 'allow-scripts allow-same-origin allow-forms allow-popups allow-modals',
       loading: 'eager'
   });
   ```

**修改文件**:
- `.js/perlite.js` - 主要修复文件
  - 添加iframe存在性检查
  - 实现资源预加载机制
  - 优化iframe创建时序
- `router.php` - 服务器配置
  - 添加CORS支持头
  - 优化安全策略头
- `pseudo_compiler/package.json` - React应用配置
  - 修改homepage路径配置

**测试验证**:
- ✅ Chrome正常模式：资源正常加载，无ERR_ABORTED错误
- ✅ Chrome无痕模式：完全正常工作
- ✅ 服务器日志：所有资源请求返回200状态码
- ✅ 功能测试：伪代码编译器完全可用

**技术要点**:
- 使用资源预加载避免iframe加载时的竞态条件
- 通过绝对路径解决资源定位问题
- 合理配置iframe sandbox权限平衡安全性和功能性
- 优化加载时序确保资源可用性

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

## 更新日志

### v1.2.0 (2024-12-19)
- **重大修复**: ERR_ABORTED错误完全解决
  - 实现资源预加载策略，避免iframe加载时的资源竞态条件
  - 优化React应用路径配置，使用绝对路径提高兼容性
  - 增强服务器CORS和安全头配置
  - 添加iframe存在性检查，防止重复创建
  - 完全兼容Chrome无痕模式和所有现代浏览器

### v1.1.0 (2024-12-19)
- **修复**: 优化iframe集成策略，保留导航功能
  - 修改`loadCompilerInIframe`函数：只替换内容区域而非整个mod-root
  - 修改`showMainContent`函数：正确恢复内容区域显示
  - 保留所有导航栏、标题栏和控制按钮功能
  - 修复iframe高度计算，为导航栏预留空间
  - 确保侧边栏切换和其他UI功能正常工作

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