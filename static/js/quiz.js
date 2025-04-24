let questions = [];
let profile = { numQuestions: 1 };
let currentQuestions = [];

function getRandomItems(arr, n) {
  const shuffled = arr.slice();
  for (let i = shuffled.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
  }
  return shuffled.slice(0, n);
}

async function loadData() {
  const [questionsRes, profileRes] = await Promise.all([
    fetch('/api/get_questions.php'),
    fetch('/api/get_profile.php')
  ]);
  questions = await questionsRes.json();
  profile = await profileRes.json();
}

function renderQuestions() {
  const container = document.getElementById('quiz-container');
  container.innerHTML = '';
  currentQuestions = getRandomItems(questions, Math.min(profile.numQuestions, questions.length));
  currentQuestions.forEach((q, idx) => {
    const qDiv = document.createElement('div');
    qDiv.className = 'md3-card';
    let optionsHtml = '';
    // 修正：options 可能为 JSON 字符串，需解析
    let opts = q.options;
    if (typeof opts === 'string') {
      try {
        opts = JSON.parse(opts);
      } catch {
        opts = [];
      }
    }
    if (q.type === 'blank') {
      optionsHtml = `
        <div class="options">
          <input type="text" name="blank${idx}" class="md3-input" autocomplete="off" placeholder="请输入答案">
        </div>
      `;
    } else if (q.type === 'multi') {
      optionsHtml = `
        <div class="options">
          ${(opts || []).map((opt, i) => `
            <div class="option">
              <label>
                <input type="checkbox" name="multi${idx}" value="${i}">
                ${opt}
              </label>
            </div>
          `).join('')}
        </div>
      `;
    } else {
      optionsHtml = `
        <div class="options">
          ${(opts || []).map((opt, i) => `
            <div class="option">
              <label>
                <input type="radio" name="option${idx}" value="${i}">
                ${opt}
              </label>
            </div>
          `).join('')}
        </div>
      `;
    }
    qDiv.innerHTML = `
      <div class="question">${idx + 1}. ${q.question}</div>
      ${optionsHtml}
      <div class="result"></div>
      <div class="explanation" style="display:none;"></div>
    `;
    container.appendChild(qDiv);
  });
  document.getElementById('submit-all-btn').style.display = 'block';
}

function checkAnswers() {
  const container = document.getElementById('quiz-container');
  const cards = container.querySelectorAll('.md3-card');
  currentQuestions.forEach((q, idx) => {
    const card = cards[idx];
    const resultDiv = card.querySelector('.result');
    const explanationDiv = card.querySelector('.explanation');
    let correct = false;
    // 解析 answer 字段（数据库可能为字符串）
    let answer = q.answer;
    if (typeof answer === 'string') {
      try {
        answer = JSON.parse(answer);
      } catch {
        // 单选题 answer 可能是数字字符串
        if (q.type === 'choice' && !isNaN(answer)) {
          answer = parseInt(answer);
        }
      }
    }
    if (q.type === 'blank') {
      const input = card.querySelector(`input[name="blank${idx}"]`);
      if (!input.value.trim()) {
        resultDiv.textContent = '请填写答案！';
        resultDiv.className = 'result incorrect';
        explanationDiv.style.display = 'none';
        return;
      }
      if (typeof answer === 'string') {
        correct = input.value.trim() === answer;
      } else if (Array.isArray(answer)) {
        correct = answer.includes(input.value.trim());
      }
    } else if (q.type === 'multi') {
      const selected = Array.from(card.querySelectorAll(`input[name="multi${idx}"]:checked`)).map(cb => parseInt(cb.value));
      if (selected.length === 0) {
        resultDiv.textContent = '请选择一个或多个选项！';
        resultDiv.className = 'result incorrect';
        explanationDiv.style.display = 'none';
        return;
      }
      const ans = Array.isArray(answer) ? answer.slice().sort().join(',') : '';
      const sel = selected.slice().sort().join(',');
      correct = ans === sel;
    } else {
      const selected = card.querySelector(`input[name="option${idx}"]:checked`);
      if (!selected) {
        resultDiv.textContent = '请选择一个选项！';
        resultDiv.className = 'result incorrect';
        explanationDiv.style.display = 'none';
        return;
      }
      correct = parseInt(selected.value) === answer;
    }
    if (correct) {
      resultDiv.textContent = '回答正确！';
      resultDiv.className = 'result correct';
      explanationDiv.style.display = 'none';
    } else {
      resultDiv.textContent = '回答错误！';
      resultDiv.className = 'result incorrect';
      explanationDiv.textContent = q.explanation || '暂无解析';
      explanationDiv.style.display = 'block';
      fetch('/api/save_wrong.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({question_id: q.id})
      });
    }
  });
}

document.getElementById('next-btn').onclick = function() {
  renderQuestions();
  document.getElementById('submit-all-btn').style.display = 'block';
};
document.getElementById('submit-all-btn').onclick = checkAnswers;

loadData().then(() => {
  renderQuestions();
  document.getElementById('submit-all-btn').style.display = 'block';
});