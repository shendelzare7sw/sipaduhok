@include('layouts.partials.cleanflow-lms-shell', [
    'shellName' => 'HOK Learning',
    'shellSubtitle' => 'Learning Management System',
    'roleLabel' => 'Siswa',
    'homeUrl' => route('siswa.lms.dashboard'),
    'backUrl' => route('siswa.sia.dashboard'),
    'backLabel' => 'Kembali ke SIA',
    'notificationContext' => 'lms',
    'legacyLayoutCss' => 'resources/css/layouts/lms.css',
    'showChatbot' => false,
])
