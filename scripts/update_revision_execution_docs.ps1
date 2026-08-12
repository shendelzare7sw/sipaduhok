param(
    [switch] $ExportPdf
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$repositoryRoot = Split-Path -Parent $PSScriptRoot
$executionRoot = Join-Path $repositoryRoot 'docs\revisi\test-execution'
$inputRoot = Join-Path $repositoryRoot 'docs\revisi\execution-input'
$testCaseDocx = Join-Path $inputRoot 'Test_Case_Positive_Negative_SIPADUHOK.docx'
$testCasePdf = Join-Path $inputRoot 'Test_Case_Positive_Negative_SIPADUHOK.pdf'
$sitDocx = Join-Path $inputRoot 'System_Integration_Testing_SIT_SIPADUHOK_Prepared_Awaiting_Execution.docx'
$sitPdf = Join-Path $inputRoot 'System_Integration_Testing_SIT_SIPADUHOK_Prepared_Awaiting_Execution.pdf'
$enDash = [char] 0x2013
$emDash = [char] 0x2014

function Get-CellText {
    param($Cell)

    return (($Cell.Range.Text -replace '[\r\a]', ' ') -replace '\s+', ' ').Trim()
}

function Set-CellText {
    param($Table, [int] $Row, [int] $Column, [string] $Text)

    $range = $Table.Cell($Row, $Column).Range
    $range.End = $range.End - 1
    $range.Text = $Text
}

function Get-DateLabel {
    param([string] $Date)

    if ([string]::IsNullOrWhiteSpace($Date)) {
        return [string] $emDash
    }

    if ($Date -eq '2026-08-12') {
        return '12 Agustus 2026'
    }

    return $Date
}

function Join-ExecutionComment {
    param($Row)

    $parts = New-Object System.Collections.Generic.List[string]
    if (-not [string]::IsNullOrWhiteSpace($Row.'Actual Result')) {
        $parts.Add("Actual: $($Row.'Actual Result')")
    }
    if (-not [string]::IsNullOrWhiteSpace($Row.Evidence)) {
        $parts.Add("Evidence: $($Row.Evidence)")
    }
    if (-not [string]::IsNullOrWhiteSpace($Row.'Defect ID')) {
        $parts.Add("Defect: $($Row.'Defect ID')")
    }
    if (-not [string]::IsNullOrWhiteSpace($Row.Notes)) {
        $parts.Add("Catatan: $($Row.Notes)")
    }
    return $parts -join "`r"
}

function Ensure-RowCount {
    param($Table, [int] $RequiredRows)

    while ($Table.Rows.Count -lt $RequiredRows) {
        [void] $Table.Rows.Add()
    }
}

function Find-RowByFirstCell {
    param($Table, [string] $Value)

    for ($rowIndex = 1; $rowIndex -le $Table.Rows.Count; $rowIndex++) {
        if ((Get-CellText $Table.Cell($rowIndex, 1)) -eq $Value) {
            return $rowIndex
        }
    }
    return $null
}

$testRows = Get-Content (Join-Path $executionRoot '02-test-case-execution.csv') -Encoding UTF8 | ConvertFrom-Csv
$sitRows = Get-Content (Join-Path $executionRoot '03-sit-execution.csv') -Encoding UTF8 | ConvertFrom-Csv

$reconciledScenarios = @(
    [pscustomobject]@{
        Id = 'TC-N-038'; Module = 'AU/USR'; Role = 'Semua pengguna'; Trace = 'RULE-038 / FEAT-001, 010'
        Scenario = 'Otorisasi: coba login memakai akun is_active=false atau lanjutkan request protected menggunakan session akun yang baru dinonaktifkan.'
        Expected = 'Login ditolak; session akun nonaktif dibatalkan; pengguna diarahkan ke halaman/pesan akun nonaktif dan tidak dapat memakai route protected.'
    },
    [pscustomobject]@{
        Id = 'TC-N-039'; Module = 'ORG/AKD/MON'; Role = 'Wakil Kepala Sekolah (Waka)'; Trace = "RULE-039 / FEAT-015${enDash}026, 087${enDash}089"
        Scenario = 'Otorisasi: Waka mencoba membuka atau mengubah data kelas, jadwal, akademik, atau monitoring milik cabang lain maupun memakai akun Waka tanpa cabang.'
        Expected = 'Target cross-cabang atau akun tanpa cabang ditolak 403/404 sesuai endpoint; daftar tetap di-scope ke cabang akun dan data tidak berubah.'
    },
    [pscustomobject]@{
        Id = 'TC-N-040'; Module = 'WKL'; Role = 'Wali Kelas'; Trace = "RULE-040 / FEAT-018, 028, 048${enDash}064, 082"
        Scenario = 'Otorisasi: Wali Kelas mencoba mengakses kelas, siswa, presensi, nilai, rapor, validasi, atau prediksi di luar current/historical assignment yang relevan.'
        Expected = 'Target di luar assignment Wali Kelas ditolak 403/404; data tidak terbaca atau berubah; akses historical hanya read-only pada riwayat yang sah.'
    },
    [pscustomobject]@{
        Id = 'TC-N-041'; Module = 'GRU/LMS'; Role = 'Guru Pengajar'; Trace = "RULE-041 / FEAT-029, 053, 056, 065${enDash}079"
        Scenario = 'Otorisasi: Guru mencoba melihat atau mengubah nilai, materi, tugas, ujian, soal, forum, meeting, atau arsip pada kelas-mapel yang tidak diampu.'
        Expected = 'Akses cross-kelas/mapel ditolak 403/404; arsip hanya dapat menjadi sumber sesuai riwayat mengajar dan target penyalinan harus assignment aktif.'
    },
    [pscustomobject]@{
        Id = 'TC-N-042'; Module = 'SIS/LMS'; Role = 'Siswa'; Trace = "RULE-042 / FEAT-067, 069, 073, 076${enDash}078, 094${enDash}095"
        Scenario = 'Otorisasi: Siswa tanpa profil/kelas yang valid atau siswa yang memilih mapel di luar jadwal kelas maupun tidak sesuai filter agama mencoba membuka konten LMS.'
        Expected = 'Profil/kelas invalid ditolak; mapel yang tidak sesuai jadwal/agama menghasilkan 403 atau redirect error; konten tidak terbaca dan data tidak berubah.'
    },
    [pscustomobject]@{
        Id = 'TC-N-043'; Module = 'SIS/LMS'; Role = 'Siswa'; Trace = "RULE-043 / FEAT-067, 069, 073, 076${enDash}078"
        Scenario = 'Otorisasi: Siswa pada jenjang yang dinonaktifkan melalui AppSetting mencoba membuka route atau konten LMS secara langsung.'
        Expected = 'Akses LMS ditolak atau diarahkan ke halaman LMS disabled; direct URL tidak membocorkan materi, tugas, ujian, forum, atau meeting.'
    },
    [pscustomobject]@{
        Id = 'TC-N-050'; Module = 'USR/KON/KEU/PRM'; Role = 'Role tanpa kewenangan'; Trace = 'RULE-050 / FEAT-013, 035, 046, 080'
        Scenario = 'Otorisasi: role non-Admin/non-eksplisit membuka direct URL tiket recovery, landing CMS, konfigurasi pembayaran, atau pengaturan KKM.'
        Expected = 'Middleware menolak akses dengan 403/redirect yang sesuai tanpa mengakhiri session; pengaturan sensitif tidak terbaca dan tidak berubah.'
    }
)

$word = New-Object -ComObject Word.Application
$word.Visible = $false
$word.DisplayAlerts = 0

try {
    $doc = $word.Documents.Open($testCaseDocx, $false, $false)
    try {
        # Metadata and revision history.
        Set-CellText $doc.Tables.Item(1) 4 2 '1.1'
        Set-CellText $doc.Tables.Item(1) 5 2 "TECHNICAL RETEST RECORDED ${emDash} REVIEW/APPROVAL PENDING"
        Set-CellText $doc.Tables.Item(1) 9 2 '101 positive + 70 negative = 171 baris Test Case unik; hasil Retest / Technical Verification 12 Agustus 2026 telah dicatat.'
        Set-CellText $doc.Tables.Item(1) 10 2 "Baseline: April 2026 Minggu III${enDash}IV; pembaruan evidence: 12 Agustus 2026"
        Set-CellText $doc.Tables.Item(2) 3 1 '1.1'
        Set-CellText $doc.Tables.Item(2) 3 2 '12 Agustus 2026'
        Set-CellText $doc.Tables.Item(2) 3 3 "Rekonsiliasi 7 ID dari RULE-038${enDash}043 dan RULE-050; pengisian hasil technical retest dan evidence."
        Set-CellText $doc.Tables.Item(2) 3 4 "Technical Retest Recorded ${emDash} Pending Review"

        # Make the module breakdown add up to 171 without double-counting combined module traces.
        $breakdown = $doc.Tables.Item(3)
        $reconciliationRowIndex = Find-RowByFirstCell $breakdown 'AUTH-RCN'
        if ($null -eq $reconciliationRowIndex) {
            $totalRowIndex = Find-RowByFirstCell $breakdown 'TOTAL'
            $newRow = $breakdown.Rows.Add($breakdown.Rows.Item($totalRowIndex))
            $reconciliationRowIndex = $newRow.Index
        }
        Set-CellText $breakdown $reconciliationRowIndex 1 'AUTH-RCN'
        Set-CellText $breakdown $reconciliationRowIndex 2 "Otorisasi lintas modul ${emDash} hasil rekonsiliasi"
        Set-CellText $breakdown $reconciliationRowIndex 3 '0'
        Set-CellText $breakdown $reconciliationRowIndex 4 '7'
        Set-CellText $breakdown $reconciliationRowIndex 5 '7'

        # The cross-cutting authorization table originally stopped at TC-N-037.
        $authorizationTable = $doc.Tables.Item(21)
        foreach ($scenario in $reconciledScenarios) {
            $rowIndex = Find-RowByFirstCell $authorizationTable $scenario.Id
            if ($null -eq $rowIndex) {
                $newRow = $authorizationTable.Rows.Add()
                $rowIndex = $newRow.Index
            }
            Set-CellText $authorizationTable $rowIndex 1 $scenario.Id
            Set-CellText $authorizationTable $rowIndex 2 '-'
            Set-CellText $authorizationTable $rowIndex 3 $scenario.Module
            Set-CellText $authorizationTable $rowIndex 4 $scenario.Role
            Set-CellText $authorizationTable $rowIndex 5 $scenario.Trace
            Set-CellText $authorizationTable $rowIndex 6 $scenario.Scenario
            Set-CellText $authorizationTable $rowIndex 7 $scenario.Expected
            Set-CellText $authorizationTable $rowIndex 8 'NOT EXECUTED'
            Set-CellText $authorizationTable $rowIndex 9 ([string] $emDash)
            Set-CellText $authorizationTable $rowIndex 10 'ID direkonsiliasi dari rule baseline.'
        }

        # Populate execution fields for all 171 Test Cases.
        $testById = @{}
        foreach ($testRow in $testRows) {
            $testById[$testRow.'Test Case ID'] = $testRow
        }
        $updatedIds = New-Object System.Collections.Generic.HashSet[string]
        for ($tableIndex = 1; $tableIndex -le $doc.Tables.Count; $tableIndex++) {
            $table = $doc.Tables.Item($tableIndex)
            if ($table.Columns.Count -ne 10) { continue }
            for ($rowIndex = 2; $rowIndex -le $table.Rows.Count; $rowIndex++) {
                $id = Get-CellText $table.Cell($rowIndex, 1)
                if (-not $testById.ContainsKey($id)) { continue }
                $testRow = $testById[$id]
                Set-CellText $table $rowIndex 8 $testRow.'Execution Status'
                Set-CellText $table $rowIndex 9 (Get-DateLabel $testRow.'Actual Test Date')
                Set-CellText $table $rowIndex 10 (Join-ExecutionComment $testRow)
                [void] $updatedIds.Add($id)
            }
        }
        if ($updatedIds.Count -ne 171) {
            throw "Test Case DOCX hanya memuat $($updatedIds.Count) dari 171 ID CSV setelah rekonsiliasi."
        }

        # Populate Test Case summary.
        $testSummary = $doc.Tables.Item(23)
        Set-CellText $testSummary 2 2 '171'
        Set-CellText $testSummary 3 2 '16'
        Set-CellText $testSummary 4 2 '0'
        Set-CellText $testSummary 5 2 '5'
        Set-CellText $testSummary 6 2 '0'
        Set-CellText $testSummary 7 2 '150'

        # Populate Test Case defect register.
        $testDefects = $doc.Tables.Item(24)
        Ensure-RowCount $testDefects 5
        $testDefectData = @(
            @('1', 'DEF-001', 'Tujuh ID Test Case tidak mempunyai baris skenario.', 'Medium', "FIXED ${emDash} direkonsiliasi dari RULE-038${enDash}043 dan RULE-050"),
            @('2', 'DEF-002', 'Fixture legacy SiswaImportStatusTest tidak memenuhi field wajib.', 'Medium', "OPEN test asset ${emDash} implementation path PASS"),
            @('3', 'DEF-003', 'Guard Midtrans mengabaikan flag disabled.', 'Medium', "FIXED ${emDash} targeted/module retest PASS"),
            @('4', 'DEF-004', 'Feature tests memakai MySQL lokal persisten.', 'High', "DEFERRED ${emDash} test infrastructure hardening required")
        )
        for ($index = 0; $index -lt $testDefectData.Count; $index++) {
            for ($column = 1; $column -le 5; $column++) {
                Set-CellText $testDefects ($index + 2) $column $testDefectData[$index][$column - 1]
            }
        }

        # Names are recommendations only; dates/signatures remain explicitly pending.
        Set-CellText $doc.Tables.Item(25) 2 2 'Yayan Wahyudi'
        Set-CellText $doc.Tables.Item(25) 2 3 '[DIISI SAAT REVIEW]'
        Set-CellText $doc.Tables.Item(25) 3 2 'Tabah Ujianto / Irent Berliana Agustin'
        Set-CellText $doc.Tables.Item(25) 3 3 '[DIISI SAAT REVIEW]'
        Set-CellText $doc.Tables.Item(25) 4 2 '[DIISI BILA DIPERLUKAN]'
        Set-CellText $doc.Tables.Item(25) 4 3 '[DIISI SAAT REVIEW]'

        $doc.Save()
        if ($ExportPdf) {
            $doc.ExportAsFixedFormat($testCasePdf, 17)
        }
    }
    finally {
        $doc.Close($false)
        [void] [System.Runtime.InteropServices.Marshal]::ReleaseComObject($doc)
    }

    $doc = $word.Documents.Open($sitDocx, $false, $false)
    try {
        # Metadata and history make the partial nature explicit.
        Set-CellText $doc.Tables.Item(1) 4 2 '1.1'
        Set-CellText $doc.Tables.Item(1) 5 2 "PARTIAL TECHNICAL RETEST ${emDash} NOT FINAL; EXIT CRITERIA BELUM TERPENUHI"
        Set-CellText $doc.Tables.Item(1) 9 2 'Hasil technical retest 12 Agustus 2026 telah dicatat. Manual technical, external integration, UAT, approval, dan BAST belum selesai/dibuat.'
        Set-CellText $doc.Tables.Item(2) 3 1 '1.1'
        Set-CellText $doc.Tables.Item(2) 3 2 '12 Agustus 2026'
        Set-CellText $doc.Tables.Item(2) 3 3 'Pengisian hasil 45 SIT: 9 PASS, 8 BLOCKED, 15 NOT EXECUTED, 13 MANUAL/UAT REQUIRED; defect dan evidence diperbarui.'
        Set-CellText $doc.Tables.Item(2) 3 4 "Partial Technical Retest ${emDash} Not Final"

        $sitById = @{}
        foreach ($sitRow in $sitRows) {
            $sitById[$sitRow.'SIT ID'] = $sitRow
        }
        $resultTable = $doc.Tables.Item(7)
        $updatedSitIds = New-Object System.Collections.Generic.HashSet[string]
        for ($rowIndex = 2; $rowIndex -le $resultTable.Rows.Count; $rowIndex++) {
            $id = Get-CellText $resultTable.Cell($rowIndex, 1)
            if (-not $sitById.ContainsKey($id)) { continue }
            $sitRow = $sitById[$id]
            Set-CellText $resultTable $rowIndex 2 $sitRow.Status
            Set-CellText $resultTable $rowIndex 3 (Get-DateLabel $sitRow.'Actual Test Date')
            $tester = if ($sitRow.Status -eq 'PASS') { "Codex ${emDash} Technical Verification" } else { [string] $emDash }
            Set-CellText $resultTable $rowIndex 4 $tester
            Set-CellText $resultTable $rowIndex 5 $(if ([string]::IsNullOrWhiteSpace($sitRow.Evidence)) { [string] $emDash } else { $sitRow.Evidence })
            Set-CellText $resultTable $rowIndex 6 $(if ([string]::IsNullOrWhiteSpace($sitRow.'Defect ID')) { [string] $emDash } else { $sitRow.'Defect ID' })
            Set-CellText $resultTable $rowIndex 7 (Join-ExecutionComment $sitRow)
            [void] $updatedSitIds.Add($id)
        }
        if ($updatedSitIds.Count -ne 45) {
            throw "SIT DOCX hanya memuat $($updatedSitIds.Count) dari 45 ID CSV."
        }

        # Evidence index for the actual retest files.
        $evidenceTable = $doc.Tables.Item(8)
        $evidenceRows = @(
            @('EV-001', 'SIT-INT-003; SIT-RSK-001; SIT-RSK-002', 'Automated test output', 'evidence/targeted-authorization-integrity.txt'),
            @('EV-002', 'SIT-RSK-005', 'Automated test output', 'evidence/targeted-siswa-import-valid-fixture.txt'),
            @('EV-003', 'SIT-RSK-007; SIT-RSK-013', 'Automated test output', 'evidence/targeted-webhook-notification-security.txt'),
            @('EV-004', 'SIT-RSK-009', 'Automated test output', 'evidence/targeted-payment-module-after-fix.txt'),
            @('EV-005', 'SIT-RSK-010', 'Before/after defect evidence', 'evidence/before-midtrans-disabled-server-side.txt; evidence/after-midtrans-disabled-server-side.txt'),
            @('EV-006', 'SIT-RSK-011', 'Automated test output', 'evidence/targeted-payment-module-after-fix.txt'),
            @('EV-007', 'Seluruh regression', 'Full regression output', 'evidence/final-php-artisan-test.txt'),
            @('EV-008', 'Test infrastructure', 'Configuration audit', 'evidence/test-database-connection-audit.txt')
        )
        Ensure-RowCount $evidenceTable ($evidenceRows.Count + 1)
        for ($index = 0; $index -lt $evidenceRows.Count; $index++) {
            $rowIndex = $index + 2
            Set-CellText $evidenceTable $rowIndex 1 $evidenceRows[$index][0]
            Set-CellText $evidenceTable $rowIndex 2 $evidenceRows[$index][1]
            Set-CellText $evidenceTable $rowIndex 3 $evidenceRows[$index][2]
            Set-CellText $evidenceTable $rowIndex 4 $evidenceRows[$index][3]
            Set-CellText $evidenceTable $rowIndex 5 '12 Agustus 2026'
            Set-CellText $evidenceTable $rowIndex 6 'Review manusia pending'
        }

        # SIT defect register.
        $sitDefects = $doc.Tables.Item(9)
        Ensure-RowCount $sitDefects 4
        $sitDefectData = @(
            @('DEF-002', 'SIT-RSK-005', 'Medium', 'Fixture test legacy tidak memenuhi field wajib; implementation path valid PASS.', 'OPEN test asset', 'EV-002; EV-007'),
            @('DEF-003', 'SIT-RSK-010', 'Medium', 'Guard Midtrans server-side mengabaikan flag disabled.', 'FIXED / RETEST PASS', 'EV-005'),
            @('DEF-004', 'Test infrastructure', 'High', '38 Feature test mengarah ke MySQL lokal persisten; isolasi suite belum memadai.', 'DEFERRED', 'EV-008')
        )
        for ($index = 0; $index -lt $sitDefectData.Count; $index++) {
            for ($column = 1; $column -le 6; $column++) {
                Set-CellText $sitDefects ($index + 2) $column $sitDefectData[$index][$column - 1]
            }
        }

        # Open items updated from actual outcomes.
        $openItems = $doc.Tables.Item(10)
        Set-CellText $openItems 2 3 'Root cause terverifikasi sebagai fixture legacy yang tidak mengisi nama_kelas dan agama. Controlled fixture valid PASS; existing failing test tetap dipertahankan sebagai DEF-002.'
        Set-CellText $openItems 2 4 'IMPLEMENTATION PASS / TEST ASSET OPEN'
        Set-CellText $openItems 4 3 'Guard server-side telah diperbaiki memakai isMidtransEnabled(); targeted test dan module retest PASS. Lihat DEF-003.'
        Set-CellText $openItems 4 4 'FIXED / RETEST PASS'
        if ($null -eq (Find-RowByFirstCell $openItems 'SIT-OPEN-007')) {
            $newOpenRow = $openItems.Rows.Add()
            $openRowIndex = $newOpenRow.Index
            Set-CellText $openItems $openRowIndex 1 'SIT-OPEN-007'
            Set-CellText $openItems $openRowIndex 2 'Test database isolation'
            Set-CellText $openItems $openRowIndex 3 'Feature suite masih bergantung pada MySQL lokal persisten. Migrasi ke database testing khusus harus dilakukan dan diverifikasi terpisah.'
            Set-CellText $openItems $openRowIndex 4 'OPEN / DEF-004'
        }

        # SIT totals, including the two statuses absent from the original template.
        $sitSummary = $doc.Tables.Item(11)
        foreach ($metric in @('NOT EXECUTED', 'MANUAL/UAT REQUIRED')) {
            if ($null -eq (Find-RowByFirstCell $sitSummary $metric)) {
                $beforeRowIndex = Find-RowByFirstCell $sitSummary 'Open Critical/High Defect'
                $newMetricRow = $sitSummary.Rows.Add($sitSummary.Rows.Item($beforeRowIndex))
                Set-CellText $sitSummary $newMetricRow.Index 1 $metric
                Set-CellText $sitSummary $newMetricRow.Index 2 '0'
            }
        }
        $summaryValues = @{
            'Total planned test' = '45'
            'Executed' = '9'
            'PASS' = '9'
            'FAIL' = '0'
            'BLOCKED' = '8'
            'N/A' = '0'
            'NOT EXECUTED' = '15'
            'MANUAL/UAT REQUIRED' = '13'
            'Open Critical/High Defect' = "1 ${emDash} DEF-004 (High, deferred)"
            'Retest Completed' = 'DEF-003 PASS; DEF-002 implementation path PASS'
            'SIT Completion Status' = "NOT COMPLETED ${emDash} EXIT CRITERIA BELUM TERPENUHI"
        }
        foreach ($metric in $summaryValues.Keys) {
            $rowIndex = Find-RowByFirstCell $sitSummary $metric
            if ($null -eq $rowIndex) { throw "Metric SIT tidak ditemukan: $metric" }
            Set-CellText $sitSummary $rowIndex 2 $summaryValues[$metric]
        }

        $doc.Save()
        if ($ExportPdf) {
            $doc.ExportAsFixedFormat($sitPdf, 17)
        }
    }
    finally {
        $doc.Close($false)
        [void] [System.Runtime.InteropServices.Marshal]::ReleaseComObject($doc)
    }
}
finally {
    $word.Quit()
    [void] [System.Runtime.InteropServices.Marshal]::ReleaseComObject($word)
    [GC]::Collect()
    [GC]::WaitForPendingFinalizers()
}

$updatedTargets = if ($ExportPdf) { 'DOCX and PDF' } else { 'DOCX' }
Write-Output "Updated Test Case and SIT $updatedTargets."
