/**
 * Pseudocode 语法分析器
 * 基于 Cambridge 9618 标准
 */

class PseudocodeParser {
    constructor() {
        this.reset();
    }

    reset() {
        this.tokens = [];
        this.position = 0;
        this.errors = [];
        this.ast = null;
    }

    parse(tokens) {
        this.reset();
        this.tokens = tokens;
        
        try {
            this.ast = this.parseProgram();
        } catch (error) {
            this.addError(error.message);
        }

        return {
            ast: this.ast,
            errors: this.errors
        };
    }

    // 当前token
    currentToken() {
        return this.position < this.tokens.length ? this.tokens[this.position] : null;
    }

    // 向前看token
    peekToken(offset = 1) {
        const pos = this.position + offset;
        return pos < this.tokens.length ? this.tokens[pos] : null;
    }

    // 前进到下一个token
    advance() {
        if (this.position < this.tokens.length) {
            this.position++;
        }
    }

    // 检查当前token是否匹配指定类型和值
    match(type, value = null) {
        const token = this.currentToken();
        if (!token) return false;
        if (token.type !== type) return false;
        if (value !== null && token.value !== value) return false;
        return true;
    }

    // 消费指定的token
    consume(type, value = null, errorMessage = null) {
        if (this.match(type, value)) {
            const token = this.currentToken();
            this.advance();
            return token;
        }
        
        if (errorMessage) {
            throw new Error(errorMessage);
        }
        
        const message = this.getFriendlyConsumeError(type, value);
        throw new Error(message);
    }

    // 解析程序
    parseProgram() {
        const statements = [];
        
        while (this.currentToken() && this.currentToken().type !== 'EOF') {
            try {
                const stmt = this.parseStatement();
                if (stmt) {
                    statements.push(stmt);
                }
            } catch (error) {
                this.addError(error.message);
                this.synchronize();
            }
        }
        
        return {
            type: 'Program',
            statements
        };
    }

    // 解析语句
    parseStatement() {
        const token = this.currentToken();
        if (!token) return null;

        // 检查关键字类型的token
        if (token.type === 'KEYWORD') {
            switch (token.value) {
                case 'DECLARE':
                    return this.parseDeclaration();
                case 'CONSTANT':
                    return this.parseConstant();
                case 'TYPE':
                    return this.parseTypeDefinition();
                case 'IF':
                    return this.parseIfStatement();
                case 'FOR':
                    return this.parseForLoop();
                case 'WHILE':
                    return this.parseWhileLoop();
                case 'REPEAT':
                    return this.parseRepeatLoop();
                case 'INPUT':
                    return this.parseInput();
                case 'OUTPUT':
                    return this.parseOutput();
                case 'PROCEDURE':
                    return this.parseProcedure();
                case 'FUNCTION':
                    return this.parseFunction();
                case 'CALL':
                    return this.parseCall();
                case 'RETURN':
                    return this.parseReturn();
                default:
                    this.addError(`第${token.line}行: 未知的语句类型: ${token.value}\n提示：语句应该以关键字开始（如DECLARE、IF、FOR等）或者是变量赋值`, token.line);
                    return null;
            }
        } else if (token.type === 'IDENTIFIER') {
            return this.parseAssignment();
        } else {
            const friendlyMessage = this.getFriendlyErrorMessage(token);
            throw new Error(friendlyMessage);
        }
    }

    // 解析变量声明
    parseDeclaration() {
        this.consume('KEYWORD', 'DECLARE');
        
        // 解析变量名列表（支持逗号分隔的多个变量）
        const identifiers = [];
        do {
            const identifier = this.consume('IDENTIFIER');
            identifiers.push(identifier);
            
            if (this.match('COMMA')) {
                this.advance();
            } else {
                break;
            }
        } while (true);
        
        this.consume('COLON');
        
        let dataType;
        if (this.match('KEYWORD', 'ARRAY')) {
            dataType = this.parseArrayType();
        } else {
            dataType = this.parseDataType();
        }
        
        // 如果只有一个变量，返回单个声明节点
        if (identifiers.length === 1) {
            return {
                type: 'Declaration',
                identifier: identifiers[0].value,
                dataType,
                line: identifiers[0].line
            };
        }
        
        // 如果有多个变量，返回多重声明节点
        return {
            type: 'MultipleDeclaration',
            identifiers: identifiers.map(id => id.value),
            dataType,
            line: identifiers[0].line
        };
    }

