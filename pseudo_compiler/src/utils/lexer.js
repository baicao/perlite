/**
 * Pseudocode 词法分析器
 * 基于 Cambridge 9618 标准
 */

class PseudocodeLexer {
    constructor() {
        // 关键字定义
        this.keywords = new Set([
            // 数据类型
            'INTEGER', 'REAL', 'CHAR', 'STRING', 'BOOLEAN', 'DATE',
            // 声明
            'DECLARE', 'CONSTANT', 'TYPE', 'ENDTYPE',
            // 控制结构
            'IF', 'THEN', 'ELSE', 'ELSEIF', 'ENDIF',
            'CASE', 'OF', 'OTHERWISE', 'ENDCASE',
            // 循环
            'FOR', 'TO', 'STEP', 'NEXT', 'WHILE', 'ENDWHILE',
            'REPEAT', 'UNTIL',
            // 过程和函数
            'PROCEDURE', 'ENDPROCEDURE', 'FUNCTION', 'ENDFUNCTION',
            'RETURNS', 'RETURN', 'BYVAL', 'BYREF', 'CALL',
            // 输入输出
            'INPUT', 'OUTPUT',
            // 文件操作
            'OPENFILE', 'READFILE', 'WRITEFILE', 'CLOSEFILE',
            'EOF', 'SEEK', 'GETPOSITION',
            // 逻辑运算
            'AND', 'OR', 'NOT',
            // 布尔值
            'TRUE', 'FALSE',
            // 其他
            'DIV', 'MOD', 'ARRAY',
            // 内置库函数
            'ASC', 'CHR', 'INT', 'RAND'
        ]);

        // 运算符定义
        this.operators = {
            '←': 'ASSIGN',
            '<-': 'ASSIGN',
            '=': 'EQUALS',
            '<>': 'NOT_EQUALS',
            '!=': 'NOT_EQUALS',
            '<': 'LESS_THAN',
            '>': 'GREATER_THAN',
            '<=': 'LESS_EQUAL',
            '>=': 'GREATER_EQUAL',
            '+': 'PLUS',
            '-': 'MINUS',
            '*': 'MULTIPLY',
            '/': 'DIVIDE',
            '(': 'LPAREN',
            ')': 'RPAREN',
            '[': 'LBRACKET',
            ']': 'RBRACKET',
            ',': 'COMMA',
            ':': 'COLON',
            ';': 'SEMICOLON',
            '.': 'DOT'
        };

        this.reset();
    }

    reset() {
        this.input = '';
        this.position = 0;
        this.line = 1;
        this.column = 1;
        this.tokens = [];
        this.errors = [];
    }

    tokenize(input) {
        this.reset();
        this.input = input;

        while (this.position < this.input.length) {
            const char = this.currentChar();

            // 跳过空白字符
            if (/\s/.test(char)) {
                this.skipWhitespace();
                continue;
            }

            // 跳过注释
            if (char === '/' && this.peek() === '/') {
                this.skipComment();
                continue;
            }

            // 字符串字面量
            if (char === '"') {
                this.readString();
                continue;
            }

            // 字符字面量
            if (char === "'") {
                this.readChar();
                continue;
            }

            // 数字
            if (this.isDigit(char)) {
                this.readNumber();
                continue;
            }

            // 标识符和关键字
            if (this.isLetter(char) || char === '_') {
                this.readIdentifier();
                continue;
            }

            // 双字符运算符
            const twoChar = char + this.peek();
            if (this.operators[twoChar]) {
                this.addToken(this.operators[twoChar], twoChar);
                this.advance();
                this.advance();
                continue;
            }

            // 单字符运算符
            if (this.operators[char]) {
                this.addToken(this.operators[char], char);
                this.advance();
                continue;
            }

            // 未知字符
            this.addError(`未知字符: '${char}'`);
            this.advance();
        }

        // 添加结束标记
        this.addToken('EOF', null);
        return {
            tokens: this.tokens,
            errors: this.errors
        };
    }

    currentChar() {
        return this.input[this.position] || null;
    }

    peek(offset = 1) {
        return this.input[this.position + offset] || null;
    }

    advance() {
        if (this.position < this.input.length) {
            if (this.currentChar() === '\n') {
                this.line++;
                this.column = 1;
            } else {
                this.column++;
            }
            this.position++;
        }
    }

    skipWhitespace() {
        while (this.position < this.input.length && /\s/.test(this.currentChar())) {
            this.advance();
        }
    }

    skipComment() {
        // 跳过 // 注释
        while (this.position < this.input.length && this.currentChar() !== '\n') {
            this.advance();
        }
        if (this.currentChar() === '\n') {
            this.advance();
        }
    }

