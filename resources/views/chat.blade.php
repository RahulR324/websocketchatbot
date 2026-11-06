<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Assistant</title>

    @vite(['resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        html,
body{
    width:100%;
    height:100%;
    overflow:hidden;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Inter',sans-serif;
}

body{
    background:#FAFAFA;
    overflow-x:hidden;
}

.chat-container{
    width:100%;
    max-width:1100px;
    margin:auto;
    height:100dvh;
    display:flex;
    flex-direction:column;
}

.header{
    background:#fff;
    border-bottom:1px solid #E5E7EB;
    padding:18px 24px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-shrink:0;
}

.header-left{
    display:flex;
    align-items:center;
    gap:12px;
}

.logo{
    width:48px;
    height:48px;
    border-radius:14px;
    background:#2563EB;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    flex-shrink:0;
}

.bot-info h2{
    font-size:18px;
    color:#111827;
    font-weight:600;
}

.status{
    font-size:12px;
    color:#16A34A;
}

#messages{
    flex:1;
    overflow-y:auto;
    padding:24px;
    display:flex;
    flex-direction:column;
    gap:16px;
    scroll-behavior:smooth;
}

.welcome{
    text-align:center;
    margin:auto;
    max-width:650px;
    padding:20px;
}

.welcome h1{
    font-size:42px;
    color:#111827;
    margin-bottom:12px;
    line-height:1.2;
}

.welcome p{
    color:#6B7280;
    font-size:18px;
    line-height:1.6;
}

.message-row{
    display:flex;
    width:100%;
}

.user-row{
    justify-content:flex-end;
}

.bot-row{
    justify-content:flex-start;
}

.message{
    max-width:70%;
    padding:14px 18px;
    border-radius:18px;
    word-wrap:break-word;
    overflow-wrap:break-word;
    line-height:1.6;
    animation:fadeIn .3s ease;
}

.user-message{
    background:#2563EB;
    color:#fff;
    border-bottom-right-radius:6px;
}

.bot-message{
    background:#fff;
    color:#111827;
    border:1px solid #E5E7EB;
    border-bottom-left-radius:6px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

.bot-message p {
    margin-bottom: 0.75rem;
}

.bot-message p:last-child {
    margin-bottom: 0;
}

.bot-message h1,
.bot-message h2,
.bot-message h3,
.bot-message h4 {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.bot-message h1:first-child,
.bot-message h2:first-child,
.bot-message h3:first-child {
    margin-top: 0;
}

.bot-message ul,
.bot-message ol {
    margin-left: 1.25rem;
    margin-bottom: 0.75rem;
}

.bot-message li {
    margin-bottom: 0.25rem;
}

.bot-message pre {
    background: #1f2937;
    color: #f9fafb;
    padding: 12px;
    border-radius: 8px;
    overflow-x: auto;
    margin: 0.5rem 0;
}

.bot-message code {
    background: #f3f4f6;
    color: #1f2937;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.9em;
}

.bot-message pre code {
    background: transparent;
    color: inherit;
    padding: 0;
}

.bot-message blockquote {
    border-left: 3px solid #d1d5db;
    padding-left: 10px;
    color: #4b5563;
    margin: 0.5rem 0;
}

.bot-error-message {
    color: #dc2626 !important;
    background: #fef2f2 !important;
    border-color: #fecaca !important;
}

.input-area{
    background:#fff;
    border-top:1px solid #E5E7EB;
    padding:16px 20px;
    flex-shrink:0;
}

.input-wrapper{
    display:flex;
    gap:10px;
    width:100%;
}

#message{
    flex:1;
    min-height:52px;
    padding:14px 20px;
    border:1px solid #E5E7EB;
    border-radius:30px;
    outline:none;
    font-size:16px;
}

#message:focus{
    border-color:#2563EB;
}

.send-btn{
    background:#2563EB;
    color:white;
    border:none;
    border-radius:30px;
    min-width:100px;
    padding:0 24px;
    cursor:pointer;
    font-weight:600;
    transition:.3s;
}

.send-btn:hover{
    background:#1D4ED8;
}

.typing{
    background:#fff;
    border:1px solid #E5E7EB;
    padding:14px 18px;
    border-radius:18px;
    width:70px;
    box-shadow:0 4px 15px rgba(0,0,0,.05);
}

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(10px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

.quiz-option{
    width: 100%;
    margin-top: 10px;
    padding: 12px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.correct{
    background: #28a745 !important;
    color: white !important;
}

.wrong{
    background: #dc3545 !important;
    color: white !important;
}

/* Large Desktop */

@media (min-width:1400px){

    .chat-container{
        max-width:1200px;
    }

    .message{
        max-width:60%;
    }
}

/* Laptop */

@media (max-width:1024px){

    .chat-container{
        max-width:100%;
    }

    .message{
        max-width:75%;
    }
}

/* Tablet */

@media (max-width:768px){

    .header{
        padding:14px 16px;
    }

    .logo{
        width:42px;
        height:42px;
    }

    .bot-info h2{
        font-size:16px;
    }

    .welcome h1{
        font-size:30px;
    }

    .welcome p{
        font-size:15px;
    }

    #messages{
        padding:16px;
    }

    .message{
        max-width:85%;
        font-size:15px;
    }

    .send-btn{
        min-width:80px;
    }
}

/* Mobile */

@media (max-width:480px){

    .header{
        padding:12px;
    }

    .logo{
        width:38px;
        height:38px;
        font-size:16px;
    }

    .status{
        font-size:11px;
    }

    .welcome{
        padding:10px;
    }

    .welcome h1{
        font-size:24px;
    }

    .welcome p{
        font-size:14px;
    }

    #messages{
        padding:12px;
    }

    .message{
        max-width:92%;
        padding:12px 14px;
        font-size:14px;
    }

    .input-area{
        padding:12px;
    }

    #message{
        min-height:48px;
        font-size:16px;
        padding:12px 16px;
    }

    .send-btn{
        min-width:70px;
        padding:0 16px;
    }
}
    </style>
