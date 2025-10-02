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
            '^': 'POINTER',
            '.': 'DOT'
        };

        // 分隔符定义
        this.delimiters = {
            '(': 'LPAREN',
            ')': 'RPAREN',
            '[': 'LBRACKET',
            ']': 'RBRACKET',
            ',': 'COMMA',
            ':': 'COLON',
            ';': 'SEMICOLON'
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
            this.skipWhitespace();
            
            if (this.position >= this.input.length) break;

            const char = this.currentChar();

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

            // 运算符（多字符）
            const twoChar = char + (this.peek() || '');
            if (this.operators[twoChar]) {
                this.addToken(this.operators[twoChar], twoChar);
                this.advance();
                this.advance();
                continue;
            }

            // 运算符（单字符）
            if (this.operators[char]) {
                this.addToken(this.operators[char], char);
                this.advance();
                continue;
            }

            // 分隔符
            if (this.delimiters[char]) {
                this.addToken(this.delimiters[char], char);
                this.advance();
                continue;
            }

            // 未知字符
            this.addError(`未知字符: '${char}'`);
            this.advance();
        }

        this.addToken('EOF', '');
        return {
            tokens: this.tokens,
            errors: this.errors
        };
    }

    currentChar() {
        return this.input[this.position];
    }

    peek(offset = 1) {
        const pos = this.position + offset;
        return pos < this.input.length ? this.input[pos] : null;
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
        while (this.position < this.input.length && 
               /\s/.test(this.currentChar())) {
            this.advance();
        }
    }

    skipComment() {
        // 跳过 //
        this.advance();
        this.advance();
        
        // 跳过到行尾
        while (this.position < this.input.length && 
               this.currentChar() !== '\n') {
            this.advance();
        }
    }

    readString() {
        const startLine = this.line;
        const startColumn = this.column;
        let value = '';
        
        this.advance(); // 跳过开始的引号
        
        while (this.position < this.input.length && 
               this.currentChar() !== '"') {
            if (this.currentChar() === '\n') {
                this.addError('字符串不能跨行', startLine, startColumn);
                break;
            }
            value += this.currentChar();
            this.advance();
        }
        
        if (this.position >= this.input.length) {
            this.addError('未闭合的字符串', startLine, startColumn);
        } else {
            this.advance(); // 跳过结束的引号
        }
        
        this.addToken('STRING_LITERAL', value, startLine, startColumn);
    }

    readChar() {
        const startLine = this.line;
        const startColumn = this.column;
        let value = '';
        
        this.advance(); // 跳过开始的单引号
        
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
        
        if (this.position >= this.input.length || 
            this.currentChar() !== "'") {
            this.addError('未闭合的字符字面量', startLine, startColumn);
        } else {
            this.advance(); // 跳过结束的单引号
        }
        
        if (value.length !== 1) {
            this.addError('字符字面量必须包含恰好一个字符', startLine, startColumn);
        }
        
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
                    break; // 第二个小数点，停止
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
        this.addToken(tokenType, value.toUpperCase(), startLine, startColumn);
    }

    isDigit(char) {
        return char >= '0' && char <= '9';
    }

    isLetter(char) {
        return (char >= 'a' && char <= 'z') || 
               (char >= 'A' && char <= 'Z');
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
            type: 'LEXICAL_ERROR',
            message,
            line,
            column
        });
    }

    // 辅助方法：获取token类型的显示名称
    getTokenTypeName(type) {
        const names = {
            'KEYWORD': '关键字',
            'IDENTIFIER': '标识符',
            'STRING_LITERAL': '字符串字面量',
            'CHAR_LITERAL': '字符字面量',
            'INTEGER_LITERAL': '整数字面量',
            'REAL_LITERAL': '实数字面量',
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
            'POINTER': '指针',
            'DOT': '点',
            'LPAREN': '左括号',
            'RPAREN': '右括号',
            'LBRACKET': '左方括号',
            'RBRACKET': '右方括号',
            'COMMA': '逗号',
            'COLON': '冒号',
            'SEMICOLON': '分号',
            'EOF': '文件结束'
        };
        return names[type] || type;
    }
}

// 导出类
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PseudocodeLexer;
} else {
    window.PseudocodeLexer = PseudocodeLexer;
}