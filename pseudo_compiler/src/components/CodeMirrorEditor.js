import React, { useRef, useEffect } from 'react';
import CodeMirror from 'codemirror';
import 'codemirror/lib/codemirror.css';
import 'codemirror/theme/material.css';
import 'codemirror/theme/monokai.css';
import 'codemirror/addon/edit/closebrackets';
import 'codemirror/addon/edit/matchbrackets';
import 'codemirror/addon/selection/active-line';
import 'codemirror/addon/fold/foldcode';
import 'codemirror/addon/fold/foldgutter';
import 'codemirror/addon/fold/brace-fold';
import 'codemirror/addon/fold/foldgutter.css';
import 'codemirror/addon/hint/show-hint';
import 'codemirror/addon/hint/show-hint.css';
import './CodeMirrorEditor.css';

// 定义伪代码语法模式 - Cambridge 9618标准
const definePseudocodeMode = (CodeMirror) => {
  CodeMirror.defineMode('pseudocode', function() {
    // Cambridge 9618 伪代码关键字
    const keywords = {
      // 控制结构
      'IF': true, 'THEN': true, 'ELSE': true, 'ELSEIF': true, 'ENDIF': true,
      'WHILE': true, 'ENDWHILE': true, 'FOR': true, 'TO': true, 'STEP': true, 'NEXT': true,
      'REPEAT': true, 'UNTIL': true, 'CASE': true, 'OF': true, 'OTHERWISE': true, 'ENDCASE': true,
      
      // 过程和函数
      'PROCEDURE': true, 'ENDPROCEDURE': true, 'FUNCTION': true, 'ENDFUNCTION': true,
      'CALL': true, 'RETURN': true, 'RETURNS': true,
      
      // 数据声明
      'DECLARE': true, 'CONSTANT': true, 'TYPE': true, 'ENDTYPE': true,
      'ARRAY': true, 'RECORD': true, 'ENDRECORD': true,
      
      // 数据类型
      'INTEGER': true, 'REAL': true, 'STRING': true, 'CHAR': true, 'BOOLEAN': true, 'DATE': true,
      
      // 输入输出
      'INPUT': true, 'OUTPUT': true, 'READ': true, 'write': true,
      
      // 逻辑运算
      'AND': true, 'OR': true, 'NOT': true,
      
      // 算术运算
      'DIV': true, 'MOD': true,
      
      // 参数传递
      'BYREF': true, 'BYVAL': true,
      
      // 布尔值
      'TRUE': true, 'FALSE': true,
      
      // 其他
      'NULL': true, 'EMPTY': true
    };

    return {
      token: function(stream, state) {
        // 跳过空白字符
        if (stream.eatSpace()) return null;
        
        // 注释 - 支持 // 和 /* */ 两种格式
        if (stream.match(/\/\/.*$/)) {
          return 'comment';
        }
        if (stream.match(/\/\*/)) {
          state.inComment = true;
          return 'comment';
        }
        if (state.inComment) {
          if (stream.match(/\*\//)) {
            state.inComment = false;
          } else {
            stream.next();
          }
          return 'comment';
        }
        
        // 字符串 - 支持双引号和单引号
        if (stream.match(/"([^"\\]|\\.)*"/)) {
          return 'string';
        }
        if (stream.match(/'([^'\\]|\\.)*'/)) {
          return 'string';
        }
        
        // 数字 - 支持整数、小数和科学计数法
        if (stream.match(/\b\d+(\.\d+)?([eE][+-]?\d+)?\b/)) {
          return 'number';
        }
        
        // 赋值操作符 ←
        if (stream.match(/←/)) {
          return 'def';
        }
        
        // 比较操作符
        if (stream.match(/[≤≥≠]/)) {
          return 'operator';
        }
        
        // 其他操作符
        if (stream.match(/[+\-*/%=<>!&|^~?:]/)) {
          return 'operator';
        }
        
        // 复合操作符
        if (stream.match(/(:=|<-|->|<=|>=|==|!=|&&|\|\||<<|>>)/)) {
          return 'operator';
        }
        
        // 标点符号
        if (stream.match(/[{}[\]();,.]/)) {
          return 'punctuation';
        }
        
        // 关键字和标识符
        if (stream.match(/\b[a-zA-Z_][a-zA-Z0-9_]*\b/)) {
          const word = stream.current().toUpperCase();
          if (keywords[word]) {
            return 'keyword';
          }
          return 'variable';
        }
        
        // 其他字符
        stream.next();
        return null;
      },
      
      startState: function() {
        return {
          inComment: false,
          indentLevel: 0
        };
      },
      
      // 智能缩进
      indent: function(state, textAfter) {
        const indentUnit = 2;
        const afterKeywords = /^(THEN|ELSE|ELSEIF|ENDIF|ENDWHILE|ENDFOR|NEXT|UNTIL|ENDPROCEDURE|ENDFUNCTION|ENDCASE|OTHERWISE)$/i;
        const beforeKeywords = /^(IF|WHILE|FOR|REPEAT|PROCEDURE|FUNCTION|CASE)$/i;
        
        if (afterKeywords.test(textAfter.trim())) {
          return Math.max(0, state.indentLevel - indentUnit);
        }
        
        return state.indentLevel || 0;
      },
      
      // 电子字符处理
      electricChars: "EFRN"
    };
  });
};

