# React开发与PHP iframe集成方案

## 📋 项目概述

本文档记录了伪代码编译器从传统HTML版本迁移到React版本的完整方案，以及与PHP iframe的集成策略。

## 🏗️ 项目结构

```
pseudo_compiler/
├── src/                    # React源代码
│   ├── App.js             # 主应用组件
│   ├── App.css            # 应用样式
│   ├── index.js           # 入口文件
│   └── components/        # React组件
├── public/                # 静态资源
│   ├── index.html         # HTML模板
│   └── manifest.json      # PWA配置
├── build/                 # 构建输出目录
│   ├── index.html         # 构建后的HTML
│   └── static/            # 优化后的静态资源
├── index.html             # 传统HTML版本（保留）
├── styles.css             # 传统样式（保留）
└── package.json           # 项目配置
```

## 🚀 开发环境配置

### 1. React开发服务器
```bash
cd pseudo_compiler
npm start
# 运行在 http://localhost:3000
```

### 2. 生产构建
```bash
npm run build
# 生成优化后的静态文件到 build/ 目录
```

### 3. PHP服务器
```bash
php -S localhost:8000 router.php
# 主站运行在 http://localhost:8000
```

## 🔧 iframe集成方案

### 当前实现

**PHP端配置** (`router.php`):
```php
header('X-Frame-Options: ALLOWALL'); // 允许在任何iframe中加载
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
```

**JavaScript集成** (`perlite.js`):
```javascript
function loadCompilerInIframe() {
    // 动态创建iframe
    const iframe = $('<iframe>', {
        id: 'compiler-iframe',
        src: '/pseudo_compiler/build/index.html', // 生产环境
        // src: 'http://localhost:3000',          // 开发环境
        style: 'width: 100%; height: 100%; border: none;',
        sandbox: 'allow-scripts allow-same-origin allow-forms allow-popups allow-modals'
    });
    
    // 跨窗口通信
    iframe[0].contentWindow.postMessage('parent-ready', '*');
}
```

### 环境切换策略

**自动环境检测**:
```javascript
const isDevelopment = window.location.hostname === 'localhost' && 
                     window.location.port === '8000';
const iframeSrc = isDevelopment 
    ? 'http://localhost:3000'           // React开发服务器
    : '/pseudo_compiler/build/index.html'; // 构建版本
```

## 📊 版本对比

| 特性 | 传统HTML版本 | React版本 |
|------|-------------|-----------|
| 开发体验 | 基础 | 现代化，热重载 |
| 代码组织 | 单文件 | 组件化 |
| 性能优化 | 手动 | 自动优化 |
| 构建输出 | 原始文件 | 压缩优化 |
| iframe兼容性 | ✅ | ✅ |
| 维护成本 | 高 | 低 |

## 🛠️ 开发工作流

### 日常开发
1. 启动React开发服务器: `npm start`
2. 启动PHP服务器: `php -S localhost:8000 router.php`
3. 访问 `http://localhost:8000/pseudocode.php` 进行测试

### 生产部署
1. 构建React应用: `npm run build`
2. 确保iframe指向构建版本: `/pseudo_compiler/build/index.html`
3. 部署到生产服务器

## 🔍 故障排除

### 常见问题

**1. iframe加载失败**
- 检查CORS配置
- 确认路径正确性
- 查看浏览器控制台错误

**2. 跨窗口通信问题**
- 确保消息监听器正确设置
- 检查origin验证
- 使用postMessage API

**3. 资源加载错误**
- 检查相对路径配置
- 确认静态资源可访问
- 验证Content-Type头

### 调试工具

项目中包含多个测试文件用于调试:
- `test_iframe.html` - 基础iframe测试
- `test_iframe_debug.html` - 详细调试信息
- `test_iframe_preload.html` - 资源预加载测试

## 📈 性能优化

### React构建优化
- 自动代码分割
- CSS/JS压缩
- 资源哈希命名
- Tree shaking

### iframe加载优化
- 资源预加载
- 延迟显示机制
- 错误重试策略
- 加载状态指示

## 🔒 安全考虑

### iframe安全配置
```javascript
sandbox: 'allow-scripts allow-same-origin allow-forms allow-popups allow-modals'
```

### CORS策略
- 开发环境: 允许所有来源
- 生产环境: 限制特定域名

## 📝 最佳实践

1. **开发阶段使用React开发服务器**，享受热重载和调试工具
2. **生产环境使用构建版本**，获得最佳性能
3. **保持传统HTML版本作为备份**，确保向后兼容
4. **定期更新依赖**，保持安全性和性能
5. **使用环境变量**管理不同环境的配置

## 🚀 未来规划

- [ ] 完全迁移到React版本
- [ ] 移除传统HTML版本
- [ ] 添加TypeScript支持
- [ ] 实现PWA功能
- [ ] 优化移动端体验

---

**更新日期**: 2024年12月
**维护者**: Pseudocode Compiler Team