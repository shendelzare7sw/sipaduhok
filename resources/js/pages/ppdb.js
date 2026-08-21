// Tab functionality
const tabButtons = document.querySelectorAll('.tab-button');
const tabContents = document.querySelectorAll('.tab-content');

tabButtons.forEach(button => {
    button.addEventListener('click', () => {
        const tabId = button.getAttribute('data-tab');
        tabButtons.forEach(btn => btn.classList.remove('active'));
        tabContents.forEach(content => content.classList.remove('active'));
        button.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    });
});

// Show/hide jurusan field based on jenjang selection
const jenjangSelect = document.getElementById('jenjangSelect');
const jurusanField = document.getElementById('jurusanField');

if (jenjangSelect && jurusanField) {
    jenjangSelect.addEventListener('change', function () {
        if (this.value === 'paket-c') {
            jurusanField.classList.remove('hidden');
        } else {
            jurusanField.classList.add('hidden');
        }
    });
}

// Form submission
const form = document.getElementById('registrationForm');
const modal = document.getElementById('successModal');

if (form && modal) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        form.reset();
        if (jurusanField) jurusanField.classList.add('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

window.closeModal = function() {
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};

// Smooth scroll for navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) target.scrollIntoView({ behavior: 'smooth' });
    });
});

// Add scroll effect to navbar
const navbar = document.querySelector('nav');
if (navbar) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('shadow-lg');
        } else {
            navbar.classList.remove('shadow-lg');
        }
    });
}
