@include('layouts.partials.cleanflow-lms-shell', [
    'shellName' => 'HOK Teaching',
    'shellSubtitle' => 'Learning Management System',
    'roleLabel' => 'Guru',
    'homeUrl' => route('guru.dashboard'),
    'backUrl' => route('guru.dashboard'),
    'backLabel' => 'Kembali ke Dashboard',
    'notificationContext' => 'lms-guru',
    'legacyLayoutCss' => 'resources/css/layouts/lms-guru.css',
    'showChatbot' => true,
])
