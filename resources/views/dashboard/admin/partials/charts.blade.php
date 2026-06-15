<div class="col-lg-8 d-flex flex-column gap-4">
    <div class="dashboard-card">
        <div class="card-header-clean">
            <h5 class="card-title-clean">
                <i class="fas fa-chart-area card-title-icon"></i> Grafik Pendaftaran Siswa
            </h5>
            <select id="timeFilter" class="form-select filter-select w-auto">
                <option value="1_tahun">1 Tahun Terakhir</option>
                <option value="6_bulan" selected>6 Bulan Terakhir</option>
                <option value="3_bulan">3 Bulan Terakhir</option>
            </select>
        </div>
        <div class="card-body p-0">
            <div class="chart-container registration-chart-container">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-4 flex-grow-1">
        <div class="col-md-6">
            <div class="dashboard-card h-100">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="fas fa-chart-pie card-title-icon"></i> Distribusi Gender
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="mini-chart-container d-flex justify-content-center">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="dashboard-card h-100">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="fas fa-chart-bar card-title-icon"></i> Siswa per Kelas
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="mini-chart-container">
                        <canvas id="classChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
