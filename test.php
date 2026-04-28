<?php
try {
    // 1. Setup Auth
    $admin = App\Models\User::where('role', 'admin')->first();
    auth()->login($admin);

    // 2. Mock Request
    $request = Illuminate\Http\Request::create('/admin/keuangan/tagihan', 'GET');

    // 3. Instantiate Controller & Call Method
    $controller = new \App\Http\Controllers\Admin\Keuangan\TagihanController();
    $response = $controller->index($request);

    // 4. Render View (this is where blade compilation/execution happens)
    echo $response->render();
    
    echo "\n\n=== SUCCESS ===\n";
} catch (\Exception $e) {
    echo "\n\n=== EXCEPTION CAUGHT ===\n";
    echo "ERROR MESSAGE: " . $e->getMessage() . "\n";
    echo "FILE: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo $e->getTraceAsString();
}