    // 解析数组类型
    parseArrayType() {
        this.consume('KEYWORD', 'ARRAY');
        this.consume('LBRACKET');
        
        const dimensions = [];
        do {
            const lower = this.parseExpression();
            this.consume('COLON');
            const upper = this.parseExpression();
            dimensions.push({ lower, upper });
            
            if (this.match('COMMA')) {
                this.advance();
            } else {
                break;
            }
        } while (true);
        
        this.consume('RBRACKET');
        this.consume('KEYWORD', 'OF');
        const elementType = this.parseDataType();
        
        return {
            type: 'ArrayType',
            dimensions,
            elementType
        };
    }

    // 解析数据类型
    parseDataType() {
        const validTypes = ['INTEGER', 'REAL', 'CHAR', 'STRING', 'BOOLEAN', 'DATE'];
        const token = this.currentToken();
        
        if (token && token.type === 'KEYWORD' && validTypes.includes(token.value)) {
            this.advance();
            return {
                type: 'DataType',
                value: token.value
            };
        }
        
        if (token && token.type === 'IDENTIFIER') {
            this.advance();
            return {
                type: 'UserDefinedType',
                value: token.value
            };
        }
        
        throw new Error('期望数据类型');
    }

    // 解析常量声明
    parseConstant() {
        this.consume('KEYWORD', 'CONSTANT');
        const identifier = this.consume('IDENTIFIER');
        this.consume('EQUALS');
        const value = this.parseExpression();
        
        return {
            type: 'Constant',
            identifier: identifier.value,
            value,
            line: identifier.line
        };
    }

    // 解析类型定义
    parseTypeDefinition() {
        this.consume('KEYWORD', 'TYPE');
        const identifier = this.consume('IDENTIFIER');
        
        const fields = [];
        while (!this.match('KEYWORD', 'ENDTYPE')) {
            if (this.match('KEYWORD', 'DECLARE')) {
                this.advance();
                const fieldName = this.consume('IDENTIFIER');
                this.consume('COLON');
                
                let fieldType;
                if (this.match('KEYWORD', 'ARRAY')) {
                    fieldType = this.parseArrayType();
                } else {
                    fieldType = this.parseDataType();
                }
                
                fields.push({
                    name: fieldName.value,
                    type: fieldType,
                    line: fieldName.line
                });
            } else {
                throw new Error('TYPE定义中期望DECLARE语句');
            }
        }
        
        this.consume('KEYWORD', 'ENDTYPE');
        
        return {
            type: 'TypeDefinition',
            identifier: identifier.value,
            fields,
            line: identifier.line
        };
    }

    // 解析赋值语句
    parseAssignment() {
        const left = this.parseLeftHandSide();
        this.consume('ASSIGN');
        const right = this.parseExpression();
        
        return {
            type: 'Assignment',
            left,
            right,
            line: left.line
        };
    }

    // 解析左值（变量、数组元素等）
    parseLeftHandSide() {
        const identifier = this.consume('IDENTIFIER');
        let node = {
            type: 'Identifier',
            value: identifier.value,
            line: identifier.line
        };
        
        // 处理函数调用
        if (this.match('LPAREN')) {
            this.advance();
            const args = this.parseArgumentList();
            this.consume('RPAREN');
            
            node = {
                type: 'FunctionCall',
                name: identifier.value,
                arguments: args,
                line: identifier.line
            };
        }
        
        // 处理数组索引
        while (this.match('LBRACKET')) {
            this.advance();
            const indices = [];
            
            do {
                indices.push(this.parseExpression());
                if (this.match('COMMA')) {
                    this.advance();
                } else {
                    break;
                }
            } while (true);
            
            this.consume('RBRACKET');
            
            node = {
                type: 'ArrayAccess',
                array: node,
                indices,
                line: identifier.line
            };
        }
        
        // 处理结构体字段访问
        while (this.match('DOT')) {
            this.advance();
            const field = this.consume('IDENTIFIER');
            
            node = {
                type: 'FieldAccess',
                object: node,
                field: field.value,
                line: identifier.line
            };
        }
        
        return node;
    }

