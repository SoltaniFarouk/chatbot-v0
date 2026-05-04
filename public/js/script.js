// ============ API SERVICE ============
const ApiService = {
    async post(url, body) {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        return await response.json();
    },

    async get(url) {
        const response = await fetch(url);
        return await response.json();
    }
};
// ============ VALIDATION SERVICE ============
const ConversationAnswerService = {
    async save(conversationId, questionId, valueText = null, valueInt = null, valueDecimal = null, isValid = true, rawInput = null) {
        //console.log('A conversationId:', conversationId);
        //console.log('A questionId:', questionId);
        //console.log('A valueText:', valueText);
        //console.log('A valueInt:', valueInt);
        //console.log('A valueDecimal:', valueDecimal);
        //console.log('A isValid:', isValid);
        //console.log('A rawInput:', rawInput);

        return await ApiService.post('/api/conversation-answer', {
            conversation_id: conversationId,
            question_id:     questionId,
            value_text:      valueText,
            value_int:       valueInt,
            value_decimal:   valueDecimal,
            is_valid:        isValid,
            raw_input:       rawInput
        });
    }
};
// ============ VALIDATION SERVICE ============
const ValidationService = {

    validate(question, value) {
        const description = question.description.toLowerCase();

        // Email validation
        if (description.includes('email')) {
            return this.validateEmail(value);
        }

        // Age validation
        if (description.includes('old') || description.includes('age')) {
            return this.validateAge(value);
        }

        // Username validation
        if (description.includes('username')) {
            return this.validateUsername(value);
        }

        // Family status validation
        if (description.includes('family status')) {
            return this.validateFamilyStatus(value);
        }

        // Budget validation
        if (description.includes('budget')) {
            return this.validateBudget(value);
        }

        // Number of people validation
        if (description.includes('how many') || description.includes('covered')) {
            return this.validateNumberCovered(value);
        }

        // Default - accept any non-empty value
        return { valid: true };
    },

    validateEmail(value) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regex.test(value)) {
            return {
                valid: false,
                message: 'Please enter a valid email address. Example: name@example.com'
            };
        }
        return { valid: true };
    },

    validateAge(value) {
        const age = parseInt(value);
        if (isNaN(age) || age < 18 || age > 120) {
            return {
                valid: false,
                message: 'Please enter a valid age between 18 and 120.'
            };
        }
        return { valid: true, parsed: age };
    },

    validateUsername(value) {
        if (value.length < 3 || value.length > 150) {
            return {
                valid: false,
                message: 'Username must be between 3 and 150 characters.'
            };
        }
        if (!/^[a-zA-Z0-9_]+$/.test(value)) {
            return {
                valid: false,
                message: 'Username can only contain letters, numbers and underscores.'
            };
        }
        return { valid: true };
    },

    validateFamilyStatus(value) {
        const allowed = ['single', 'married', 'divorced', 'widowed'];
        if (!allowed.includes(value.toLowerCase())) {
            return {
                valid: false,
                message: 'Please enter one of: single, married, divorced, widowed.'
            };
        }
        return { valid: true, parsed: value.toLowerCase() };
    },

    validateBudget(value) {
        const budget = parseFloat(value);
        if (isNaN(budget) || budget <= 0) {
            return {
                valid: false,
                message: 'Please enter a valid budget amount. Example: 5000'
            };
        }
        return { valid: true, parsed: budget };
    },

    validateNumberCovered(value) {
        const num = parseInt(value);
        if (isNaN(num) || num < 1 || num > 20) {
            return {
                valid: false,
                message: 'Please enter a valid number between 1 and 20.'
            };
        }
        return { valid: true, parsed: num };
    }
};
// ============ HELPER ============
function addMessage(type, text) {
    const messages = document.getElementById('messages');
    const msg = document.createElement('div');
    msg.className = 'message ' + type;
    msg.innerHTML = text;
    messages.appendChild(msg);
    messages.scrollTop = messages.scrollHeight;
}

// ==============================================================================
// ============ MAIN LOGIC ============

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


