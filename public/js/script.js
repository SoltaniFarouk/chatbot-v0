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

            // Return full visitor object
            return {
                visitor_id:    data.data.visitor_id,
                visitor_token: data.data.visitor_token,
                terminal:      data.data.terminal,
                ip_address:    data.data.ip_address,
                created_at:    data.data.created_at,
                user:          null
            };
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
        try {
            // Step 1 - Get visitor by token
            const visitorResponse = await fetch('/api/visitor/' + token);
            const visitorData = await visitorResponse.json();

            if (visitorData.success) {
                // Step 2 - Build visitor object
                const visitor = {
                    visitor_id:     visitorData.data.visitor_id,
                    visitor_token:  visitorData.data.visitor_token,
                    terminal:       visitorData.data.terminal,
                    ip_address:     visitorData.data.ip_address,
                    created_at:     visitorData.data.created_at,
                    user:           null
                };

                // Step 3 - If user_id exists, get user data
                if (visitorData.data.user_id) {
                    const userResponse = await fetch('/api/user/' + visitorData.data.user_id);
                    const userData = await userResponse.json();

                    if (userData.success) {
                        visitor.user = {
                            user_id:        userData.data.user_id,
                            username:       userData.data.username,
                            email:          userData.data.email,
                            address:        userData.data.address,
                            age:            userData.data.age,
                            phone_number:   userData.data.phone_number,
                            number_covered: userData.data.number_covered,
                            family_status:  userData.data.family_status,
                            is_enabled:     userData.data.is_enabled,
                            created_at:     userData.data.created_at
                        };

                        console.log('Welcome back:', visitor.user.username);
                    }
                } else {
                    console.log('Visitor is anonymous (no user linked)');
                }

                console.log('Visitor object:', visitor);
                return visitor;

            } else {
                // Token not found in DB - register new visitor
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
        return await registerVisitor();
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

function displayWelcomeMessage(visitor) {
    const messages = document.getElementById('messages');
    const botMsg = document.createElement('div');
    botMsg.className = 'message bot';

    if (visitor && visitor.user && visitor.user.username) {
        botMsg.innerHTML = 'Bot: Hello ' + visitor.user.username + '! I can help you find the best health insurance package. To get started';
    } else {
        botMsg.innerHTML = 'Bot: Hello! I can help you find the best health insurance package. To get started';
    }

    messages.appendChild(botMsg);
}

window.onload = async function () {
    //await checkToken();
    const visitor = await checkToken();
    displayWelcomeMessage(visitor);
};