    // 解析IF语句
    parseIfStatement() {
        this.consume('KEYWORD', 'IF');
        const condition = this.parseExpression();
        this.consume('KEYWORD', 'THEN');
        
        const thenStatements = [];
        while (!this.match('KEYWORD', 'ELSE') && 
               !this.match('KEYWORD', 'ELSEIF') && 
               !this.match('KEYWORD', 'ENDIF')) {
            thenStatements.push(this.parseStatement());
        }
        
        const elseIfClauses = [];
        while (this.match('KEYWORD', 'ELSEIF')) {
            this.advance();
            const elseIfCondition = this.parseExpression();
            this.consume('KEYWORD', 'THEN');
            
            const elseIfStatements = [];
            while (!this.match('KEYWORD', 'ELSE') && 
                   !this.match('KEYWORD', 'ELSEIF') && 
                   !this.match('KEYWORD', 'ENDIF')) {
                elseIfStatements.push(this.parseStatement());
            }
            
            elseIfClauses.push({
                condition: elseIfCondition,
                statements: elseIfStatements
            });
        }
        
        let elseStatements = [];
        if (this.match('KEYWORD', 'ELSE')) {
            this.advance();
            while (!this.match('KEYWORD', 'ENDIF')) {
                elseStatements.push(this.parseStatement());
            }
        }
        
        this.consume('KEYWORD', 'ENDIF');
        
        return {
            type: 'IfStatement',
            condition,
            thenStatements,
            elseIfClauses,
            elseStatements
        };
    }

    // 解析FOR循环
    parseForLoop() {
        this.consume('KEYWORD', 'FOR');
        const variable = this.consume('IDENTIFIER');
        this.consume('ASSIGN');
        const start = this.parseExpression();
        this.consume('KEYWORD', 'TO');
        const end = this.parseExpression();
        
        let step = null;
        if (this.match('KEYWORD', 'STEP')) {
            this.advance();
            step = this.parseExpression();
        }
        
        const statements = [];
        while (!this.match('KEYWORD', 'NEXT')) {
            statements.push(this.parseStatement());
        }
        
        this.consume('KEYWORD', 'NEXT');
        this.consume('IDENTIFIER', variable.value);
        
        return {
            type: 'ForLoop',
            variable: variable.value,
            start,
            end,
            step,
            statements
        };
    }

    // 解析WHILE循环
    parseWhileLoop() {
        this.consume('KEYWORD', 'WHILE');
        const condition = this.parseExpression();
        
        const statements = [];
        while (!this.match('KEYWORD', 'ENDWHILE')) {
            statements.push(this.parseStatement());
        }
        
        this.consume('KEYWORD', 'ENDWHILE');
        
        return {
            type: 'WhileLoop',
            condition,
            statements
        };
    }

    // 解析REPEAT循环
    parseRepeatLoop() {
        this.consume('KEYWORD', 'REPEAT');
        
        const statements = [];
        while (!this.match('KEYWORD', 'UNTIL')) {
            statements.push(this.parseStatement());
        }
        
        this.consume('KEYWORD', 'UNTIL');
        const condition = this.parseExpression();
        
        return {
            type: 'RepeatLoop',
            statements,
            condition
        };
    }

    // 解析INPUT语句
    parseInput() {
        this.consume('KEYWORD', 'INPUT');
        
        let prompt = null;
        let variable;
        
        if (this.match('STRING_LITERAL')) {
            prompt = this.currentToken().value;
            this.advance();
            this.consume('COMMA');
        }
        
        variable = this.parseLeftHandSide();
        
        return {
            type: 'Input',
            prompt,
            variable
        };
    }

    // 解析OUTPUT语句
    parseOutput() {
        this.consume('KEYWORD', 'OUTPUT');
        
        const expressions = [];
        do {
            expressions.push(this.parseExpression());
            if (this.match('COMMA')) {
                this.advance();
            } else {
                break;
            }
        } while (true);
        
        return {
            type: 'Output',
            expressions
        };
    }

    // 解析过程定义
    parseProcedure() {
        this.consume('KEYWORD', 'PROCEDURE');
        const name = this.consume('IDENTIFIER');
        
        this.consume('LPAREN');
        const parameters = this.parseParameterList();
        this.consume('RPAREN');
        
        const statements = [];
        while (!this.match('KEYWORD', 'ENDPROCEDURE')) {
            statements.push(this.parseStatement());
        }
        
        this.consume('KEYWORD', 'ENDPROCEDURE');
        
        return {
            type: 'Procedure',
            name: name.value,
            parameters,
            statements
        };
    }

