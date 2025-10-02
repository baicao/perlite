#!/usr/bin/env node

/**
 * 简化版伪代码编译器自动测试脚本
 * 用于批量运行tests目录中的所有测试案例，并验证编译结果
 */

const fs = require('fs');
const path = require('path');

class SimpleTestRunner {
    constructor() {
        this.testsDir = path.join(__dirname, 'tests');
        this.results = {
            total: 0,
            passed: 0,
            failed: 0,
            errors: 0
        };
    }

    /**
     * 运行所有测试
     */
    async runAllTests() {
        console.log('🚀 开始运行伪代码编译器测试套件...\n');

        // 获取所有测试文件
        const testFiles = fs.readdirSync(this.testsDir)
            .filter(file => file.endsWith('.txt'))
            .sort();

        console.log(`📁 发现 ${testFiles.length} 个测试文件\n`);

        // 运行每个测试
        for (const filename of testFiles) {
            await this.runSingleTest(filename);
        }

        this.printSummary();
    }

    /**
     * 运行单个测试
     */
    async runSingleTest(filename) {
        const filePath = path.join(this.testsDir, filename);
        this.results.total++;

        try {
            console.log(`🧪 测试: ${filename}`);
            
            // 读取测试文件
            const pseudocode = fs.readFileSync(filePath, 'utf8');
            
            // 尝试编译
            const compileResult = this.compileTest(pseudocode);
            
            if (compileResult.success) {
                console.log('  ✅ 编译成功');
                this.results.passed++;
            } else {
                console.log('  ❌ 编译失败:', compileResult.error);
                this.results.failed++;
            }

        } catch (error) {
            console.log('  💥 测试错误:', error.message);
            this.results.errors++;
        }

        console.log(''); // 空行分隔
    }

    /**
     * 编译测试代码
     */
    compileTest(pseudocode) {
        try {
            // 检查编译器是否存在
            const compilerPath = path.join(__dirname, 'src', 'utils', 'compiler.js');
            
            if (!fs.existsSync(compilerPath)) {
                return {
                    success: false,
                    error: '编译器文件不存在: ' + compilerPath
                };
            }

            // 尝试加载编译器
            try {
                // 清除缓存
                delete require.cache[require.resolve(compilerPath)];
                
                // 加载编译器模块
                const compilerModule = require(compilerPath);
                
                if (!compilerModule.PseudocodeCompiler) {
                    return {
                        success: false,
                        error: '编译器模块未正确导出 PseudocodeCompiler 类'
                    };
                }

                // 创建编译器实例并编译
                const compilerInstance = new compilerModule.PseudocodeCompiler();
                const result = compilerInstance.compile(pseudocode);

                return {
                    success: result.success,
                    code: result.generatedCode || '',
                    error: result.success ? null : this.formatErrors(result.errors)
                };

            } catch (requireError) {
                return {
                    success: false,
                    error: `加载编译器失败: ${requireError.message}`
                };
            }

        } catch (error) {
            return {
                success: false,
                error: `编译过程错误: ${error.message}`
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
        }).join('; ');
    }

    /**
     * 打印测试摘要
     */
    printSummary() {
        console.log('📊 测试结果摘要');
        console.log('==================================================');
        console.log(`总测试数: ${this.results.total}`);
        console.log(`✅ 通过: ${this.results.passed}`);
        console.log(`❌ 失败: ${this.results.failed}`);
        console.log(`💥 错误: ${this.results.errors}`);
        
        const successRate = this.results.total > 0 ? 
            ((this.results.passed / this.results.total) * 100).toFixed(1) : 0;
        console.log(`📈 成功率: ${successRate}%`);
        
        if (this.results.failed === 0 && this.results.errors === 0) {
            console.log('\n🎉 所有测试都通过了！');
        } else {
            console.log('\n⚠️  存在失败的测试，请检查上述输出');
        }
    }
}

// 主函数
async function main() {
    const runner = new SimpleTestRunner();
    await runner.runAllTests();
}

// 如果直接运行此脚本
if (require.main === module) {
    main().catch(console.error);
}

module.exports = SimpleTestRunner;