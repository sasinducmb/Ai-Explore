@auth
@php
    $user = Auth::user();
    $isAdmin = $user->email === 'admin@example.com' ||
               ($user->role ?? null) === 'ADMIN' ||
               ($user->is_admin ?? false);
@endphp

@if(!$isAdmin)
<!-- Modern Session Timer -->
<div id="session-timer" class="fixed top-6 right-6 bg-white border-2 border-blue-200 rounded-2xl shadow-2xl px-6 py-4 z-50 hidden transform transition-all duration-300 hover:scale-105">
    <div class="flex items-center space-x-4">
        <!-- Timer Icon with Animation -->
        <div class="relative">
            <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <!-- Pulse Ring Animation -->
            <div class="absolute inset-0 w-12 h-12 bg-blue-400 rounded-full animate-ping opacity-20"></div>
        </div>

        <!-- Timer Content -->
        <div class="flex flex-col">
            <span class="text-sm font-semibold text-gray-600 uppercase tracking-wider">Session Expires</span>
            <div class="flex items-center space-x-2">
                <span id="timer-display" class="text-2xl font-bold text-blue-600 font-mono">30:00</span>
                <div class="flex flex-col space-y-1">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-bounce" style="animation-delay: 0ms;"></div>
                    <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce" style="animation-delay: 150ms;"></div>
                    <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 300ms;"></div>
                </div>
            </div>
        </div>

        <!-- Extend Button -->
        <button id="extend-session-mini" class="ml-2 px-3 py-2 bg-blue-500 text-white text-xs font-medium rounded-lg hover:bg-blue-600 transition-colors duration-200 shadow-md hover:shadow-lg">
            Extend
        </button>
    </div>

    <!-- Progress Bar -->
    <div class="mt-3 w-full bg-gray-200 rounded-full h-2 overflow-hidden">
        <div id="session-progress" class="h-2 bg-gradient-to-r from-green-400 via-yellow-400 to-red-500 rounded-full transition-all duration-1000 ease-linear" style="width: 100%"></div>
    </div>
</div>

<!-- Compact Mobile Timer -->
<div id="mobile-session-timer" class="fixed top-4 right-4 lg:hidden bg-white border-2 border-blue-200 rounded-xl shadow-lg px-4 py-3 z-50 hidden">
    <div class="flex items-center space-x-3">
        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <div>
            <div class="text-xs text-gray-500 uppercase font-medium">Session</div>
            <div id="mobile-timer-display" class="text-lg font-bold text-blue-600 font-mono">30:00</div>
        </div>
    </div>
</div>

<!-- Enhanced Session Warning Modal -->
<div id="session-warning-modal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 hidden backdrop-blur-sm">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md mx-4 transform transition-all duration-300 scale-95 hover:scale-100">
        <!-- Header with Icon -->
        <div class="flex items-center justify-center mb-6">
            <div class="w-20 h-20 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-full flex items-center justify-center shadow-lg">
                <svg class="w-10 h-10 text-white animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
        </div>

        <!-- Content -->
        <div class="text-center mb-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">⏰ Session Expiring Soon!</h3>
            <p class="text-gray-600 text-lg leading-relaxed">
                Your session will expire in
                <span id="warning-timer" class="font-bold text-2xl text-red-600 bg-red-50 px-3 py-1 rounded-lg">5:00</span>
            </p>
            <p class="text-gray-500 mt-3">Would you like to extend your session to continue working?</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <button id="extend-session-btn" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Extend Session
                </span>
            </button>
            <button id="logout-now-btn" class="flex-1 bg-gradient-to-r from-gray-400 to-gray-500 text-white px-6 py-4 rounded-xl hover:from-gray-500 hover:to-gray-600 transition-all duration-200 font-semibold text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <span class="flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout Now
                </span>
            </button>
        </div>

        <!-- Close Button -->
        <button id="close-warning" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors duration-200">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<!-- Floating Extension Success Notification -->
