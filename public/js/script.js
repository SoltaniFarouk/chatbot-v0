async function registerVisitor() {
    try {
        const response = await fetch('/api/visitor/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                terminal: getTerminalType(),
                user_id: null
            })
        });

        const data = await response.json();

        if (data.success) {
            localStorage.setItem('chatbot20_token', data.data.visitor_token);
            console.log('New token created:', data.data.visitor_token);
            return data.data.visitor_token;
        } else {
            console.error('Registration failed:', data.message);
            return null;
        }
    } catch (error) {
        console.error('API error:', error);
        return null;
    }
}

function getTerminalType() {
    const ua = navigator.userAgent;
    if (/mobile/i.test(ua)) return 'mobile';
    if (/tablet|ipad/i.test(ua)) return 'tablet';
    return 'desktop';
}

async function checkToken() {
    const token = localStorage.getItem('chatbot20_token');

    if (token) {
        //console.log('Token exists:', token);
        //return token;
        try {
            const response = await fetch('/api/visitor/' + token);
            const data = await response.json();

            if (data.success) {
                console.log('Token exists:', token);
                console.log('Visitor ID:', data.data.visitor_id);
                console.log('User ID:', data.data.user_id);
                return data.data;
            } else {
                // Token exists in localStorage but not in DB
                console.log('Token not found in DB, registering new visitor...');
                localStorage.removeItem('chatbot20_token');
                localStorage.removeItem('chatbot20_visitor_id');
                return await registerVisitor();
            }

        } catch (error) {
            console.error('API error:', error);
            return null;
        }
    } else {
        console.log('No token found, registering visitor...');
        const newToken = await registerVisitor();
        return newToken;
    }
}

function toggleChat() {
    const box = document.getElementById('chatBox');
    box.style.display = box.style.display === 'flex' ? 'none' : 'flex';
}

function sendMessage() {
    const input = document.getElementById('userInput');
    const messages = document.getElementById('messages');

    let text = input.value.trim();
    if (text === '') return;

    // User message
    let userMsg = document.createElement('div');
    userMsg.className = 'message user';
    userMsg.innerHTML = 'You: ' + text;
    messages.appendChild(userMsg);

    // Simple bot response
    let botMsg = document.createElement('div');
    botMsg.className = 'message bot';

    if (text.toLowerCase() === 'hello') {
        botMsg.innerHTML = 'Bot: Hello 👋 how are you?';
    } else {
        botMsg.innerHTML = 'Bot: I received: ' + text;
    }

    messages.appendChild(botMsg);

    input.value = '';
    messages.scrollTop = messages.scrollHeight;
}

window.onload = async function () {
    await checkToken();
};