/**
 * FSSAI License Verification System
 * Main JavaScript File
 *
 * Common utilities and functionality
 */

// ============================================
// Utility Functions
// ============================================

/**
 * Format a license number with spaces for readability
 * @param {string} license - 14-digit license number
 * @returns {string} Formatted license number
 */
function formatLicenseNumber(license) {
    if (!license || license.length !== 14) return license;
    // Format: 1 0012 0210 0001
    return `${license[0]} ${license.slice(1, 5)} ${license.slice(5, 9)} ${license.slice(9)}`;
}

/**
 * Sanitize HTML to prevent XSS
 * @param {string} str - String to sanitize
 * @returns {string} Sanitized string
 */
function sanitizeHTML(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

/**
 * Format date to Indian locale
 * @param {string} dateString - Date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-IN', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    } catch (e) {
        return dateString;
    }
}

/**
 * Show a toast notification
 * @param {string} message - Message to display
 * @param {string} type - Toast type (success, danger, warning, info)
 */
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-${getToastIcon(type)} me-2"></i>${sanitizeHTML(message)}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, { delay: 5000 });
    bsToast.show();

    // Remove toast after hiding
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

/**
 * Get icon for toast type
 * @param {string} type - Toast type
 * @returns {string} Bootstrap icon name
 */
function getToastIcon(type) {
    const icons = {
        success: 'check-circle-fill',
        danger: 'exclamation-triangle-fill',
        warning: 'exclamation-circle-fill',
        info: 'info-circle-fill'
    };
    return icons[type] || icons.info;
}

/**
 * Create toast container if it doesn't exist
 * @returns {HTMLElement} Toast container
 */
function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1100';
    document.body.appendChild(container);
    return container;
}

/**
 * Validate FSSAI license number format
 * @param {string} license - License number to validate
 * @returns {boolean} True if valid format
 */
function isValidLicenseFormat(license) {
    if (!license) return false;
    const cleaned = license.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
    return cleaned.length === 14 && /^[A-Z0-9]{14}$/.test(cleaned);
}

/**
 * Extract license number from various input formats
 * @param {string} input - Raw input string
 * @returns {string} Cleaned 14-digit license number
 */
function extractLicenseNumber(input) {
    if (!input) return '';
    return input.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();
}

// ============================================
// API Helper Functions
// ============================================

/**
 * Verify a license number via API
 * DISABLED - Using verify.php implementation instead
 * @param {string} licenseNumber - License to verify
 * @returns {Promise<Object>} API response
 */
/*
async function verifyLicense(licenseNumber) {
    const response = await fetch('api/verify-license.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            license_number: licenseNumber,
            input_method: 'Manual'
        })
    });

    if (!response.ok) {
        throw new Error('Verification failed');
    }

    return await response.json();
}
*/

/**
 * Submit a fake license report
 * @param {Object} data - Report data
 * @returns {Promise<Object>} API response
 */
async function submitReport(data) {
    const response = await fetch('api/submit-report.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    });

    if (!response.ok) {
        throw new Error('Report submission failed');
    }

    return await response.json();
}

// ============================================
// QR Code Scanner Helper
// ============================================

let qrScannerInstance = null;

/**
 * Initialize QR code scanner
 * @param {string} elementId - DOM element ID for scanner
 * @param {function} onScan - Callback when QR is scanned
 * @returns {Html5Qrcode} Scanner instance
 */
function initQRScanner(elementId, onScan) {
    if (typeof Html5Qrcode === 'undefined') {
        console.error('Html5Qrcode library not loaded');
        return null;
    }

    qrScannerInstance = new Html5Qrcode(elementId);
    return qrScannerInstance;
}

/**
 * Start QR scanning
 * @param {function} onScan - Success callback
 * @param {function} onError - Error callback
 */
function startQRScan(onScan, onError) {
    if (!qrScannerInstance) {
        console.error('QR scanner not initialized');
        return;
    }

    qrScannerInstance.start(
        { facingMode: 'environment' },
        {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        },
        (decodedText) => {
            const licenseMatch = decodedText.match(/[a-zA-Z0-9]{14}/);
            if (licenseMatch) {
                onScan(licenseMatch[0]);
            } else {
                onError('No valid license number found in QR code');
            }
        },
        onError || (() => {})
    ).catch(err => {
        onError('Error starting camera: ' + err);
    });
}

/**
 * Stop QR scanning
 */
function stopQRScan() {
    if (qrScannerInstance) {
        qrScannerInstance.stop().catch(err => {
            console.error('Error stopping scanner:', err);
        });
    }
}

// ============================================
// Form Validation Helpers
// ============================================

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} True if valid
 */
function isValidEmail(email) {
    if (!email) return true; // Empty is OK (optional field)
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * Validate Indian phone number
 * @param {string} phone - Phone number to validate
 * @returns {boolean} True if valid
 */
function isValidIndianPhone(phone) {
    if (!phone) return true; // Empty is OK (optional field)
    const cleaned = phone.replace(/[\s\-+]/g, '');
    return /^(\+91)?[6-9]\d{9}$/.test(cleaned);
}

/**
 * Validate Indian PIN code
 * @param {string} pincode - PIN code to validate
 * @returns {boolean} True if valid
 */
function isValidPINCode(pincode) {
    if (!pincode) return true; // Empty is OK (optional field)
    return /^[1-9]\d{5}$/.test(pincode);
}

// ============================================
// Local Storage Helpers
// ============================================

/**
 * Get recent searches from localStorage
 * @returns {Array} Recent searches
 */
function getRecentSearches() {
    try {
        const searches = localStorage.getItem('fssai_recent_searches');
        return searches ? JSON.parse(searches) : [];
    } catch (e) {
        return [];
    }
}

/**
 * Add a search to recent searches
 * @param {string} licenseNumber - License number searched
 */
function addRecentSearch(licenseNumber) {
    try {
        let searches = getRecentSearches();
        // Remove if already exists
        searches = searches.filter(s => s !== licenseNumber);
        // Add to beginning
        searches.unshift(licenseNumber);
        // Keep only last 10
        searches = searches.slice(0, 10);
        localStorage.setItem('fssai_recent_searches', JSON.stringify(searches));
    } catch (e) {
        // Ignore localStorage errors
    }
}

/**
 * Clear recent searches
 */
function clearRecentSearches() {
    try {
        localStorage.removeItem('fssai_recent_searches');
    } catch (e) {
        // Ignore localStorage errors
    }
}

// ============================================
// Initialize on DOM Ready
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation to cards
    document.querySelectorAll('.card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.classList.add('fade-in');
    });

    // Auto-format license number inputs
    document.querySelectorAll('input[placeholder*="license number"], input[pattern*="A-Z0-9"]')
        .forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        });

    // Add loading state to all forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            }
        });
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// ============================================
// Console Branding
// ============================================

console.log('%c FSSAI License Verification System ',
    'background: #0d6efd; color: white; font-size: 16px; padding: 10px; border-radius: 5px;');
console.log('%c Protecting consumers, one license at a time ',
    'color: #198754; font-size: 12px;');
