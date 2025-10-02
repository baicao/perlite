import React, { useState, useCallback } from 'react';
import LLMVLIntegration from '../utils/llm-vl-integration';

const SyntaxChecker = ({ code, onSuggestions }) => {
    const [isChecking, setIsChecking] = useState(false);
    const [lastCheck, setLastCheck] = useState(null);
    const [suggestions, setSuggestions] = useState([]);
    const [confidence, setConfidence] = useState(0);

    const llmvl = new LLMVLIntegration();

    const checkSyntax = useCallback(async () => {
        if (!code.trim()) {
            setSuggestions([]);
            return;
        }

        setIsChecking(true);
        try {
            const result = await llmvl.analyzePseudocode(code, 'text');
            
            if (result.success) {
                setSuggestions(result.errors || []);
                setConfidence(result.confidence || 0);
                setLastCheck(new Date());
                
                // 通知父组件
                if (onSuggestions) {
                    onSuggestions(result);
                }
            } else {
                console.error('语法检查失败:', result.error);
                setSuggestions([]);
            }
        } catch (error) {
            console.error('语法检查异常:', error);
            setSuggestions([]);
        } finally {
            setIsChecking(false);
        }
    }, [code, onSuggestions]);

    const applySuggestion = (suggestion) => {
        // 这里可以实现自动修正功能
        if (onSuggestions) {
            onSuggestions({
                type: 'apply_suggestion',
                suggestion: suggestion
            });
        }
    };

    const getSeverityColor = (type) => {
        switch (type.toLowerCase()) {
            case 'syntax_error':
            case 'assignment_error':
                return '#ff4444';
            case 'style_warning':
            case 'naming_convention':
                return '#ff8800';
            case 'suggestion':
            case 'improvement':
                return '#0088ff';
            default:
                return '#666666';
        }
    };

    const getSeverityIcon = (type) => {
        switch (type.toLowerCase()) {
            case 'syntax_error':
            case 'assignment_error':
                return '❌';
            case 'style_warning':
            case 'naming_convention':
                return '⚠️';
            case 'suggestion':
            case 'improvement':
                return '💡';
            default:
                return 'ℹ️';
        }
    };

    return (
        <div className="syntax-checker">
            <div className="checker-header">
                <h3>智能语法检查</h3>
                <div className="checker-controls">
                    <button 
                        onClick={checkSyntax} 
                        disabled={isChecking || !code.trim()}
                        className="check-button"
                    >
                        {isChecking ? '检查中...' : '检查语法'}
                    </button>
                    {confidence > 0 && (
                        <span className="confidence-score">
                            置信度: {(confidence * 100).toFixed(1)}%
                        </span>
                    )}
                </div>
            </div>

            {lastCheck && (
                <div className="last-check">
                    最后检查: {lastCheck.toLocaleTimeString()}
                </div>
            )}

            <div className="suggestions-list">
                {suggestions.length === 0 && !isChecking && (
                    <div className="no-suggestions">
                        {code.trim() ? '未发现语法问题 ✅' : '请输入代码进行检查'}
                    </div>
                )}

                {suggestions.map((suggestion, index) => (
                    <div key={index} className="suggestion-item">
                        <div className="suggestion-header">
                            <span className="severity-icon">
                                {getSeverityIcon(suggestion.type)}
                            </span>
                            <span 
                                className="suggestion-type"
                                style={{ color: getSeverityColor(suggestion.type) }}
                            >
                                第{suggestion.line}行 - {suggestion.type}
                            </span>
                        </div>
                        
                        <div className="suggestion-message">
                            {suggestion.message}
                        </div>
                        
                        {suggestion.suggestion && (
                            <div className="suggestion-advice">
                                <strong>建议:</strong> {suggestion.suggestion}
                            </div>
                        )}
                        
                        {suggestion.corrected && (
                            <div className="suggestion-correction">
                                <strong>修正:</strong>
                                <code className="corrected-code">
                                    {suggestion.corrected}
                                </code>
                                <button 
                                    className="apply-button"
                                    onClick={() => applySuggestion(suggestion)}
                                >
                                    应用修正
                                </button>
                            </div>
                        )}
                    </div>
                ))}
            </div>

            <style jsx>{`
                .syntax-checker {
                    background: #f8f9fa;
                    border: 1px solid #e9ecef;
                    border-radius: 8px;
                    padding: 16px;
                    margin: 16px 0;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                }

                .checker-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 16px;
                    padding-bottom: 8px;
                    border-bottom: 1px solid #dee2e6;
                }

                .checker-header h3 {
                    margin: 0;
                    color: #495057;
                    font-size: 18px;
                }

                .checker-controls {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }

                .check-button {
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 8px 16px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                    transition: background-color 0.2s;
                }

                .check-button:hover:not(:disabled) {
                    background: #0056b3;
                }

                .check-button:disabled {
                    background: #6c757d;
                    cursor: not-allowed;
                }

                .confidence-score {
                    font-size: 12px;
                    color: #28a745;
                    font-weight: bold;
                }

                .last-check {
                    font-size: 12px;
                    color: #6c757d;
                    margin-bottom: 12px;
                }

                .suggestions-list {
                    max-height: 400px;
                    overflow-y: auto;
                }

                .no-suggestions {
                    text-align: center;
                    color: #6c757d;
                    font-style: italic;
                    padding: 20px;
                }

                .suggestion-item {
                    background: white;
                    border: 1px solid #dee2e6;
                    border-radius: 6px;
                    padding: 12px;
                    margin-bottom: 8px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }

                .suggestion-header {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    margin-bottom: 8px;
                }

                .severity-icon {
                    font-size: 16px;
                }

                .suggestion-type {
                    font-weight: bold;
                    font-size: 14px;
                }

                .suggestion-message {
                    color: #495057;
                    margin-bottom: 8px;
                    line-height: 1.4;
                }

                .suggestion-advice {
                    color: #28a745;
                    font-size: 14px;
                    margin-bottom: 8px;
                }

                .suggestion-correction {
                    background: #f8f9fa;
                    padding: 8px;
                    border-radius: 4px;
                    border-left: 3px solid #28a745;
                }

                .corrected-code {
                    display: block;
                    background: #e9ecef;
                    padding: 4px 8px;
                    border-radius: 3px;
                    font-family: 'Courier New', monospace;
                    font-size: 13px;
                    margin: 4px 0;
                    white-space: pre-wrap;
                }

                .apply-button {
                    background: #28a745;
                    color: white;
                    border: none;
                    padding: 4px 8px;
                    border-radius: 3px;
                    cursor: pointer;
                    font-size: 12px;
                    margin-top: 4px;
                }

                .apply-button:hover {
                    background: #218838;
                }
            `}</style>
        </div>
    );
};

export default SyntaxChecker;