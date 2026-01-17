/**
 * Currency Formatter
 * Format input dengan pemisah ribuan (titik) tanpa desimal
 */

class CurrencyFormatter {
    constructor() {
        this.init();
    }

    init() {
        // Auto-bind ke semua input dengan class .currency-input
        document.addEventListener('DOMContentLoaded', () => {
            this.bindInputs();
        });
    }

    bindInputs() {
        const inputs = document.querySelectorAll('.currency-input');
        inputs.forEach(input => {
            // Set initial value jika ada
            if (input.value) {
                input.value = this.formatNumber(input.value);
            }

            // Format saat user mengetik
            input.addEventListener('input', (e) => {
                this.handleInput(e.target);
            });

            // Format saat blur (kehilangan focus)
            input.addEventListener('blur', (e) => {
                this.handleBlur(e.target);
            });

            // Handle paste
            input.addEventListener('paste', (e) => {
                setTimeout(() => this.handleInput(e.target), 0);
            });
        });
    }

    handleInput(input) {
        // Simpan posisi cursor
        const cursorPosition = input.selectionStart;
        const oldLength = input.value.length;

        // Hapus semua karakter selain angka
        let value = input.value.replace(/[^0-9]/g, '');

        // Format dengan pemisah ribuan
        const formatted = this.formatNumber(value);

        // Update nilai input
        input.value = formatted;

        // Restore posisi cursor dengan adjustment
        const newLength = formatted.length;
        const diff = newLength - oldLength;
        input.setSelectionRange(cursorPosition + diff, cursorPosition + diff);

        // Update hidden input jika ada
        this.updateHiddenInput(input, value);
    }

    handleBlur(input) {
        // Cleanup format saat blur
        let value = input.value.replace(/[^0-9]/g, '');
        input.value = this.formatNumber(value);
        this.updateHiddenInput(input, value);
    }

    formatNumber(value) {
        // Hapus leading zeros kecuali value = "0"
        value = value.replace(/^0+/, '') || '0';

        // Tambahkan pemisah ribuan (titik)
        return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    parseValue(formatted) {
        // Parse formatted string ke number
        return parseInt(formatted.replace(/\./g, ''), 10) || 0;
    }

    updateHiddenInput(input, rawValue) {
        // Cari hidden input yang sesuai
        const hiddenInputName = input.name + '_raw';
        let hiddenInput = document.querySelector(`input[name="${hiddenInputName}"]`);

        if (!hiddenInput) {
            // Buat hidden input jika belum ada
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = hiddenInputName;
            input.parentNode.insertBefore(hiddenInput, input.nextSibling);
        }

        hiddenInput.value = rawValue;
    }

    // Static method untuk digunakan di form submit handler
    static prepareFormData(formElement) {
        const currencyInputs = formElement.querySelectorAll('.currency-input');
        currencyInputs.forEach(input => {
            // Ganti nilai dengan raw value (tanpa pemisah)
            const rawValue = input.value.replace(/\./g, '');
            input.value = rawValue;
        });
    }
}

// Initialize
const currencyFormatter = new CurrencyFormatter();

// Helper function untuk manual format
window.formatCurrency = function(value) {
    value = String(value).replace(/[^0-9]/g, '');
    value = value.replace(/^0+/, '') || '0';
    return value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
};

// Helper function untuk parse
window.parseCurrency = function(formatted) {
    return parseInt(String(formatted).replace(/\./g, ''), 10) || 0;
};

// Export untuk digunakan di form submit
window.CurrencyFormatter = CurrencyFormatter;
