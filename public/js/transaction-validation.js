/**
 * Transaction Form Validation
 * Handles client-side validation for guest information and payments
 */

class TransactionValidator {
    constructor() {
        this.errors = {};
        this.isValid = true;
    }

    // Philippine phone number patterns
    static phonePatterns = [
        // Mobile numbers (09XX-XXX-XXXX format variations)
        /^09\d{2}-\d{3}-\d{4}$/, // 0912-999-7211 (with dashes)
        /^09\d{9}$/, // 09129997211 (continuous)
        /^\+639\d{9}$/, // +639129997211 (international format)
        /^639\d{9}$/, // 639129997211 (international without +)
        /^09\d{2}\s\d{3}\s\d{4}$/, // 0912 999 7211 (with spaces)
        
        // Metro Manila landline (02-8XXX-XXXX format)
        /^02-8\d{3}-\d{4}$/, // 02-8123-4567
        /^028\d{7}$/, // 0281234567 (continuous)
        /^\+63028\d{7}$/, // +63028123456 (international)
        /^63028\d{7}$/, // 63028123456 (international without +)
        /^02\s8\d{3}\s\d{4}$/, // 02 8123 4567 (with spaces)
        
        // Outside Manila landline (XXX-XXX-XXXX format)
        /^0\d{2}-\d{3}-\d{4}$/, // 082-123-4567 (area code format)
        /^0\d{9}$/, // 0821234567 (continuous)
        /^\+630\d{9}$/, // +630821234567 (international)
        /^630\d{9}$/, // 630821234567 (international without +)
        /^0\d{2}\s\d{3}\s\d{4}$/, // 082 123 4567 (with spaces)
        
        // Legacy formats (backward compatibility)
        /^(\+63|0063|63)?[-.\s]?9\d{2}[-.\s]?\d{3}[-.\s]?\d{4}$/, // Flexible mobile
        /^(\+63|0063|63)?[-.\s]?2[-.\s]?\d{3}[-.\s]?\d{4}$/, // Flexible Manila landline
        /^(\+63|0063|63)?[-.\s]?\d{2}[-.\s]?\d{3}[-.\s]?\d{4}$/ // Flexible other landlines
    ];

