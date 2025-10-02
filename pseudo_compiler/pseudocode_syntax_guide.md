# Cambridge 9618 Pseudocode 语法指南

## 1. 基本格式规则

### 1.1 字体和大小写
- 使用等宽字体（如 Courier New）
- **关键字使用大写**：IF, REPEAT, PROCEDURE, DECLARE 等
- **标识符使用混合大小写**（camelCase）：NumberOfPlayers, StudentName
- **元变量**用尖括号包围：`<condition>`, `<statement>`

### 1.2 缩进和注释
- 使用3个空格进行缩进
- 注释使用双斜杠：`// 这是注释`
- 多行注释每行都用 `//` 开头

## 2. 数据类型

### 2.1 基本数据类型
- `INTEGER` - 整数
- `REAL` - 实数（带小数点）
- `CHAR` - 单个字符
- `STRING` - 字符串
- `BOOLEAN` - 布尔值（TRUE/FALSE）
- `DATE` - 日期（格式：dd/mm/yyyy）

### 2.2 字面量表示
- 整数：`5`, `-3`
- 实数：`4.7`, `0.3`, `-4.0`
- 字符：`'x'`, `'C'`, `'@'`
- 字符串：`"This is a string"`, `""`
- 布尔值：`TRUE`, `FALSE`
- 日期：`02/01/2005`

## 3. 变量和常量

### 3.1 变量声明
```pseudocode
DECLARE <identifier> : <data type>

// 示例
DECLARE Counter : INTEGER
DECLARE TotalToPay : REAL
DECLARE GameOver : BOOLEAN
```

### 3.2 常量声明
```pseudocode
CONSTANT <identifier> = <value>

// 示例
CONSTANT HourlyRate = 6.50
CONSTANT DefaultText = "N/A"
```

### 3.3 赋值操作
```pseudocode
<identifier> ← <value>

// 示例
Counter ← 0
Counter ← Counter + 1
TotalToPay ← NumberOfHours * HourlyRate
```

## 4. 数组

### 4.1 一维数组声明
```pseudocode
DECLARE <identifier> : ARRAY[<lower>:<upper>] OF <data type>

// 示例
DECLARE StudentNames : ARRAY[1:30] OF STRING
```

### 4.2 二维数组声明
```pseudocode
DECLARE <identifier> : ARRAY[<lower1>:<upper1>, <lower2>:<upper2>] OF <data type>

// 示例
DECLARE NoughtsAndCrosses : ARRAY[1:3,1:3] OF CHAR
```

### 4.3 数组使用
```pseudocode
// 访问数组元素
StudentNames[1] ← "Ali"
NoughtsAndCrosses[2,3] ← 'X'
StudentNames[n+1] ← StudentNames[n]

// 数组赋值
SavedGame ← NoughtsAndCrosses

// 循环处理数组元素
FOR Index ← 1 TO 30
   StudentNames[Index] ← ""
NEXT Index
```

## 5. 用户定义数据类型

### 5.1 枚举类型
```pseudocode
TYPE <identifier> = (value1, value2, value3, ...)

// 示例
TYPE Season = (Spring, Summer, Autumn, Winter)
```

### 5.2 指针类型
```pseudocode
TYPE <identifier> = ^<data type>

// 示例
TYPE TIntPointer = ^INTEGER
TYPE TCharPointer = ^CHAR

// 声明指针变量
DECLARE MyPointer : TIntPointer
```

### 5.3 复合数据类型（记录）
```pseudocode
TYPE <identifier>
   DECLARE <field1> : <data type>
   DECLARE <field2> : <data type>
   ...
ENDTYPE

// 示例
TYPE Student
   DECLARE LastName : STRING
   DECLARE FirstName : STRING
   DECLARE DateOfBirth : DATE
   DECLARE YearGroup : INTEGER
   DECLARE FormGroup : CHAR
ENDTYPE
```

### 5.4 使用用户定义类型
```pseudocode
// 声明变量
DECLARE Pupil1 : Student
DECLARE Form : ARRAY[1:30] OF Student
DECLARE ThisSeason : Season

// 访问字段（点记法）
Pupil1.LastName ← "Johnson"
Pupil1.FirstName ← "Leroy"
Pupil1.DateOfBirth ← 02/01/2005
Pupil1.YearGroup ← 6
Pupil1.FormGroup ← 'A'

// 结构赋值
Pupil2 ← Pupil1

// 枚举赋值
ThisSeason ← Spring
```

## 6. 标识符规则

- 只能包含字母（A-Z, a-z）、数字（0-9）和下划线（_）
- 必须以字母开头，不能以数字开头
- 不能使用重音字母
- 应该使用描述性名称
- 大小写不敏感（Countdown 和 countdown 被视为相同）
- 不能使用关键字作为变量名

## 7. 运算符

### 7.1 赋值运算符
- `←` 赋值

### 7.2 算术运算符
- `+` 加法
- `-` 减法
- `*` 乘法
- `/` 除法
- `MOD` 取模
- `DIV` 整数除法

