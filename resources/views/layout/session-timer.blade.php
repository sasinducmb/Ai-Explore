@auth
@php
    $user = Auth::user();
    $isAdmin = $user->email === 'admin@admin.com' || ($user->is_admin ?? false);
@endphp

@if(!$isAdmin)
<!-- Session Timer -->
<div id="session-timer" class="fixed top-4 right-4 bg-white border border-gray-300 rounded-lg shadow-lg px-4 py-2 z-50 hidden">
    <div class="flex items-center space-x-2">
        <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
        </svg>
        <span class="text-sm font-medium text-gray-700">Session expires in:</span>
        <span id="timer-display" class="text-sm font-bold text-orange-600">30:00</span>
    </div>
</div>

<!-- Session Warning Modal -->
<div id="session-warning-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md mx-4">
        <div class="flex items-center mb-4">
            <svg class="w-8 h-8 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900">Session Expiring Soon</h3>
        </div>
        <p class="text-gray-600 mb-6">Your session will expire in <span id="warning-timer" class="font-bold text-red-600">5:00</span>. Would you like to extend your session?</p>
        <div class="flex space-x-3">
            <button id="extend-session-btn" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                Extend Session
            </button>
            <button id="logout-now-btn" class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition duration-200">
                Logout Now
            </button>
        </div>
    </div>
</div>

<script>
class SessionManager {
    constructor() {
        this.timeoutDuration = {{ session('session_timeout', 1800) }}; // 30 minutes in seconds
        this.warningTime = 5 * 60; // Show warning 5 minutes before timeout
        this.lastActivity = {{ session('last_activity', time()) }};
        this.currentTime = Math.floor(Date.now() / 1000);

        this.timerElement = document.getElementById('timer-display');
        this.sessionTimer = document.getElementById('session-timer');
        this.warningModal = document.getElementById('session-warning-modal');
        this.warningTimer = document.getElementById('warning-timer');

        this.warningShown = false;
        this.interval = null;

        this.init();
    }

    init() {
        this.startTimer();
        this.setupEventListeners();
        this.setupActivityTracking();
    }

    startTimer() {
        this.interval = setInterval(() => {
            this.updateTimer();
        }, 1000);

        // Show timer after 2 seconds
        setTimeout(() => {
            this.sessionTimer.classList.remove('hidden');
        }, 2000);
    }

    updateTimer() {
        const now = Math.floor(Date.now() / 1000);
        const elapsed = now - this.lastActivity;
        const remaining = this.timeoutDuration - elapsed;

        if (remaining <= 0) {
            this.handleTimeout();
            return;
        }

        // Show warning when 5 minutes remaining
        if (remaining <= this.warningTime && !this.warningShown) {
            this.showWarning();
        }

        this.updateDisplay(remaining);
    }

    updateDisplay(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        const display = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;

        this.timerElement.textContent = display;

        // Update warning timer if warning is shown
        if (this.warningShown) {
            this.warningTimer.textContent = display;
        }

        // Change color when time is running out
        if (seconds <= 300) { // 5 minutes
            this.timerElement.className = 'text-sm font-bold text-red-600';
        } else if (seconds <= 600) { // 10 minutes
            this.timerElement.className = 'text-sm font-bold text-orange-600';
        } else {
            this.timerElement.className = 'text-sm font-bold text-green-600';
        }
    }

    showWarning() {
        this.warningShown = true;
        this.warningModal.classList.remove('hidden');

        // Auto-hide timer display when warning is shown
        this.sessionTimer.classList.add('hidden');
    }

    hideWarning() {
        this.warningShown = false;
        this.warningModal.classList.add('hidden');
        this.sessionTimer.classList.remove('hidden');
    }

    extendSession() {
        // Make AJAX request to extend session
        fetch('/extend-session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.lastActivity = Math.floor(Date.now() / 1000);
                this.hideWarning();
                this.showNotification('Session extended successfully!', 'success');
            } else {
                this.showNotification('Failed to extend session', 'error');
            }
        })
        .catch(error => {
            console.error('Error extending session:', error);
            this.showNotification('Error extending session', 'error');
        });
    }

    handleTimeout() {
        clearInterval(this.interval);

        // Make logout request
        fetch('/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(() => {
            window.location.href = '/login?timeout=1';
        })
        .catch(() => {
            window.location.href = '/login?timeout=1';
        });
    }

    setupEventListeners() {
        document.getElementById('extend-session-btn').addEventListener('click', () => {
            this.extendSession();
        });

        document.getElementById('logout-now-btn').addEventListener('click', () => {
            this.handleTimeout();
        });

        // Close warning on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.warningShown) {
                this.hideWarning();
            }
        });
    }

    setupActivityTracking() {
        // Track user activity to update last activity time
        const activityEvents = ['mousedown', 'keydown', 'scroll', 'touchstart'];
        let lastUpdate = Date.now();

        activityEvents.forEach(event => {
            document.addEventListener(event, () => {
                const now = Date.now();
                // Update server every 5 minutes of activity
                if (now - lastUpdate > 5 * 60 * 1000) {
                    this.updateActivity();
                    lastUpdate = now;
                }
            });
        });
    }

    updateActivity() {
        fetch('/update-activity', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.lastActivity = Math.floor(Date.now() / 1000);
            }
        })
        .catch(error => {
            console.error('Error updating activity:', error);
        });
    }

    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Initialize session manager when page loads
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('session-timer')) {
        new SessionManager();
    }
});
</script>
@endif
@endauth
