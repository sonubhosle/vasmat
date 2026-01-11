document.addEventListener('DOMContentLoaded', function() {
    // Get all dropdowns
    const dropdowns = document.querySelectorAll('.custom-dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const options = dropdown.querySelector('.dropdown-options');
        const selectedValue = dropdown.querySelector('.selected-value');
        const hiddenInput = dropdown.querySelector('.subject-input');
        const optionItems = dropdown.querySelectorAll('.dropdown-option');
        
        // Toggle dropdown on click
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
        
        // Handle option selection
        optionItems.forEach(option => {
            option.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                
                // Update display
                selectedValue.textContent = value;
                
                // Update hidden input
                hiddenInput.value = value;
                
                // Update active state
                optionItems.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
                
                // Close dropdown
                dropdown.classList.remove('open');
            });
        });
    });
});