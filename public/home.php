<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="0.9em" font-size="90">🤖</text></svg>'>
  <title>V0 Chatbot</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="center-text">
  <h1>Welcome to My Chatbot Project</h1>
  <p>Click the button to open the chatbot</p>
</div>

<button class="chat-btn" onclick="toggleChat()">Chat</button>

<div class="chat-box" id="chatBox">
  <div class="chat-header">My Messenger Bot</div>
  <div class="chat-messages" id="messages">
    <div class="message bot">Bot: Hello! How can I help you?</div>
  </div>
  <div class="chat-input">
    <input type="text" id="userInput" placeholder="Type a message...">
    <button onclick="sendMessage()">Send</button>
  </div>
</div>

<script src="js/script.js"></script>
</body>
</html>