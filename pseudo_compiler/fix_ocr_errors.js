// OCR错误修复脚本
// 修复Cambridge 9618伪代码指南中的常见OCR识别错误

const fs = require('fs');
const path = require('path');

// 定义OCR错误映射
const ocrErrorMappings = {
    // 赋值操作符错误
    '< ': '← ',
    
    // 其他常见OCR错误
    'xX!': 'X',
    'eg ': 'e.g. ',
    'eg.': 'e.g.',
    'ie ': 'i.e. ',
    'ie.': 'i.e.',
    
    // 特殊字符错误
    '—': '-',
    '–': '-',
    '…': '...',
};

// 特殊规则：上下文相关的修复
const contextualFixes = [
    {
        // FOR循环中的赋值
        pattern: /FOR\s+(\w+)\s*<\s*(\d+)\s*TO/g,
        replacement: 'FOR $1 ← $2 TO'
    },
    {
        // 一般赋值语句
        pattern: /(\w+)\s*<\s*([^\n]+)/g,
        replacement: '$1 ← $2'
    },
    {
        // 数组赋值
        pattern: /(\w+\[\w+\])\s*<\s*([^\n]+)/g,
        replacement: '$1 ← $2'
    }
];

function fixOCRErrors(text) {
    let fixedText = text;
    
    // 应用简单的字符替换
    for (const [error, correction] of Object.entries(ocrErrorMappings)) {
        fixedText = fixedText.replace(new RegExp(error.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'), 'g'), correction);
    }
    
    // 应用上下文相关的修复
    for (const fix of contextualFixes) {
        fixedText = fixedText.replace(fix.pattern, fix.replacement);
    }
    
    return fixedText;
}

function processFile(filePath) {
    try {
        const content = fs.readFileSync(filePath, 'utf8');
        const fixedContent = fixOCRErrors(content);
        
        // 创建备份
        const backupPath = filePath + '.backup';
        if (!fs.existsSync(backupPath)) {
            fs.writeFileSync(backupPath, content);
        }
        
        // 写入修复后的内容
        fs.writeFileSync(filePath, fixedContent);
        
        console.log(`已修复文件: ${path.basename(filePath)}`);
        
        // 统计修复的错误数量
        const errorCount = (content.match(/<(?!\s*[A-Z])/g) || []).length;
        if (errorCount > 0) {
            console.log(`  - 修复了 ${errorCount} 个赋值操作符错误`);
        }
        
    } catch (error) {
        console.error(`处理文件 ${filePath} 时出错:`, error.message);
    }
}

function processDirectory(dirPath) {
    try {
        const files = fs.readdirSync(dirPath);
        
        for (const file of files) {
            if (file.endsWith('.txt') && !file.endsWith('.backup')) {
                const filePath = path.join(dirPath, file);
                processFile(filePath);
            }
        }
        
        console.log('\n所有文件处理完成！');
        
    } catch (error) {
        console.error('处理目录时出错:', error.message);
    }
}

// 主函数
function main() {
    const txtDir = path.join(__dirname, 'txt');
    
    console.log('开始修复OCR错误...');
    console.log(`处理目录: ${txtDir}`);
    console.log('='.repeat(50));
    
    processDirectory(txtDir);
}

// 如果直接运行此脚本
if (require.main === module) {
    main();
}

module.exports = { fixOCRErrors, processFile, processDirectory };