</head>
<body>

<div class="chat-container">

    <div class="header">
        <div class="header-left">
            <div class="logo">
                <i class="fa-solid fa-robot"></i>
            </div>

            <div class="bot-info">
                <h2>Knowledge Assistant</h2>
            </div>
        </div>
    </div>

    <div id="messages">

        <div class="welcome" id="welcome">
            <h1>Hello, how can I help you today?</h1>
            <p>Ask questions and get instant answers from our knowledge base.</p>
        </div>

    </div>

    <div class="input-area">
        <div class="input-wrapper">

            <input
                type="text"
                id="message"
                placeholder="Ask a question..."
                onkeypress="handleEnter(event)"
            >

            <button class="send-btn" onclick="sendMessage()">
                Send <i class="fa-solid fa-paper-plane"></i>
            </button>

        </div>
    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const messages = document.getElementById('messages');
    const messageInput = document.getElementById('message');
    const sendButton = document.querySelector('.send-btn');
    let quizData = [];
    let currentQuestion = 0;
    let score = 0;

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    function renderBotAnswer(answer) {
        document.getElementById('typingRow')?.remove();
        const row = document.createElement('div');
        row.className = 'message-row bot-row';
        const bubble = document.createElement('div');
        bubble.className = 'message bot-message';
        row.appendChild(bubble);
        messages.appendChild(row);

        const text = String(answer || '');
        const tokens = text.match(/\S+\s*/g) || [text];
        let i = 0;
        let shown = '';
        const step = 2;
        const delay = 18;

        const tick = () => {
            if (i >= tokens.length) {
                bubble.innerHTML = marked.parse(text);
                messages.scrollTop = messages.scrollHeight;
                return;
            }
            shown += tokens.slice(i, i + step).join('');
            i += step;
            bubble.innerHTML = marked.parse(shown);
            messages.scrollTop = messages.scrollHeight;
            setTimeout(tick, delay);
        };
        tick();
    }

    function setupEchoListener() {
        if (!window.Echo) {
            return;
        }

        try {
            window.Echo.leave('chat-channel');
        } catch (e) {}

        window.Echo.channel('chat-channel')
            .listen('.StreamChunk', (e) => {
                if (e && e.finished && e.chunk) {
                    renderBotAnswer(e.chunk);
                }
            });
    }

    setupEchoListener();
    window.addEventListener('echo:ready', () => {
        setupEchoListener();
    });

    window.handleEnter = function(event)
    {
        if(event.key === 'Enter')
        {
            sendMessage();
        }
    };

    window.sendMessage = function()
    {
        let msg = messageInput.value.trim();

        if (msg === '')
        {
            return;
        }

        document.getElementById('welcome')?.remove();

        const userRow = document.createElement('div');
        userRow.className = 'message-row user-row';

        const userBubble = document.createElement('div');
        userBubble.className = 'message user-message';
        userBubble.textContent = msg;

        userRow.appendChild(userBubble);
        messages.appendChild(userRow);

        messageInput.value = '';
        messageInput.disabled = true;
        if (sendButton) sendButton.disabled = true;

        document.getElementById('typingRow')?.remove();
        const typingRow = document.createElement('div');
        typingRow.className = 'message-row bot-row';
        typingRow.id = 'typingRow';

        typingRow.innerHTML = `
            <div class="typing">
                <i class="fa-solid fa-ellipsis"></i>
            </div>
        `;

        messages.appendChild(typingRow);

        messages.scrollTo({
            top: messages.scrollHeight,
            behavior: 'smooth'
        });

        fetch('/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: msg
            })
        })
        .then(async (response) => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                const errorMsg = data.message || data.error || ('Request failed with status ' + response.status);
                throw new Error(errorMsg);
            }
            return data;
        })
        .then((data) => {
            messageInput.disabled = false;
            if (sendButton) sendButton.disabled = false;
            messageInput.focus();

            if (data.error) {
                document.getElementById('typingRow')?.remove();
                const errorRow = document.createElement('div');
                errorRow.className = 'message-row bot-row';
                errorRow.innerHTML = `
                    <div class="message bot-message bot-error-message">
                        ${escapeHtml(data.error)}
                    </div>
                `;
                messages.appendChild(errorRow);
                messages.scrollTop = messages.scrollHeight;
                return;
            }

            if (data.type === 'quiz') {
                quizData = Array.isArray(data.quiz) ? data.quiz : [];
                currentQuestion = 0;
                score = 0;

                document.getElementById('typingRow')?.remove();
                showQuestion();
                return;
            }

            if (data.answer) {
                renderBotAnswer(data.answer);
                return;
            }

            console.log('Streaming Started');
        })
        .catch((error) => {
            messageInput.disabled = false;
            if (sendButton) sendButton.disabled = false;
            messageInput.focus();

            document.getElementById('typingRow')?.remove();

            const errorRow = document.createElement('div');
            errorRow.className = 'message-row bot-row';
            errorRow.innerHTML = `
                <div class="message bot-message bot-error-message">
                    ${escapeHtml(error.message || 'Connection Error')}
                </div>
            `;

            messages.appendChild(errorRow);
            messages.scrollTop = messages.scrollHeight;
        });
    };

    function showQuestion() {
        if (!quizData || !quizData[currentQuestion]) return;
        const q = quizData[currentQuestion];
        const row = document.createElement('div');
        row.className = 'message-row bot-row';

        const optionsHtml = (q.options || []).map((option, idx) => `
            <button
                class="quiz-option"
                onclick="checkAnswer(this, ${idx})"
            >
                ${escapeHtml(option)}
            </button>
        `).join('');

        row.innerHTML = `
            <div class="message bot-message">
                <h4>
                    Question ${currentQuestion + 1} of ${quizData.length}
                </h4>
                <p>${escapeHtml(q.question)}</p>
                ${optionsHtml}
            </div>
        `;

        messages.appendChild(row);
        messages.scrollTop = messages.scrollHeight;
    }

    window.checkAnswer = function(button, selectedIdx) {
        const q = quizData[currentQuestion];
        if (!q) return;

        const selected = (q.options && q.options[selectedIdx] !== undefined)
            ? String(q.options[selectedIdx]).trim()
            : '';
        const correctAnswer = String(q.answer || '').trim();

        const buttons = button.parentElement.querySelectorAll('.quiz-option');

        buttons.forEach(btn => {
            btn.disabled = true;

            if (btn.innerText.trim() === correctAnswer) {
                btn.classList.add('correct');
            }

            if (btn === button && selected !== correctAnswer) {
                btn.classList.add('wrong');
            }
        });

        if (selected === correctAnswer) {
            score++;
        }

        setTimeout(() => {
            currentQuestion++;
            if (currentQuestion < quizData.length) {
                showQuestion();
            } else {
                showQuizResult();
            }
        }, 1000);
    };

    function showQuizResult() {
        const row = document.createElement('div');
        row.className = 'message-row bot-row';

        row.innerHTML = `
            <div class="message bot-message">
                <h3>Quiz Completed</h3>
                <p>Score: ${score}/${quizData.length}</p>
            </div>
        `;

        messages.appendChild(row);
        messages.scrollTop = messages.scrollHeight;
    }
});

</script>
</body>
</html>
