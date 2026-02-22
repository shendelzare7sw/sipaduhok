window.filterTeachers = function(category) {
    const cards = document.querySelectorAll('.teacher-card');
    const buttons = document.querySelectorAll('.filter-btn');

    buttons.forEach(btn => {
        btn.classList.remove('bg-[#165fac]', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    event.target.classList.remove('bg-gray-100', 'text-gray-700');
    event.target.classList.add('bg-[#165fac]', 'text-white');

    cards.forEach(card => {
        if (category === 'all' || card.dataset.category === category) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
};
