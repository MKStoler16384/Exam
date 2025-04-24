# Exam

一个基于静态网页的随机题目抽取与答题系统，支持单选、多选、填空题，题库和配置均为 JSON 文件，界面采用 Material You 风格。

## 功能特性

- 随机抽取指定数量的不重复题目
- 支持单选、多选、填空题型
- 统一提交答案，答错显示解析
- 题库和配置分离，易于扩展
- 响应式设计，适配移动端
- Material You 风格美化

## 文件结构

- `index.html`：主页面，包含题目渲染与答题逻辑
- `questions.json`：题库文件，支持多种题型
- `profiles.json`：配置文件，控制每次生成题目数量
- `README.md`：项目说明

## 题库格式（questions.json）

```json
[
  {
    "type": "choice", // 单选题
    "question": "题干内容",
    "options": ["选项A", "选项B", ...],
    "answer": 0, // 正确答案下标
    "explanation": "解析内容"
  },
  {
    "type": "multi", // 多选题
    "question": "题干内容",
    "options": ["选项A", "选项B", ...],
    "answer": [0,2], // 正确答案下标数组
    "explanation": "解析内容"
  },
  {
    "type": "blank", // 填空题
    "question": "题干内容",
    "answer": ["答案1", "答案2"], // 正确答案字符串或数组
    "explanation": "解析内容"
  }
]
```

## 配置格式（profiles.json）

```json
{
  "numQuestions": 5 // 每次随机抽取的题目数量
}
```

## 使用方法

1. 将所有文件放在同一目录下（如 `/workspaces/Exam`）。
2. 用浏览器直接打开 `index.html` 即可使用。
3. 修改 `questions.json` 可自定义题库，修改 `profiles.json` 可调整题目数量。

