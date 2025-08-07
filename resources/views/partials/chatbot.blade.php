<div class="chatbot-widget" id="chatbotWidget">
    <!-- Chatbot Button -->
    <div class="chatbot-button" id="chatbotButton">
        <img src="{{ asset('assets/images/assistant.png') }}" alt="Assistant" class="chatbot-avatar">
        <div class="chatbot-pulse"></div>
    </div>

    <!-- Chatbot Window -->
    <div class="chatbot-window" id="chatbotWindow">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <img src="{{ asset('assets/images/assistant.png') }}" alt="Assistant" class="chatbot-header-avatar">
                <div class="chatbot-header-text">
                    <h4>AI Assistant</h4>
                    <span class="chatbot-status">Online</span>
                </div>
            </div>
            <button class="chatbot-close" id="chatbotClose">
                <i class="ri-close-line"></i>
            </button>
        </div>

        <!-- Messages Area -->
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="chatbot-message bot-message">
                <img src="{{ asset('assets/images/assistant.png') }}" alt="Assistant" class="message-avatar">
                <div class="message-content">
                    <p>Xin chào! Tôi là AI Assistant. Tôi có thể giúp gì cho bạn hôm nay?</p>
                    <span class="message-time">{{ date('H:i') }}</span>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="chatbot-input">
            <div class="input-wrapper">
                <button class="quick-action-btn" id="quickActionBtn">
                    <i class="ri-menu-line"></i>
                </button>
                <input type="text" class="form-control" id="chatbotInput" placeholder="Nhập tin nhắn...">
                <button class="send-btn" id="chatbotSend">
                    <i class="ri-send-plane-2-line"></i>
                </button>
            </div>

            <!-- Quick Actions Menu -->
            <div class="quick-actions-menu" id="quickActionsMenu">
                <div class="quick-action-item" data-action="help">
                    <i class="ri-question-line"></i>
                    <span>Trợ giúp</span>
                </div>
                <div class="quick-action-item" data-action="contact">
                    <i class="ri-phone-line"></i>
                    <span>Liên hệ</span>
                </div>
                <div class="quick-action-item" data-action="info">
                    <i class="ri-information-line"></i>
                    <span>Thông tin</span>
                </div>
                <div class="quick-action-item" data-action="settings">
                    <i class="ri-settings-line"></i>
                    <span>Cài đặt</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatbotButton = document.getElementById('chatbotButton');
        const chatbotWindow = document.getElementById('chatbotWindow');
        const chatbotClose = document.getElementById('chatbotClose');
        const chatbotInput = document.getElementById('chatbotInput');
        const chatbotSend = document.getElementById('chatbotSend');
        const chatbotMessages = document.getElementById('chatbotMessages');
        const quickActionBtn = document.getElementById('quickActionBtn');
        const quickActionsMenu = document.getElementById('quickActionsMenu');

        let isOpen = false;
        let isMenuOpen = false;

        // Toggle chatbot window
        chatbotButton.addEventListener('click', function() {
            if (isOpen) {
                closeChatbot();
            } else {
                openChatbot();
            }
        });

        // Close chatbot
        chatbotClose.addEventListener('click', function() {
            closeChatbot();
        });

        // Send message on Enter
        chatbotInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // Send message on button click
        chatbotSend.addEventListener('click', function() {
            sendMessage();
        });

        // Toggle quick actions menu
        quickActionBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (isMenuOpen) {
                closeQuickActionsMenu();
            } else {
                openQuickActionsMenu();
            }
        });

        // Handle quick action clicks
        document.querySelectorAll('.quick-action-item').forEach(item => {
            item.addEventListener('click', function() {
                const action = this.dataset.action;
                handleQuickAction(action);
                closeQuickActionsMenu();
            });
        });

        function openChatbot() {
            chatbotWindow.classList.add('active');
            isOpen = true;
            // Focus on input
            setTimeout(() => {
                chatbotInput.focus();
            }, 300);
        }

        function closeChatbot() {
            chatbotWindow.classList.remove('active');
            isOpen = false;
            closeQuickActionsMenu();
        }

        function openQuickActionsMenu() {
            quickActionsMenu.classList.add('active');
            quickActionBtn.classList.add('active');
            isMenuOpen = true;
        }

        function closeQuickActionsMenu() {
            quickActionsMenu.classList.remove('active');
            quickActionBtn.classList.remove('active');
            isMenuOpen = false;
        }

        function sendMessage() {
            const message = chatbotInput.value.trim();
            if (message === '') return;

            // Close quick actions menu if open
            if (isMenuOpen) {
                closeQuickActionsMenu();
            }

            // Add user message
            addMessage(message, 'user');
            chatbotInput.value = '';

            // Show typing indicator
            showTypingIndicator();

            // Simulate bot response (replace with actual API call)
            setTimeout(() => {
                hideTypingIndicator();
                const responses = [
                    "Hiện tại bạn không có quyền truy cập chức năng này.",
                ];
                const randomResponse = responses[Math.floor(Math.random() * responses.length)];
                addMessage(randomResponse, 'bot');
            }, 1500);
        }

        function handleQuickAction(action) {
            let message = '';
            switch (action) {
                case 'help':
                    message = 'Trợ giúp';
                    break;
                case 'contact':
                    message = 'Thông tin';
                    break;
                case 'info':
                    message = 'Thông tin về câu lạc bộ';
                    break;
                case 'settings':
                    message = 'Cài đặt';
                    break;
                default:
                    return;
            }

            chatbotInput.value = message;
            sendMessage();
        }

        function addMessage(message, sender) {
            const messageDiv = document.createElement('div');
            messageDiv.className = `chatbot-message ${sender}-message`;

            const now = new Date();
            const timeString = now.toLocaleTimeString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit'
            });

            if (sender === 'user') {
                messageDiv.innerHTML = `
                <div class="message-content">
                    <p>${escapeHtml(message)}</p>
                    <span class="message-time">${timeString}</span>
                </div>
            `;
            } else {
                messageDiv.innerHTML = `
                <img src="{{ asset('assets/images/assistant.png') }}" alt="Assistant" class="message-avatar">
                <div class="message-content">
                    <p>${escapeHtml(message)}</p>
                    <span class="message-time">${timeString}</span>
                </div>
            `;
            }

            chatbotMessages.appendChild(messageDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        function showTypingIndicator() {
            const typingDiv = document.createElement('div');
            typingDiv.className = 'chatbot-message bot-message';
            typingDiv.id = 'typingIndicator';
            typingDiv.innerHTML = `
            <img src="{{ asset('assets/images/assistant.png') }}" alt="Assistant" class="message-avatar">
            <div class="message-content">
                <div class="typing-indicator">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        `;

            chatbotMessages.appendChild(typingDiv);
            chatbotMessages.scrollTop = chatbotMessages.scrollHeight;
        }

        function hideTypingIndicator() {
            const typingIndicator = document.getElementById('typingIndicator');
            if (typingIndicator) {
                typingIndicator.remove();
            }
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) {
                return map[m];
            });
        }

        // Close chatbot when clicking outside
        document.addEventListener('click', function(event) {
            if (isOpen && !chatbotButton.contains(event.target) && !chatbotWindow.contains(event
                    .target)) {
                closeChatbot();
            }

            // Close quick actions menu when clicking outside
            if (isMenuOpen && !quickActionBtn.contains(event.target) && !quickActionsMenu.contains(event
                    .target)) {
                closeQuickActionsMenu();
            }
        });
    });
