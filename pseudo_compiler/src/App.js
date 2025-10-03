import React, { useState, useEffect, useRef } from 'react';
import './App.css';
import PseudocodeEditor from './components/PseudocodeEditor';
import OutputPanel from './components/OutputPanel';
import { PseudocodeCompiler } from './utils/compiler';

function App() {
  const [code, setCode] = useState(`// Pseudocode Compiler
// Cambridge IGCSE Computer Science - 0478
// Cambridge International AS & A Levels Computer Science - 9618

DECLARE num : INTEGER
DECLARE result : INTEGER

INPUT "Enter a number: ", num
result ← num * 2
OUTPUT "Result is: ", result`);
  
  const [compiledCode, setCompiledCode] = useState('');
  const [errors, setErrors] = useState([]);
  const [warnings, setWarnings] = useState([]);
  const [consoleOutput, setConsoleOutput] = useState([]);
  const [activeTab, setActiveTab] = useState('console');

  const [statistics, setStatistics] = useState(null);
  const [compilationStatus, setCompilationStatus] = useState(null);
  
  const compilerRef = useRef(new PseudocodeCompiler());

  // 组件加载时自动编译默认代码
  useEffect(() => {
    compileCode(true); // silent编译，不显示状态消息
  }, []); // 空依赖数组，只在组件挂载时执行一次

  // 编译代码
  const compileCode = (silent = false) => {
    try {
      const result = compilerRef.current.compile(code);
      
      if (result.success) {
        setCompiledCode(result.generatedCode);
        setErrors([]);
        setWarnings(result.warnings || []);
        setStatistics(result.statistics);
        
        // 设置编译成功状态
        setCompilationStatus({
          type: 'success',
          message: '编译成功！',
          timestamp: new Date().toLocaleTimeString()
        });
      } else {
        setErrors(result.errors || []);
        setCompiledCode('');
        
        if (!silent) {
        setConsoleOutput([{
          type: 'error',
          message: '编译失败，请检查语法错误',
          timestamp: new Date().toLocaleTimeString()
        }]);
        setActiveTab('console');
      }
      }
    } catch (error) {
      setErrors([{
        type: '编译错误',
        message: error.message,
        line: 0
      }]);
      setCompiledCode('');
      
      if (!silent) {
        setConsoleOutput([{
          type: 'error',
          message: `编译过程中发生错误: ${error.message}`,
          timestamp: new Date().toLocaleTimeString()
        }]);
        setActiveTab('console');
      }
    }
  };

  // 运行代码
  const runCode = () => {
    if (!compiledCode) {
      setConsoleOutput([{
        type: 'error',
        message: '请先编译代码',
        timestamp: new Date().toLocaleTimeString()
      }]);
      setActiveTab('console');
      return;
    }
    
    setActiveTab('console');
    executeCode(compiledCode);
  };

  // 编译并运行
  const compileAndRun = () => {
    // 检查代码是否为空
    if (!code.trim()) {
      const errorMsg = '请先输入Pseudocode代码';
      setConsoleOutput([{
        type: 'error',
        message: errorMsg,
        timestamp: new Date().toLocaleTimeString()
      }]);
      setActiveTab('console');
      return;
    }
    
    try {
      const result = compilerRef.current.compile(code);
      
      if (result.success) {
        setCompiledCode(result.generatedCode);
        setErrors([]);
        setWarnings(result.warnings || []);
        setStatistics(result.statistics);
        
        // 设置编译并运行成功状态
        setCompilationStatus({
          type: 'success',
          message: '编译并运行成功！',
          timestamp: new Date().toLocaleTimeString()
        });
        
        // 直接运行编译后的代码
        setActiveTab('console');
        executeCode(result.generatedCode);
      } else {
        const errors = result.errors || [];
        setErrors(errors);
        setCompiledCode('');
        
        // 构建详细的错误信息
        const errorCount = errors.length;
        const firstError = errors[0];
        let errorMsg = `编译失败 (${errorCount}个错误)`;
        
        if (firstError) {
          const location = firstError.line > 0 ? `第${firstError.line}行: ` : '';
          errorMsg += ` - ${location}${firstError.message}`;
        }
        
        setActiveTab('errors');
        
        // 在控制台显示错误
        setConsoleOutput([{
          type: 'error',
          message: errorMsg,
          timestamp: new Date().toLocaleTimeString()
        }]);
      }
    } catch (error) {
      console.error('编译错误详情:', error);
      
      const errorInfo = {
        type: '编译错误',
        message: error.message,
        line: 0
      };
      setErrors([errorInfo]);
      setCompiledCode('');
      
      let errorMsg = `编译过程中发生错误: ${error.message}`;
      if (error.stack) {
        console.error('编译错误堆栈:', error.stack);
        errorMsg += `\n详细信息: ${error.stack.split('\n')[1] || ''}`;
      }
      
      setActiveTab('errors');
      
      // 在控制台显示错误
      setConsoleOutput([{
        type: 'error',
        message: errorMsg,
        timestamp: new Date().toLocaleTimeString()
      }]);
    }
  };

  // 执行代码
  const executeCode = (jsCode) => {
    setConsoleOutput([]);
    
    // 检查生成的代码是否为空
    if (!jsCode || jsCode.trim() === '') {
      const errorMsg = '无法运行：编译器未生成有效的JavaScript代码';
      setConsoleOutput([{
        type: 'error',
        message: errorMsg,
        timestamp: new Date().toLocaleTimeString()
      }]);
      setActiveTab('console');
      return;
    }
    
    // 在函数作用域顶层保存原始console.log
    const originalConsoleLog = console.log;
    
    try {
      // 创建安全的执行环境
      const outputs = [];
      let currentOutputLine = null; // 共享的当前输出行变量
      
      // 重写console.log来捕获输出 - 每次调用创建新行
      console.log = (...args) => {
        const message = args.join(' ');
        const timestamp = new Date().toLocaleTimeString();
        
        // 每个console.log调用都创建新的输出行
        const outputLine = {
          type: 'output',
          message: message,
          timestamp: timestamp
        };
        outputs.push(outputLine);
        currentOutputLine = outputLine;
      };
      
      // 重写OUTPUT函数 - 每次OUTPUT创建新行（符合伪代码标准）
      window.OUTPUT = (...args) => {
        const message = args.join(' ');
        const timestamp = new Date().toLocaleTimeString();
        
        // 每个OUTPUT语句都创建新的输出行
        const outputLine = {
          type: 'output',
          message: message,
          timestamp: timestamp
        };
        outputs.push(outputLine);
        currentOutputLine = outputLine;
      };
      
      // 重写INPUT函数以提供更好的错误提示
      window.INPUT = (promptText) => {
        // 多重检查prompt函数是否可用
        let promptAvailable = false;
        try {
          promptAvailable = typeof window.prompt === 'function' && 
                           window.prompt !== null && 
                           window.prompt !== undefined &&
                           !window.prompt.toString().includes('not supported');
        } catch (e) {
          promptAvailable = false;
        }
        
        if (!promptAvailable) {
          outputs.push({
            type: 'warning',
            message: `INPUT提示: ${promptText || '请输入值'} (浏览器环境不支持prompt，使用默认值: "示例输入")`,
            timestamp: new Date().toLocaleTimeString()
          });
          return '示例输入'; // 返回默认值
        }
        
        try {
          const input = window.prompt(promptText || '请输入值:');
          if (input === null) {
            outputs.push({
              type: 'info',
              message: '用户取消了输入操作，使用空字符串',
              timestamp: new Date().toLocaleTimeString()
            });
            return ''; // 用户取消时返回空字符串
          }
          return input;
        } catch (error) {
          // 如果prompt调用失败，提供默认值
          outputs.push({
            type: 'warning',
            message: `INPUT错误: ${error.message} (使用默认值: "示例输入")`,
            timestamp: new Date().toLocaleTimeString()
          });
          return '示例输入';
        }
      };
      
      // 执行代码
      eval(jsCode);
      
      // 如果没有输出，显示提示信息
      if (outputs.length === 0) {
        outputs.push({
          type: 'info',
          message: '程序执行完成，无输出内容',
          timestamp: new Date().toLocaleTimeString()
        });
      }
      
      setConsoleOutput(outputs);
      
    } catch (error) {
      // 详细的错误信息
      let errorMsg = `运行时错误: ${error.message}`;
      if (error.stack) {
        console.error('完整错误堆栈:', error.stack);
        errorMsg += `\n堆栈信息: ${error.stack.split('\n')[0]}`;
      }
      
      // 检查是否是性能保护错误
      if (error.message.includes('循环次数限制') || error.message.includes('执行超时')) {
        errorMsg = `性能保护: ${error.message}`;
      }
      
      setConsoleOutput([{
        type: 'error',
        message: errorMsg,
        timestamp: new Date().toLocaleTimeString()
      }]);
      setActiveTab('console');
    } finally {
      // 无论如何都要恢复原始console.log
      console.log = originalConsoleLog;
    }
  };

  // 清空编辑器
  const clearEditor = () => {
    setCode('');
    setCompiledCode('');
    setErrors([]);
    setWarnings([]);
    setConsoleOutput([]);
    setStatistics(null);
  };

  // 清空控制台
  const clearConsole = () => {
    setConsoleOutput([]);
  };

  // 复制JavaScript代码
  const copyJavaScript = () => {
    if (compiledCode) {
      navigator.clipboard.writeText(compiledCode).then(() => {
        setConsoleOutput(prev => [...prev, {
          type: 'info',
          message: '代码已复制到剪贴板',
          timestamp: new Date().toLocaleTimeString()
        }]);
        setActiveTab('console');
      }).catch(() => {
        setConsoleOutput(prev => [...prev, {
          type: 'error',
          message: '复制失败',
          timestamp: new Date().toLocaleTimeString()
        }]);
        setActiveTab('console');
      });
    }
  };



  // 加载示例
  const loadExample = (exampleCode) => {
    setCode(exampleCode);
  };

  // 键盘快捷键
  useEffect(() => {
    const handleKeyDown = (e) => {
      if (e.ctrlKey || e.metaKey) {
        switch (e.key) {
          case 'Enter':
            e.preventDefault();
            compileAndRun();
            break;
          case 'l':
          case 'L':
            e.preventDefault();
            clearEditor();
            break;
          default:
            break;
        }
      }
    };

    document.addEventListener('keydown', handleKeyDown);
    return () => document.removeEventListener('keydown', handleKeyDown);
  }, [compiledCode]);

  return (
    <div className="App">
      <div className="container">
        <header className="header">
          <h1>
          <i className="fas fa-code"></i>
          Pseudocode Compiler
        </h1>
          <p className="subtitle">
            Cambridge IGCSE Computer Science - 0478<br/>
            Cambridge International AS & A Levels Computer Science - 9618
          </p>
        </header>

        <div className="main-content">
          <PseudocodeEditor
            code={code}
            onChange={setCode}
            onCompile={compileCode}
            onCompileAndRun={compileAndRun}
            onClear={clearEditor}
            onLoadExample={loadExample}

          />
          
          <OutputPanel
              activeTab={activeTab}
              onTabChange={setActiveTab}
              consoleOutput={consoleOutput}
              compiledCode={compiledCode}
              errors={errors}
              warnings={warnings}
              statistics={statistics}
              onClearConsole={clearConsole}
              onCopyJavaScript={copyJavaScript}
              compilationStatus={compilationStatus}
            />
        </div>
      </div>

      
    </div>
  );
}

export default App;