#!/usr/bin/env node

/**
 * 伪代码编译器自动测试脚本
 * 用于批量运行tests目录中的所有测试案例，并验证编译结果
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// 测试分类配置
const TEST_CATEGORIES = {
    basic: {
        name: '基础语法测试',
        files: ['test_basic.txt', 'test_ultra_simple.txt', 'test_simple.txt', 'test_minimal_safe.txt']
    },
    variables: {
        name: '变量和数据类型测试',
        files: ['test_char_error.txt', 'test_simple_char.txt']
    },
    arrays: {
        name: '数组测试',
        files: ['test_array_basic.txt', 'test_split_2_array.txt', 'test_split_7_complex_array.txt']
    },
    functions: {
        name: '函数测试',
        files: ['test_function_basic.txt', 'test_split_3_function.txt']
    },
    procedures: {
        name: '过程测试',
        files: ['test_procedure_basic.txt', 'test_split_4_procedure.txt', 'test_byref.txt', 'test_byref_simple.txt']
    },
    records: {
        name: '记录类型测试',
        files: ['test_record_basic.txt', 'test_correct_record.txt', 'test_split_1_record.txt']
    },
    loops: {
        name: '循环测试',
        files: ['test_for_loop.txt']
    },
    advanced: {
        name: '高级功能测试',
        files: ['test_advanced.txt', 'test_advanced_features.txt', 'test_advanced_optimized.txt']
    },
    library: {
        name: '库函数测试',
        files: ['test_library_functions.txt', 'test_simple_library.txt']
    },
    comprehensive: {
        name: '综合测试',
        files: ['test_no_input.txt', 'test_split_5_recursive.txt', 'test_split_6_prime.txt']
    },
    performance: {
        name: '性能和优化测试',
        files: ['test_performance.txt', 'test_optimized.txt']
    },
    edge_cases: {
        name: '边界情况和错误处理',
        files: ['test_debug_crash.txt']
    }
};

// 加载预期结果配置
let EXPECTED_RESULTS = {};
try {
    const expectationsPath = path.join(__dirname, 'test_expectations.json');
    if (fs.existsSync(expectationsPath)) {
        EXPECTED_RESULTS = JSON.parse(fs.readFileSync(expectationsPath, 'utf8'));
    }
} catch (error) {
    console.warn('⚠️  无法加载测试预期结果配置:', error.message);
}

class TestRunner {
    constructor() {
        this.testsDir = path.join(__dirname, 'tests');
        this.expectations = EXPECTED_RESULTS; // 添加expectations属性
        this.results = {
            total: 0,
            passed: 0,
            failed: 0,
            errors: 0,
            details: []
        };
    }

    /**
     * 运行所有测试
     */
    async runAllTests() {
        console.log('🚀 开始运行伪代码编译器测试套件...\n');
        
        const testFiles = fs.readdirSync(this.testsDir)
            .filter(file => file.endsWith('.txt'))
            .sort();

        console.log(`📁 发现 ${testFiles.length} 个测试文件\n`);

        // 按分类运行测试
        for (const [categoryId, category] of Object.entries(TEST_CATEGORIES)) {
            console.log(`\n📋 ${category.name}`);
            console.log('='.repeat(50));
            
            const categoryFiles = category.files.filter(file => testFiles.includes(file));
            
            for (const file of categoryFiles) {
                await this.runSingleTest(file, categoryId);
            }
        }

        // 运行未分类的测试
        const uncategorizedFiles = testFiles.filter(file => 
            !Object.values(TEST_CATEGORIES).some(cat => cat.files.includes(file))
        );

        if (uncategorizedFiles.length > 0) {
            console.log(`\n📋 未分类测试`);
            console.log('='.repeat(50));
            
            for (const file of uncategorizedFiles) {
                await this.runSingleTest(file, 'uncategorized');
            }
        }

        this.printSummary();
    }

    /**
     * 运行单个测试
     */
    async runSingleTest(filename, category) {
        const filePath = path.join(this.testsDir, filename);
        
        if (!fs.existsSync(filePath)) {
            return {
                filename,
                status: 'error',
                message: '测试文件不存在',
                compilationTime: 0,
                executionTime: 0
            };
        }

        const pseudocode = fs.readFileSync(filePath, 'utf8');
        const startTime = Date.now();

        // 编译测试
        const compileResult = this.compileTest(pseudocode);
        const compilationTime = Date.now() - startTime;

        if (!compileResult.success) {
            return {
                filename,
                status: 'compilation_failed',
                message: compileResult.error,
                compilationTime,
                executionTime: 0,
                expected: this.expectations[filename] || null
            };
        }

        // 执行测试
        const execStartTime = Date.now();
        const execResult = this.executeTest(compileResult.code);
        const executionTime = Date.now() - execStartTime;

        // 验证输出
        const validation = this.validateOutput(filename, execResult.output);

        return {
            filename,
            status: validation.match ? 'passed' : 'failed',
            message: validation.message,
            compilationTime,
            executionTime,
            output: execResult.output,
            expected: this.expectations[filename] || null,
            warnings: compileResult.warnings || []
        };
    }

    /**
     * 编译测试代码
     */
    compileTest(pseudocode) {
        try {
            // 检查编译器是否存在
            const compilerPath = path.join(__dirname, 'src', 'utils', 'compiler.js');
            
            if (!fs.existsSync(compilerPath)) {
                throw new Error('编译器文件不存在: ' + compilerPath);
            }

            try {
                // 使用Node.js require方式调用编译器
                delete require.cache[require.resolve(compilerPath)];
                const compilerModule = require(compilerPath);
                
                if (!compilerModule.PseudocodeCompiler) {
                    throw new Error('编译器模块未正确导出 PseudocodeCompiler 类');
                }

                const compilerInstance = new compilerModule.PseudocodeCompiler();
                const result = compilerInstance.compile(pseudocode);

                return {
                    success: result.success,
                    code: result.generatedCode || '',
                    error: result.success ? null : this.formatErrors(result.errors),
                    warnings: result.warnings || []
                };

            } catch (execError) {
                return {
                    success: false,
                    error: execError.message
                };
            }

        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * 格式化错误信息
     */
    formatErrors(errors) {
        if (!errors || errors.length === 0) {
            return '未知编译错误';
        }
        
        return errors.map(error => {
            const location = error.line > 0 ? `[${error.line}:${error.column}] ` : '';
            return `${error.type}: ${location}${error.message}`;
        }).join('\n');
    }

    /**
     * 执行编译后的代码
     */
    executeTest(compiledCode) {
        try {
            // 这里需要执行编译后的JavaScript代码
            // 暂时返回模拟结果
            return {
                success: true,
                output: 'Mock output for testing'
            };
        } catch (error) {
            return {
                success: false,
                error: error.message
            };
        }
    }

    /**
     * 验证输出结果
     */
    validateOutput(filename, actualOutput) {
        const expected = this.expectations[filename];
        
        if (!expected) {
            return {
                match: true,
                message: '未定义预期结果，跳过验证'
            };
        }

        if (!expected.shouldCompile) {
            return {
                match: false,
                message: '此测试预期编译失败，但编译成功了'
            };
        }

        // 如果没有定义预期输出，只验证编译成功
        if (!expected.expectedOutputs || expected.expectedOutputs.length === 0) {
            return {
                match: true,
                message: '编译成功，未定义具体输出验证'
            };
        }

        // 简单的输出匹配验证
        const actualLines = actualOutput.split('\n')
            .map(line => line.trim())
            .filter(line => line.length > 0);
        const expectedLines = expected.expectedOutputs;

        // 检查是否包含所有预期输出
        const missingOutputs = [];
        for (const expectedLine of expectedLines) {
            const found = actualLines.some(actualLine => 
                actualLine.includes(expectedLine) || 
                this.normalizeOutput(actualLine) === this.normalizeOutput(expectedLine)
            );
            
            if (!found) {
                missingOutputs.push(expectedLine);
            }
        }

        if (missingOutputs.length > 0) {
            return {
                match: false,
                message: `缺少预期输出: ${missingOutputs.join(', ')}`
            };
        }

        return {
            match: true,
            message: '输出匹配预期结果'
        };
    }

    /**
     * 标准化输出用于比较
     */
    normalizeOutput(output) {
        return output.trim()
            .replace(/\s+/g, ' ')
            .toLowerCase();
    }

    /**
     * 打印测试摘要
     */
    printSummary() {
        console.log('\n' + '='.repeat(60));
        console.log('📊 测试结果摘要');
        console.log('='.repeat(60));
        
        console.log(`总测试数: ${this.results.total}`);
        console.log(`✅ 通过: ${this.results.passed}`);
        console.log(`❌ 失败: ${this.results.failed}`);
        console.log(`💥 错误: ${this.results.errors}`);
        
        const successRate = this.results.total > 0 ? 
            ((this.results.passed / this.results.total) * 100).toFixed(1) : 0;
        console.log(`📈 成功率: ${successRate}%`);

        // 按分类显示结果
        console.log('\n📋 分类结果:');
        for (const [categoryId, category] of Object.entries(TEST_CATEGORIES)) {
            const categoryResults = this.results.details.filter(r => r.category === categoryId);
            if (categoryResults.length > 0) {
                const passed = categoryResults.filter(r => r.status === 'passed').length;
                console.log(`  ${category.name}: ${passed}/${categoryResults.length}`);
            }
        }

        // 显示失败的测试
        const failedTests = this.results.details.filter(r => r.status !== 'passed');
        if (failedTests.length > 0) {
            console.log('\n❌ 失败的测试:');
            failedTests.forEach(test => {
                console.log(`  ${test.filename}: ${test.error}`);
            });
        }

        console.log('\n' + '='.repeat(60));
        
        // 生成测试报告文件
        this.generateReport();
    }

    /**
     * 生成详细的测试报告
     */
    generateReport() {
        const reportPath = path.join(__dirname, 'test_report.json');
        const report = {
            timestamp: new Date().toISOString(),
            summary: {
                total: this.results.total,
                passed: this.results.passed,
                failed: this.results.failed,
                errors: this.results.errors,
                successRate: this.results.total > 0 ? 
                    ((this.results.passed / this.results.total) * 100).toFixed(1) : 0
            },
            categories: {},
            details: this.results.details
        };

        // 按分类统计
        for (const [categoryId, category] of Object.entries(TEST_CATEGORIES)) {
            const categoryResults = this.results.details.filter(r => r.category === categoryId);
            if (categoryResults.length > 0) {
                const passed = categoryResults.filter(r => r.status === 'passed').length;
                report.categories[categoryId] = {
                    name: category.name,
                    total: categoryResults.length,
                    passed: passed,
                    failed: categoryResults.length - passed
                };
            }
        }

        fs.writeFileSync(reportPath, JSON.stringify(report, null, 2));
        console.log(`📄 详细报告已保存到: ${reportPath}`);
    }
}

// 主函数
async function main() {
    const runner = new TestRunner();
    await runner.runAllTests();
}

// 如果直接运行此脚本
if (require.main === module) {
    main().catch(console.error);
}

module.exports = TestRunner;