async function sendMessage() {
    const input = document.getElementById('userInput');
    const text  = input.value.trim();
    if (text === '') return;

    addMessage('user', 'You: ' + text);
    input.value = '';

    try {
        const conversationId  = parseInt(localStorage.getItem('chatbot20_conversation_id'));
        const questions       = JSON.parse(localStorage.getItem('chatbot20_questions'));
        const currentStep     = parseInt(localStorage.getItem('chatbot20_current_step'));
        const currentQuestion = questions[currentStep];


        // Step 1 - Validate answer
        const validation = ValidationService.validate(currentQuestion, text);
     
        // Step 2 - Always save the answer (valid or not)
        const description = currentQuestion.description.toLowerCase();
        let value_text    = null;
        let value_int     = null;
        let value_decimal = null;

        if (validation.valid) {
            if (description.includes('old') || description.includes('age') || description.includes('how many') || description.includes('covered')) {
                value_int = parseInt(text);
            } else if (description.includes('budget')) {
                value_decimal = parseFloat(text);
            } else {
                value_text = text;
            }

            // if Step is email, used api/user/fast to create user immediately and link to conversation
            if (description.includes('email')) {
                try {
                    // Create user with minimal info (email only)
                    const userResponse = await ApiService.post('/api/user/fast', {
                        email: text
                    });
                    if (userResponse.success) {
                        const userId = userResponse.data.user_id;
                        console.log('User created with ID:', userId);
                    }
                } catch (error) {
                    console.error('Error creating user:', error);
                }
            }           
        }


        //add console log to check values before saving
        console.log('Saving answer:', {
            conversation_id: conversationId,
            question_id: currentQuestion.id,
            value_text: value_text,
            value_int: value_int,
            value_decimal: value_decimal,
            is_valid: validation.valid,
            raw_input: text
        });
        

        // Step 3 - Save to API (always - valid or invalid)
        await ConversationAnswerService.save(
            conversationId,
            currentQuestion.step_order,
            value_text,
            value_int,
            value_decimal,
            validation.valid,  // is_valid
            text               // raw_input - always save what user typed
        );

        // Step 4 - If invalid, ask again
        if (!validation.valid) {
            addMessage('bot', 'Bot: ⚠️ ' + validation.message);
            return; // Stay on same question
        }

        // Step 5 - If valid, move to next question
        const nextStep = currentStep + 1;

        if (nextStep < questions.length) {
            localStorage.setItem('chatbot20_current_step', nextStep);
            addMessage('bot', 'Bot: ✅ Got it! ' + questions[nextStep].description);
        } else {
            addMessage('bot', 'Bot: ✅ Thank you! We have all your information. We will find the best package for you! 🎉');
            localStorage.removeItem('chatbot20_conversation_id');
            localStorage.removeItem('chatbot20_questions');
            localStorage.removeItem('chatbot20_current_step');
        }

    } catch (error) {
        console.error('Send message error:', error);
        addMessage('bot', 'Bot: Sorry, something went wrong. Please try again.');
    }
}
//-----
async function displayWelcomeMessage(visitor) {
    const messages = document.getElementById('messages');

    // Welcome message
    const welcomeMsg = document.createElement('div');
    welcomeMsg.className = 'message bot';

    if (visitor && visitor.user && visitor.user.username) {
        welcomeMsg.innerHTML = 'Bot: Hello ' + visitor.user.username + '! I can help you find the best health insurance package.';
        messages.appendChild(welcomeMsg);
    } else {
        welcomeMsg.innerHTML = 'Bot: Hello! I can help you find the best health insurance package. To get started, please answer a few questions.';
        messages.appendChild(welcomeMsg);

        try {
            // Step 1 - Get all questions from API
            const questionResponse = await fetch('/api/question');
            const questionData = await questionResponse.json();

            if (questionData.success && questionData.data.length > 0) {

                // Step 2 - Create new conversation
                const conversationResponse = await fetch('/api/conversation', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        visitor_id: visitor.visitor_id
                    })
                });
                const conversationData = await conversationResponse.json();

                if (conversationData.success) {
                    const conversationId = conversationData.data.conversation_id;

                    // Step 3 - Store conversation_id and questions in localStorage
                    localStorage.setItem('chatbot20_conversation_id', conversationId);
                    localStorage.setItem('chatbot20_questions', JSON.stringify(questionData.data));
                    localStorage.setItem('chatbot20_current_step', 0);

                    console.log('Conversation created:', conversationId);

                    // Step 4 - Display first question
                    const firstQuestion = questionData.data[0];
                    const questionMsg = document.createElement('div');
                    questionMsg.className = 'message bot';
                    questionMsg.innerHTML = 'Bot: ' + firstQuestion.description;
                    messages.appendChild(questionMsg);

                    console.log('First question:', firstQuestion.description);
                }
            }

        } catch (error) {
            console.error('Failed to start conversation:', error);
        }
    }

    messages.scrollTop = messages.scrollHeight;
}



window.onload = async function () {
    const visitor = await checkToken();
    await displayWelcomeMessage(visitor);
};