<div id="extension-notification" class="fixed top-6 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-xl shadow-2xl z-50 hidden">
    <div class="flex items-center space-x-3">
        <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <span class="font-semibold">Session extended successfully!</span>
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
        this.mobileTimerElement = document.getElementById('mobile-timer-display');
        this.sessionTimer = document.getElementById('session-timer');
        this.mobileSessionTimer = document.getElementById('mobile-session-timer');
        this.warningModal = document.getElementById('session-warning-modal');
        this.warningTimer = document.getElementById('warning-timer');
        this.progressBar = document.getElementById('session-progress');
        this.extendMiniBtn = document.getElementById('extend-session-mini');
        this.extensionNotification = document.getElementById('extension-notification');

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

        // Show timer after 3 seconds with animation
        setTimeout(() => {
            this.sessionTimer.classList.remove('hidden');
            this.mobileSessionTimer.classList.remove('hidden');
            this.sessionTimer.style.transform = 'translateX(0)';
        }, 3000);
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
        this.updateProgressBar(remaining);
    }

    updateDisplay(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        const display = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;

        this.timerElement.textContent = display;
        this.mobileTimerElement.textContent = display;

        // Update warning timer if warning is shown
        if (this.warningShown) {
            this.warningTimer.textContent = display;
        }

        // Change colors based on remaining time
        const timerClasses = this.getTimerClasses(seconds);
        this.timerElement.className = timerClasses;
        this.mobileTimerElement.className = timerClasses.replace('text-2xl', 'text-lg');
    }

    getTimerClasses(seconds) {
        if (seconds <= 300) { // 5 minutes - critical
            return 'text-2xl font-bold text-red-600 font-mono animate-pulse';
        } else if (seconds <= 600) { // 10 minutes - warning
            return 'text-2xl font-bold text-orange-500 font-mono';
        } else if (seconds <= 900) { // 15 minutes - caution
            return 'text-2xl font-bold text-yellow-600 font-mono';
        } else {
            return 'text-2xl font-bold text-blue-600 font-mono';
        }
    }

    updateProgressBar(seconds) {
        const percentage = (seconds / this.timeoutDuration) * 100;
        this.progressBar.style.width = `${percentage}%`;

        // Change progress bar color based on remaining time
        if (seconds <= 300) {
            this.progressBar.className = 'h-2 bg-red-500 rounded-full transition-all duration-1000 ease-linear';
        } else if (seconds <= 600) {
            this.progressBar.className = 'h-2 bg-orange-500 rounded-full transition-all duration-1000 ease-linear';
        } else if (seconds <= 900) {
            this.progressBar.className = 'h-2 bg-yellow-500 rounded-full transition-all duration-1000 ease-linear';
        } else {
            this.progressBar.className = 'h-2 bg-gradient-to-r from-green-400 via-blue-500 to-purple-500 rounded-full transition-all duration-1000 ease-linear';
        }
    }

    showWarning() {
        this.warningShown = true;
        this.warningModal.classList.remove('hidden');

        // Add entrance animation
        setTimeout(() => {
            this.warningModal.querySelector('.bg-white').style.transform = 'scale(1)';
        }, 50);

        // Hide main timer when warning is shown
        this.sessionTimer.style.transform = 'translateX(100%)';
        this.mobileSessionTimer.style.transform = 'translateX(100%)';
    }

    hideWarning() {
        this.warningShown = false;

        // Add exit animation
        this.warningModal.querySelector('.bg-white').style.transform = 'scale(0.95)';
        setTimeout(() => {
            this.warningModal.classList.add('hidden');
            this.sessionTimer.style.transform = 'translateX(0)';
            this.mobileSessionTimer.style.transform = 'translateX(0)';
        }, 300);
    }

    extendSession() {
        // Disable buttons and show loading
        const extendBtn = document.getElementById('extend-session-btn');
        const originalText = extendBtn.innerHTML;
        extendBtn.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Extending...</span>';
        extendBtn.disabled = true;

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
                this.showExtensionNotification();
            } else {
                this.showNotification('Failed to extend session', 'error');
            }
        })
        .catch(error => {
            console.error('Error extending session:', error);
            this.showNotification('Error extending session', 'error');
        })
        .finally(() => {
            extendBtn.innerHTML = originalText;
            extendBtn.disabled = false;
        });
    }

    showExtensionNotification() {
        this.extensionNotification.classList.remove('hidden');
        setTimeout(() => {
            this.extensionNotification.classList.add('hidden');
        }, 4000);
    }

    handleTimeout() {
        clearInterval(this.interval);

        // Show logout animation
        this.sessionTimer.style.transform = 'translateX(100%)';
        this.mobileSessionTimer.style.transform = 'translateX(100%)';

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
        // Main extend button
        document.getElementById('extend-session-btn').addEventListener('click', () => {
            this.extendSession();
        });

        // Mini extend button
        this.extendMiniBtn.addEventListener('click', () => {
            this.extendSession();
        });

        // Logout button
        document.getElementById('logout-now-btn').addEventListener('click', () => {
            this.handleTimeout();
        });

        // Close warning button
        document.getElementById('close-warning').addEventListener('click', () => {
            this.hideWarning();
        });

        // Close warning on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.warningShown) {
                this.hideWarning();
            }
        });

        // Click outside to close warning
        this.warningModal.addEventListener('click', (e) => {
            if (e.target === this.warningModal) {
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
        notification.className = `fixed top-6 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-xl shadow-2xl z-50 transition-all duration-300 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-6 h-6 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        ${type === 'success' ?
                            '<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>' :
                            '<path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>'
                        }
                    </svg>
                </div>
                <span class="font-semibold">${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transform = 'translate(-50%, -20px) scale(0.95)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
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

<style>
/* Additional animations */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-6px);
    }
}

#session-timer {
    animation: slideInRight 0.5s ease-out;
}

#session-timer:hover {
    animation: float 3s ease-in-out infinite;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    #session-timer {
        top: 1rem;
        right: 1rem;
        padding: 1rem;
    }
}

@media (max-width: 640px) {
    #session-timer {
        display: none;
    }

    #mobile-session-timer {
        display: block;
    }
}
</style>
@endif
@endauth
