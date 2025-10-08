/**
 * Pseudocode 编译器主应用程序
 * 提供用户界面交互和编译功能
 */

class PseudocodeApp {
    constructor() {
        this.compiler = new PseudocodeCompiler();
        this.editor = null;
        this.outputArea = null;
        this.errorArea = null;
        this.init();
    }

    // 初始化应用程序
    init() {
        this.setupEditor();
        this.setupEventListeners();
        this.loadExamples();
        this.showWelcomeMessage();
    }

    // 设置代码编辑器
    setupEditor() {
        this.editor = document.getElementById('pseudocodeEditor');
        this.outputArea = document.getElementById('jsOutput');
        this.errorArea = document.getElementById('errorOutput');
        
        // 检查必要的元素是否存在
        if (!this.editor) {
            console.warn('pseudocodeEditor 元素未找到，跳过编辑器设置');
            return;
        }
        
        // 设置编辑器默认内容
        this.editor.value = `// 欢迎使用 Cambridge 9618 Pseudocode 编译器
// 在此输入您的 Pseudocode 代码

DECLARE num : INTEGER
DECLARE result : INTEGER

INPUT "请输入一个数字: ", num
result ← num * 2
OUTPUT "结果是: ", result`;
        
        // 添加自动完成
        this.setupAutoComplete();
    }

