<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Regresi-guard untuk F-01 & F-02 (Broken Access Control pada preview file):
 *  - Token preview harus acak & terikat ke pemilik (user lain tidak bisa membuka).
 *  - Route raw-path lama (/storage-preview?path=) harus sudah dihapus.
 */
class FilePreviewAccessTest extends TestCase
{
    public function test_view_document_hanya_bisa_dibuka_pemiliknya(): void
    {
        $userA = new User();
        $userA->id = 900001;
        $userB = new User();
        $userB->id = 900002;

        $path = 'audit-test/dummy.pdf';
        Storage::disk('public')->put($path, "%PDF-1.4 dummy");

        try {
            // User A membuat token preview (helper mengikat ke auth()->id()).
            $this->actingAs($userA);
            $url = preview_url($path);
            $this->assertNotNull($url);
            $token = last(explode('/', $url));

            // Pemilik boleh membuka.
            $this->actingAs($userA)->withoutMiddleware()
                ->get('/view-document/' . $token)->assertOk();

            // User lain DITOLAK (403) walau tahu token-nya.
            $this->actingAs($userB)->withoutMiddleware()
                ->get('/view-document/' . $token)->assertForbidden();

            // Token asal-asalan / tidak dikenal => 404 (tidak bisa dienumerasi).
            $this->actingAs($userA)->withoutMiddleware()
                ->get('/view-document/tokenAcakYangTidakAdaDiCache123')->assertNotFound();
        } finally {
            Storage::disk('public')->delete($path);
        }
    }

    public function test_route_storage_preview_raw_path_sudah_dihapus(): void
    {
        $user = new User();
        $user->id = 900003;

        $this->actingAs($user)->withoutMiddleware()
            ->get('/storage-preview?path=audit-test/dummy.pdf')
            ->assertNotFound();
    }
}
