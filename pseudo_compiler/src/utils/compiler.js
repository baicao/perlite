/**
 * Pseudocode 编译器主文件
 * 整合词法分析、语法分析和代码生成
 */

import PseudocodeLexer from './lexer.js';
import PseudocodeParser from './parser.js';
import PseudocodeGenerator from './generator.js';

class PseudocodeCompiler {
    constructor() {
        this.lexer = new PseudocodeLexer();
        this.parser = new PseudocodeParser();
        this.generator = new PseudocodeGenerator();
        this.reset();
    }

    reset() {
        this.sourceCode = '';
        this.tokens = [];
        this.ast = null;
        this.generatedCode = '';
        this.errors = [];
        this.warnings = [];
    }

    // 编译Pseudocode源代码
    compile(sourceCode) {
        this.reset();
        this.sourceCode = sourceCode;

        try {
            // 第一步：词法分析
            const lexResult = this.lexer.tokenize(sourceCode);
            this.tokens = lexResult.tokens;
            this.addErrors(lexResult.errors, 'LEXICAL');

            if (this.tokens.length === 0) {
                this.addError('源代码为空或无有效tokens', 'COMPILATION');
                return this.getResult();
            }

            // 第二步：语法分析
            const parseResult = this.parser.parse(this.tokens);
            this.ast = parseResult.ast;
            this.addErrors(parseResult.errors, 'SYNTAX');

            if (!this.ast || this.hasErrors()) {
                return this.getResult();
            }

            // 第三步：语义分析（简单检查）
            this.performSemanticAnalysis();

            if (this.hasErrors()) {
                return this.getResult();
            }

            // 第四步：代码生成
            const generateResult = this.generator.generate(this.ast);
            this.generatedCode = generateResult.code;
            this.addErrors(generateResult.errors, 'GENERATION');

        } catch (error) {
            this.addError(`编译过程中发生错误: ${error.message}`, 'COMPILATION');
        }

        return this.getResult();
    }

    // 执行语义分析
    performSemanticAnalysis() {
        const analyzer = new SemanticAnalyzer();
        const result = analyzer.analyze(this.ast);
        this.addErrors(result.errors, 'SEMANTIC');
        this.addWarnings(result.warnings);
    }

    // 获取编译结果
    getResult() {
        return {
            success: !this.hasErrors(),
            sourceCode: this.sourceCode,
            tokens: this.tokens,
            ast: this.ast,
            generatedCode: this.generatedCode,
            errors: this.errors,
            warnings: this.warnings
        };
    }

    // 检查是否有错误
    hasErrors() {
        return this.errors.length > 0;
    }

    // 添加错误
    addError(message, type = 'UNKNOWN', line = 0, column = 0) {
        this.errors.push({
            type,
            message,
            line,
            column,
            timestamp: new Date().toISOString()
        });
    }

    // 批量添加错误
    addErrors(errors, type) {
        for (const error of errors) {
            this.errors.push({
                ...error,
                type: error.type || type,
                timestamp: new Date().toISOString()
            });
        }
    }

    // 添加警告
    addWarning(message, line = 0, column = 0) {
        this.warnings.push({
            message,
            line,
            column,
            timestamp: new Date().toISOString()
        });
    }

    // 批量添加警告
    addWarnings(warnings) {
        for (const warning of warnings) {
            this.warnings.push({
                ...warning,
                timestamp: new Date().toISOString()
            });
        }
    }

    // 格式化错误信息
    formatErrors() {
        return this.errors.map(error => {
            const location = error.line > 0 ? `[${error.line}:${error.column}] ` : '';
            return `${error.type}: ${location}${error.message}`;
        }).join('\n');
    }

    // 格式化警告信息
    formatWarnings() {
        return this.warnings.map(warning => {
            const location = warning.line > 0 ? `[${warning.line}:${warning.column}] ` : '';
            return `WARNING: ${location}${warning.message}`;
        }).join('\n');
    }

    // 获取编译统计信息
    getStatistics() {
        return {
            sourceLines: this.sourceCode.split('\n').length,
            tokenCount: this.tokens.length,
            errorCount: this.errors.length,
            warningCount: this.warnings.length,
            generatedLines: this.generatedCode.split('\n').length
        };
    }
}

/**
 * 简单的语义分析器
 */
class SemanticAnalyzer {
    constructor() {
        this.reset();
    }

    reset() {
        this.symbolTable = new Map();
        this.currentScope = 'global';
        this.errors = [];
        this.warnings = [];
        this.functionTable = new Map();
        this.procedureTable = new Map();
        this.scopeStack = [new Map()]; // 作用域栈，第一个是全局作用域
    }

    analyze(ast) {
        this.reset();
        
        if (ast && ast.statements) {
            this.analyzeStatements(ast.statements);
        }

        return {
            errors: this.errors,
            warnings: this.warnings
        };
    }

    analyzeStatements(statements) {
        for (const stmt of statements) {
            this.analyzeStatement(stmt);
        }
    }

