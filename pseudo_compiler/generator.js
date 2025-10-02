/**
 * Pseudocode 代码生成器
 * 将AST转换为可执行的JavaScript代码
 */

class PseudocodeGenerator {
    constructor() {
        this.reset();
    }

    reset() {
        this.output = [];
        this.indentLevel = 0;
        this.variables = new Map();
        this.functions = new Map();
        this.procedures = new Map();
        this.errors = [];
    }

    generate(ast) {
        this.reset();
        
        try {
            this.generateProgram(ast);
        } catch (error) {
            this.addError(error.message);
        }

        return {
            code: this.output.join('\n'),
            errors: this.errors
        };
    }

    // 生成程序
    generateProgram(node) {
        this.emit('// Generated JavaScript from Pseudocode');
        this.emit('// Runtime support functions');
        this.emit('');
        
        // 添加运行时支持函数
        this.generateRuntimeSupport();
        this.emit('');
        
        this.emit('// Main program');
        this.emit('function main() {');
        this.indent();
        
        // 生成所有语句
        for (const statement of node.statements) {
            this.generateStatement(statement);
        }
        
        this.dedent();
        this.emit('}');
        this.emit('');
        this.emit('// Run the program');
        this.emit('main();');
    }

    // 生成运行时支持函数
    generateRuntimeSupport() {
        this.emit('// Input/Output functions for browser environment');
        this.emit('function INPUT(prompt = "") {');
        this.emit('    const answer = window.prompt(prompt || "Enter value:");');
        this.emit('    if (answer === null) return null;');
        this.emit('    // Try to parse as number first');
        this.emit('    const num = parseFloat(answer);');
        this.emit('    if (!isNaN(num) && isFinite(num)) {');
        this.emit('        return num;');
        this.emit('    } else {');
        this.emit('        return answer;');
        this.emit('    }');
        this.emit('}');
        this.emit('');
        
        this.emit('function OUTPUT(...args) {');
        this.emit('    console.log(...args);');
        this.emit('}');
        this.emit('');
        
        this.emit('// Math functions');
        this.emit('const DIV = (a, b) => Math.floor(a / b);');
        this.emit('const MOD = (a, b) => a % b;');
        this.emit('const SQRT = Math.sqrt;');
        this.emit('const ABS = Math.abs;');
        this.emit('const ROUND = Math.round;');
        this.emit('const INT = Math.floor;');
        this.emit('const RAND = (max) => {');
        this.emit('    if (typeof max !== "number" || max <= 0) {');
        this.emit('        throw new Error("RAND函数需要一个正整数参数");');
        this.emit('    }');
        this.emit('    return Math.random() * max;');
        this.emit('};');
        this.emit('');
        
        this.emit('// String functions');
        this.emit('const LENGTH = (str) => str.length;');
        this.emit('const SUBSTRING = (str, start, length) => str.substr(start - 1, length);');
        this.emit('const LEFT = (str, length) => str.substr(0, length);');
        this.emit('const RIGHT = (str, length) => str.substr(-length);');
        this.emit('const MID = (str, start, length) => str.substr(start - 1, length);');
        this.emit('const UCASE = (str) => str.toUpperCase();');
        this.emit('const LCASE = (str) => str.toLowerCase();');
        this.emit('');
        this.emit('// Character functions');
        this.emit('const ASC = (char) => {');
        this.emit('    if (typeof char !== "string" || char.length === 0) {');
        this.emit('        throw new Error("ASC函数需要一个字符参数");');
        this.emit('    }');
        this.emit('    return char.charCodeAt(0);');
        this.emit('};');
        this.emit('');
        this.emit('const CHR = (code) => {');
        this.emit('    if (typeof code !== "number" || code < 0 || code > 65535) {');
        this.emit('        throw new Error("CHR函数需要一个0-65535之间的整数参数");');
        this.emit('    }');
        this.emit('    return String.fromCharCode(code);');
        this.emit('};');
        this.emit('');
        
        this.emit('// Array helper functions');
        this.emit('function createArray(dimensions, defaultValue = 0) {');
        this.emit('    if (dimensions.length === 1) {');
        this.emit('        const [lower, upper] = dimensions[0];');
        this.emit('        const size = upper - lower + 1;');
        this.emit('        return new Array(size).fill(defaultValue);');
        this.emit('    } else {');
        this.emit('        const [lower, upper] = dimensions[0];');
        this.emit('        const size = upper - lower + 1;');
        this.emit('        const result = new Array(size);');
        this.emit('        for (let i = 0; i < size; i++) {');
        this.emit('            result[i] = createArray(dimensions.slice(1), defaultValue);');
        this.emit('        }');
        this.emit('        return result;');
        this.emit('    }');
        this.emit('}');
        this.emit('');
        
        this.emit('function arrayAccess(array, indices, baseLower = []) {');
        this.emit('    let current = array;');
        this.emit('    for (let i = 0; i < indices.length; i++) {');
        this.emit('        const index = indices[i] - (baseLower[i] || 0);');
        this.emit('        current = current[index];');
        this.emit('    }');
        this.emit('    return current;');
        this.emit('}');
        this.emit('');
        
        this.emit('function arraySet(array, indices, value, baseLower = []) {');
        this.emit('    let current = array;');
        this.emit('    for (let i = 0; i < indices.length - 1; i++) {');
        this.emit('        const index = indices[i] - (baseLower[i] || 0);');
        this.emit('        current = current[index];');
        this.emit('    }');
        this.emit('    const lastIndex = indices[indices.length - 1] - (baseLower[indices.length - 1] || 0);');
        this.emit('    current[lastIndex] = value;');
        this.emit('}');
    }

