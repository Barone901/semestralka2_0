/**
 * Form Validation Module
 *
 * Validácia formulárov na strane klienta.
 * Podporuje: required, email, minLength, maxLength, pattern, match
 */

import Toast from './toast';

const FormValidator = {
    // Validačné pravidlá
    rules: {
        required: (value) => {
            return value.trim() !== '' || 'This field is required.';
        },

        email: (value) => {
            if (!value) return true;
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(value) || 'Please enter a valid email address.';
        },

        minLength: (value, length) => {
            if (!value) return true;
            return value.length >= length || `The minimum length is ${length} characters.`;
        },

        maxLength: (value, length) => {
            if (!value) return true;
            return value.length <= length || `The minimum length is ${length} characters.`;
        },

        pattern: (value, regex, message = 'Invalid format.') => {
            if (!value) return true;
            return new RegExp(regex).test(value) || message;
        },

        match: (value, targetSelector, message = 'The values do not match.') => {
            const target = document.querySelector(targetSelector);
            if (!target) return true;
            return value === target.value || message;
        },

        password: (value) => {
            if (!value) return true;
            const hasMinLength = value.length >= 8;
            const hasUppercase = /[A-Z]/.test(value);
            const hasLowercase = /[a-z]/.test(value);
            const hasNumber = /[0-9]/.test(value);

            if (!hasMinLength) return 'The password must be at least 8 characters long.';
            if (!hasUppercase) return 'The password must contain a capital letter.';
            if (!hasLowercase) return 'The password must contain a lowercase letter.';
            if (!hasNumber) return 'The password must contain a number.';
            return true;
        },

        phone: (value) => {
            if (!value) return true;
            const regex = /^(\+421|0)?[0-9]{9}$/;
            return regex.test(value.replace(/\s/g, '')) || 'Please enter a valid phone number.';
        },

        numeric: (value) => {
            if (!value) return true;
            return !isNaN(value) || 'Enter a numerical value.';
        },

        min: (value, min) => {
            if (!value) return true;
            return parseFloat(value) >= min || `The minimum value is ${min}.`;
        },

        max: (value, max) => {
            if (!value) return true;
            return parseFloat(value) <= max || `The maximum value is ${max}.`;
        },
    },

    /**
     * Inicializácia validácie na formulári.
     */
    init(formSelector = '[data-validate]') {
        const forms = document.querySelectorAll(formSelector);
        forms.forEach(form => this.setupForm(form));
    },

    /**
     * Nastavenie validácie pre konkrétny formulár.
     */
    setupForm(form) {
        // Prevent default HTML5 validation
        form.setAttribute('novalidate', 'true');

        // Live validation on blur
        const inputs = form.querySelectorAll('[data-rules]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => {
                // Clear error on input
                if (input.classList.contains('border-red-500')) {
                    this.clearFieldError(input);
                }
            });
        });

        // Validate on submit
        form.addEventListener('submit', (e) => {
            const isValid = this.validateForm(form);
            if (!isValid) {
                e.preventDefault();
                Toast.error('Please correct the errors in the form.');

                // Focus first error field
                const firstError = form.querySelector('.border-red-500');
                if (firstError) {
                    firstError.focus();
                }
            }
        });
    },

    /**
     * Validácia celého formulára.
     */
    validateForm(form) {
        const inputs = form.querySelectorAll('[data-rules]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    },

    /**
     * Validácia jedného poľa.
     */
    validateField(input) {
        const rules = input.dataset.rules?.split('|') || [];
        const value = input.value;
        let isValid = true;
        let errorMessage = '';

        for (const rule of rules) {
            const [ruleName, ...params] = rule.split(':');
            const ruleFunc = this.rules[ruleName];

            if (ruleFunc) {
                const result = ruleFunc(value, ...params);
                if (result !== true) {
                    isValid = false;
                    errorMessage = result;
                    break;
                }
            }
        }

        if (isValid) {
            this.clearFieldError(input);
        } else {
            this.showFieldError(input, errorMessage);
        }

        return isValid;
    },

    /**
     * Zobrazenie chyby pri poli.
     */
    showFieldError(input, message) {
        this.clearFieldError(input);

        // Add error styles
        input.classList.add('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        input.classList.remove('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');

        // Create error message element
        const errorEl = document.createElement('p');
        errorEl.className = 'mt-1 text-sm text-red-600 validation-error';
        errorEl.textContent = message;

        // Insert after input
        input.parentNode.insertBefore(errorEl, input.nextSibling);
    },

    /**
     * Odstránenie chyby pri poli.
     */
    clearFieldError(input) {
        // Remove error styles
        input.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
        input.classList.add('border-gray-300', 'focus:border-indigo-500', 'focus:ring-indigo-500');

        // Remove error message
        const errorEl = input.parentNode.querySelector('.validation-error');
        if (errorEl) {
            errorEl.remove();
        }
    },

    /**
     * Manuálna validácia s custom pravidlami.
     */
    validate(data, rules) {
        const errors = {};

        for (const [field, fieldRules] of Object.entries(rules)) {
            const value = data[field] || '';
            const ruleList = fieldRules.split('|');

            for (const rule of ruleList) {
                const [ruleName, ...params] = rule.split(':');
                const ruleFunc = this.rules[ruleName];

                if (ruleFunc) {
                    const result = ruleFunc(value, ...params);
                    if (result !== true) {
                        errors[field] = result;
                        break;
                    }
                }
            }
        }

        return {
            isValid: Object.keys(errors).length === 0,
            errors,
        };
    },

    /**
     * Zobrazenie serverových chýb.
     */
    showServerErrors(form, errors) {
        // Clear all previous errors
        form.querySelectorAll('.validation-error').forEach(el => el.remove());
        form.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500', 'focus:border-red-500', 'focus:ring-red-500');
            el.classList.add('border-gray-300');
        });

        // Show new errors
        for (const [field, messages] of Object.entries(errors)) {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                const message = Array.isArray(messages) ? messages[0] : messages;
                this.showFieldError(input, message);
            }
        }

        // Focus first error
        const firstError = form.querySelector('.border-red-500');
        if (firstError) {
            firstError.focus();
        }
    },
};

export default FormValidator;