    analyzeStatement(node) {
        switch (node.type) {
            case 'Declaration':
                this.analyzeDeclaration(node);
                break;
            case 'MultipleDeclaration':
                this.analyzeMultipleDeclaration(node);
                break;
            case 'Constant':
                this.analyzeConstant(node);
                break;
            case 'TypeDefinition':
                this.analyzeTypeDefinition(node);
                break;
            case 'Assignment':
                this.analyzeAssignment(node);
                break;
            case 'IfStatement':
                this.analyzeIfStatement(node);
                break;
            case 'ForLoop':
                this.analyzeForLoop(node);
                break;
            case 'WhileLoop':
                this.analyzeWhileLoop(node);
                break;
            case 'RepeatLoop':
                this.analyzeRepeatLoop(node);
                break;
            case 'Input':
                this.analyzeInput(node);
                break;
            case 'Output':
                this.analyzeOutput(node);
                break;
            case 'Procedure':
                this.analyzeProcedure(node);
                break;
            case 'Function':
                this.analyzeFunction(node);
                break;
            case 'Call':
                this.analyzeCall(node);
                break;
            case 'Return':
                this.analyzeReturn(node);
                break;
        }
    }

    analyzeDeclaration(node) {
        const varName = node.identifier;
        
        if (this.isSymbolDeclaredInCurrentScope(varName)) {
            this.addError(`变量 '${varName}' 已经声明过`, node.line);
        } else {
            this.declareSymbol(varName, {
                type: 'variable',
                dataType: node.dataType,
                declared: true,
                used: false,
                line: node.line
            });
        }
    }

    analyzeMultipleDeclaration(node) {
        for (const varName of node.identifiers) {
            if (this.isSymbolDeclaredInCurrentScope(varName)) {
                this.addError(`变量 '${varName}' 已经声明过`, node.line);
            } else {
                this.declareSymbol(varName, {
                    type: 'variable',
                    dataType: node.dataType,
                    declared: true,
                    used: false,
                    line: node.line
                });
            }
        }
    }

    analyzeConstant(node) {
        const constName = node.identifier;
        
        if (this.isSymbolDeclaredInCurrentScope(constName)) {
            this.addError(`常量 '${constName}' 已经声明过`, node.line);
        } else {
            this.declareSymbol(constName, {
                type: 'constant',
                dataType: node.dataType,
                value: node.value,
                declared: true,
                used: false,
                line: node.line
            });
        }
    }

    analyzeTypeDefinition(node) {
        const typeName = node.identifier;
        
        if (this.isSymbolDeclaredInCurrentScope(typeName)) {
            this.addError(`类型 '${typeName}' 已经定义过`, node.line);
        } else {
            this.declareSymbol(typeName, {
                type: 'userType',
                fields: node.fields,
                declared: true,
                used: false,
                line: node.line
            });
        }
    }

    analyzeAssignment(node) {
        // 检查左值
        this.checkLeftValue(node.left);
        
        // 分析右值表达式
        this.analyzeExpression(node.right);
    }

    analyzeIfStatement(node) {
        this.analyzeExpression(node.condition);
        this.analyzeStatements(node.thenStatements);
        
        for (const elseIf of node.elseIfClauses) {
            this.analyzeExpression(elseIf.condition);
            this.analyzeStatements(elseIf.statements);
        }
        
        this.analyzeStatements(node.elseStatements);
    }

    analyzeForLoop(node) {
        // FOR循环变量是隐式声明的，自动添加到符号表
        if (!this.lookupSymbol(node.variable)) {
            this.declareSymbol(node.variable, {
                type: 'INTEGER',
                scope: this.currentScope,
                line: node.line || 0,
                isLoopVariable: true
            });
        }
        
        this.analyzeExpression(node.start);
        this.analyzeExpression(node.end);
        
        if (node.step) {
            this.analyzeExpression(node.step);
        }
        
        this.analyzeStatements(node.statements);
    }

    analyzeWhileLoop(node) {
        this.analyzeExpression(node.condition);
        this.analyzeStatements(node.statements);
    }

    analyzeRepeatLoop(node) {
        this.analyzeStatements(node.statements);
        this.analyzeExpression(node.condition);
    }

    analyzeInput(node) {
        this.checkLeftValue(node.variable);
    }

    analyzeOutput(node) {
        for (const expr of node.expressions) {
            this.analyzeExpression(expr);
        }
    }

    analyzeProcedure(node) {
        if (this.procedureTable.has(node.name)) {
            this.addError(`过程 '${node.name}' 已经定义过`, node.line);
        } else {
            this.procedureTable.set(node.name, {
                parameters: node.parameters,
                statements: node.statements
            });
        }
        
        // 进入新作用域
        this.enterScope();
        
        // 将过程参数添加到当前作用域
        for (const param of node.parameters) {
            if (this.isSymbolDeclaredInCurrentScope(param.name)) {
                this.addError(`参数 '${param.name}' 已经声明过`, node.line);
            } else {
                this.declareSymbol(param.name, {
                    type: 'parameter',
                    dataType: param.dataType,
                    declared: true,
                    used: false,
                    line: node.line
                });
            }
        }
        
        // 分析过程体
        this.analyzeStatements(node.statements);
        
        // 退出作用域
        this.exitScope();
    }