    // 生成语句
    generateStatement(node) {
        switch (node.type) {
            case 'Declaration':
                this.generateDeclaration(node);
                break;
            case 'MultipleDeclaration':
                this.generateMultipleDeclaration(node);
                break;
            case 'Constant':
                this.generateConstant(node);
                break;
            case 'Assignment':
                this.generateAssignment(node);
                break;
            case 'IfStatement':
                this.generateIfStatement(node);
                break;
            case 'ForLoop':
                this.generateForLoop(node);
                break;
            case 'WhileLoop':
                this.generateWhileLoop(node);
                break;
            case 'RepeatLoop':
                this.generateRepeatLoop(node);
                break;
            case 'Input':
                this.generateInput(node);
                break;
            case 'Output':
                this.generateOutput(node);
                break;
            case 'Procedure':
                this.generateProcedure(node);
                break;
            case 'Function':
                this.generateFunction(node);
                break;
            case 'Call':
                this.generateCall(node);
                break;
            case 'Return':
                this.generateReturn(node);
                break;
            default:
                this.addError(`未知的语句类型: ${node.type}`);
        }
    }

    // 生成变量声明
    generateDeclaration(node) {
        const varName = node.identifier;
        
        if (node.dataType.type === 'ArrayType') {
            // 数组声明
            const dimensions = node.dataType.dimensions.map(dim => 
                `[${this.generateExpression(dim.lower)}, ${this.generateExpression(dim.upper)}]`
            ).join(', ');
            
            this.emit(`let ${varName} = createArray([${dimensions}]);`);
            this.variables.set(varName, {
                type: 'array',
                elementType: node.dataType.elementType.value,
                dimensions: node.dataType.dimensions
            });
        } else {
            // 普通变量声明
            const defaultValue = this.getDefaultValue(node.dataType.value);
            this.emit(`let ${varName} = ${defaultValue};`);
            this.variables.set(varName, {
                type: 'variable',
                dataType: node.dataType.value
            });
        }
    }

    // 生成多变量声明
    generateMultipleDeclaration(node) {
        for (const varName of node.identifiers) {
            if (node.dataType.type === 'ArrayType') {
                // 数组声明
                const dimensions = node.dataType.dimensions.map(dim => 
                    `[${this.generateExpression(dim.lower)}, ${this.generateExpression(dim.upper)}]`
                ).join(', ');
                
                this.emit(`let ${varName} = createArray([${dimensions}]);`);
                this.variables.set(varName, {
                    type: 'array',
                    elementType: node.dataType.elementType.value,
                    dimensions: node.dataType.dimensions
                });
            } else {
                // 普通变量声明
                const defaultValue = this.getDefaultValue(node.dataType.value);
                this.emit(`let ${varName} = ${defaultValue};`);
                this.variables.set(varName, {
                    type: 'variable',
                    dataType: node.dataType.value
                });
            }
        }
    }

    // 生成常量声明
    generateConstant(node) {
        const value = this.generateExpression(node.value);
        this.emit(`const ${node.identifier} = ${value};`);
    }

