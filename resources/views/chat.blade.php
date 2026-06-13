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
    let currentBotText = '';
    let currentBotMessage = null;
    let quizData = [];
    let currentQuestion = 0;
    let score = 0;

    if (window.Echo) {

        window.Echo.channel('chat-channel')
        .listen('.StreamChunk', (e) => {

            document.getElementById('typingRow')?.remove();

            if (!currentBotMessage) {

                const row = document.createElement('div');
                row.className = 'message-row bot-row';

                const bubble = document.createElement('div');
                bubble.className = 'message bot-message';

                row.appendChild(bubble);

                messages.appendChild(row);

                currentBotMessage = bubble;
            }

            if (!e.finished) {
                currentBotText += e.chunk + ' ';
                currentBotMessage.innerHTML = marked.parse(currentBotText);

            } else {

                currentBotMessage = null;
                currentBotText = '';

            }
            messages.scrollTop = messages.scrollHeight;
        });

    } else {

        console.error('Echo not loaded');

    }

    window.handleEnter = function(event)
    {
        if(event.key === 'Enter')
        {
            sendMessage();
        }
    }

    window.sendMessage = function()
    {
        let msg = document
            .getElementById('message')
            .value
            .trim();

        if(msg === '')
        {
            return;
        }

        currentBotMessage = null;
        currentBotText = '';
        document.getElementById('welcome')?.remove();

        const userRow = document.createElement('div');
        userRow.className = 'message-row user-row';

        const userBubble = document.createElement('div');
        userBubble.className = 'message user-message';
        userBubble.textContent = msg;

        userRow.appendChild(userBubble);

        messages.appendChild(userRow);

        document.getElementById('message').value = '';

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
        .then(response => response.json())
        .then(data => {
            if(data.type === 'quiz'){
                quizData = data.quiz;
                currentQuestion = 0;
                score = 0;

                document.getElementById('typingRow')?.remove();
                showQuestion();
                return;
            }

            console.log('Streaming Started');

        })
        .catch(error => {

            document.getElementById('typingRow')?.remove();

            const errorRow = document.createElement('div');
            errorRow.className = 'message-row bot-row';

            errorRow.innerHTML = `
                <div class="message bot-message">
                    Connection Error
                </div>
            `;

            messages.appendChild(errorRow);
        });
    }

    function showQuestion(){
        console.log('show question called');
        const q = quizData[currentQuestion];
        const row = document.createElement('div');
        row.className = 'message-row bot-row';
        row.innerHTML = `
            <div class="message bot-message">

                <h4>
                    Question ${currentQuestion + 1}
                    of
                    ${quizData.length}
                </h4>

                <p>${q.question}</p>

                ${q.options.map(option => `
                <button
                    class="quiz-option"
                    onclick="checkAnswer(this,'${option}')"
                >
                    ${option}
                </button>
                `).join('')}

            </div>
        `;

        messages.appendChild(row);
        messages.scrollTop = messages.scrollHeight;
    }

    window.checkAnswer = function(button, selected)
    {
        const q = quizData[currentQuestion];

        const buttons = button.parentElement.querySelectorAll('.quiz-option');

        buttons.forEach(btn => {

            btn.disabled = true;

            if (btn.innerText.trim() === q.answer.trim()) {
                btn.classList.add('correct');
            }

            if (
                btn.innerText.trim() === selected.trim() &&
                selected.trim() !== q.answer.trim()
            ) {
                btn.classList.add('wrong');
            }

        });

        if (selected.trim() === q.answer.trim()) {
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
    }

    function showQuizResult(){
        const row = document.createElement('div');
        row.className = 'message-row bot-row';

        row.innerHTML = `
        <div class="message bot-message">

            <h3>
                Quiz Completed
            </h3>

            <p>
                Score:
                ${score}/${quizData.length}
            </p>

        </div>
        `;

        messages.appendChild(row);

    }


});

</script>
</body>
</html>