    // 解析函数定义
    parseFunction() {
        this.consume('KEYWORD', 'FUNCTION');
        const name = this.consume('IDENTIFIER');
        
        this.consume('LPAREN');
        const parameters = this.parseParameterList();
        this.consume('RPAREN');
        
        this.consume('KEYWORD', 'RETURNS');
        const returnType = this.parseDataType();
        
        const statements = [];
        while (!this.match('KEYWORD', 'ENDFUNCTION')) {
            statements.push(this.parseStatement());
        }
        
        this.consume('KEYWORD', 'ENDFUNCTION');
        
        return {
            type: 'Function',
            name: name.value,
            parameters,
            returnType,
            statements
        };
    }

    // 解析参数列表
    parseParameterList() {
        const parameters = [];
        
        if (!this.match('RPAREN')) {
            do {
                let passBy = 'BYVAL'; // 默认按值传递
                if (this.match('KEYWORD', 'BYREF') || this.match('KEYWORD', 'BYVAL')) {
                    passBy = this.currentToken().value;
                    this.advance();
                }
                
                const name = this.consume('IDENTIFIER');
                this.consume('COLON');
                const dataType = this.parseDataType();
                
                parameters.push({
                    name: name.value,
                    dataType,
                    passBy
                });
                
                if (this.match('COMMA')) {
                    this.advance();
                } else {
                    break;
                }
            } while (true);
        }
        
        return parameters;
    }

    // 解析函数/过程调用
    parseCall() {
        this.consume('KEYWORD', 'CALL');
        const name = this.consume('IDENTIFIER');
        
        this.consume('LPAREN');
        const args = this.parseArgumentList();
        this.consume('RPAREN');
        
        return {
            type: 'Call',
            name: name.value,
            arguments: args
        };
    }

    // 解析参数列表
    parseArgumentList() {
        const args = [];
        
        if (!this.match('RPAREN')) {
            do {
                args.push(this.parseExpression());
                if (this.match('COMMA')) {
                    this.advance();
                } else {
                    break;
                }
            } while (true);
        }
        
        return args;
    }

    // 解析RETURN语句
    parseReturn() {
        this.consume('KEYWORD', 'RETURN');
        let value = null;
        
        if (!this.isStatementEnd()) {
            value = this.parseExpression();
        }
        
        return {
            type: 'Return',
            value
        };
    }

    // 解析表达式
    parseExpression() {
        return this.parseLogicalOr();
    }

    // 解析逻辑或
    parseLogicalOr() {
        let left = this.parseLogicalAnd();
        
        while (this.match('KEYWORD', 'OR')) {
            const operator = this.currentToken().value;
            this.advance();
            const right = this.parseLogicalAnd();
            
            left = {
                type: 'BinaryExpression',
                operator,
                left,
                right
            };
        }
        
        return left;
    }

    // 解析逻辑与
    parseLogicalAnd() {
        let left = this.parseEquality();
        
        while (this.match('KEYWORD', 'AND')) {
            const operator = this.currentToken().value;
            this.advance();
            const right = this.parseEquality();
            
            left = {
                type: 'BinaryExpression',
                operator,
                left,
                right
            };
        }
        
        return left;
    }

    // 解析相等性比较
    parseEquality() {
        let left = this.parseRelational();
        
        while (this.match('EQUALS') || this.match('NOT_EQUALS')) {
            const operator = this.currentToken().type;
            this.advance();
            const right = this.parseRelational();
            
            left = {
                type: 'BinaryExpression',
                operator,
                left,
                right
            };
        }
        
        return left;
    }

    // 解析关系比较
    parseRelational() {
        let left = this.parseAdditive();
        
        while (this.match('LESS_THAN') || this.match('GREATER_THAN') || 
               this.match('LESS_EQUAL') || this.match('GREATER_EQUAL')) {
            const operator = this.currentToken().type;
            this.advance();
            const right = this.parseAdditive();
            
            left = {
                type: 'BinaryExpression',
                operator,
                left,
                right
            };
        }
        
        return left;
    }

    // 解析加减运算
    parseAdditive() {
        let left = this.parseMultiplicative();
        
        while (this.match('PLUS') || this.match('MINUS')) {
            const operator = this.currentToken().type;
            this.advance();
            const right = this.parseMultiplicative();
            
            left = {
                type: 'BinaryExpression',
                operator,
                left,
                right
            };
        }
        
        return left;
    }

