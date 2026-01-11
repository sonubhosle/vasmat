document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        if (!localStorage.getItem('mitPopupClosed')) {
            const popup = document.getElementById('welcomePopup');
            const content = document.getElementById('popupContent');
            
            if (popup && content) {
                // Show popup
                popup.classList.remove('hidden');
                popup.classList.add('flex');
                
                // Trigger animation
                setTimeout(() => {
                    content.classList.remove('scale-95', 'opacity-0');
                    content.classList.add('scale-100', 'opacity-100');
                }, 10);
            }
        }
    }, 1000);
});

window.closeWelcomePopup = function() {
    const popup = document.getElementById('welcomePopup');
    const content = document.getElementById('popupContent');
    const dontShow = document.getElementById('dontShowAgain').checked;
    
    if (content) {
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
    }
    
    if (dontShow) {
        localStorage.setItem('mitPopupClosed', 'true');
    }
    
    setTimeout(() => {
        popup.classList.remove('flex');
        popup.classList.add('hidden');
    }, 300);
};

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('welcomePopup');
    if (popup) {
        popup.addEventListener('click', function(event) {
            if (event.target === this) {
                closeWelcomePopup();
            }
        });
    }
});

document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeWelcomePopup();
    }
});