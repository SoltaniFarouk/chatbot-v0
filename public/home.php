<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <link rel="icon" href='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y="0.9em" font-size="90">🤖</text></svg>'>
  <title>V0 Chatbot</title>

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/table.css">
</head>

<body>

<button class="chat-btn" onclick="toggleChat()">Chat</button>

<div class="chat-box" id="chatBox">
  <div class="chat-header">My Messenger Bot</div>

  <div class="chat-messages" id="messages"></div>

  <div class="chat-input">
    <input type="text" id="userInput" placeholder="Type a message...">
    <button onclick="sendMessage()">Send</button>
  </div>
</div>

<!-- TABLE VISITES -->
<div class="table-wrapper">

  <div class="table-container">
    <h2> visitation schedule </h2>

    <select id="limitSelect">
      <option value="10">10</option>
      <option value="15">15</option>
      <option value="20">20</option>
      <option value="25">25</option>
    </select>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Conversation_ID</th>
          <th>Question</th>
        </tr>
      </thead>

      <tbody id="tableBody"></tbody>
    </table>

    <div class="pagination" id="pagination"></div>
  </div>

</div>

<script src="js/script.js"></script>
<script src="js/visits.js"></script>

</body>
</html>