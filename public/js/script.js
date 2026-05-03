  function toggleChat() {
    const box = document.getElementById("chatBox");
    box.style.display = box.style.display === "flex" ? "none" : "flex";
  }

  function sendMessage() {
    const input = document.getElementById("userInput");
    const messages = document.getElementById("messages");

    let text = input.value.trim();
    if (text === "") return;

    // User message
    let userMsg = document.createElement("div");
    userMsg.className = "message user";
    userMsg.innerHTML = "You: " + text;
    messages.appendChild(userMsg);

    // Simple bot response
    let botMsg = document.createElement("div");
    botMsg.className = "message bot";

    if (text.toLowerCase() === "hello") {
      botMsg.innerHTML = "Bot: Hello 👋 how are you?";
    } else {
      botMsg.innerHTML = "Bot: I received: " + text;
    }

    messages.appendChild(botMsg);

    input.value = "";
    messages.scrollTop = messages.scrollHeight;  
  }