// 自定义自动完成功能
const setupAutoComplete = (CodeMirror) => {
  CodeMirror.registerHelper('hint', 'pseudocode', function(cm, options) {
    const cursor = cm.getCursor();
    const token = cm.getTokenAt(cursor);
    const start = token.start;
    const end = cursor.ch;
    const word = token.string;
    
    const completions = [];
    
    // Cambridge 9618 伪代码关键字
    const keywords = [
      'IF', 'THEN', 'ELSE', 'ELSEIF', 'ENDIF',
      'WHILE', 'ENDWHILE', 'FOR', 'TO', 'STEP', 'NEXT',
      'REPEAT', 'UNTIL', 'CASE', 'OF', 'OTHERWISE', 'ENDCASE',
      'PROCEDURE', 'ENDPROCEDURE', 'FUNCTION', 'ENDFUNCTION',
      'CALL', 'RETURN', 'RETURNS',
      'DECLARE', 'CONSTANT', 'TYPE', 'ENDTYPE',
      'ARRAY', 'RECORD', 'ENDRECORD',
      'INTEGER', 'REAL', 'STRING', 'CHAR', 'BOOLEAN', 'DATE',
      'INPUT', 'OUTPUT', 'read', 'write',
      'AND', 'OR', 'NOT', 'DIV', 'MOD',
      'BYREF', 'BYVAL', 'TRUE', 'FALSE', 'NULL', 'EMPTY'
    ];
    
    // 关键字补全
    keywords.forEach(keyword => {
      if (keyword.toLowerCase().startsWith(word.toLowerCase())) {
        completions.push({
          text: keyword,
          displayText: keyword,
          className: 'cm-keyword-completion'
        });
      }
    });
    
    // 常用代码片段
    const snippets = [
      {
        text: 'IF condition THEN\n    // code\nENDIF',
        displayText: 'IF-THEN-ENDIF',
        className: 'cm-snippet-completion'
      },
      {
        text: 'IF condition THEN\n    // code\nELSE\n    // code\nENDIF',
        displayText: 'IF-THEN-ELSE-ENDIF',
        className: 'cm-snippet-completion'
      },
      {
        text: 'WHILE condition DO\n    // code\nENDWHILE',
        displayText: 'WHILE-DO-ENDWHILE',
        className: 'cm-snippet-completion'
      },
      {
        text: 'FOR i ← 1 TO n\n    // code\nNEXT i',
        displayText: 'FOR-TO-NEXT',
        className: 'cm-snippet-completion'
      },
      {
        text: 'REPEAT\n    // code\nUNTIL condition',
        displayText: 'REPEAT-UNTIL',
        className: 'cm-snippet-completion'
      },
      {
        text: 'PROCEDURE name(parameters)\n    // code\nENDPROCEDURE',
        displayText: 'PROCEDURE',
        className: 'cm-snippet-completion'
      },
      {
        text: 'FUNCTION name(parameters) RETURNS type\n    // code\n    RETURN value\nENDFUNCTION',
        displayText: 'FUNCTION',
        className: 'cm-snippet-completion'
      },
      {
        text: 'CASE OF variable\n    value1: // code\n    value2: // code\n    OTHERWISE: // code\nENDCASE',
        displayText: 'CASE-OF',
        className: 'cm-snippet-completion'
      }
    ];
    
    // 代码片段补全
    snippets.forEach(snippet => {
      if (snippet.displayText.toLowerCase().includes(word.toLowerCase()) || word.length === 0) {
        completions.push(snippet);
      }
    });
    
    return {
      list: completions,
      from: CodeMirror.Pos(cursor.line, start),
      to: CodeMirror.Pos(cursor.line, end)
    };
  });
};

