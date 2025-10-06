#!/usr/bin/env node

/**
 * 手动测试编译器功能
 * 系统性测试所有语法功能
 */

const fs = require('fs');
const path = require('path');

// 模拟浏览器环境
global.window = {};

// 加载编译器模块
const PseudocodeLexer = require('./lexer.js');
const PseudocodeParser = require('./parser.js');
const PseudocodeGenerator = require('./generator.js');

// 将类添加到全局对象，供compiler.js使用
global.PseudocodeLexer = PseudocodeLexer;
global.PseudocodeParser = PseudocodeParser;
global.PseudocodeGenerator = PseudocodeGenerator;

// 将类添加到全局window对象
window.PseudocodeLexer = PseudocodeLexer;
window.PseudocodeParser = PseudocodeParser;
window.PseudocodeGenerator = PseudocodeGenerator;

// 加载编译器
const { PseudocodeCompiler } = require('./compiler.js');

const compiler = new PseudocodeCompiler();

// 测试用例
const testCases = [
    {
        name: "基础语法 - 变量声明和赋值",
        code: `DECLARE x : INTEGER
x ← 10
OUTPUT x`
    },
    {
        name: "控制结构 - FOR循环",
        code: `FOR i ← 1 TO 5
    OUTPUT i
NEXT i`
    },
    {
        name: "控制结构 - IF-THEN-ELSE",
        code: `DECLARE x : INTEGER
x ← 15
IF x > 10 THEN
    OUTPUT "x is greater than 10"
ELSE
    OUTPUT "x is not greater than 10"
ENDIF`
    },
    {
        name: "控制结构 - WHILE循环",
        code: `DECLARE i : INTEGER
i ← 1
WHILE i <= 3 DO
    OUTPUT i
    i ← i + 1
ENDWHILE`
    },
    {
        name: "函数定义和调用",
        code: `FUNCTION Add(a : INTEGER, b : INTEGER) RETURNS INTEGER
    RETURN a + b
ENDFUNCTION

DECLARE result : INTEGER
result ← Add(5, 3)
OUTPUT result`
    },
    {
        name: "过程定义和调用",
        code: `PROCEDURE PrintMessage(message : STRING)
    OUTPUT "Message: ", message
ENDPROCEDURE

CALL PrintMessage("Hello World")`
    },
    {
        name: "数组操作",
        code: `DECLARE Numbers : ARRAY[1:3] OF INTEGER
Numbers[1] ← 10
Numbers[2] ← 20
Numbers[3] ← 30

FOR i ← 1 TO 3
    OUTPUT Numbers[i]
NEXT i`
    },
    {
        name: "记录类型",
        code: `TYPE Person
    DECLARE Name : STRING
    DECLARE Age : INTEGER
ENDTYPE

DECLARE Student : Person
Student.Name ← "John"
Student.Age ← 20
OUTPUT Student.Name, Student.Age`
    }
];

console.log('='.repeat(60));
console.log('Pseudocode 编译器功能测试');
console.log('='.repeat(60));

let passCount = 0;
let totalCount = testCases.length;

testCases.forEach((testCase, index) => {
    console.log(`\n${index + 1}. ${testCase.name}`);
    console.log('-'.repeat(40));
    console.log('测试代码:');
    console.log(testCase.code);
    console.log('\n编译结果:');
    
    try {
        const result = compiler.compile(testCase.code);
        
        if (result.errors && result.errors.length > 0) {
            console.log('❌ 编译失败');
            console.log('错误信息:');
            result.errors.forEach(error => console.log('  - ' + error));
        } else {
            console.log('✅ 编译成功');
            passCount++;
            
            if (result.jsCode) {
                console.log('生成的JavaScript代码:');
                console.log(result.jsCode.substring(0, 200) + (result.jsCode.length > 200 ? '...' : ''));
            }
            
            if (result.warnings && result.warnings.length > 0) {
                console.log('警告信息:');
                result.warnings.forEach(warning => console.log('  - ' + warning));
            }
        }
    } catch (error) {
        console.log('❌ 编译异常');
        console.log('异常信息:', error.message);
    }
});

console.log('\n' + '='.repeat(60));
console.log(`测试总结: ${passCount}/${totalCount} 通过`);
console.log('='.repeat(60));

// 如果有测试文件，也运行它们
const testsDir = path.join(__dirname, 'tests');
if (fs.existsSync(testsDir)) {
    console.log('\n运行测试文件:');
    const testFiles = fs.readdirSync(testsDir).filter(file => file.endsWith('.txt'));
    
    testFiles.forEach(file => {
        const filePath = path.join(testsDir, file);
        const content = fs.readFileSync(filePath, 'utf8');
        
        console.log(`\n测试文件: ${file}`);
        console.log('-'.repeat(30));
        
        try {
            const result = compiler.compile(content);
            if (result.errors && result.errors.length > 0) {
                console.log('❌ 编译失败');
                result.errors.forEach(error => console.log('  - ' + error));
            } else {
                console.log('✅ 编译成功');
            }
        } catch (error) {
            console.log('❌ 编译异常:', error.message);
        }
    });
}