</script>

<style>
    .chatbot-widget {
        position: fixed;
        bottom: 70px;
        right: 20px;
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }

    .chatbot-button {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
        position: relative;
        animation: float 3s ease-in-out infinite;
    }

    .chatbot-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    }

    .chatbot-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
    }

    .chatbot-pulse {
        position: absolute;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(102, 126, 234, 0.3);
        animation: pulse 2s infinite;
    }

    .chatbot-window {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 380px;
        height: 600px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        display: none;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }

    .chatbot-window.active {
        display: flex;
        animation: slideUp 0.3s ease;
    }

    .chatbot-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .chatbot-header-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .chatbot-header-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .chatbot-header-text h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .chatbot-status {
        font-size: 12px;
        opacity: 0.8;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .chatbot-status::before {
        content: '';
        width: 6px;
        height: 6px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
    }

    .chatbot-close {
        background: rgba(255, 255, 255, 0.1);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .chatbot-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .chatbot-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 16px;
        background: #f8fafc;
    }

    .chatbot-message {
        display: flex;
        gap: 10px;
        max-width: 85%;
    }

    .bot-message {
        align-self: flex-start;
    }

    .user-message {
        align-self: flex-end;
        flex-direction: row-reverse;
    }

    .user-message .message-content {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .message-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .message-content {
        background: white;
        padding: 12px 12px;
        border-radius: 16px;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        position: relative;
    }

    .user-message .message-content {
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 4px;
    }

    .message-content p {
        margin: 0;
        font-size: 13px;
        line-height: 1.4;
    }

    .message-time {
        font-size: 11px;
        color: #6b7280;
        opacity: 0.7;
    }

    .user-message .message-time {
        color: rgba(255, 255, 255, 0.7);
    }

    .chatbot-input {
        padding: 10px;
        background: white;
        border-top: 1px solid #e5e7eb;
        position: relative;
    }

    .input-wrapper {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #d1d5db;
        border-radius: 24px;
        padding: 4px;
        transition: all 0.2s ease;
    }

    .input-wrapper:focus-within {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .quick-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: #e5e7eb;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-right: 8px;
        flex-shrink: 0;
    }

    .quick-action-btn:hover {
        background: #d1d5db;
        color: #374151;
    }

    .quick-action-btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .chatbot-input .form-control {
        flex: 1;
        border: none;
        background: transparent;
        outline: none;
        font-size: 14px;
        margin: 0;
    }

    .send-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-left: 8px;
        flex-shrink: 0;
    }

    .send-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .send-btn:disabled {
        background: #d1d5db;
        color: #9ca3af;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .quick-actions-menu {
        position: absolute;
        bottom: 70px;
        left: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        border: 1px solid #e5e7eb;
        padding: 8px;
        min-width: 180px;
        display: none;
        z-index: 1000;
    }

    .quick-actions-menu.active {
        display: block;
        animation: slideUpMenu 0.2s ease;
    }

    .quick-action-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
        color: #374151;
    }

    .quick-action-item:hover {
        background: #f3f4f6;
        color: #667eea;
    }

    .quick-action-item i {
        font-size: 16px;
        width: 16px;
        text-align: center;
    }

    .chatbot-input .input-group {
        display: flex;
        gap: 10px;
    }

    .chatbot-input .btn {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: all 0.2s ease;
    }

    .chatbot-input .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    /* Animations */
    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-6px);
        }
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        100% {
            transform: scale(1.3);
            opacity: 0;
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Scrollbar styling */
    .chatbot-messages::-webkit-scrollbar {
        width: 4px;
    }

    .chatbot-messages::-webkit-scrollbar-track {
        background: transparent;
    }

    .chatbot-messages::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 2px;
    }

    .chatbot-messages::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .chatbot-widget {
            bottom: 130px;
            right: 15px;
        }

        .chatbot-window {
            width: calc(100vw - 30px);
            height: 60vh;
            bottom: 80px;
            right: -10px;
        }

        .chatbot-button {
            width: 70px;
            height: 70px;
        }

        .chatbot-avatar {
            width: 35px;
            height: 35px;
        }
    }

    /* Typing indicator */
    .typing-indicator {
        display: flex;
        gap: 4px;
        padding: 12px 16px;
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #9ca3af;
        animation: typing 1.4s infinite ease-in-out;
    }

    .typing-dot:nth-child(1) {
        animation-delay: -0.32s;
    }

    .typing-dot:nth-child(2) {
        animation-delay: -0.16s;
    }

    @keyframes typing {

        0%,
        80%,
        100% {
            transform: scale(0.8);
            opacity: 0.5;
        }

        40% {
            transform: scale(1);
            opacity: 1;
        }
    }

    @keyframes slideUpMenu {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