    // 生成赋值语句
    generateAssignment(node) {
        const right = this.generateExpression(node.right);
        
        if (node.left.type === 'Identifier') {
            this.emit(`${node.left.value} = ${right};`);
        } else if (node.left.type === 'ArrayAccess') {
            const arrayName = node.left.array.value;
            const indices = node.left.indices.map(idx => this.generateExpression(idx)).join(', ');
            // 获取数组的基础索引信息
            const arrayInfo = this.variables.get(arrayName);
            if (arrayInfo && arrayInfo.type === 'array') {
                const baseLower = arrayInfo.dimensions.map(dim => this.generateExpression(dim.lower)).join(', ');
                this.emit(`arraySet(${arrayName}, [${indices}], ${right}, [${baseLower}]);`);
            } else {
                this.emit(`arraySet(${arrayName}, [${indices}], ${right});`);
            }
        } else if (node.left.type === 'FieldAccess') {
            const object = this.generateExpression(node.left.object);
            this.emit(`${object}.${node.left.field} = ${right};`);
        }
    }

    // 生成IF语句
    generateIfStatement(node) {
        const condition = this.generateExpression(node.condition);
        this.emit(`if (${condition}) {`);
        this.indent();
        
        for (const stmt of node.thenStatements) {
            this.generateStatement(stmt);
        }
        
        this.dedent();
        
        // 处理ELSEIF子句
        for (const elseIf of node.elseIfClauses) {
            const elseIfCondition = this.generateExpression(elseIf.condition);
            this.emit(`} else if (${elseIfCondition}) {`);
            this.indent();
            
            for (const stmt of elseIf.statements) {
                this.generateStatement(stmt);
            }
            
            this.dedent();
        }
        
        // 处理ELSE子句
        if (node.elseStatements.length > 0) {
            this.emit('} else {');
            this.indent();
            
            for (const stmt of node.elseStatements) {
                this.generateStatement(stmt);
            }
            
            this.dedent();
        }
        
        this.emit('}');
    }

    // 生成FOR循环
    generateForLoop(node) {
        const start = this.generateExpression(node.start);
        const end = this.generateExpression(node.end);
        const step = node.step ? this.generateExpression(node.step) : '1';
        
        this.emit(`for (let ${node.variable} = ${start}; ${node.variable} <= ${end}; ${node.variable} += ${step}) {`);
        this.indent();
        
        for (const stmt of node.statements) {
            this.generateStatement(stmt);
        }
        
        this.dedent();
        this.emit('}');
    }

    // 生成WHILE循环
    generateWhileLoop(node) {
        const condition = this.generateExpression(node.condition);
        this.emit(`while (${condition}) {`);
        this.indent();
        
        for (const stmt of node.statements) {
            this.generateStatement(stmt);
        }
        
        this.dedent();
        this.emit('}');
    }

    // 生成REPEAT循环
    generateRepeatLoop(node) {
        this.emit('do {');
        this.indent();
        
        for (const stmt of node.statements) {
            this.generateStatement(stmt);
        }
        
        this.dedent();
        const condition = this.generateExpression(node.condition);
        this.emit(`} while (!(${condition}));`);
    }

    // 生成INPUT语句
    generateInput(node) {
        const prompt = node.prompt ? `"${node.prompt}"` : '""';
        
        if (node.variable.type === 'Identifier') {
            this.emit(`${node.variable.value} = INPUT(${prompt});`);
        } else if (node.variable.type === 'ArrayAccess') {
            const arrayName = node.variable.array.value;
            const indices = node.variable.indices.map(idx => this.generateExpression(idx)).join(', ');
            this.emit(`arraySet(${arrayName}, [${indices}], INPUT(${prompt}));`);
        }
    }

    // 生成OUTPUT语句
    generateOutput(node) {
        const expressions = node.expressions.map(expr => this.generateExpression(expr)).join(', ');
        this.emit(`OUTPUT(${expressions});`);
    }

    // 生成过程定义
    generateProcedure(node) {
        const params = node.parameters.map(p => p.name).join(', ');
        this.emit(`function ${node.name}(${params}) {`);
        this.indent();
        
        for (const stmt of node.statements) {
            this.generateStatement(stmt);
        }
        
        this.dedent();
        this.emit('}');
        this.emit('');
        
        this.procedures.set(node.name, node);
    }

    // 生成函数定义
    generateFunction(node) {
        const params = node.parameters.map(p => p.name).join(', ');
        this.emit(`function ${node.name}(${params}) {`);
        this.indent();
        
        for (const stmt of node.statements) {
            this.generateStatement(stmt);
        }
        
        this.dedent();
        this.emit('}');
        this.emit('');
        
        this.functions.set(node.name, node);
    }

    // 生成函数/过程调用
    generateCall(node) {
        const args = node.arguments.map(arg => this.generateExpression(arg)).join(', ');
        this.emit(`${node.name}(${args});`);
    }

