            function filterGuruOptions(jadwalId) {
                const searchInput = document.getElementById('searchGuru' + jadwalId);
                const searchTerm = searchInput.value.toLowerCase().trim();
                const guruList = document.querySelectorAll('#guruList' + jadwalId + ' .guru-opt-item[data-name]');

                guruList.forEach(item => {
                    const name = item.getAttribute('data-name') || '';
                    if (searchTerm === '' || name.includes(searchTerm)) {
                        item.style.display = 'flex';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }

            function selectGuruForJadwal(jadwalId, guruId, guruName) {
                document.getElementById('guruIdBaru' + jadwalId).value = guruId;
                const display = document.getElementById('selectedGuruDisplay' + jadwalId);
                const nameSpan = document.getElementById('selectedGuruName' + jadwalId);

                if (guruId === '' || guruName.includes('Kosongkan')) {
                    display.style.display = 'none';
                } else {
                    display.style.display = 'block';
                    nameSpan.textContent = guruName;
                }
            }

            function clearGuruSelection{{ $jadwal->id }}() {
                document.getElementById('guruIdBaru{{ $jadwal->id }}').value = '';
                document.getElementById('selectedGuruDisplay{{ $jadwal->id }}').style.display = 'none';
            }

        function showToast(icon, title) {
            Swal.fire({
                icon: icon,
                title: title,
                toast: true,
                position: 'top',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        }

        // Toggle select all (desktop thead checkbox)
        function toggleSelectAllJadwal() {
            const selectAll = document.getElementById('select-all-jadwal');
            const checkboxes = document.querySelectorAll('.jadwal-checkbox');
            checkboxes.forEach(checkbox => { checkbox.checked = selectAll.checked; });
            const mobileSelectAll = document.getElementById('mobile-select-all-jadwal');
            if (mobileSelectAll) mobileSelectAll.checked = selectAll.checked;
            updateBulkButtons();
        }

        // Toggle select all (mobile "Pilih Semua" checkbox)
        function mobileToggleSelectAll() {
            const mobileSelectAll = document.getElementById('mobile-select-all-jadwal');
            const checkboxes = document.querySelectorAll('.jadwal-checkbox');
            checkboxes.forEach(cb => { cb.checked = mobileSelectAll.checked; });
            const desktopSelectAll = document.getElementById('select-all-jadwal');
            if (desktopSelectAll) desktopSelectAll.checked = mobileSelectAll.checked;
            updateBulkButtons();
        }

        // Update bulk action buttons visibility + card highlight
        function updateBulkButtons() {
            const checkboxes = document.querySelectorAll('.jadwal-checkbox:checked');
            const count = checkboxes.length;
            const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
            const bulkStatusBtn = document.getElementById('bulkStatusBtn');
            const selectedCount = document.getElementById('selectedCount');

            if (count > 0) {
                bulkDeleteBtn.style.display = 'inline-block';
                bulkStatusBtn.style.display = 'inline-block';
                selectedCount.textContent = count;
            } else {
                bulkDeleteBtn.style.display = 'none';
                bulkStatusBtn.style.display = 'none';
            }

            // Sync desktop select-all checkbox state
            const selectAll = document.getElementById('select-all-jadwal');
            const allCheckboxes = document.querySelectorAll('.jadwal-checkbox');
            const allChecked = allCheckboxes.length > 0 && count === allCheckboxes.length;
            selectAll.checked = allChecked;
            const mobileSelectAll = document.getElementById('mobile-select-all-jadwal');
            if (mobileSelectAll) mobileSelectAll.checked = allChecked;

            // Highlight selected cards on mobile
            allCheckboxes.forEach(cb => {
                const tr = cb.closest('tr');
                if (tr) tr.classList.toggle('card-selected', cb.checked);
            });
        }

        // Bulk delete function
        function bulkDelete() {
            const checkboxes = document.querySelectorAll('.jadwal-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (ids.length === 0) {
                showToast('warning', 'Pilih minimal 1 jadwal untuk dihapus');
                return;
            }

            // Set count and IDs
            document.getElementById('bulkDeleteCount').textContent = ids.length;
            document.getElementById('bulk-delete-ids').value = JSON.stringify(ids);

            // Build item list in modal
            const listEl = document.getElementById('bulkDeleteList');
            listEl.innerHTML = Array.from(checkboxes).map(cb => `
                <div style="display:flex; align-items:center; gap:10px; background:#f8f9fa; border-radius:8px; padding:10px 12px;">
                    <div style="width:34px; height:34px; background:#fff0f0; border-radius:7px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <i class="fas fa-book text-danger" style="font-size:13px;"></i>
                    </div>
                    <div style="min-width:0; flex:1;">
                        <div style="font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${cb.dataset.mapel}</div>
                        <div style="font-size:12px; color:#6c757d;">
                            <i class="fas fa-school me-1"></i>${cb.dataset.kelas}
                            &nbsp;&middot;&nbsp;
                            <i class="fas fa-calendar-day me-1"></i>${cb.dataset.hari}, ${cb.dataset.jam}
                        </div>
                    </div>
                </div>
            `).join('');

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('bulkDeleteModal'));
            modal.show();
        }

        // Bulk update status function - shows modal
        function bulkUpdateStatus() {
            const checkboxes = document.querySelectorAll('.jadwal-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => cb.value);

            if (ids.length === 0) {
                showToast('warning', 'Pilih minimal 1 jadwal untuk diubah statusnya');
                return;
            }

            // Set count in modal
            document.getElementById('bulkStatusCount').textContent = ids.length;

            // Store IDs temporarily in a data attribute
            document.getElementById('bulkStatusModal').setAttribute('data-selected-ids', JSON.stringify(ids));

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('bulkStatusModal'));
            modal.show();
        }

        // Submit bulk status update after modal confirmation
        function submitBulkStatus() {
            // Get stored IDs
            const idsJson = document.getElementById('bulkStatusModal').getAttribute('data-selected-ids');

            if (!idsJson) {
                showToast('error', 'Data tidak valid');
                return;
            }

            // Get selected status from radio buttons
            const statusRadio = document.querySelector('input[name="status_choice"]:checked');
            if (!statusRadio) {
                showToast('warning', 'Pilih status terlebih dahulu');
                return;
            }

            const status = statusRadio.value;

            // Create and submit form
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.jadwal-pelajaran.bulk-update-status") }}';

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = '{{ csrf_token() }}';
            form.appendChild(csrfInput);

            const idsInput = document.createElement('input');
            idsInput.type = 'hidden';
            idsInput.name = 'jadwal_ids';
            idsInput.value = idsJson;
            form.appendChild(idsInput);

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;
            form.appendChild(statusInput);

            document.body.appendChild(form);
            form.submit();
        }

        function filterPrintKelas() {
            const cabangId = document.getElementById('printCabangId').value;
            const kelasSelect = document.getElementById('printKelasId');
            const options = kelasSelect.querySelectorAll('option[data-cabang]');

            kelasSelect.value = "";
            kelasSelect.disabled = cabangId === "";

            options.forEach(opt => {
                if (cabangId === "" || opt.getAttribute('data-cabang') == cabangId) {
                    opt.style.display = "";
                } else {
                    opt.style.display = "none";
                    // If selected option is hidden, deselect
                    if (opt.selected) kelasSelect.value = "";
                }
            });
        }

        function submitCetakKelas(type) {
            const kelasId = document.getElementById('printKelasId').value;
            if (!kelasId) {
                showToast('warning', 'Silakan pilih kelas terlebih dahulu!');
                return;
            }

            let url = "";
            const tahunAjaranParam = "?tahun_ajaran_id={{ request('tahun_ajaran_id', $currentTahunAjaran->id) }}";

            if (type === 'excel') {
                // Endpoint Export Excel: /admin/jadwal-pelajaran/kelas/{id}/export-excel
                url = "{{ url('admin/jadwal-pelajaran/kelas') }}/" + kelasId + "/export-excel" + tahunAjaranParam;
            } else {
                // Endpoint Export PDF: /admin/jadwal-pelajaran/kelas/{id}/print
                url = "{{ url('admin/jadwal-pelajaran/kelas') }}/" + kelasId + "/print" + tahunAjaranParam;
            }
            
            window.open(url, '_blank');
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('cetakKelasModal'));
            modal.hide();
        }
