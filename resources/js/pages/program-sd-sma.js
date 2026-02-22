const tabs = document.querySelectorAll('.tab-btn');
const contents = document.querySelectorAll('.tab-content');

tabs.forEach(btn => {
    btn.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        contents.forEach(c => c.classList.add('hidden'));

        btn.classList.add('active');
        const activeContent = document.getElementById('tab-' + btn.dataset.tab);

        activeContent.style.opacity = 0;
        activeContent.classList.remove('hidden');

        setTimeout(() => {
            activeContent.classList.add('fade-in');
        }, 10);
    });
});

// Default: activate first tab
if (tabs.length > 0) tabs[0].click();