const CodeMirrorEditor = ({ value, onChange, theme = 'cambridge', options = {} }) => {
  const editorRef = useRef(null);
  const codeMirrorRef = useRef(null);

  useEffect(() => {
    if (editorRef.current && !codeMirrorRef.current) {
      // 定义伪代码模式和自动完成
      definePseudocodeMode(CodeMirror);
      setupAutoComplete(CodeMirror);
      
      // 创建 CodeMirror 实例
      codeMirrorRef.current = CodeMirror.fromTextArea(editorRef.current, {
        mode: 'pseudocode',
        theme: theme,
        lineNumbers: true,
        lineWrapping: true,
        matchBrackets: true,
        autoCloseBrackets: true,
        styleActiveLine: true,
        foldGutter: true,
        gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
        hintOptions: {
          hint: CodeMirror.hint.pseudocode,
          completeSingle: false
        },
        extraKeys: {
          'Ctrl-Space': 'autocomplete',
          'Ctrl-/': 'toggleComment',
          'Ctrl-D': 'deleteLine',
          'Ctrl-Shift-K': 'deleteLine',
          'Alt-Up': 'swapLineUp',
          'Alt-Down': 'swapLineDown',
          'Ctrl-]': 'indentMore',
          'Ctrl-[': 'indentLess',
          'Tab': function(cm) {
            if (cm.somethingSelected()) {
              cm.indentSelection('add');
            } else {
              cm.replaceSelection('  ');
            }
          },
          'Shift-Tab': 'indentLess'
        },
        indentUnit: 2,
        smartIndent: true,
        electricChars: true,
        ...options
      });

      // 设置初始值
      if (value) {
        codeMirrorRef.current.setValue(value);
      }

      // 监听变化
      codeMirrorRef.current.on('change', (instance) => {
        const newValue = instance.getValue();
        if (onChange) {
          onChange(newValue);
        }
      });
    }
  }, []);

  // 更新主题
  useEffect(() => {
    if (codeMirrorRef.current) {
      codeMirrorRef.current.setOption('theme', theme);
    }
  }, [theme]);

  // 更新值
  useEffect(() => {
    if (codeMirrorRef.current && value !== codeMirrorRef.current.getValue()) {
      const cursor = codeMirrorRef.current.getCursor();
      codeMirrorRef.current.setValue(value || '');
      codeMirrorRef.current.setCursor(cursor);
    }
  }, [value]);

  // 更新选项
  useEffect(() => {
    if (codeMirrorRef.current) {
      Object.keys(options).forEach(key => {
        codeMirrorRef.current.setOption(key, options[key]);
      });
    }
  }, [options]);

  return (
    <div className="codemirror-editor">
      <textarea ref={editorRef} defaultValue={value || ''} />
    </div>
  );
};

// 注册伪代码语法模式
if (typeof window !== 'undefined' && window.CodeMirror) {
  definePseudocodeMode(window.CodeMirror);
  setupAutoComplete(window.CodeMirror);
}

export default CodeMirrorEditor;