/**
 * Registration Form Handler
 * IPSC 2026 Registration System
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');
    const formMessage = document.getElementById('formMessage');
    
    // CSRF token disabled for now
    // generateCSRFToken();
    
    // Form submission handler
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear previous errors
        clearErrors();
        hideMessage();
        
        // Client-side validation
        if (!validateForm()) {
            showMessage('Please correct the errors in the form', 'error');
            return;
        }
        
        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Submitting...</span>';
        
        try {
            // Prepare form data
            const formData = new FormData(form);
            
            // Send to server
            const response = await fetch('process_registration.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage(result.message, 'success');
                form.reset();
                // Scroll to success message
                formMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // CSRF token regeneration disabled
                // generateCSRFToken();
            } else {
                if (result.errors) {
                    displayErrors(result.errors);
                }
                showMessage(result.message, 'error');
            }
        } catch (error) {
            console.error('Submission error:', error);
            showMessage('An error occurred while submitting the form. Please try again.', 'error');
        } finally {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i><span>Submit Registration</span>';
        }
    });
    
    // Real-time validation on blur
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        // Clear error on focus
        input.addEventListener('focus', function() {
            clearFieldError(this);
        });
    });
    
    // Handle paper submission radio change
    const paperRadios = form.querySelectorAll('input[name="submitted_paper"]');
    paperRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const abstractIdField = document.getElementById('abstract_id');
            if (this.value === 'No') {
                abstractIdField.value = 'N/A';
            } else if (abstractIdField.value === 'N/A') {
                abstractIdField.value = '';
            }
        });
    });
    
    // Handle payment status radio change
    const paymentRadios = form.querySelectorAll('input[name="payment_status"]');
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const referenceField = document.getElementById('payment_reference');
            const amountField = document.getElementById('amount_transferred');
            // Payment reference and amount fields are always required
        });
    });
});

/**
 * Generate CSRF Token (Disabled)
 */
// function generateCSRFToken() {
//     const token = Math.random().toString(36).substr(2) + Date.now().toString(36);
//     document.getElementById('csrf_token').value = token;
    
//     // Store in session storage for verification (if needed)
//     sessionStorage.setItem('csrf_token', token);
// }

/**
 * Validate entire form
 */
function validateForm() {
    let isValid = true;
    const form = document.getElementById('registrationForm');
    const inputs = form.querySelectorAll('input, select, textarea');
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * Validate individual field
 */
function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required';
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }
    
    // Phone validation
    if (field.name === 'phone' && value) {
        const phoneRegex = /^[0-9+\-\s()]{8,20}$/;
        if (!phoneRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number';
        }
    }
    
    // Number validation
    if (field.type === 'number' && value) {
        if (isNaN(value) || parseFloat(value) < 0) {
            isValid = false;
            errorMessage = 'Please enter a valid number';
        }
    }
    
    // Name validation
    if ((field.name === 'first_name' || field.name === 'last_name') && value) {
        if (value.length < 2) {
            isValid = false;
            errorMessage = 'Must be at least 2 characters';
        }
    }
    
    // Affiliation validation
    if (field.name === 'affiliation' && value) {
        if (value.length < 10) {
            isValid = false;
            errorMessage = 'Please provide complete affiliation details';
        }
    }
    
    // Radio button validation
    if (field.type === 'radio') {
        const radioGroup = document.querySelectorAll(`input[name="${field.name}"]`);
        const isChecked = Array.from(radioGroup).some(radio => radio.checked);
        if (field.hasAttribute('required') && !isChecked) {
            isValid = false;
            errorMessage = 'Please select an option';
        }
    }
    
    // Display error or clear
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

/**
 * Show field error
 */
function showFieldError(field, message) {
    field.classList.add('error');
    const errorElement = document.getElementById(`${field.name}-error`);
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
}

/**
 * Clear field error
 */
function clearFieldError(field) {
    field.classList.remove('error');
    const errorElement = document.getElementById(`${field.name}-error`);
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.classList.remove('show');
    }
}

/**
 * Clear all errors
 */
function clearErrors() {
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(element => {
        element.textContent = '';
        element.classList.remove('show');
    });
    
    const errorFields = document.querySelectorAll('.error');
    errorFields.forEach(field => {
        field.classList.remove('error');
    });
}

/**
 * Display server-side errors
 */
function displayErrors(errors) {
    for (const fieldName in errors) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            const errorMessages = errors[fieldName];
            const errorMessage = Array.isArray(errorMessages) ? errorMessages[0] : errorMessages;
            showFieldError(field, errorMessage);
        }
    }
}

/**
 * Show form message
 */
function showMessage(message, type) {
    const formMessage = document.getElementById('formMessage');
    formMessage.className = `form-message ${type}`;
    formMessage.innerHTML = message;
    formMessage.style.display = 'block';
    
    // Auto-hide success messages after 10 seconds
    if (type === 'success') {
        setTimeout(() => {
            hideMessage();
        }, 10000);
    }
}

/**
 * Hide form message
 */
function hideMessage() {
    const formMessage = document.getElementById('formMessage');
    formMessage.style.display = 'none';
}

/**
 * Format phone number (optional enhancement)
 */
function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length > 10) {
        value = value.slice(0, 10);
    }
    input.value = value;
}

/**
 * Confirm before leaving page with unsaved changes
 */
let formModified = false;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registrationForm');
    
    form.addEventListener('change', function() {
        formModified = true;
    });
    
    form.addEventListener('submit', function() {
        formModified = false;
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (formModified) {
            e.preventDefault();
            e.returnValue = '';
            return '';
        }
    });
});