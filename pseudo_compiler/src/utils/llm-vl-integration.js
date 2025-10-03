// LLM-VL模型集成模块
// 用于改进伪代码语法识别准确性

class LLMVLIntegration {
    constructor() {
        this.apiEndpoint = process.env.LLM_VL_API_ENDPOINT || 'https://api.openai.com/v1/chat/completions';
        this.apiKey = process.env.LLM_VL_API_KEY || '';
        this.model = 'gpt-4-vision-preview';
    }

    /**
     * 使用LLM-VL模型分析伪代码图像或文本
     * @param {string} input - 输入内容（文本或图像URL）
     * @param {string} type - 输入类型：'text' 或 'image'
     * @returns {Promise<Object>} 分析结果
     */
    async analyzePseudocode(input, type = 'text') {
        try {
            const prompt = this.buildPrompt(type);
            const messages = this.buildMessages(prompt, input, type);
            
            const response = await this.callLLMVL(messages);
            return this.parseResponse(response);
            
        } catch (error) {
            console.error('LLM-VL分析失败:', error);
            return {
                success: false,
                error: error.message,
                suggestions: []
            };
        }
    }

    /**
     * 构建分析提示词
     * @param {string} type - 输入类型
     * @returns {string} 提示词
     */
    buildPrompt(type) {
        const basePrompt = `你是一个专业的Cambridge 9618伪代码语法分析专家。请分析${type === 'image' ? '图像中的' : ''}伪代码，识别以下问题：

1. 语法错误和不规范用法
2. 赋值操作符错误（应使用 ← 而不是 < 或 =）
3. 关键字大小写错误（应为大写：IF, FOR, WHILE, REPEAT等）
4. 标识符命名规范问题
5. 缩进和格式问题
6. 数据类型声明错误

请提供：
- 错误位置（行号）
- 错误类型
- 修正建议
- 修正后的代码

输出格式为JSON：
{
  "errors": [
    {
      "line": 行号,
      "type": "错误类型",
      "message": "错误描述",
      "suggestion": "修正建议",
      "corrected": "修正后的代码"
    }
  ],
  "corrected_code": "完整的修正后代码",
  "confidence": 0.95
}`;

        return basePrompt;
    }

    /**
     * 构建API消息
     * @param {string} prompt - 提示词
     * @param {string} input - 输入内容
     * @param {string} type - 输入类型
     * @returns {Array} 消息数组
     */
    buildMessages(prompt, input, type) {
        const messages = [
            {
                role: 'system',
                content: prompt
            }
        ];

        if (type === 'text') {
            messages.push({
                role: 'user',
                content: `请分析以下伪代码：\n\n${input}`
            });
        } else if (type === 'image') {
            messages.push({
                role: 'user',
                content: [
                    {
                        type: 'text',
                        text: '请分析这张伪代码图像：'
                    },
                    {
                        type: 'image_url',
                        image_url: {
                            url: input
                        }
                    }
                ]
            });
        }

        return messages;
    }

    /**
     * 调用LLM-VL API
     * @param {Array} messages - 消息数组
     * @returns {Promise<Object>} API响应
     */
    async callLLMVL(messages) {
        const response = await fetch(this.apiEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${this.apiKey}`
            },
            body: JSON.stringify({
                model: this.model,
                messages: messages,
                max_tokens: 2000,
                temperature: 0.1
            })
        });

        if (!response.ok) {
            throw new Error(`API请求失败: ${response.status} ${response.statusText}`);
        }

        return await response.json();
    }

    /**
     * 解析API响应
     * @param {Object} response - API响应
     * @returns {Object} 解析结果
     */
    parseResponse(response) {
        try {
            const content = response.choices[0].message.content;
            const jsonMatch = content.match(/\{[\s\S]*\}/);
            
            if (jsonMatch) {
                const result = JSON.parse(jsonMatch[0]);
                return {
                    success: true,
                    ...result
                };
            } else {
                return {
                    success: false,
                    error: '无法解析响应格式',
                    raw_response: content
                };
            }
        } catch (error) {
            return {
                success: false,
                error: '响应解析失败',
                raw_response: response
            };
        }
    }

    /**
     * 批量分析多个文件
     * @param {Array} files - 文件路径数组
     * @returns {Promise<Array>} 分析结果数组
     */
    async batchAnalyze(files) {
        const results = [];
        
        // 检查是否在Node.js环境中
        if (typeof window !== 'undefined') {
            console.warn('batchAnalyze is not supported in browser environment');
            return [];
        }
        
        // 动态导入fs模块，避免webpack打包错误
        let fs;
        try {
            fs = eval('require')('fs');
        } catch (e) {
            console.warn('fs module not available');
            return [];
        }
        
        for (const file of files) {
            try {
                const content = fs.readFileSync(file, 'utf8');
                const result = await this.analyzePseudocode(content, 'text');
                
                results.push({
                    file: file,
                    ...result
                });
                
                // 添加延迟避免API限制
                await new Promise(resolve => setTimeout(resolve, 1000));
                
            } catch (error) {
                results.push({
                    file: file,
                    success: false,
                    error: error.message
                });
            }
        }
        
        return results;
    }

    /**
     * 生成语法检查报告
     * @param {Array} results - 分析结果数组
     * @returns {string} HTML格式的报告
     */
    generateReport(results) {
        let html = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>伪代码语法检查报告</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .file-section { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; }
                .error { color: red; margin: 5px 0; }
                .suggestion { color: green; margin: 5px 0; }
                .corrected-code { background: #f5f5f5; padding: 10px; margin: 10px 0; }
                .confidence { font-weight: bold; color: blue; }
            </style>
        </head>
        <body>
            <h1>伪代码语法检查报告</h1>
            <p>生成时间: ${new Date().toLocaleString()}</p>
        `;

        for (const result of results) {
            html += `
            <div class="file-section">
                <h2>文件: ${result.file}</h2>
            `;

            if (result.success) {
                html += `<p class="confidence">置信度: ${(result.confidence * 100).toFixed(1)}%</p>`;
                
                if (result.errors && result.errors.length > 0) {
                    html += '<h3>发现的错误:</h3>';
                    for (const error of result.errors) {
                        html += `
                        <div class="error">
                            <strong>第${error.line}行 - ${error.type}:</strong> ${error.message}<br>
                            <span class="suggestion">建议: ${error.suggestion}</span><br>
                            <code>${error.corrected}</code>
                        </div>
                        `;
                    }
                } else {
                    html += '<p style="color: green;">未发现语法错误</p>';
                }

                if (result.corrected_code) {
                    html += `
                    <h3>修正后的代码:</h3>
                    <pre class="corrected-code">${result.corrected_code}</pre>
                    `;
                }
            } else {
                html += `<p class="error">分析失败: ${result.error}</p>`;
            }

            html += '</div>';
        }

        html += `
        </body>
        </html>
        `;

        return html;
    }
}

// 导出模块
// ES6 导出
export default LLMVLIntegration;

// 兼容浏览器环境
if (typeof window !== 'undefined') {
    window.LLMVLIntegration = LLMVLIntegration;
}

// 如果直接运行此脚本，执行示例
if (require.main === module) {
    const integration = new LLMVLIntegration();
    
    // 示例：分析文本
    const sampleCode = `
    FOR i < 1 TO 10
        sum < sum + i
    NEXT i
    `;
    
    integration.analyzePseudocode(sampleCode, 'text')
        .then(result => {
            console.log('分析结果:', JSON.stringify(result, null, 2));
        })
        .catch(error => {
            console.error('分析失败:', error);
        });
}