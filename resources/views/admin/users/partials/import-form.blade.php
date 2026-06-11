<div class="user-import user-import--{{ $variant }}">
    <div class="instructions">
        <h6><i class="fas fa-info-circle"></i> {{ $heading }}</h6>
        <ol>
            @foreach($instructions as $instruction)
                <li>{!! $instruction !!}</li>
            @endforeach
        </ol>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-file-import import-icon"></i>Upload File Excel</h5>
        </div>
        <div class="card-body">
            <div class="template-action">
                <a href="{{ $templateRoute }}" class="btn btn-success">
                    <i class="fas fa-download"></i>
                    Download Template
                </a>
            </div>

            <form action="{{ $storeRoute }}" method="POST" enctype="multipart/form-data">
                @csrf
                <button type="button" class="upload-area" data-upload-area>
                    <span class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></span>
                    <span class="upload-title">Klik untuk memilih file atau drag & drop</span>
                    <span class="upload-hint">Format: .xlsx, .xls (Maks 5MB)</span>
                </button>

                <input type="file" name="file" class="file-input" data-file-input accept=".xlsx,.xls">

                <div class="file-selected" data-file-selected>
                    <div class="file-summary">
                        <i class="fas fa-file-excel file-icon"></i>
                        <div>
                            <strong data-file-name>-</strong>
                            <div class="file-size" data-file-size>-</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-clear-file" data-clear-file>
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="import-actions">
                    <a href="{{ $backRoute }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" data-submit-import disabled>
                        <i class="fas fa-upload"></i>
                        Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
