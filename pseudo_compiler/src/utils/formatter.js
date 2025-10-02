/**
 * Pseudocode 格式化工具
 * 根据 Cambridge 9618 标准格式化 Pseudocode
 */

class PseudocodeFormatter {
  constructor() {
    // Cambridge 9618 Pseudocode 关键字列表
    this.keywords = new Set([
      // 数据类型
      'INTEGER', 'REAL', 'CHAR', 'STRING', 'BOOLEAN', 'DATE',
      // 声明
      'DECLARE', 'CONSTANT', 'TYPE', 'ENDTYPE',
      // 控制结构
      'IF', 'THEN', 'ELSE', 'ELSEIF', 'ENDIF', 'CASE', 'OF', 'OTHERWISE', 'ENDCASE',
      // 循环
      'FOR', 'TO', 'STEP', 'NEXT', 'WHILE', 'ENDWHILE', 'REPEAT', 'UNTIL',
      // 过程函数
      'PROCEDURE', 'ENDPROCEDURE', 'FUNCTION', 'ENDFUNCTION', 'RETURNS', 'RETURN', 'BYVAL', 'BYREF', 'CALL',
      // 输入输出
      'INPUT', 'OUTPUT',
      // 文件操作
      'OPENFILE', 'READFILE', 'WRITEFILE', 'CLOSEFILE', 'EOF', 'SEEK', 'GETPOSITION',
      // 逻辑运算
      'AND', 'OR', 'NOT',
      // 其他
      'TRUE', 'FALSE', 'DIV', 'MOD', 'ARRAY'
    ]);

    // 需要增加缩进的关键字
    this.indentIncreaseKeywords = new Set([
      'IF', 'ELSE', 'ELSEIF', 'CASE', 'FOR', 'WHILE', 'REPEAT', 
      'PROCEDURE', 'FUNCTION', 'TYPE'
    ]);

    // 需要减少缩进的关键字
    this.indentDecreaseKeywords = new Set([
      'ENDIF', 'ENDCASE', 'NEXT', 'ENDWHILE', 'UNTIL', 
      'ENDPROCEDURE', 'ENDFUNCTION', 'ENDTYPE'
    ]);

    // 中间关键字（既减少又增加缩进）
    this.middleKeywords = new Set(['ELSE', 'ELSEIF', 'OTHERWISE']);
  }

  /**
   * 格式化 Pseudocode 代码
   * @param {string} code - 原始代码
   * @returns {string} - 格式化后的代码
   */
  format(code) {
    if (!code || typeof code !== 'string') {
      return '';
    }

    const lines = code.split('\n');
    const formattedLines = [];
    let indentLevel = 0;
    const indentSize = 4; // 使用4个空格缩进

    for (let i = 0; i < lines.length; i++) {
      let line = lines[i].trim();
      
      // 跳过空行和注释行
      if (!line || line.startsWith('//')) {
        formattedLines.push(line);
        continue;
      }

      // 处理缩进
      const { newIndentLevel, shouldIndentThisLine } = this.calculateIndent(line, indentLevel);
      
      // 格式化关键字为大写
      const formattedLine = this.formatKeywords(line);
      
      // 应用缩进
      const indentedLine = shouldIndentThisLine 
        ? ' '.repeat(Math.max(0, indentLevel * indentSize)) + formattedLine
        : formattedLine;
      
      formattedLines.push(indentedLine);
      indentLevel = newIndentLevel;
    }

    return formattedLines.join('\n');
  }

  /**
   * 计算缩进级别
   * @param {string} line - 当前行
   * @param {number} currentIndent - 当前缩进级别
   * @returns {object} - 新的缩进级别和是否需要缩进当前行
   */
  calculateIndent(line, currentIndent) {
    const firstWord = this.getFirstKeyword(line);
    let newIndentLevel = currentIndent;
    let shouldIndentThisLine = true;

    // 处理减少缩进的关键字
    if (this.indentDecreaseKeywords.has(firstWord)) {
      newIndentLevel = Math.max(0, currentIndent - 1);
      shouldIndentThisLine = true; // 这些关键字本身需要缩进
    }
    // 处理中间关键字（ELSE, ELSEIF, OTHERWISE）
    else if (this.middleKeywords.has(firstWord)) {
      newIndentLevel = currentIndent; // 保持当前级别
      shouldIndentThisLine = true;
      // ELSEIF 后面还会增加缩进
      if (firstWord === 'ELSEIF') {
        newIndentLevel = currentIndent;
      }
    }
    // 处理增加缩进的关键字
    else if (this.indentIncreaseKeywords.has(firstWord)) {
      shouldIndentThisLine = true;
      newIndentLevel = currentIndent + 1;
    }

    // 特殊处理 CASE 语句中的选项
    if (this.isCaseOption(line)) {
      shouldIndentThisLine = true;
      newIndentLevel = currentIndent; // CASE 选项不改变缩进级别
    }

    return { newIndentLevel, shouldIndentThisLine };
  }

  /**
   * 获取行的第一个关键字
   * @param {string} line - 代码行
   * @returns {string} - 第一个关键字
   */
  getFirstKeyword(line) {
    const words = line.trim().split(/\s+/);
    return words[0] ? words[0].toUpperCase() : '';
  }

  /**
   * 检查是否是 CASE 语句的选项
   * @param {string} line - 代码行
   * @returns {boolean}
   */
  isCaseOption(line) {
    const trimmed = line.trim();
    // 匹配 "数字 :" 或 "OTHERWISE :" 的模式
    return /^\d+\s*:/.test(trimmed) || trimmed.startsWith('OTHERWISE');
  }

  /**
   * 格式化关键字为大写
   * @param {string} line - 代码行
   * @returns {string} - 格式化后的行
   */
  formatKeywords(line) {
    // 使用正则表达式匹配单词边界，避免部分匹配
    let formattedLine = line;
    
    for (const keyword of this.keywords) {
      // 创建正则表达式，匹配完整单词
      const regex = new RegExp(`\\b${keyword.toLowerCase()}\\b`, 'gi');
      formattedLine = formattedLine.replace(regex, keyword);
    }

    return formattedLine;
  }

  /**
   * 格式化赋值操作符
   * @param {string} code - 代码
   * @returns {string} - 格式化后的代码
   */
  formatAssignmentOperators(code) {
    // 将 <- 或 < 替换为 ←
    return code.replace(/\s*<-?\s*/g, ' ← ');
  }

  /**
   * 完整格式化（包括关键字大写、缩进和操作符）
   * @param {string} code - 原始代码
   * @returns {string} - 完全格式化后的代码
   */
  formatComplete(code) {
    if (!code || typeof code !== 'string') {
      return '';
    }

    // 首先格式化赋值操作符
    let formattedCode = this.formatAssignmentOperators(code);
    
    // 然后进行完整格式化
    formattedCode = this.format(formattedCode);
    
    return formattedCode;
  }
}

export default PseudocodeFormatter;