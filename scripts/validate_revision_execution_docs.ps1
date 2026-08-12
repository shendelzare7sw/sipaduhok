Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$repositoryRoot = Split-Path -Parent $PSScriptRoot
$inputRoot = Join-Path $repositoryRoot 'docs\revisi\execution-input'
$testCaseDocx = Join-Path $inputRoot 'Test_Case_Positive_Negative_SIPADUHOK.docx'
$sitDocx = Join-Path $inputRoot 'System_Integration_Testing_SIT_SIPADUHOK_Prepared_Awaiting_Execution.docx'
$enDash = [char] 0x2013

function Get-CellText {
    param($Cell)
    return (($Cell.Range.Text -replace '[\r\a]', ' ') -replace '\s+', ' ').Trim()
}

function Assert-Equal {
    param($Actual, $Expected, [string] $Label)
    if ($Actual -ne $Expected) {
        throw "$Label expected=$Expected actual=$Actual"
    }
}

$word = New-Object -ComObject Word.Application
$word.Visible = $false
$word.DisplayAlerts = 0

try {
    $doc = $word.Documents.Open($testCaseDocx, $false, $true)
    try {
        $ids = New-Object System.Collections.Generic.List[string]
        $statuses = New-Object System.Collections.Generic.List[string]
        for ($tableIndex = 1; $tableIndex -le $doc.Tables.Count; $tableIndex++) {
            $table = $doc.Tables.Item($tableIndex)
            if ($table.Columns.Count -ne 10) { continue }
            for ($rowIndex = 2; $rowIndex -le $table.Rows.Count; $rowIndex++) {
                $id = Get-CellText $table.Cell($rowIndex, 1)
                if ($id -notmatch '^TC-[PN]-\d{3}$') { continue }
                $ids.Add($id)
                $statuses.Add((Get-CellText $table.Cell($rowIndex, 8)))
            }
        }
        Assert-Equal $ids.Count 171 'Test Case row count'
        Assert-Equal (($ids | Sort-Object -Unique).Count) 171 'Test Case unique ID count'
        Assert-Equal (($ids | Where-Object { $_ -match '^TC-P-' }).Count) 101 'Positive Test Case count'
        Assert-Equal (($ids | Where-Object { $_ -match '^TC-N-' }).Count) 70 'Negative Test Case count'
        Assert-Equal (($statuses | Where-Object { $_ -eq 'PASS' }).Count) 16 'Test Case PASS count'
        Assert-Equal (($statuses | Where-Object { $_ -eq 'BLOCKED' }).Count) 5 'Test Case BLOCKED count'
        Assert-Equal (($statuses | Where-Object { $_ -eq 'NOT EXECUTED' }).Count) 150 'Test Case NOT EXECUTED count'
        foreach ($requiredId in @('TC-N-038', 'TC-N-039', 'TC-N-040', 'TC-N-041', 'TC-N-042', 'TC-N-043', 'TC-N-050')) {
            if (-not $ids.Contains($requiredId)) { throw "Missing reconciled ID: $requiredId" }
        }
        $content = $doc.Content.Text
        if ($content -notmatch "April 2026 Minggu III${enDash}IV") { throw 'Test Case historical period label missing.' }
        if ($content -notmatch '12 Agustus 2026') { throw 'Test Case retest date missing.' }
        Write-Output 'TEST CASE: 171 unique rows; 101 positive; 70 negative; 16 PASS; 5 BLOCKED; 150 NOT EXECUTED.'
    }
    finally {
        $doc.Close($false)
        [void] [System.Runtime.InteropServices.Marshal]::ReleaseComObject($doc)
    }

    $doc = $word.Documents.Open($sitDocx, $false, $true)
    try {
        $table = $doc.Tables.Item(7)
        $ids = New-Object System.Collections.Generic.List[string]
        $statuses = New-Object System.Collections.Generic.List[string]
        for ($rowIndex = 2; $rowIndex -le $table.Rows.Count; $rowIndex++) {
            $id = Get-CellText $table.Cell($rowIndex, 1)
            if ($id -notmatch '^SIT-') { continue }
            $ids.Add($id)
            $statuses.Add((Get-CellText $table.Cell($rowIndex, 2)))
        }
        Assert-Equal $ids.Count 45 'SIT row count'
        Assert-Equal (($ids | Sort-Object -Unique).Count) 45 'SIT unique ID count'
        Assert-Equal (($statuses | Where-Object { $_ -eq 'PASS' }).Count) 9 'SIT PASS count'
        Assert-Equal (($statuses | Where-Object { $_ -eq 'BLOCKED' }).Count) 8 'SIT BLOCKED count'
        Assert-Equal (($statuses | Where-Object { $_ -eq 'NOT EXECUTED' }).Count) 15 'SIT NOT EXECUTED count'
        Assert-Equal (($statuses | Where-Object { $_ -eq 'MANUAL/UAT REQUIRED' }).Count) 13 'SIT MANUAL/UAT REQUIRED count'
        $content = $doc.Content.Text
        if ($content -notmatch 'NOT COMPLETED') { throw 'SIT non-final completion status missing.' }
        if ($content -notmatch 'DEF-003') { throw 'DEF-003 missing from SIT.' }
        if ($content -notmatch 'FIXED / RETEST PASS') { throw 'DEF-003 fixed/retest status missing from SIT.' }
        if ($content -notmatch 'DEF-002') { throw 'DEF-002 missing from SIT.' }
        if ($content -notmatch 'DEF-004') { throw 'DEF-004 missing from SIT.' }
        Write-Output 'SIT: 45 unique rows; 9 PASS; 8 BLOCKED; 15 NOT EXECUTED; 13 MANUAL/UAT REQUIRED; NOT FINAL.'
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
