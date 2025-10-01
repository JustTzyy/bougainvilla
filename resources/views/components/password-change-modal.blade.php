<!-- Password Change Modal -->
<div id="passwordChangeModal" class="modal-overlay" style="display: none;">
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h3 class="modal-title">Security Alert</h3>
            <button type="button" class="modal-close" onclick="closePasswordModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="alert-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>You are currently using a default password. For security reasons, please change your password immediately.</p>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closePasswordModal()">
                    <i class="fas fa-times"></i>
                    Later
                </button>
                <button type="button" class="btn btn-primary" onclick="goToPasswordSettings()">
                    <i class="fas fa-key"></i>
                    Change Password
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(5px);
}

.modal-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow: hidden;
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    background: linear-gradient(135deg, #8B0000, #DC143C);
    color: white;
    padding: 25px 30px;
    display: flex;
    align-items: center;
    gap: 15px;
    position: relative;
}

.modal-icon {
    font-size: 24px;
    opacity: 0.9;
}

.modal-title {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    flex: 1;
}

.modal-close {
    background: none;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    transition: background-color 0.2s;
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.2);
}

.modal-body {
    padding: 30px;
}

.alert-message {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 30px;
    padding: 20px;
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 10px;
    color: #856404;
}

.alert-message i {
    font-size: 20px;
    color: #f39c12;
    margin-top: 2px;
}

.alert-message p {
    margin: 0;
    line-height: 1.6;
    font-size: 16px;
}

.modal-actions {
    display: flex;
    gap: 15px;
    justify-content: flex-end;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
}

.btn-primary {
    background: linear-gradient(135deg, #8B0000, #DC143C);
    color: white;
    box-shadow: 0 4px 15px rgba(139, 0, 0, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #7A0000, #C41E3A);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(139, 0, 0, 0.4);
}

@media (max-width: 600px) {
    .modal-container {
        width: 95%;
        margin: 20px;
    }
    
    .modal-header {
        padding: 20px;
    }
    
    .modal-body {
        padding: 20px;
    }
    
    .modal-actions {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
function closePasswordModal() {
    document.getElementById('passwordChangeModal').style.display = 'none';
}

function goToPasswordSettings() {
    // Determine the correct settings route based on user role
    const isAdmin = {{ Auth::user()->roleID == 1 ? 'true' : 'false' }};
    const settingsRoute = isAdmin ? '{{ route("adminPages.settings") }}' : '{{ route("frontdesk.settings") }}';
    
    // Redirect to settings page and automatically open password tab
    window.location.href = settingsRoute + '#password-tab';
}

// Show modal when page loads if password hasn't been changed
document.addEventListener('DOMContentLoaded', function() {
    const passwordChanged = {{ isset($password_changed) ? ($password_changed ? 'true' : 'false') : (Auth::user()->password_changed ? 'true' : 'false') }};
    
    // Check if we're on a settings page (don't show modal on settings pages)
    const isSettingsPage = window.location.pathname.includes('/settings') || 
                          window.location.pathname.includes('adminPages/settings') || 
                          window.location.pathname.includes('frontdesk/settings');
    
    if (!passwordChanged && !isSettingsPage) {
        // Small delay to ensure page is fully loaded
        setTimeout(function() {
            document.getElementById('passwordChangeModal').style.display = 'flex';
        }, 1000);
    }
});
</script>
