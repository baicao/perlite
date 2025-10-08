import React, { useRef, useEffect, useState } from 'react';
import SyntaxChecker from './SyntaxChecker';
import CodeMirrorEditor from './CodeMirrorEditor';
import './PseudocodeEditor.css';
import PseudocodeFormatter from '../utils/formatter';

const PseudocodeEditor = ({
  code,
  onChange,
  onCompile,
  onCompileAndRun,
  onClear,
  onLoadExample,
  errors = [],
  warnings = []
}) => {
  const [syntaxSuggestions, setSyntaxSuggestions] = useState([]);
  const [showExampleModal, setShowExampleModal] = useState(false);
  const [formatter] = useState(new PseudocodeFormatter());

  // Handle keyboard shortcuts
  const handleKeyDown = (e) => {
    if (e.ctrlKey || e.metaKey) {
      if (e.key === 'Enter') {
        e.preventDefault();
        onCompileAndRun();
      } else if (e.key === 'l' || e.key === 'L') {
        e.preventDefault();
        onClear();
      } else if (e.shiftKey && (e.key === 'F' || e.key === 'f')) {
        e.preventDefault();
        formatCode();
      } else if (e.key === 'a' || e.key === 'A') {
        // 全选功能 - 让浏览器默认行为处理
        // 不阻止默认行为，让Ctrl+A/Cmd+A正常工作
        // 不需要preventDefault()，直接让默认行为执行
      }
    }
    
    if (e.key === 'Tab') {
      e.preventDefault();
      const start = e.target.selectionStart;
      const end = e.target.selectionEnd;
      const newValue = code.substring(0, start) + '    ' + code.substring(end);
      onChange(newValue);
      
      // Set cursor position
      setTimeout(() => {
        e.target.selectionStart = e.target.selectionEnd = start + 4;
      }, 0);
    }
  };

  // Example code data
  const examples = [
    {
      name: 'Basic Input/Output',
      code: `// Pseudocode Compiler
// Cambridge IGCSE Computer Science - 0478
// Cambridge International AS & A Levels Computer Science - 9618

DECLARE num : INTEGER
DECLARE result : INTEGER

INPUT "Enter a number: ", num
result ← num * 2
OUTPUT "Result is: ", result`
    },
    {
      name: 'Conditional Statements (IF-ELSEIF-ELSE)',
      code: `DECLARE score : INTEGER

INPUT "Enter score: ", score

IF score >= 90 THEN
    OUTPUT "Excellent"
ELSEIF score >= 80 THEN
    OUTPUT "Good"
ELSEIF score >= 70 THEN
    OUTPUT "Average"
ELSEIF score >= 60 THEN
    OUTPUT "Pass"
ELSE
    OUTPUT "Fail"
ENDIF`
    },
    {
      name: 'CASE Statement (Multiple Selection)',
      code: `DECLARE dayNumber : INTEGER
DECLARE dayName : STRING

INPUT "Enter day number (1-7): ", dayNumber

CASE OF dayNumber
    1 : dayName ← "Monday"
    2 : dayName ← "Tuesday"
    3 : dayName ← "Wednesday"
    4 : dayName ← "Thursday"
    5 : dayName ← "Friday"
    6 : dayName ← "Saturday"
    7 : dayName ← "Sunday"
    OTHERWISE : dayName ← "Invalid input"
ENDCASE

OUTPUT "Today is: ", dayName`
    },
    {
      name: 'FOR Loop',
      code: `DECLARE i : INTEGER
DECLARE sum : INTEGER

sum ← 0

FOR i ← 1 TO 10
    sum ← sum + i
    OUTPUT "Current i=", i, ", Sum=", sum
NEXT i

OUTPUT "Sum from 1 to 10 is: ", sum`
    },
    {
      name: 'WHILE Loop',
      code: `DECLARE num : INTEGER
DECLARE count : INTEGER

count ← 0
num ← 1

WHILE num <= 100
    count ← count + 1
    num ← num * 2
    OUTPUT "Iteration ", count, ": ", num
ENDWHILE

OUTPUT "Loop executed ", count, " times"`
    },
    {
      name: 'REPEAT-UNTIL Loop',
      code: `DECLARE password : STRING
DECLARE attempts : INTEGER

attempts ← 0

REPEAT
    attempts ← attempts + 1
    INPUT "Enter password: ", password
    IF password <> "123456" THEN
        OUTPUT "Wrong password, try again"
    ENDIF
UNTIL password = "123456" OR attempts >= 3

IF password = "123456" THEN
    OUTPUT "Login successful!"
ELSE
    OUTPUT "Too many attempts, account locked"
ENDIF`
    },
    {
      name: 'One-Dimensional Array Operations',
      code: `DECLARE numbers : ARRAY[1:5] OF INTEGER
DECLARE i : INTEGER
DECLARE sum : INTEGER
DECLARE max : INTEGER

// Input array elements
FOR i ← 1 TO 5
    INPUT "Enter number ", i, ": ", numbers[i]
NEXT i

// Calculate sum and maximum
sum ← 0
max ← numbers[1]
FOR i ← 1 TO 5
    sum ← sum + numbers[i]
    IF numbers[i] > max THEN
        max ← numbers[i]
    ENDIF
NEXT i

OUTPUT "Sum of array elements: ", sum
OUTPUT "Maximum value: ", max`
    },
    {
      name: 'Two-Dimensional Array Operations',
      code: `DECLARE matrix : ARRAY[1:3, 1:3] OF INTEGER
DECLARE i, j : INTEGER
DECLARE sum : INTEGER

// Input matrix elements
FOR i ← 1 TO 3
    FOR j ← 1 TO 3
        INPUT "Enter matrix[", i, ",", j, "]: ", matrix[i, j]
    NEXT j
NEXT i

// Calculate diagonal sum
sum ← 0
FOR i ← 1 TO 3
    sum ← sum + matrix[i, i]
NEXT i

OUTPUT "Main diagonal sum: ", sum`
    },
    {
      name: 'Record Type (RECORD)',
      code: `TYPE Student
    Name : STRING
    Age : INTEGER
    Grade : REAL
ENDTYPE

DECLARE student1 : Student
DECLARE average : REAL

// Input student information
INPUT "Enter student name: ", student1.Name
INPUT "Enter student age: ", student1.Age
INPUT "Enter student grade: ", student1.Grade

// Output student information
OUTPUT "Student Information:"
OUTPUT "Name: ", student1.Name
OUTPUT "Age: ", student1.Age
OUTPUT "Grade: ", student1.Grade

// Determine grade level
IF student1.Grade >= 90 THEN
    OUTPUT "Level: A"
ELSEIF student1.Grade >= 80 THEN
    OUTPUT "Level: B"
ELSE
    OUTPUT "Level: C"
ENDIF`
    },
    {
      name: 'Procedure (PROCEDURE)',
      code: `PROCEDURE PrintHeader()
    OUTPUT "================================"
    OUTPUT "    Welcome to Calculator"
    OUTPUT "================================"
ENDPROCEDURE

PROCEDURE PrintResult(operation : STRING, num1 : REAL, num2 : REAL, result : REAL)
    OUTPUT num1, " ", operation, " ", num2, " = ", result
ENDPROCEDURE

DECLARE a, b, sum : REAL

CALL PrintHeader()

INPUT "Enter first number: ", a
INPUT "Enter second number: ", b

sum ← a + b
CALL PrintResult("+", a, b, sum)`
    },
    {
      name: 'Function (FUNCTION)',
      code: `FUNCTION CalculateFactorial(n : INTEGER) RETURNS INTEGER
    DECLARE result : INTEGER
    DECLARE i : INTEGER
    
    result ← 1
    FOR i ← 1 TO n
        result ← result * i
    NEXT i
    
    RETURN result
ENDFUNCTION

FUNCTION IsEven(number : INTEGER) RETURNS BOOLEAN
    IF number MOD 2 = 0 THEN
        RETURN TRUE
    ELSE
        RETURN FALSE
    ENDIF
ENDFUNCTION

DECLARE num : INTEGER
DECLARE factorial : INTEGER

INPUT "Enter a positive integer: ", num

factorial ← CalculateFactorial(num)
OUTPUT num, " factorial is: ", factorial

IF IsEven(num) THEN
    OUTPUT num, " is even"
ELSE
    OUTPUT num, " is odd"
ENDIF`
    },
    {
      name: 'String Processing',
      code: `DECLARE fullName : STRING
DECLARE firstName : STRING
DECLARE lastName : STRING
DECLARE nameLength : INTEGER

INPUT "Enter your full name: ", fullName

nameLength ← LENGTH(fullName)
OUTPUT "Your name length is: ", nameLength

// Extract first 3 characters as surname
firstName ← LEFT(fullName, 3)
OUTPUT "Surname: ", firstName

// Convert to uppercase
OUTPUT "Uppercase name: ", UCASE(fullName)

// Convert to lowercase
OUTPUT "Lowercase name: ", LCASE(fullName)`
    },
    {
      name: 'Mathematical Functions',
      code: `DECLARE radius : REAL
DECLARE area : REAL
DECLARE circumference : REAL
DECLARE randomNum : REAL

INPUT "Enter circle radius: ", radius

// Calculate area (π ≈ 3.14159)
area ← 3.14159 * radius * radius
OUTPUT "Circle area: ", ROUND(area, 2)

// Calculate circumference
circumference ← 2 * 3.14159 * radius
OUTPUT "Circle circumference: ", ROUND(circumference, 2)

// Generate random number
randomNum ← RANDOM() * 100
OUTPUT "Random number (0-100): ", INT(randomNum)

// Calculate square root
OUTPUT "Square root of radius: ", SQR(radius)`
    }
  ];

  // Format code
  const formatCode = () => {
    try {
      const formattedCode = formatter.formatComplete(code);
      onChange(formattedCode);
    } catch (error) {
      console.error('Format error:', error);
      alert('Code formatting failed, please check code syntax');
    }
  };

  // Show example selection
  const showExamples = () => {
    setShowExampleModal(true);
  };

  // Load selected example
  const loadSelectedExample = (exampleCode) => {
    onLoadExample(exampleCode);
    setShowExampleModal(false);
  };

  return (
    <div className="editor-section">
      <div className="editor-header">

        <div className="editor-controls">
          <button 
            className="control-btn compile-btn"
            onClick={onCompileAndRun}
            title="Compile and Run (Ctrl+Enter)"
          >
            <i className="fas fa-play"></i> Run
          </button>
          
          <button 
            className="control-btn format-btn"
            onClick={formatCode}
            title="Format Code (Ctrl+Shift+F)"
          >
            <i className="fas fa-magic"></i> Format
          </button>
          
          <button 
            className="control-btn example-btn"
            onClick={showExamples}
            title="Load Example Code"
          >
            <i className="fas fa-lightbulb"></i> Examples
          </button>
          
          <button 
            className="control-btn clear-btn"
            onClick={onClear}
            title="Clear Editor (Ctrl+L)"
          >
            <i className="fas fa-eraser"></i> Clear
          </button>
        </div>
      </div>
      
      <div className="editor-container">
        <div className="editor-wrapper">
          <CodeMirrorEditor
            value={code}
            onChange={onChange}
            theme="cambridge"
            options={{
              placeholder: "Enter your Pseudocode here...\n\nExample:\nDECLARE x : INTEGER\nDECLARE y : INTEGER\nINPUT 'Enter first number: ', x\nINPUT 'Enter second number: ', y\nOUTPUT 'Sum is: ', x + y"
            }}
          />
        </div>
      </div>

      {/* 示例选择模态框 */}
      {showExampleModal && (
        <div className="example-modal-overlay" onClick={() => setShowExampleModal(false)}>
          <div className="example-modal" onClick={(e) => e.stopPropagation()}>
            <div className="example-modal-header">
              <h3><i className="fas fa-lightbulb"></i> Select Example Code</h3>
              <button 
                className="close-btn"
                onClick={() => setShowExampleModal(false)}
              >
                <i className="fas fa-times"></i>
              </button>
            </div>
            <div className="example-modal-body">
              {examples.map((example, index) => (
                <div 
                  key={index} 
                  className="example-item"
                  onClick={() => loadSelectedExample(example.code)}
                >
                  <h4>{example.name}</h4>
                  <pre className="example-preview">{example.code.substring(0, 200)}...</pre>
                </div>
              ))}
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

export default PseudocodeEditor;