@include('layouts.partials.lms-exam-shell', [
    'defaultTitle' => 'Ujian',
    'roleLabel' => isset($ujian) ? $ujian->tipe_label : 'Peserta Ujian',
])
