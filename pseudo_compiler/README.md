# Cambridge 9618 伪代码编译器 (React版本)

一个专为Cambridge 9618计算机科学课程设计的伪代码编译器，支持完整的伪代码语法解析、编译和执行。

## 🚀 功能特性

### 📝 智能代码编辑器
- **语法高亮**: 完整支持Cambridge 9618伪代码语法高亮
- **智能自动完成**: 关键字和代码片段自动补全
- **智能缩进**: 根据代码结构自动调整缩进
- **代码折叠**: 支持代码块折叠和展开
- **括号匹配**: 自动匹配和高亮括号
- **多主题支持**: 内置多种编辑器主题

### 🔧 编译器功能
- **词法分析**: 完整的伪代码词法分析器
- **语法解析**: 支持所有Cambridge 9618伪代码语法结构
- **语义分析**: 变量声明、类型检查、作用域管理
- **代码生成**: 生成可执行的JavaScript代码
- **错误处理**: 详细的编译错误提示和位置定位

### 📋 支持的语法结构
- **控制结构**: IF-THEN-ELSE, WHILE, FOR, REPEAT-UNTIL, CASE-OF
- **过程和函数**: PROCEDURE, FUNCTION, 参数传递(BYREF/BYVAL)
- **数据类型**: INTEGER, REAL, STRING, CHAR, BOOLEAN, DATE
- **数据结构**: ARRAY, RECORD, 多维数组
- **输入输出**: INPUT, OUTPUT, 文件操作
- **逻辑运算**: AND, OR, NOT
- **算术运算**: +, -, *, /, DIV, MOD

### ⌨️ 快捷键支持
- `Ctrl+Space`: 触发自动完成
- `Ctrl+/`: 切换注释
- `Ctrl+D`: 删除当前行
- `Alt+Up/Down`: 移动行
- `Ctrl+]/[`: 增加/减少缩进
- `Tab/Shift+Tab`: 缩进控制

## 🛠️ 技术栈

- **前端框架**: React 18.2.0
- **代码编辑器**: CodeMirror 5.65.20
- **语法高亮**: 自定义Cambridge 9618伪代码模式
- **构建工具**: Create React App
- **样式**: CSS3 + 自定义主题

## 📦 安装和运行

### 环境要求
- Node.js >= 14.0.0
- npm >= 6.0.0

### 安装依赖
```bash
npm install
```

### 开发模式运行
```bash
npm start
```
应用将在 http://localhost:3000 启动

### 构建生产版本
```bash
npm run build
```

### 运行测试
```bash
npm test
```

## 🎯 使用说明

1. **编写代码**: 在左侧编辑器中输入Cambridge 9618伪代码
2. **语法提示**: 使用`Ctrl+Space`触发自动完成
3. **编译执行**: 点击"编译并运行"按钮
4. **查看结果**: 在右侧面板查看编译结果和执行输出
5. **错误调试**: 编译错误会在编辑器中高亮显示

## 📝 代码示例

```pseudocode
// 计算阶乘的函数
FUNCTION Factorial(n: INTEGER) RETURNS INTEGER
    IF n <= 1 THEN
        RETURN 1
    ELSE
        RETURN n * Factorial(n - 1)
    ENDIF
ENDFUNCTION

// 主程序
DECLARE num: INTEGER
DECLARE result: INTEGER

OUTPUT "请输入一个数字: "
INPUT num

result ← Factorial(num)
OUTPUT num, "的阶乘是: ", result
```

## 🔧 项目结构

```
pseudo_compiler/
├── public/                 # 静态资源
├── src/
│   ├── components/         # React组件
│   │   ├── CodeMirrorEditor.js    # 代码编辑器组件
│   │   ├── CompilerOutput.js      # 编译输出组件
│   │   └── ...
│   ├── utils/             # 工具函数
│   ├── App.js             # 主应用组件
│   └── index.js           # 应用入口
├── lexer.js               # 词法分析器
├── parser.js              # 语法分析器
├── compiler.js            # 编译器
└── package.json           # 项目配置
```

## 🤝 贡献指南

1. Fork 本项目
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 创建 Pull Request

## 📄 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情

## 🙏 致谢

- Cambridge International Examinations 提供的9618课程标准
- CodeMirror 团队提供的优秀代码编辑器
- React 团队提供的前端框架

## 📞 联系方式

如有问题或建议，请通过以下方式联系：
- 提交 Issue
- 发送邮件至项目维护者

---

**注意**: 本项目仅用于教育目的，帮助学生学习和理解Cambridge 9618计算机科学课程中的伪代码概念。