    // 解析乘除运算
    parseMultiplicative() {
        let left = this.parseUnary();
        
        while (this.match('MULTIPLY') || this.match('DIVIDE') || 
               this.match('KEYWORD', 'DIV') || this.match('KEYWORD', 'MOD')) {
            const operator = this.currentToken().value || this.currentToken().type;
            this.advance();
            const right = this.parseUnary();
            
            left = {
                type: 'BinaryExpression',
                operator,
                left,
                right
            };
        }
        
        return left;
    }

    // 解析一元运算
    parseUnary() {
        if (this.match('KEYWORD', 'NOT') || this.match('MINUS')) {
            const operator = this.currentToken().value || this.currentToken().type;
            this.advance();
            const operand = this.parseUnary();
            
            return {
                type: 'UnaryExpression',
                operator,
                operand
            };
        }
        
        return this.parsePrimary();
    }

    // 解析基本表达式
    parsePrimary() {
        const token = this.currentToken();
        
        if (!token) {
            throw new Error('意外的表达式结束');
        }
        
        // 字面量
        if (token.type === 'INTEGER_LITERAL' || 
            token.type === 'REAL_LITERAL' || 
            token.type === 'STRING_LITERAL' || 
            token.type === 'CHAR_LITERAL') {
            this.advance();
            return {
                type: 'Literal',
                dataType: token.type,
                value: token.value
            };
        }
        
        // 布尔字面量
        if (token.type === 'KEYWORD' && (token.value === 'TRUE' || token.value === 'FALSE')) {
            this.advance();
            return {
                type: 'Literal',
                dataType: 'BOOLEAN_LITERAL',
                value: token.value
            };
        }
        
        // 括号表达式
        if (token.type === 'LPAREN') {
            this.advance();
            const expr = this.parseExpression();
            this.consume('RPAREN');
            return expr;
        }
        
        // 标识符（变量、函数调用等）
        if (token.type === 'IDENTIFIER') {
            return this.parseLeftHandSide();
        }
        
        // 库函数关键字（ASC、CHR、INT、RAND等）
        if (token.type === 'KEYWORD' && 
            ['ASC', 'CHR', 'INT', 'RAND', 'LENGTH', 'SUBSTRING', 'LEFT', 'RIGHT', 'MID', 'UCASE', 'LCASE'].includes(token.value)) {
            const functionName = token.value;
            this.advance();
            
            // 检查是否有参数列表
            if (this.match('LPAREN')) {
                this.advance();
                const args = this.parseArgumentList();
                this.consume('RPAREN');
                
                return {
                    type: 'FunctionCall',
                    name: functionName,
                    arguments: args,
                    line: token.line
                };
            } else {
                throw new Error(`库函数 ${functionName} 需要参数列表`);
            }
        }
        
        throw new Error(`意外的token: ${token.type}(${token.value})`);
    }

    // 检查是否是语句结束
    isStatementEnd() {
        const token = this.currentToken();
        if (!token || token.type === 'EOF') return true;
        
        const endKeywords = [
            'ENDIF', 'ENDWHILE', 'ENDPROCEDURE', 'ENDFUNCTION',
            'NEXT', 'UNTIL', 'ELSE', 'ELSEIF', 'OTHERWISE'
        ];
        
        return token.type === 'KEYWORD' && endKeywords.includes(token.value);
    }

    // 错误恢复
    synchronize() {
        this.advance();
        
        while (this.currentToken() && this.currentToken().type !== 'EOF') {
            const token = this.currentToken();
            
            if (token.type === 'KEYWORD') {
                const syncKeywords = [
                    'DECLARE', 'CONSTANT', 'IF', 'FOR', 'WHILE', 'REPEAT',
                    'INPUT', 'OUTPUT', 'PROCEDURE', 'FUNCTION', 'CALL', 'RETURN'
                ];
                
                if (syncKeywords.includes(token.value)) {
                    return;
                }
            }
            
            this.advance();
        }
    }