    // Name validation (letters, spaces, hyphens, apostrophes only)
    static namePattern = /^[a-zA-Z\s\-'\.]+$/;

    // ZIP code pattern (4-5 digits)
    static zipPattern = /^\d{4,5}$/;

    /**
     * Validate Philippine phone number
     */
    static validatePhoneNumber(phone) {
        if (!phone || phone.trim() === '') return { isValid: true, message: '' }; // Optional field
        
        const cleanPhone = phone.replace(/[\s\-\.]/g, '');
        
        // Check if it matches any Philippine phone pattern
        const isValid = this.phonePatterns.some(pattern => pattern.test(phone));
        
        if (!isValid) {
            return {
                isValid: false,
                message: 'Please enter a valid Philippine phone number (Mobile: 0912-999-7211, 09129997211, +639129997211 | Landline: 02-8123-4567, 082-123-4567)'
            };
        }

        return { isValid: true, message: '' };
    }

    /**
     * Validate name fields
     */
    static validateName(name, fieldName, required = true) {
        if (!name || name.trim() === '') {
            if (required) {
                return {
                    isValid: false,
                    message: `${fieldName} is required`
                };
            }
            return { isValid: true, message: '' };
        }

        if (name.length < 2) {
            return {
                isValid: false,
                message: `${fieldName} must be at least 2 characters long`
            };
        }

        if (name.length > 50) {
            return {
                isValid: false,
                message: `${fieldName} must not exceed 50 characters`
            };
        }

        if (!this.namePattern.test(name)) {
            return {
                isValid: false,
                message: `${fieldName} can only contain letters, spaces, hyphens, and apostrophes`
            };
        }

        return { isValid: true, message: '' };
    }

    /**
     * Validate address fields
     */
    static validateAddress(address, fieldName, required = true) {
        if (!address || address.trim() === '') {
            if (required) {
                return {
                    isValid: false,
                    message: `${fieldName} is required`
                };
            }
            return { isValid: true, message: '' };
        }

        if (address.length < 3) {
            return {
                isValid: false,
                message: `${fieldName} must be at least 3 characters long`
            };
        }

        if (address.length > 255) {
            return {
                isValid: false,
                message: `${fieldName} must not exceed 255 characters`
            };
        }

        return { isValid: true, message: '' };
    }

    /**
     * Validate ZIP code
     */
    static validateZipCode(zip, citySelected = false) {
        if (!zip || zip.trim() === '') {
            // If a city is selected, the ZIP code should be auto-filled
            // Give a more helpful message in this case
            if (citySelected) {
                return {
                    isValid: false,
                    message: 'ZIP code will be auto-filled when city is selected'
                };
            }
            return {
                isValid: false,
                message: 'ZIP code is required'
            };
        }

        if (!this.zipPattern.test(zip)) {
            return {
                isValid: false,
                message: 'ZIP code must be 4-5 digits (e.g., 1234 or 12345)'
            };
        }

        return { isValid: true, message: '' };
    }

    /**
     * Validate payment amount
     */
    static validatePaymentAmount(amount, total) {
        const numAmount = parseFloat(amount);
        const numTotal = parseFloat(total);

        if (!amount || amount.trim() === '') {
            return {
                isValid: false,
                message: 'Payment amount is required'
            };
        }

        if (isNaN(numAmount) || numAmount < 0) {
            return {
                isValid: false,
                message: 'Payment amount must be a positive number'
            };
        }

        if (numAmount < numTotal) {
            return {
                isValid: false,
                message: `Payment amount must be at least ₱${numTotal.toFixed(2)}`
            };
        }

        if (numAmount > 999999.99) {
            return {
                isValid: false,
                message: 'Payment amount cannot exceed ₱999,999.99'
            };
        }

        return { isValid: true, message: '' };
    }

    /**
     * Show error message on input field
     */
    static showFieldError(input, message) {
        // Remove existing error
        this.clearFieldError(input);
        
        // Add error class
        input.classList.add('error');
        
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        errorDiv.textContent = message;
        errorDiv.style.cssText = `
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
            display: block;
        `;
        
        // Insert error message after input
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
    }

    /**
     * Clear error message from input field
     */
    static clearFieldError(input) {
        input.classList.remove('error');
        const existingError = input.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
    }

    /**
     * Validate entire guest form
     */
    static validateGuestForm(guestIndex) {
        let isValid = true;
        const errors = [];

        // Get form elements
        const firstNameInput = document.querySelector(`input[name="guests[${guestIndex}][firstName]"]`);
        const middleNameInput = document.querySelector(`input[name="guests[${guestIndex}][middleName]"]`);
        const lastNameInput = document.querySelector(`input[name="guests[${guestIndex}][lastName]"]`);
        const phoneInput = document.querySelector(`input[name="guests[${guestIndex}][number]"]`);
        const streetInput = document.querySelector(`input[name="guests[${guestIndex}][address][street]"]`);
        const provinceInput = document.querySelector(`select[name="guests[${guestIndex}][address][province]"]`);
        const cityInput = document.querySelector(`select[name="guests[${guestIndex}][address][city]"]`);
        const zipInput = document.querySelector(`input[name="guests[${guestIndex}][address][zipcode]"]`);

        // Validate first name
        if (firstNameInput) {
            const nameValidation = this.validateName(firstNameInput.value, 'First name', true);
            if (!nameValidation.isValid) {
                this.showFieldError(firstNameInput, nameValidation.message);
                isValid = false;
            } else {
                this.clearFieldError(firstNameInput);
            }
        }

        // Validate middle name (optional)
        if (middleNameInput) {
            const nameValidation = this.validateName(middleNameInput.value, 'Middle name', false);
            if (!nameValidation.isValid) {
                this.showFieldError(middleNameInput, nameValidation.message);
                isValid = false;
            } else {
                this.clearFieldError(middleNameInput);
            }
        }

        // Validate last name
        if (lastNameInput) {
            const nameValidation = this.validateName(lastNameInput.value, 'Last name', true);
            if (!nameValidation.isValid) {
                this.showFieldError(lastNameInput, nameValidation.message);
                isValid = false;
            } else {
                this.clearFieldError(lastNameInput);
            }
        }

        // Validate phone number
        if (phoneInput) {
            const phoneValidation = this.validatePhoneNumber(phoneInput.value);
            if (!phoneValidation.isValid) {
                this.showFieldError(phoneInput, phoneValidation.message);
                isValid = false;
            } else {
                this.clearFieldError(phoneInput);
            }
        }

        // Validate street address
        if (streetInput) {
            const addressValidation = this.validateAddress(streetInput.value, 'Street address', true);
            if (!addressValidation.isValid) {
                this.showFieldError(streetInput, addressValidation.message);
                isValid = false;
            } else {
                this.clearFieldError(streetInput);
            }
        }

        // Validate province
        if (provinceInput) {
            if (!provinceInput.value) {
                this.showFieldError(provinceInput, 'Province is required');
                isValid = false;
            } else {
                this.clearFieldError(provinceInput);
            }
        }

        // Validate city
        if (cityInput) {
            if (!cityInput.value) {
                this.showFieldError(cityInput, 'City is required');
                isValid = false;
            } else {
                this.clearFieldError(cityInput);
            }
        }

        // Validate ZIP code (with city selection context)
        if (zipInput) {
            const citySelected = cityInput && cityInput.value && cityInput.value !== '';
            
            // If city is selected but ZIP is empty, give it a moment to auto-fill
            if (citySelected && (!zipInput.value || zipInput.value === '')) {
                // Clear any existing error and show a helpful message
                this.clearFieldError(zipInput);
                // Don't mark as invalid immediately - ZIP should auto-fill
                return true; // Allow form to proceed, ZIP will be filled by city selection
            }
            
            const zipValidation = this.validateZipCode(zipInput.value, citySelected);
            if (!zipValidation.isValid) {
                this.showFieldError(zipInput, zipValidation.message);
                isValid = false;
            } else {
                this.clearFieldError(zipInput);
            }
        }

        return isValid;
    }

    /**
     * Validate payment section
     */
    static validatePayment() {
        let isValid = true;
        
        const amountPaidInput = document.getElementById('amountPaid');
        const totalAmountElement = document.getElementById('totalAmount');
        
        if (amountPaidInput && totalAmountElement) {
            const total = totalAmountElement.textContent.replace('₱', '').replace(',', '');
            const paymentValidation = this.validatePaymentAmount(amountPaidInput.value, total);
            
            if (!paymentValidation.isValid) {
                this.showFieldError(amountPaidInput, paymentValidation.message);
                isValid = false;
            } else {
                this.clearFieldError(amountPaidInput);
            }
        }

        return isValid;
    }

    /**
     * Add real-time validation to form inputs
     */
    static addRealTimeValidation() {
        // Add CSS for error styling
        if (!document.getElementById('validation-styles')) {
            const style = document.createElement('style');
            style.id = 'validation-styles';
            style.textContent = `
                .form-input.error {
                    border-color: #dc3545 !important;
                    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
                }
                .field-error {
                    color: #dc3545;
                    font-size: 12px;
                    margin-top: 4px;
                    display: block;
                }
            `;
            document.head.appendChild(style);
        }

        // Add validation on form submission
        document.addEventListener('submit', function(e) {
            if (e.target.id === 'processStayForm') {
                let isValid = true;
                
                // Validate all guest forms
                const guestForms = document.querySelectorAll('.guest-form');
                guestForms.forEach((form, index) => {
                    if (!TransactionValidator.validateGuestForm(index)) {
                        isValid = false;
                    }
                });

                // Validate payment
                if (!TransactionValidator.validatePayment()) {
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                    alert('Please fix the errors in the form before submitting.');
                    return false;
                }
            }
        });
    }
}

// Initialize validation when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    TransactionValidator.addRealTimeValidation();
});

// Make validator available globally
window.TransactionValidator = TransactionValidator;