### 7.3 关系运算符
- `=` 等于
- `<>` 不等于
- `<` 小于
- `>` 大于
- `<=` 小于等于
- `>=` 大于等于

### 7.4 逻辑运算符
- `AND` 逻辑与
- `OR` 逻辑或
- `NOT` 逻辑非

## 8. 控制结构

### 8.1 条件语句（IF）
```pseudocode
IF <condition> THEN
   <statements>
ENDIF

IF <condition> THEN
   <statements>
ELSE
   <statements>
ENDIF

IF <condition1> THEN
   <statements>
ELSEIF <condition2> THEN
   <statements>
ELSE
   <statements>
ENDIF
```

### 8.2 多路选择（CASE）
```pseudocode
CASE OF <identifier>
   <value1> : <statement>
   <value2> : <statement>
   ...
   OTHERWISE : <statement>
ENDCASE
```

### 8.3 循环结构

#### FOR 循环（计数控制）
```pseudocode
FOR <identifier> ← <value1> TO <value2>
   <statements>
NEXT <identifier>

FOR <identifier> ← <value1> TO <value2> STEP <increment>
   <statements>
NEXT <identifier>
```

#### WHILE 循环（前置条件）
```pseudocode
WHILE <condition>
   <statements>
ENDWHILE
```

#### REPEAT 循环（后置条件）
```pseudocode
REPEAT
   <statements>
UNTIL <condition>
```

## 9. 过程和函数

### 9.1 过程定义
```pseudocode
PROCEDURE <identifier>(<parameter list>)
   <statements>
ENDPROCEDURE

// 示例
PROCEDURE SWAP(BYREF X : INTEGER, Y : INTEGER)
   Temp ← X
   X ← Y
   Y ← Temp
ENDPROCEDURE
```

### 9.2 函数定义
```pseudocode
FUNCTION <identifier>(<parameter list>) RETURNS <data type>
   <statements>
   RETURN <value>
ENDFUNCTION

// 示例
FUNCTION CalculateArea(Length : REAL, Width : REAL) RETURNS REAL
   RETURN Length * Width
ENDFUNCTION
```

### 9.3 参数传递
- `BYVAL` - 按值传递（默认）
- `BYREF` - 按引用传递

### 9.4 调用过程和函数
```pseudocode
// 调用过程
CALL SWAP(Number1, Number2)

// 调用函数
Area ← CalculateArea(10.5, 8.2)
```

## 10. 输入输出

### 10.1 输入
```pseudocode
INPUT <identifier>
INPUT <prompt>, <identifier>

// 示例
INPUT UserName
INPUT "Enter your age: ", Age
```

### 10.2 输出
```pseudocode
OUTPUT <value>
OUTPUT <value1>, <value2>, ...

// 示例
OUTPUT "Hello World"
OUTPUT "Your name is ", UserName
OUTPUT "Sum = ", Total
```

## 11. 文件处理

### 11.1 文本文件
```pseudocode
// 打开文件
OPENFILE <filename> FOR READ/WRITE/APPEND

// 读取文件
READFILE <filename>, <variable>

// 写入文件
WRITEFILE <filename>, <data>

// 关闭文件
CLOSEFILE <filename>

// 检查文件结束
EOF(<filename>)
```

### 11.2 随机文件
```pseudocode
// 定位文件指针
SEEK <filename>, <position>

// 获取文件指针位置
<position> ← GETPOSITION(<filename>)
```

## 12. 字符串函数

- `LENGTH(<string>)` - 返回字符串长度
- `SUBSTRING(<string>, <start>, <length>)` - 提取子字符串
- `LEFT(<string>, <length>)` - 从左边提取字符
- `RIGHT(<string>, <length>)` - 从右边提取字符
- `MID(<string>, <start>, <length>)` - 从中间提取字符
- `LCASE(<string>)` - 转换为小写
- `UCASE(<string>)` - 转换为大写

## 13. 数学函数

- `INT(<number>)` - 取整数部分
- `ROUND(<number>, <places>)` - 四舍五入
- `RANDOM()` - 生成0-1之间的随机数
- `SQR(<number>)` - 平方根
- `ABS(<number>)` - 绝对值

## 14. 关键字列表

**数据类型**: INTEGER, REAL, CHAR, STRING, BOOLEAN, DATE
**声明**: DECLARE, CONSTANT, TYPE, ENDTYPE
**控制结构**: IF, THEN, ELSE, ELSEIF, ENDIF, CASE, OF, OTHERWISE, ENDCASE
**循环**: FOR, TO, STEP, NEXT, WHILE, ENDWHILE, REPEAT, UNTIL
**过程函数**: PROCEDURE, ENDPROCEDURE, FUNCTION, ENDFUNCTION, RETURNS, RETURN, BYVAL, BYREF, CALL
**输入输出**: INPUT, OUTPUT
**文件操作**: OPENFILE, READFILE, WRITEFILE, CLOSEFILE, EOF, SEEK, GETPOSITION
**逻辑运算**: AND, OR, NOT
**其他**: TRUE, FALSE, DIV, MOD

---

*本指南基于 Cambridge International AS & A Level Computer Science 9618 Pseudocode Guide*