    // 生成友好的consume错误提示
    getFriendlyConsumeError(expectedType, expectedValue = null) {
        const token = this.currentToken();
        const line = token ? token.line : '未知';
        
        if (expectedType === 'COLON') {
            // 检查当前上下文，判断是参数声明还是变量声明
            const currentTokenValue = token ? token.value : '';
            if (currentTokenValue === 'RETURNS' || currentTokenValue === 'REAL' || currentTokenValue === 'INTEGER' || 
                currentTokenValue === 'STRING' || currentTokenValue === 'BOOLEAN' || currentTokenValue === 'CHAR' || currentTokenValue === 'DATE') {
                return `第${line}行: 在函数参数或返回类型声明中发现语法错误\n提示：请检查函数声明语法，如 FUNCTION name(param : TYPE) RETURNS TYPE`;
            }
            return `第${line}行: 缺少冒号(:)\n提示：在变量声明中，变量名后面需要加冒号，如 DECLARE name : STRING`;
        }
        
        if (expectedType === 'KEYWORD' && expectedValue) {
            if (expectedValue === 'THEN') {
                return `第${line}行: IF语句后缺少THEN关键字\n提示：IF条件后必须跟THEN，如 IF x > 0 THEN`;
            }
            if (expectedValue === 'ENDIF') {
                return `第${line}行: IF语句缺少ENDIF结束标记\n提示：每个IF语句都必须以ENDIF结束`;
            }
            if (expectedValue === 'ENDWHILE') {
                return `第${line}行: WHILE循环缺少ENDWHILE结束标记\n提示：每个WHILE循环都必须以ENDWHILE结束`;
            }
            if (expectedValue === 'NEXT') {
                return `第${line}行: FOR循环缺少NEXT结束标记\n提示：每个FOR循环都必须以NEXT结束`;
            }
            return `第${line}行: 缺少关键字 ${expectedValue}\n提示：请检查语法结构是否完整`;
        }
        
        if (expectedType === 'IDENTIFIER') {
            return `第${line}行: 这里需要一个变量名\n提示：变量名只能包含字母、数字和下划线，且不能以数字开头`;
        }
        
        if (expectedType === 'ASSIGN') {
            return `第${line}行: 缺少赋值符号(←)\n提示：给变量赋值时需要使用 ← 符号，如 x ← 10`;
        }
        
        // 默认错误提示
        const expected = expectedValue ? `${expectedValue}` : this.getTokenTypeName(expectedType);
        const actual = token ? token.value : '文件结束';
        return `第${line}行: 期望 ${expected}，但得到 ${actual}\n提示：请检查语法是否正确`;
    }
    
    // 获取token类型的中文名称
    getTokenTypeName(type) {
        const names = {
            'KEYWORD': '关键字',
            'IDENTIFIER': '变量名',
            'COLON': '冒号(:)',
            'SEMICOLON': '分号(;)',
            'COMMA': '逗号(,)',
            'LPAREN': '左括号((',
            'RPAREN': '右括号))',
            'ASSIGN': '赋值符号(←)',
            'EOF': '文件结束'
        };
        return names[type] || type;
    }
    
    // 生成友好的错误提示
    getFriendlyErrorMessage(token) {
        const tokenType = token.type;
        const tokenValue = token.value;
        
        if (tokenType === 'COLON') {
            return `第${token.line}行: 发现冒号(:)，但这里应该是一个完整的语句。\n提示：冒号通常用在变量声明中，如 DECLARE name : STRING\n请检查是否缺少了关键字（如DECLARE、IF、FOR等）或变量名。`;
        }
        
        if (tokenType === 'SEMICOLON') {
            return `第${token.line}行: 发现分号(;)，但这里应该是一个完整的语句。\n提示：请检查前面的语句是否完整。`;
        }
        
        if (tokenType === 'COMMA') {
            return `第${token.line}行: 发现逗号(,)，但这里应该是一个完整的语句。\n提示：逗号通常用于分隔参数或数组元素，请检查是否在错误的位置。`;
        }
        
        if (tokenType === 'RPAREN' || tokenType === 'RBRACKET') {
            return `第${token.line}行: 发现${tokenType === 'RPAREN' ? '右括号)' : '右方括号]'}，但这里应该是一个完整的语句。\n提示：请检查括号是否配对正确。`;
        }
        
        // 默认错误提示
        return `第${token.line}行: 这里应该是一个语句的开始，但发现了 ${tokenValue}\n提示：语句应该以关键字开始（如DECLARE、IF、FOR、WHILE等）或者是变量赋值。`;
    }
    
    // 添加错误
    addError(message, line = null, column = null) {
        const token = this.currentToken();
        this.errors.push({
            type: 'SYNTAX_ERROR',
            message,
            line: line || (token ? token.line : 0),
            column: column || (token ? token.column : 0)
        });
    }
}

// 兼容浏览器和Node.js环境
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PseudocodeParser;
} else {
    window.PseudocodeParser = PseudocodeParser;
}