    // 设置自动完成
    setupAutoComplete() {
        const editor = this.editor;
        
        if (!editor) return;
        
        editor.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                e.preventDefault();
                this.handleTabCompletion();
            }
        });
    }

    // 处理Tab自动完成
    handleTabCompletion() {
        const editor = this.editor;
        const cursorPos = editor.selectionStart;
        const textBefore = editor.value.substring(0, cursorPos);
        const lastWord = textBefore.split(/\s+/).pop();
        
        const completions = {
            'dec': 'DECLARE  : ',
            'con': 'CONSTANT  = ',
            'if': 'IF  THEN\n\nENDIF',
            'for': 'FOR  ← 1 TO 10\n\nNEXT ',
            'while': 'WHILE \n\nENDWHILE',
            'repeat': 'REPEAT\n\nUNTIL ',
            'proc': 'PROCEDURE ()\n\nENDPROCEDURE',
            'func': 'FUNCTION () RETURNS \n\nENDFUNCTION',
            'input': 'INPUT "", ',
            'output': 'OUTPUT '
        };
        
        if (completions[lastWord.toLowerCase()]) {
            const completion = completions[lastWord.toLowerCase()];
            const newText = textBefore.slice(0, -lastWord.length) + completion + editor.value.substring(cursorPos);
            editor.value = newText;
            
            // 设置光标位置
            const newCursorPos = cursorPos - lastWord.length + completion.indexOf(' ') + 1;
            editor.setSelectionRange(newCursorPos, newCursorPos);
        }
    }

    // 设置事件监听器
    setupEventListeners() {
        // 编译运行按钮
        const compileBtn = document.getElementById('compileBtn');
        console.log('设置编译按钮事件监听器，按钮元素:', compileBtn);
        
        if (compileBtn) {
            compileBtn.addEventListener('click', () => {
                console.log('编译按钮被点击！');
                this.compileAndRun();
            });
        } else {
            console.error('找不到编译按钮元素！');
        }
        
        // 清除按钮
        const clearBtn = document.getElementById('clearBtn');
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                this.clearEditor();
            });
        }
        
        // 示例按钮
        const exampleBtn = document.getElementById('exampleBtn');
        if (exampleBtn) {
            exampleBtn.addEventListener('click', () => {
                this.showExamples();
            });
        }
        
        // 清空控制台按钮
        const clearConsoleBtn = document.getElementById('clearConsole');
        if (clearConsoleBtn) {
            clearConsoleBtn.addEventListener('click', () => {
                this.clearConsole();
            });
        }
        
        // 复制JS代码按钮
        const copyJsBtn = document.getElementById('copyJs');
        if (copyJsBtn) {
            copyJsBtn.addEventListener('click', () => {
                this.copyJavaScript();
            });
        }
        
        // Tab切换
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', (e) => {
                this.switchTab(e.target.dataset.tab);
            });
        });
        
        // 键盘快捷键
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey || e.metaKey) {
                switch (e.key) {
                    case 'Enter':
                        e.preventDefault();
                        this.compileCode();
                        break;
                    case 'l':
                        e.preventDefault();
                        this.clearEditor();
                        break;
                }
            }
        });
        
        // 实时编译（可选）
        let compileTimeout;
        if (this.editor) {
            this.editor.addEventListener('input', () => {
                clearTimeout(compileTimeout);
                compileTimeout = setTimeout(() => {
                    const autoCompileCheckbox = document.getElementById('auto-compile');
                    if (autoCompileCheckbox && autoCompileCheckbox.checked) {
                        this.compileCode(true); // 静默编译
                    }
                }, 1000);
            });
        }
    }

    // 编译并运行代码
    compileAndRun() {
        console.log('compileAndRun 方法被调用');
        this.addToConsole('开始编译...', 'info');
        
        this.compileCode();
        if (this.lastCompileResult && this.lastCompileResult.success) {
            console.log('编译成功，开始运行代码');
            this.addToConsole('编译成功，开始运行...', 'info');
            this.runCode();
        } else {
            console.log('编译失败或无编译结果');
            this.addToConsole('编译失败，请检查代码', 'error');
        }
    }

    // 编译代码
    compileCode(silent = false) {
        const sourceCode = this.editor.value.trim();
        
        if (!sourceCode) {
            if (!silent) {
                this.showError('请输入 Pseudocode 代码');
            }
            return;
        }
        
        try {
            const result = this.compiler.compile(sourceCode);
            
            // 保存编译结果
            this.lastCompileResult = result;
            
            if (result.success) {
                this.showOutput(result.generatedCode);
                this.clearErrors();
                
                if (!silent) {
                    this.showSuccess('编译成功！');
                }
                
                // 显示统计信息
                this.showStatistics(this.compiler.getStatistics());
            } else {
                this.showErrors(result.errors);
                if (!silent) {
                    this.showError('编译失败，请检查错误信息');
                }
            }
            
            // 显示警告
            if (result.warnings.length > 0) {
                this.showWarnings(result.warnings);
            }
            
        } catch (error) {
            this.showError(`编译过程中发生错误: ${error.message}`);
        }
    }

    // 运行代码
    runCode() {
        if (!this.lastCompileResult || !this.lastCompileResult.success) {
            this.showError('请先编译代码');
            return;
        }
        
        try {
            // 在控制台中运行生成的JavaScript代码
            this.executeGeneratedCode(this.lastCompileResult.generatedCode);
        } catch (error) {
            this.showError(`运行时错误: ${error.message}`);
        }
    }

    // 执行生成的代码
    executeGeneratedCode(jsCode) {
        // 切换到控制台标签
        this.switchTab('console');
        
        // 清空控制台
        this.clearConsole();
        
        // 在当前页面的控制台中执行
        this.executeInConsole(jsCode);
    }
    
    // 在控制台中执行代码
    executeInConsole(jsCode) {
        const consoleOutput = document.getElementById('consoleOutput');
        
        // 重定向console.log到页面控制台
        const originalLog = console.log;
        const originalError = console.error;
        
        console.log = (...args) => {
            this.addToConsole(args.join(' '), 'output');
            originalLog.apply(console, args);
        };
        
        console.error = (...args) => {
            this.addToConsole(args.join(' '), 'error');
            originalError.apply(console, args);
        };
        
        // 模拟INPUT函数
        window.INPUT = (prompt = "") => {
            return new Promise((resolve) => {
                const answer = window.prompt(prompt || "请输入:");
                if (answer === null) {
                    resolve("");
                } else {
                    const num = parseFloat(answer);
                    if (!isNaN(num) && isFinite(num)) {
                        resolve(num);
                    } else {
                        resolve(answer);
                    }
                }
            });
        };
        
        window.OUTPUT = (...args) => {
            console.log(...args);
        };
        
        // 其他运行时函数
        window.DIV = (a, b) => Math.floor(a / b);
        window.MOD = (a, b) => a % b;
        window.SQRT = Math.sqrt;
        window.ABS = Math.abs;
        window.ROUND = Math.round;
        window.INT = Math.floor;
        
        window.LENGTH = (str) => str.length;
        window.SUBSTRING = (str, start, length) => str.substr(start - 1, length);
        window.LEFT = (str, length) => str.substr(0, length);
        window.RIGHT = (str, length) => str.substr(-length);
        window.MID = (str, start, length) => str.substr(start - 1, length);
        window.UCASE = (str) => str.toUpperCase();
        window.LCASE = (str) => str.toLowerCase();
        
        window.createArray = function(dimensions, defaultValue = 0) {
            if (dimensions.length === 1) {
                const [lower, upper] = dimensions[0];
                const size = upper - lower + 1;
                return new Array(size).fill(defaultValue);
            } else {
                const [lower, upper] = dimensions[0];
                const size = upper - lower + 1;
                const result = new Array(size);
                for (let i = 0; i < size; i++) {
                    result[i] = createArray(dimensions.slice(1), defaultValue);
                }
                return result;
            }
        };
        
        window.arrayAccess = function(array, indices, baseLower = []) {
            let current = array;
            for (let i = 0; i < indices.length; i++) {
                const index = indices[i] - (baseLower[i] || 0);
                current = current[index];
            }
            return current;
        };
        
        window.arraySet = function(array, indices, value, baseLower = []) {
            let current = array;
            for (let i = 0; i < indices.length - 1; i++) {
                const index = indices[i] - (baseLower[i] || 0);
                current = current[index];
            }
            const lastIndex = indices[indices.length - 1] - (baseLower[indices.length - 1] || 0);
            current[lastIndex] = value;
        };
        
        try {
            // 使用eval执行生成的代码
            eval(jsCode);
        } catch (error) {
            this.addToConsole(`运行时错误: ${error.message}`, 'error');
        } finally {
            // 恢复原始console函数
            console.log = originalLog;
            console.error = originalError;
        }
    }
    
    // 添加内容到控制台
    addToConsole(message, type = 'output') {
        const consoleOutput = document.getElementById('consoleOutput');
        const messageElement = document.createElement('div');
        messageElement.className = `console-message console-${type}`;
        messageElement.textContent = message;
        consoleOutput.appendChild(messageElement);
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
    }
    
    // 清空控制台
    clearConsole() {
        const consoleOutput = document.getElementById('consoleOutput');
        consoleOutput.innerHTML = '';
    }
    
    // 复制JavaScript代码
    copyJavaScript() {
        const jsOutput = document.getElementById('jsOutput');
        const text = jsOutput.textContent;
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(() => {
                this.showSuccess('JavaScript代码已复制到剪贴板');
            }).catch(() => {
                this.fallbackCopyText(text);
            });
        } else {
            this.fallbackCopyText(text);
        }
    }
    
    // 备用复制方法
    fallbackCopyText(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            this.showSuccess('JavaScript代码已复制到剪贴板');
        } catch (err) {
            this.showError('复制失败，请手动复制');
        }
        document.body.removeChild(textArea);
    }
    
    // 切换标签
    switchTab(tabName) {
        // 移除所有活动状态
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });
        
        // 激活选中的标签
        const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
        const activePane = document.getElementById(tabName);
        
        if (activeButton && activePane) {
            activeButton.classList.add('active');
            activePane.classList.add('active');
        }
    }
    
    // 在新窗口中执行（备用方法）
    executeInNewWindow(jsCode) {
        // 创建一个新的执行环境
        const executionWindow = window.open('', '_blank', 'width=600,height=400');
        
        // 这个方法现在不使用，保留作为备用
        console.log('在新窗口中执行代码功能暂未实现');
    }

    // 显示输出
    showOutput(code) {
        this.outputArea.textContent = code;
        this.outputArea.style.display = 'block';
    }

    // 显示错误
    showErrors(errors) {
        const errorHtml = errors.map(error => {
            const location = error.line > 0 ? `[${error.line}:${error.column}] ` : '';
            return `<div class="error-item">
                <span class="error-type">${error.type}:</span>
                <span class="error-location">${location}</span>
                <span class="error-message">${error.message}</span>
            </div>`;
        }).join('');
        
        this.errorArea.innerHTML = errorHtml;
        this.errorArea.style.display = 'block';
    }

    // 显示警告
    showWarnings(warnings) {
        const warningHtml = warnings.map(warning => {
            const location = warning.line > 0 ? `[${warning.line}:${warning.column}] ` : '';
            return `<div class="warning-item">
                <span class="warning-type">WARNING:</span>
                <span class="warning-location">${location}</span>
                <span class="warning-message">${warning.message}</span>
            </div>`;
        }).join('');
        
        const warningContainer = document.getElementById('warnings');
        if (warningContainer) {
            warningContainer.innerHTML = warningHtml;
            warningContainer.style.display = 'block';
        }
    }

    // 清除错误
    clearErrors() {
        this.errorArea.innerHTML = '';
        this.errorArea.style.display = 'none';
        
        const warningContainer = document.getElementById('warnings');
        if (warningContainer) {
            warningContainer.innerHTML = '';
            warningContainer.style.display = 'none';
        }
    }

    // 显示成功消息
    showSuccess(message) {
        this.showNotification(message, 'success');
    }

    // 显示错误消息
    showError(message) {
        this.showNotification(message, 'error');
    }

    // 显示通知
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // 自动消失
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // 显示统计信息
    showStatistics(stats) {
        const statsContainer = document.getElementById('statistics');
        if (statsContainer) {
            statsContainer.innerHTML = `
                <div class="stat-item">源代码行数: ${stats.sourceLines}</div>
                <div class="stat-item">Token数量: ${stats.tokenCount}</div>
                <div class="stat-item">生成代码行数: ${stats.generatedLines}</div>
                <div class="stat-item">错误数量: ${stats.errorCount}</div>
                <div class="stat-item">警告数量: ${stats.warningCount}</div>
            `;
        }
    }

    // 清除编辑器
    clearEditor() {
        this.editor.value = '';
        this.outputArea.textContent = '';
        this.clearErrors();
        this.editor.focus();
    }

    // 加载示例
    loadExamples() {
        this.examples = [
            {
                name: '基本输入输出',
                code: `DECLARE name : STRING
DECLARE age : INTEGER

INPUT "请输入您的姓名: ", name
INPUT "请输入您的年龄: ", age

OUTPUT "您好, ", name
OUTPUT "您今年 ", age, " 岁"`
            },
            {
                name: '条件语句 (IF-ELSEIF-ELSE)',
                code: `DECLARE score : INTEGER

INPUT "请输入分数: ", score

IF score >= 90 THEN
    OUTPUT "优秀"
ELSEIF score >= 80 THEN
    OUTPUT "良好"
ELSEIF score >= 70 THEN
    OUTPUT "中等"
ELSEIF score >= 60 THEN
    OUTPUT "及格"
ELSE
    OUTPUT "不及格"
ENDIF`
            },
            {
                name: 'CASE语句 (多路选择)',
                code: `DECLARE dayNumber : INTEGER
DECLARE dayName : STRING

INPUT "请输入星期几(1-7): ", dayNumber

CASE OF dayNumber
    1 : dayName ← "星期一"
    2 : dayName ← "星期二"
    3 : dayName ← "星期三"
    4 : dayName ← "星期四"
    5 : dayName ← "星期五"
    6 : dayName ← "星期六"
    7 : dayName ← "星期日"
    OTHERWISE : dayName ← "无效输入"
ENDCASE

OUTPUT "今天是: ", dayName`
            },
            {
                name: 'FOR循环',
                code: `DECLARE i : INTEGER
DECLARE sum : INTEGER

sum ← 0

FOR i ← 1 TO 10
    sum ← sum + i
NEXT i

OUTPUT "1到10的和是: ", sum`
            },
            {
                name: 'WHILE循环',
                code: `DECLARE num : INTEGER
DECLARE factorial : INTEGER

INPUT "请输入一个数字: ", num
factorial ← 1

WHILE num > 1
    factorial ← factorial * num
    num ← num - 1
ENDWHILE

OUTPUT "阶乘结果: ", factorial`
            },
            {
                name: 'REPEAT-UNTIL循环',
                code: `DECLARE password : STRING
DECLARE attempts : INTEGER

attempts ← 0

REPEAT
    attempts ← attempts + 1
    INPUT "请输入密码: ", password
    IF password <> "123456" THEN
        OUTPUT "密码错误，请重试"
    ENDIF
UNTIL password = "123456" OR attempts >= 3

IF password = "123456" THEN
    OUTPUT "登录成功！"
ELSE
    OUTPUT "尝试次数过多，账户已锁定"
ENDIF`
            },
            {
                name: '一维数组操作',
                code: `DECLARE numbers : ARRAY[1:5] OF INTEGER
DECLARE i : INTEGER
DECLARE sum : INTEGER
DECLARE max : INTEGER

// 输入数组元素
FOR i ← 1 TO 5
    INPUT "请输入第", i, "个数字: ", numbers[i]
NEXT i

// 计算总和和最大值
sum ← 0
max ← numbers[1]
FOR i ← 1 TO 5
    sum ← sum + numbers[i]
    IF numbers[i] > max THEN
        max ← numbers[i]
    ENDIF
NEXT i

OUTPUT "数组元素的总和为: ", sum
OUTPUT "最大值为: ", max`
            },
            {
                name: '二维数组操作',
                code: `DECLARE matrix : ARRAY[1:3, 1:3] OF INTEGER
DECLARE i, j : INTEGER
DECLARE sum : INTEGER

// 输入矩阵元素
FOR i ← 1 TO 3
    FOR j ← 1 TO 3
        INPUT "请输入matrix[", i, ",", j, "]: ", matrix[i, j]
    NEXT j
NEXT i

// 计算对角线元素之和
sum ← 0
FOR i ← 1 TO 3
    sum ← sum + matrix[i, i]
NEXT i

OUTPUT "主对角线元素之和: ", sum`
            },
            {
                name: '记录类型 (RECORD)',
                code: `TYPE Student
    Name : STRING
    Age : INTEGER
    Grade : REAL
ENDTYPE

DECLARE student1 : Student

// 输入学生信息
INPUT "请输入学生姓名: ", student1.Name
INPUT "请输入学生年龄: ", student1.Age
INPUT "请输入学生成绩: ", student1.Grade

// 输出学生信息
OUTPUT "学生信息:"
OUTPUT "姓名: ", student1.Name
OUTPUT "年龄: ", student1.Age
OUTPUT "成绩: ", student1.Grade

// 判断成绩等级
IF student1.Grade >= 90 THEN
    OUTPUT "等级: A"
ELSEIF student1.Grade >= 80 THEN
    OUTPUT "等级: B"
ELSE
    OUTPUT "等级: C"
ENDIF`
            },
            {
                name: '过程 (PROCEDURE)',
                code: `PROCEDURE PrintHeader()
    OUTPUT "================================"
    OUTPUT "    欢迎使用计算器程序"
    OUTPUT "================================"
ENDPROCEDURE

PROCEDURE PrintResult(operation : STRING, num1 : REAL, num2 : REAL, result : REAL)
    OUTPUT num1, " ", operation, " ", num2, " = ", result
ENDPROCEDURE

DECLARE a, b, sum : REAL

CALL PrintHeader()

INPUT "请输入第一个数字: ", a
INPUT "请输入第二个数字: ", b

sum ← a + b
CALL PrintResult("+", a, b, sum)`
            },
            {
                name: '函数 (FUNCTION)',
                code: `FUNCTION CalculateFactorial(n : INTEGER) RETURNS INTEGER
    DECLARE result : INTEGER
    DECLARE i : INTEGER
    
    result ← 1
    FOR i ← 1 TO n
        result ← result * i
    NEXT i
    
    RETURN result
ENDFUNCTION

FUNCTION IsEven(number : INTEGER) RETURNS BOOLEAN
    IF number MOD 2 = 0 THEN
        RETURN TRUE
    ELSE
        RETURN FALSE
    ENDIF
ENDFUNCTION

DECLARE num : INTEGER
DECLARE factorial : INTEGER

INPUT "请输入一个正整数: ", num

factorial ← CalculateFactorial(num)
OUTPUT num, "的阶乘是: ", factorial

IF IsEven(num) THEN
    OUTPUT num, "是偶数"
ELSE
    OUTPUT num, "是奇数"
ENDIF`
            },
            {
                name: '字符串处理',
                code: `DECLARE fullName : STRING
DECLARE firstName : STRING
DECLARE nameLength : INTEGER

INPUT "请输入您的全名: ", fullName

nameLength ← LENGTH(fullName)
OUTPUT "您的姓名长度为: ", nameLength

// 提取前3个字符作为姓
firstName ← LEFT(fullName, 3)
OUTPUT "姓氏: ", firstName

// 转换为大写
OUTPUT "大写姓名: ", UCASE(fullName)

// 转换为小写
OUTPUT "小写姓名: ", LCASE(fullName)`
            },
            {
                name: '数学函数应用',
                code: `DECLARE radius : REAL
DECLARE area : REAL
DECLARE circumference : REAL
DECLARE randomNum : REAL

INPUT "请输入圆的半径: ", radius

// 计算面积 (π ≈ 3.14159)
area ← 3.14159 * radius * radius
OUTPUT "圆的面积: ", ROUND(area, 2)

// 计算周长
circumference ← 2 * 3.14159 * radius
OUTPUT "圆的周长: ", ROUND(circumference, 2)

// 生成随机数
randomNum ← RANDOM() * 100
OUTPUT "随机数(0-100): ", INT(randomNum)

// 计算平方根
OUTPUT "半径的平方根: ", SQR(radius)`
            }
        ];
    }

    // 显示示例
    showExamples() {
        // 创建一个简单的示例选择对话框
        const exampleNames = this.examples.map((ex, i) => `${i + 1}. ${ex.name}`).join('\n');
        const choice = prompt(`请选择示例 (输入数字):\n\n${exampleNames}`);
        
        if (choice) {
            const index = parseInt(choice) - 1;
            if (index >= 0 && index < this.examples.length) {
                this.loadExample(index);
            } else {
                this.showError('无效的选择');
            }
        }
    }

    // 加载示例代码
    loadExample(index) {
        if (index >= 0 && index < this.examples.length) {
            this.editor.value = this.examples[index].code;
            this.highlightSyntax();
            this.showSuccess(`已加载示例: ${this.examples[index].name}`);
        }
    }

    // 显示帮助
    showHelp() {
        this.showNotification(`Cambridge 9618 Pseudocode 编译器帮助

快捷键:
- Ctrl+Enter: 编译并运行
- Ctrl+L: 清空编辑器

支持的语法:
- 变量声明: DECLARE name : TYPE
- 赋值: variable ← value
- 输入: INPUT prompt, variable
- 输出: OUTPUT value1, value2
- 条件: IF...THEN...ELSE...ENDIF
- 循环: FOR...TO...NEXT, WHILE...ENDWHILE
- 数组: ARRAY[1:10] OF INTEGER
- 函数: FUNCTION...RETURNS...ENDFUNCTION`, 'info');
    }

    // 显示欢迎消息
    showWelcomeMessage() {
        this.showNotification('欢迎使用 Cambridge 9618 Pseudocode 编译器！', 'info');
    }
}