    analyzeFunction(node) {
        if (this.functionTable.has(node.name)) {
            this.addError(`函数 '${node.name}' 已经定义过`, node.line);
        } else {
            this.functionTable.set(node.name, {
                parameters: node.parameters,
                returnType: node.returnType,
                statements: node.statements
            });
        }
        
        // 进入新作用域
        this.enterScope();
        
        // 将函数参数添加到当前作用域
        for (const param of node.parameters) {
            if (this.isSymbolDeclaredInCurrentScope(param.name)) {
                this.addError(`参数 '${param.name}' 已经声明过`, node.line);
            } else {
                this.declareSymbol(param.name, {
                    type: 'parameter',
                    dataType: param.dataType,
                    declared: true,
                    used: false,
                    line: node.line
                });
            }
        }
        
        // 分析函数体
        this.analyzeStatements(node.statements);
        
        // 退出作用域
        this.exitScope();
    }

    analyzeCall(node) {
        // 检查函数/过程是否存在
        if (!this.functionTable.has(node.name) && !this.procedureTable.has(node.name)) {
            this.addError(`未定义的函数或过程 '${node.name}'`, node.line);
        }
        
        // 分析参数
        for (const arg of node.arguments) {
            this.analyzeExpression(arg);
        }
    }

    analyzeReturn(node) {
        if (node.value) {
            this.analyzeExpression(node.value);
        }
    }

    analyzeExpression(node) {
        if (!node) return;
        
        switch (node.type) {
            case 'Identifier':
                this.checkVariableUsage(node.value, node.line);
                break;
            case 'ArrayAccess':
                this.checkVariableUsage(node.array.value, node.line);
                for (const index of node.indices) {
                    this.analyzeExpression(index);
                }
                break;
            case 'FieldAccess':
                this.analyzeExpression(node.object);
                break;
            case 'FunctionCall':
                // 检查函数是否存在（包括内置库函数）
                const builtInFunctions = ['ASC', 'CHR', 'INT', 'RAND', 'LENGTH', 'SUBSTRING', 'LEFT', 'RIGHT', 'MID', 'UCASE', 'LCASE'];
                if (!this.functionTable.has(node.name) && !builtInFunctions.includes(node.name)) {
                    this.addError(`未定义的函数 '${node.name}'`, node.line);
                }
                // 分析参数
                for (const arg of node.arguments) {
                    this.analyzeExpression(arg);
                }
                break;
            case 'BinaryExpression':
                this.analyzeExpression(node.left);
                this.analyzeExpression(node.right);
                break;
            case 'UnaryExpression':
                this.analyzeExpression(node.operand);
                break;
            case 'Literal':
                // 字面量不需要特殊处理
                break;
        }
    }

    checkLeftValue(node) {
        if (node.type === 'Identifier') {
            if (!this.lookupSymbol(node.value)) {
                this.addError(`未声明的变量 '${node.value}'`, node.line);
            }
        } else if (node.type === 'ArrayAccess') {
            if (!this.lookupSymbol(node.array.value)) {
                this.addError(`未声明的数组 '${node.array.value}'`, node.line);
            }
            for (const index of node.indices) {
                this.analyzeExpression(index);
            }
        } else if (node.type === 'FieldAccess') {
            this.analyzeExpression(node.object);
        }
    }

    checkVariableUsage(varName, line) {
        const symbol = this.lookupSymbol(varName);
        if (symbol) {
            symbol.used = true;
        } else {
            this.addError(`未声明的变量 '${varName}'`, line);
        }
    }

    addError(message, line = 0) {
        this.errors.push({
            type: 'SEMANTIC_ERROR',
            message,
            line,
            column: 0
        });
    }

    addWarning(message, line = 0) {
        this.warnings.push({
            message,
            line,
            column: 0
        });
    }

    // 作用域管理方法
    enterScope() {
        this.scopeStack.push(new Map());
    }

    exitScope() {
        if (this.scopeStack.length > 1) {
            this.scopeStack.pop();
        }
    }

    getCurrentScope() {
        return this.scopeStack[this.scopeStack.length - 1];
    }

    declareSymbol(name, symbol) {
        const currentScope = this.getCurrentScope();
        currentScope.set(name, symbol);
        // 只有在全局作用域时才添加到全局符号表
        if (this.scopeStack.length === 1) {
            this.symbolTable.set(name, symbol);
        }
    }

    isSymbolDeclaredInCurrentScope(name) {
        const currentScope = this.getCurrentScope();
        return currentScope.has(name);
    }

    lookupSymbol(name) {
        // 从当前作用域向上查找
        for (let i = this.scopeStack.length - 1; i >= 0; i--) {
            const scope = this.scopeStack[i];
            if (scope.has(name)) {
                return scope.get(name);
            }
        }
        return null;
    }
}

// 兼容浏览器和Node.js环境
// ES6 导出
export { PseudocodeCompiler, SemanticAnalyzer };

// 兼容浏览器环境
if (typeof window !== 'undefined') {
    window.PseudocodeCompiler = PseudocodeCompiler;
    window.SemanticAnalyzer = SemanticAnalyzer;
}