document.addEventListener('DOMContentLoaded', () => {
    const jenjangSelect = document.getElementById('jenjang');
    const btnAutoGenerate = document.getElementById('btnAutoGenerate');
    const kodeMapelInput = document.getElementById('kode_mapel');
    const suggestionsContainer = document.getElementById('suggestionsContainer');
    const suggestionsList = document.getElementById('suggestionsList');
    const suggestionsLoading = document.getElementById('suggestionsLoading');
    const suggestionsEmpty = document.getElementById('suggestionsEmpty');
    const suggestUrl = suggestionsContainer?.dataset.suggestUrl;

    const showContainer = () => suggestionsContainer?.classList.remove('d-none');
    const hideElement = (element) => element?.classList.add('d-none');
    const showElement = (element) => element?.classList.remove('d-none');

    btnAutoGenerate?.addEventListener('click', () => {
        const jenjang = jenjangSelect?.value || '';

        showContainer();
        if (suggestionsList) {
            suggestionsList.innerHTML = '';
        }

        if (!jenjang || !suggestUrl) {
            hideElement(suggestionsLoading);
            showElement(suggestionsEmpty);
            return;
        }

        showElement(suggestionsLoading);
        hideElement(suggestionsEmpty);

        fetch(`${suggestUrl}?jenjang=${encodeURIComponent(jenjang)}`)
            .then((response) => response.json())
            .then((data) => {
                hideElement(suggestionsLoading);

                if (!suggestionsList) {
                    return;
                }

                suggestionsList.innerHTML = '';
                if (data.suggestions && data.suggestions.length > 0) {
                    data.suggestions.forEach((code) => {
                        const badge = document.createElement('button');
                        badge.type = 'button';
                        badge.className = 'btn btn-sm btn-outline-success';
                        badge.innerHTML = `<i class="fas fa-check-circle me-1"></i> ${code}`;
                        badge.addEventListener('click', () => {
                            if (kodeMapelInput) {
                                kodeMapelInput.value = code;
                            }
                            suggestionsContainer?.classList.add('d-none');
                        });
                        suggestionsList.appendChild(badge);
                    });
                    return;
                }

                suggestionsList.innerHTML = '<small class="text-muted"><i class="fas fa-check-circle me-1"></i> Semua kode umum sudah terpakai di jenjang ini. Silakan input manual.</small>';
            })
            .catch((error) => {
                hideElement(suggestionsLoading);
                if (suggestionsList) {
                    suggestionsList.innerHTML = '<small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Gagal memuat saran. Silakan input manual.</small>';
                }
                console.error('Error:', error);
            });
    });
});
