import React, { useState } from 'react';
import './OutputPanel.css';

const OutputPanel = ({ 
  activeTab: propActiveTab,
  onTabChange,
  consoleOutput, 
  compiledCode, 
  errors,
  warnings,
  statistics,
  onClearConsole,
  onCopyJavaScript,
  compilationStatus
}) => {
  const currentActiveTab = propActiveTab || 'console';

  const renderConsoleOutput = () => {
    if (!consoleOutput || consoleOutput.length === 0) {
      return <div className="empty-state">暂无输出内容</div>;
    }

    return (
      <div className="console-content">
        {consoleOutput.map((line, index) => {
          // 确保始终渲染字符串，避免渲染对象导致React错误
          let message;
          if (typeof line === 'string') {
            message = line;
          } else if (line && typeof line === 'object') {
            message = line.message || JSON.stringify(line);
          } else {
            message = String(line || '');
          }
          
          return (
            <div key={index} className={`console-line console-${line.type || 'output'}`}>
              <span className="console-message">{message}</span>
            </div>
          );
        })}
      </div>
    );
  };

  const renderGeneratedCode = () => {
    if (!compiledCode) {
      return <div className="empty-state">暂无生成的代码</div>;
    }

    return (
      <pre className="generated-code">
        <code>{compiledCode}</code>
      </pre>
    );
  };

  const getErrorSuggestion = (error) => {
    const message = error.message || error;
    const suggestions = {
      '未定义的变量': '请确保在使用变量前先用DECLARE语句声明变量',
      '语法错误': '请检查语法是否符合Cambridge 9618 Pseudocode规范',
      '缺少分号': '请在语句末尾添加分号',
      '括号不匹配': '请检查所有括号是否正确配对',
      '类型不匹配': '请检查变量类型是否与赋值类型匹配',
      '缺少END': '请为每个BEGIN添加对应的END语句',
      '无效的标识符': '变量名只能包含字母、数字和下划线，且不能以数字开头'
    };
    
    for (const [key, suggestion] of Object.entries(suggestions)) {
      if (message.includes(key)) {
        return suggestion;
      }
    }
    return '请参考语法指南检查代码格式';
  };

  const renderErrors = () => {
    if (!errors || errors.length === 0) {
      return <div className="empty-state">暂无错误信息</div>;
    }

    return (
      <div className="error-content">
        {errors.map((error, index) => {
          // 确保始终渲染字符串，避免渲染对象导致React错误
          let message;
          if (typeof error === 'string') {
            message = error;
          } else if (error && typeof error === 'object') {
            message = error.message || JSON.stringify(error);
          } else {
            message = String(error || '');
          }
          
          return (
            <div key={index} className="error-item simple">
              <i className="fas fa-exclamation-circle error-icon"></i>
              <div className="error-details">
                <div className="error-message">
                  {error.line && <span className="error-line">第{error.line}行: </span>}
                  {message}
                </div>
              </div>
            </div>
          );
        })}
      </div>
    );
  };

  const getTabContent = () => {
    switch (currentActiveTab) {
      case 'console':
        return renderConsoleOutput();
      case 'code':
        // 代码tab已隐藏，重定向到控制台
        return renderConsoleOutput();
      case 'errors':
        return renderErrors();
      default:
        return renderConsoleOutput();
    }
  };

  const getErrorCount = () => {
    return errors ? errors.length : 0;
  };

  const renderStatusBar = () => {
    if (!compilationStatus) return null;
    
    const { type, message, timestamp } = compilationStatus;
    const statusClass = `status-${type}`;
    const icon = type === 'success' ? 'fa-check-circle' : 
                 type === 'error' ? 'fa-exclamation-circle' : 
                 type === 'running' ? 'fa-spinner fa-spin' : 'fa-info-circle';
    
    return (
      <div className={`status-bar ${statusClass}`}>
        <div className="status-content">
          <i className={`fas ${icon}`}></i>
          <span className="status-message">{message}</span>
          {timestamp && <span className="status-timestamp">[{timestamp}]</span>}
        </div>
      </div>
    );
  };

  return (
    <div className="output-section">
      {renderStatusBar()}
      <div className="output-header">
        <div className="tab-buttons">
          <button 
            className={`tab-btn ${currentActiveTab === 'console' ? 'active' : ''}`}
            onClick={() => onTabChange && onTabChange('console')}
          >
            <i className="fas fa-terminal"></i> Console
          </button>
          {/* 隐藏生成的代码tab */}
          <button 
            className={`tab-btn ${currentActiveTab === 'errors' ? 'active' : ''} ${getErrorCount() > 0 ? 'has-errors' : ''}`}
            onClick={() => onTabChange && onTabChange('errors')}
          >
            <i className="fas fa-bug"></i> Errors 
            {getErrorCount() > 0 && (
              <span className="error-badge">{getErrorCount()}</span>
            )}
          </button>
        </div>
        
        {currentActiveTab === 'console' && (
          <button 
            className="btn btn-secondary btn-sm"
            onClick={onClearConsole}
            title="Clear Console"
          >
            <i className="fas fa-broom"></i> Clear
          </button>
        )}
      </div>
      
      <div className="output-content">
        {getTabContent()}
      </div>
    </div>
  );
};

export default OutputPanel;