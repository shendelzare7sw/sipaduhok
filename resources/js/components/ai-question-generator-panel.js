(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const panel = document.getElementById('aiQuestionGeneratorPanel');

        if (!panel) {
            return;
        }

        const difficultyHints = {
            easy: 'Fakta dasar & hafalan (C1-C2)',
            medium: 'Aplikasi konsep & perhitungan (C3-C4)',
            hard: 'Analisis & problem solving (C5-C6)',
        };

        const estimatedTimes = {
            3: '10-15',
            5: '15-20',
            7: '20-25',
            10: '25-30',
        };

        const collapse = panel.querySelector('#aiGeneratorCollapse');
        const collapseIcon = panel.querySelector('#collapseIcon');
        const hint = panel.querySelector('#difficultyHint');
        const countSelector = panel.querySelector('#aiQuestionCount');
        const defaultDifficulty = panel.querySelector('input[name="difficulty"]:checked');

        if (defaultDifficulty && hint) {
            hint.textContent = difficultyHints[defaultDifficulty.value];
        }

        panel.querySelectorAll('input[name="difficulty"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                if (hint) {
                    hint.textContent = difficultyHints[radio.value];
                }
            });
        });

        if (countSelector) {
            countSelector.addEventListener('change', () => {
                const time = panel.querySelector('#estimatedTime');

                if (time) {
                    time.textContent = estimatedTimes[countSelector.value] || '15-20';
                }
            });
        }

        if (collapse && collapseIcon) {
            collapse.addEventListener('shown.bs.collapse', () => collapseIcon.classList.add('is-expanded'));
            collapse.addEventListener('hidden.bs.collapse', () => collapseIcon.classList.remove('is-expanded'));
        }
    });
})();