    readString() {
        const startLine = this.line;
        const startColumn = this.column;
        let value = '';
        
        this.advance(); // 跳过开始的引号
        
        while (this.position < this.input.length && this.currentChar() !== '"') {
            const char = this.currentChar();
            if (char === '\n') {
                this.addError('字符串不能跨行', startLine, startColumn);
                return;
            }
            if (char === '\\') {
                this.advance();
                const escaped = this.currentChar();
                if (escaped === 'n') value += '\n';
                else if (escaped === 't') value += '\t';
                else if (escaped === 'r') value += '\r';
                else if (escaped === '\\') value += '\\';
                else if (escaped === '"') value += '"';
                else value += escaped;
            } else {
                value += char;
            }
            this.advance();
        }
        
        if (this.currentChar() !== '"') {
            this.addError('未闭合的字符串', startLine, startColumn);
            return;
        }
        
        this.advance(); // 跳过结束的引号
        this.addToken('STRING_LITERAL', value, startLine, startColumn);
    }

    readChar() {
        const startLine = this.line;
        const startColumn = this.column;
        let value = '';
        
        this.advance(); // 跳过开始的单引号
        
        if (this.currentChar() === "'") {
            this.addError('空字符字面量', startLine, startColumn);
            this.advance();
            return;
        }
        
        while (this.position < this.input.length && this.currentChar() !== "'") {
            const char = this.currentChar();
            if (char === '\n') {
                this.addError('字符字面量不能跨行', startLine, startColumn);
                return;
            }
            
            // 处理转义字符
            if (char === '\\') {
                this.advance();
                const escaped = this.currentChar();
                if (escaped === "'") {
                    value += "'";
                } else if (escaped === '\\') {
                    value += '\\';
                } else if (escaped === 'n') {
                    value += '\n';
                } else if (escaped === 't') {
                    value += '\t';
                } else if (escaped === 'r') {
                    value += '\r';
                } else {
                    value += escaped || '';
                }
                this.advance();
            } else {
                value += char;
                this.advance();
            }
        }
        
        if (this.currentChar() !== "'") {
            this.addError('未闭合的字符字面量', startLine, startColumn);
            return;
        }
        
        if (value.length > 1) {
            this.addError('字符字面量只能包含一个字符', startLine, startColumn);
        }
        
        this.advance(); // 跳过结束的单引号
        this.addToken('CHAR_LITERAL', value, startLine, startColumn);
    }

    readNumber() {
        const startLine = this.line;
        const startColumn = this.column;
        let value = '';
        let hasDecimal = false;
        
        while (this.position < this.input.length && 
               (this.isDigit(this.currentChar()) || this.currentChar() === '.')) {
            if (this.currentChar() === '.') {
                if (hasDecimal) {
                    this.addError('数字中包含多个小数点', startLine, startColumn);
                    break;
                }
                hasDecimal = true;
            }
            value += this.currentChar();
            this.advance();
        }
        
        const tokenType = hasDecimal ? 'REAL_LITERAL' : 'INTEGER_LITERAL';
        this.addToken(tokenType, value, startLine, startColumn);
    }

    readIdentifier() {
        const startLine = this.line;
        const startColumn = this.column;
        let value = '';
        
        while (this.position < this.input.length && 
               (this.isLetter(this.currentChar()) || 
                this.isDigit(this.currentChar()) || 
                this.currentChar() === '_')) {
            value += this.currentChar();
            this.advance();
        }
        
        const tokenType = this.keywords.has(value.toUpperCase()) ? 'KEYWORD' : 'IDENTIFIER';
        // Keep original case for identifiers, only uppercase for keywords
        const tokenValue = tokenType === 'KEYWORD' ? value.toUpperCase() : value;
        this.addToken(tokenType, tokenValue, startLine, startColumn);
    }

    isDigit(char) {
        return char && /[0-9]/.test(char);
    }

    isLetter(char) {
        return char && /[a-zA-Z]/.test(char);
    }

    addToken(type, value, line = this.line, column = this.column) {
        this.tokens.push({
            type,
            value,
            line,
            column
        });
    }

    addError(message, line = this.line, column = this.column) {
        this.errors.push({
            message,
            line,
            column,
            type: 'lexical'
        });
    }

    getTokenTypeName(type) {
        const typeNames = {
            'KEYWORD': '关键字',
            'IDENTIFIER': '标识符',
            'INTEGER_LITERAL': '整数字面量',
            'REAL_LITERAL': '实数字面量',
            'STRING_LITERAL': '字符串字面量',
            'CHAR_LITERAL': '字符字面量',
            'ASSIGN': '赋值运算符',
            'EQUALS': '等于',
            'NOT_EQUALS': '不等于',
            'LESS_THAN': '小于',
            'GREATER_THAN': '大于',
            'LESS_EQUAL': '小于等于',
            'GREATER_EQUAL': '大于等于',
            'PLUS': '加号',
            'MINUS': '减号',
            'MULTIPLY': '乘号',
            'DIVIDE': '除号',
            'LPAREN': '左括号',
            'RPAREN': '右括号',
            'LBRACKET': '左方括号',
            'RBRACKET': '右方括号',
            'COMMA': '逗号',
            'COLON': '冒号',
            'SEMICOLON': '分号',
            'DOT': '点号',
            'EOF': '文件结束'
        };
        return typeNames[type] || type;
    }
}

// 兼容浏览器和Node.js环境
// ES6 导出
export default PseudocodeLexer;

// 兼容浏览器环境
if (typeof window !== 'undefined') {
    window.PseudocodeLexer = PseudocodeLexer;
}