// 初始化应用程序
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM内容加载完成，开始初始化应用程序');
    
    try {
        const app = new PseudocodeApp();
        console.log('PseudocodeApp 实例创建成功:', app);
        
        // 将app实例暴露到全局，方便调试
        window.pseudocodeApp = app;
        
    } catch (error) {
        console.error('初始化应用程序时发生错误:', error);
        // 使用更友好的错误显示方式
        const errorDiv = document.createElement('div');
        errorDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 9999;
            max-width: 400px;
        `;
        errorDiv.innerHTML = `
            <strong>应用程序初始化失败</strong><br>
            ${error.message}
            <button onclick="this.parentElement.remove()" style="float: right; margin-left: 10px; background: none; border: none; font-size: 18px; cursor: pointer;">&times;</button>
        `;
        document.body.appendChild(errorDiv);
        
        // 5秒后自动移除错误提示
        setTimeout(() => {
            if (errorDiv.parentElement) {
                errorDiv.remove();
            }
        }, 5000);
    }
    
    // 设置模态框关闭功能
    document.querySelectorAll('.modal .close').forEach(closeBtn => {
        closeBtn.addEventListener('click', (e) => {
            e.target.closest('.modal').style.display = 'none';
        });
    });
    
    // 点击模态框外部关闭
    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
    
    // 全局错误处理
    window.addEventListener('error', (e) => {
        console.error('全局错误:', e.error);
        if (window.pseudocodeApp) {
            window.pseudocodeApp.showError(`发生错误: ${e.error.message}`);
        }
    });
});