# Exam

一个基于 PHP + SQLite 的随机题库与答题系统，支持单选、多选、填空题，支持用户注册、登录、错题本、后台管理，界面采用 Material You 绿色风格。

## 功能特性

- 用户注册、登录、登出
- 管理员自动初始化，支持后台管理
- 随机抽取指定数量的不重复题目，做过的不再抽取
- 支持单选、多选、填空题型
- 统一提交答案，答错自动保存到错题本并显示解析
- 错题本查询
- 题库管理（增删改查）、用户管理（设/取消管理员、删除用户）
- 数据安全：密码加密、SQL注入防护
- 响应式设计，Material You 绿色美化
- 题库与配置支持 JSON 导入

## 文件结构

- `index.php`：主页面，答题入口，需登录
- `static/css/style.css`：全局样式（Material You 绿色）
- `static/js/quiz.js`：前端题目渲染与答题逻辑
- `db.php`：数据库连接与初始化（SQLite 单文件）
- `questions.json`：题库文件（支持导入）
- `profiles.json`：配置文件（题目数量等）
- `pages/login.php`、`pages/register.php`、`pages/logout.php`：登录、注册、登出
- `pages/wrong_list.php`：错题本
- `pages/admin.php`：后台管理入口
- `pages/manage_questions.php`：题库管理
- `pages/manage_users.php`：用户管理
- `api/`：后端接口（题目、错题、配置等）
- `auth/`：登录、注册处理
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

当然，也可以在网页端编辑。支持html语法。

> [!WARNING]
> 首次使用请访问`tools/import_questions.php`导入题库！

## 配置格式（profiles.json）

```json
{
  "numQuestions": 5 // 每次随机抽取的题目数量
}
```

## 安装与使用

1. 克隆或下载本项目到 `/workspaces/Exam` 目录。
2. 访问 `index.php`，首次运行会自动初始化 SQLite 数据库并生成管理员账号（用户名：admin，密码见 `admin_init.txt`）。
3. 管理员可登录后台管理题库和用户。
4. 普通用户可注册、登录、答题、查看错题本。
5. 如需导入题库，可将 `questions.json` 用工具脚本导入数据库。

## 管理员功能

- 题库管理：增删改查题目，支持单选/多选/填空
- 用户管理：设/取消管理员、删除用户（不可删除自己）
- 后台入口：`/pages/admin.php`

## 数据安全

- 所有密码均加密存储
- 所有 SQL 操作均使用 PDO 预处理防注入
- 用户 session 校验

## 其它说明

- 题库与配置支持 JSON 格式，便于批量导入与维护
- 支持移动端自适应
- 如需自定义题库，请编辑 `questions.json` 并导入数据库

---

