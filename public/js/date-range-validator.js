/**
 * Date Range Validator
 * Automatically handles from/to date validation across all pages
 * Disables invalid date selections and provides user feedback
 */

document.addEventListener('DOMContentLoaded', function() {
    // Add CSS styles for date validation
    addDateValidationStyles();
    
    // Find all forms with date range inputs
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const fromInput = form.querySelector('input[type="date"]#from, input[type="date"][name="from"]');
        const toInput = form.querySelector('input[type="date"]#to, input[type="date"][name="to"]');
        
        if (fromInput && toInput) {
            setupDateRangeValidation(fromInput, toInput);
        }
    });
});

function addDateValidationStyles() {
    // Add CSS styles for date validation if they don't exist
    if (!document.getElementById('date-validation-styles')) {
        const style = document.createElement('style');
        style.id = 'date-validation-styles';
        style.textContent = `
            .date-error {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            }
            .date-error-message {
                color: #dc3545;
                font-size: 0.875rem;
                margin-top: 0.25rem;
                display: block;
            }
        `;
        document.head.appendChild(style);
    }
}

function setupDateRangeValidation(fromInput, toInput) {
    // Set initial constraints
    updateDateConstraints(fromInput, toInput);
    
    // Add event listeners
    fromInput.addEventListener('change', function() {
        updateDateConstraints(fromInput, toInput);
        validateDateRange(fromInput, toInput);
    });
    
    toInput.addEventListener('change', function() {
        validateDateRange(fromInput, toInput);
    });
    
    // Initial validation
    validateDateRange(fromInput, toInput);
}

function updateDateConstraints(fromInput, toInput) {
    const fromValue = fromInput.value;
    const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
    
    // Set maximum date for "from" input to be today (no future dates)
    fromInput.max = today;
    
    if (fromValue) {
        // Set minimum date for "to" input to be the selected "from" date
        toInput.min = fromValue;
        
        // Set maximum date for "to" input to be today (no future dates)
        toInput.max = today;
        
        // If current "to" value is less than "from" value, clear it
        if (toInput.value && toInput.value < fromValue) {
            toInput.value = fromValue;
        }
    } else {
        // Remove minimum constraint if no "from" date is selected
        toInput.removeAttribute('min');
        // Set maximum date for "to" input to be today (no future dates)
        toInput.max = today;
    }
}

function validateDateRange(fromInput, toInput) {
    const fromValue = fromInput.value;
    const toValue = toInput.value;
    const today = new Date().toISOString().split('T')[0]; // Get today's date in YYYY-MM-DD format
    
    // Remove any existing error styling
    fromInput.classList.remove('date-error');
    toInput.classList.remove('date-error');
    removeErrorMessage(fromInput.parentElement);
    removeErrorMessage(toInput.parentElement);
    
    // Check if from date is in the future
    if (fromValue && fromValue > today) {
        fromInput.classList.add('date-error');
        showErrorMessage(fromInput.parentElement, 'From date cannot be in the future');
        return false;
    }
    
    // Check if to date is in the future
    if (toValue && toValue > today) {
        toInput.classList.add('date-error');
        showErrorMessage(toInput.parentElement, 'To date cannot be in the future');
        return false;
    }
    
    // Check if from date is after to date
    if (fromValue && toValue && fromValue > toValue) {
        // Add error styling
        fromInput.classList.add('date-error');
        toInput.classList.add('date-error');
        
        // Add error message
        showErrorMessage(toInput.parentElement, 'To date must be after From date');
        
        return false;
    }
    
    return true;
}

function showErrorMessage(container, message) {
    // Remove existing error message
    removeErrorMessage(container);
    
    // Create new error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'date-error-message';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        color: #dc3545;
        font-size: 12px;
        margin-top: 4px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 4px;
    `;
    
    // Add error icon
    const errorIcon = document.createElement('i');
    errorIcon.className = 'fas fa-exclamation-triangle';
    errorIcon.style.fontSize = '10px';
    errorDiv.insertBefore(errorIcon, errorDiv.firstChild);
    
    container.appendChild(errorDiv);
}

function removeErrorMessage(container) {
    const existingError = container.querySelector('.date-error-message');
    if (existingError) {
        existingError.remove();
    }
}

// Add CSS styles for error states
const style = document.createElement('style');
style.textContent = `
    .date-error {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .date-error:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .date-error-message {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-5px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);