    // 生成RETURN语句
    generateReturn(node) {
        if (node.value) {
            const value = this.generateExpression(node.value);
            this.emit(`return ${value};`);
        } else {
            this.emit('return;');
        }
    }

    // 生成表达式
    generateExpression(node) {
        switch (node.type) {
            case 'Literal':
                return this.generateLiteral(node);
            case 'Identifier':
                return node.value;
            case 'ArrayAccess':
                return this.generateArrayAccess(node);
            case 'FieldAccess':
                return `${this.generateExpression(node.object)}.${node.field}`;
            case 'FunctionCall':
                const args = node.arguments.map(arg => this.generateExpression(arg)).join(', ');
                return `${node.name}(${args})`;
            case 'BinaryExpression':
                return this.generateBinaryExpression(node);
            case 'UnaryExpression':
                return this.generateUnaryExpression(node);
            default:
                this.addError(`未知的表达式类型: ${node.type}`);
                return 'null';
        }
    }

    // 生成字面量
    generateLiteral(node) {
        switch (node.dataType) {
            case 'INTEGER_LITERAL':
            case 'REAL_LITERAL':
                return node.value;
            case 'STRING_LITERAL':
                return `"${node.value.replace(/"/g, '\\"')}"`;
            case 'CHAR_LITERAL':
                return `'${node.value.replace(/'/g, "\\'")}'`;
            case 'BOOLEAN_LITERAL':
                return node.value.toLowerCase();
            default:
                return node.value;
        }
    }

    // 生成数组访问
    generateArrayAccess(node) {
        const arrayName = node.array.value;
        const indices = node.indices.map(idx => this.generateExpression(idx)).join(', ');
        // 获取数组的基础索引信息
        const arrayInfo = this.variables.get(arrayName);
        if (arrayInfo && arrayInfo.type === 'array') {
            const baseLower = arrayInfo.dimensions.map(dim => this.generateExpression(dim.lower)).join(', ');
            return `arrayAccess(${arrayName}, [${indices}], [${baseLower}])`;
        } else {
            return `arrayAccess(${arrayName}, [${indices}])`;
        }
    }

    // 生成二元表达式
    generateBinaryExpression(node) {
        const left = this.generateExpression(node.left);
        const right = this.generateExpression(node.right);
        
        switch (node.operator) {
            case 'AND':
                return `(${left} && ${right})`;
            case 'OR':
                return `(${left} || ${right})`;
            case 'EQUALS':
                return `(${left} === ${right})`;
            case 'NOT_EQUALS':
                return `(${left} !== ${right})`;
            case 'LESS_THAN':
                return `(${left} < ${right})`;
            case 'GREATER_THAN':
                return `(${left} > ${right})`;
            case 'LESS_EQUAL':
                return `(${left} <= ${right})`;
            case 'GREATER_EQUAL':
                return `(${left} >= ${right})`;
            case 'PLUS':
                return `(${left} + ${right})`;
            case 'MINUS':
                return `(${left} - ${right})`;
            case 'MULTIPLY':
                return `(${left} * ${right})`;
            case 'DIVIDE':
                return `(${left} / ${right})`;
            case 'DIV':
                return `DIV(${left}, ${right})`;
            case 'MOD':
                return `MOD(${left}, ${right})`;
            default:
                return `(${left} ${node.operator} ${right})`;
        }
    }

    // 生成一元表达式
    generateUnaryExpression(node) {
        const operand = this.generateExpression(node.operand);
        
        switch (node.operator) {
            case 'NOT':
                return `!(${operand})`;
            case 'MINUS':
                return `-(${operand})`;
            default:
                return `${node.operator}(${operand})`;
        }
    }

    // 获取数据类型的默认值
    getDefaultValue(dataType) {
        switch (dataType) {
            case 'INTEGER':
            case 'REAL':
                return '0';
            case 'STRING':
                return '""';
            case 'CHAR':
                return "''";
            case 'BOOLEAN':
                return 'false';
            case 'DATE':
                return 'new Date()';
            default:
                return 'null';
        }
    }

    // 输出代码行
    emit(line) {
        const indentation = '    '.repeat(this.indentLevel);
        this.output.push(indentation + line);
    }

    // 增加缩进
    indent() {
        this.indentLevel++;
    }

    // 减少缩进
    dedent() {
        if (this.indentLevel > 0) {
            this.indentLevel--;
        }
    }

    // 添加错误
    addError(message) {
        this.errors.push({
            type: 'GENERATION_ERROR',
            message
        });
    }
}

// 导出类
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PseudocodeGenerator;
} else {
    window.PseudocodeGenerator = PseudocodeGenerator;
}