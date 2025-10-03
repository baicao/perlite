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
        this.currentProcedure = null;
        this.byrefParams = new Set();
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
        
        // 首先生成所有类型定义（在main函数外部）
        this.emit('// Type definitions');
        for (const statement of node.statements) {
            if (statement.type === 'TypeDefinition') {
                this.generateStatement(statement);
            }
        }
        this.emit('');
        
        this.emit('// Main program');
        this.emit('function main() {');
        this.indent();
        
        // 生成其他语句（跳过类型定义）
        for (const statement of node.statements) {
            if (statement.type !== 'TypeDefinition') {
                this.generateStatement(statement);
            }
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
        this.emit('    // 在不支持prompt的环境中，返回模拟输入');
        this.emit('    if (typeof window === "undefined" || !window.prompt) {');
        this.emit('        console.log("INPUT: " + (prompt || "Enter value:"));');
        this.emit('        return "模拟输入值"; // 返回默认值');
        this.emit('    }');
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
        

        
        this.emit('// Performance protection');
        this.emit('let _loopCounter = 0;');
        this.emit('const MAX_LOOP_ITERATIONS = 50000; // 适当提高循环限制');
        this.emit('const _startTime = Date.now();');
        this.emit('const MAX_EXECUTION_TIME = 10000; // 10 seconds - 更宽松的时间限制');
        this.emit('');
        this.emit('function checkPerformance() {');
        this.emit('    _loopCounter++;');
        this.emit('    if (_loopCounter > MAX_LOOP_ITERATIONS) {');
        this.emit('        throw new Error("程序执行超过最大循环次数限制 (" + MAX_LOOP_ITERATIONS + ")");');
        this.emit('    }');
        this.emit('    if (Date.now() - _startTime > MAX_EXECUTION_TIME) {');
        this.emit('        throw new Error("程序执行超时 (" + MAX_EXECUTION_TIME + "ms)");');
        this.emit('    }');
        this.emit('}');
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
            case 'TypeDefinition':
                this.generateTypeDefinition(node);
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
        } else if (node.dataType.type === 'UserDefinedType') {
            // 用户定义类型（RECORD）声明
            const typeName = node.dataType.value;
            this.emit(`let ${varName} = new ${typeName}();`);
            this.variables.set(varName, {
                type: 'userType',
                dataType: typeName
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
            } else if (node.dataType.type === 'UserDefinedType') {
                // 用户定义类型（RECORD）声明
                const typeName = node.dataType.value;
                this.emit(`let ${varName} = new ${typeName}();`);
                this.variables.set(varName, {
                    type: 'userType',
                    dataType: typeName
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
            // 如果在过程内部且是BYREF参数，使用局部变量
            if (this.currentProcedure && this.byrefParams.has(node.left.value)) {
                this.emit(`_local_${node.left.value} = ${right};`);
            } else {
                this.emit(`${node.left.value} = ${right};`);
            }
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
        this.emit('checkPerformance();');
        
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
        this.emit('checkPerformance();');
        
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
        this.emit('checkPerformance();');
        
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
        
        // 设置当前过程上下文
        const previousProcedure = this.currentProcedure;
        const previousByrefParams = new Set(this.byrefParams);
        this.currentProcedure = node.name;
        this.byrefParams.clear();
        
        // 处理BYREF参数，创建局部变量存储解包后的值
        for (const param of node.parameters) {
            if (param.passBy === 'BYREF') {
                this.emit(`let _local_${param.name} = ${param.name}.value;`);
                this.byrefParams.add(param.name);
            }
        }
        
        for (const stmt of node.statements) {
            this.generateStatement(stmt);
        }
        
        // 处理BYREF参数，将局部变量的值写回引用对象
        for (const param of node.parameters) {
            if (param.passBy === 'BYREF') {
                this.emit(`${param.name}.value = _local_${param.name};`);
            }
        }
        
        // 恢复之前的上下文
        this.currentProcedure = previousProcedure;
        this.byrefParams = previousByrefParams;
        
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
        // 添加性能检查
        this.emit('checkPerformance();');
        
        // 检查是否是已知的过程或函数
        const procedure = this.procedures.get(node.name);
        const func = this.functions.get(node.name);
        const definition = procedure || func;
        
        if (definition && definition.parameters.some(p => p.passBy === 'BYREF')) {
            // 有BYREF参数，需要特殊处理
            const byrefVars = [];
            
            // 创建临时变量包装BYREF参数
            for (let i = 0; i < node.arguments.length; i++) {
                const arg = node.arguments[i];
                const param = definition.parameters[i];
                
                if (param && param.passBy === 'BYREF' && arg.type === 'Identifier') {
                    const tempVar = `_ref_${arg.value}_${i}`;
                    this.emit(`let ${tempVar} = {value: ${arg.value}};`);
                    byrefVars.push({tempVar, originalVar: arg.value, index: i});
                }
            }
            
            // 生成函数调用
            const args = [];
            for (let i = 0; i < node.arguments.length; i++) {
                const arg = node.arguments[i];
                const param = definition.parameters[i];
                
                if (param && param.passBy === 'BYREF' && arg.type === 'Identifier') {
                    const tempVar = `_ref_${arg.value}_${i}`;
                    args.push(tempVar);
                } else {
                    args.push(this.generateExpression(arg));
                }
            }
            
            this.emit(`${node.name}(${args.join(', ')});`);
            
            // 更新BYREF变量的值
            for (const {tempVar, originalVar} of byrefVars) {
                this.emit(`${originalVar} = ${tempVar}.value;`);
            }
        } else {
            // 普通函数调用
            const args = node.arguments.map(arg => this.generateExpression(arg)).join(', ');
            this.emit(`${node.name}(${args});`);
        }
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

    // 生成类型定义（RECORD）
    generateTypeDefinition(node) {
        const typeName = node.identifier;
        
        // 生成构造函数
        this.emit(`function ${typeName}() {`);
        this.indent();
        
        // 初始化所有字段
        for (const field of node.fields) {
            const defaultValue = this.getDefaultValue(field.type.value || field.type.elementType?.value || 'INTEGER');
            this.emit(`this.${field.name} = ${defaultValue};`);
        }
        
        this.dedent();
        this.emit('}');
        this.emit('');
    }

    // 生成表达式
    generateExpression(node) {
        switch (node.type) {
            case 'Literal':
                return this.generateLiteral(node);
            case 'Identifier':
                // 如果在过程内部且是BYREF参数，使用局部变量
                if (this.currentProcedure && this.byrefParams.has(node.value)) {
                    return `_local_${node.value}`;
                }
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

// 兼容浏览器和Node.js环境
// ES6 导出
export default PseudocodeGenerator;

// 兼容浏览器环境
if (typeof window !== 'undefined') {
    window.PseudocodeGenerator = PseudocodeGenerator;
}