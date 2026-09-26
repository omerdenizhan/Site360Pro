<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    public function test_download_password_form_is_public(): void
    {
        $this->get('/downloads')
            ->assertOk()
            ->assertSee('142857');
    }

    public function test_wrong_password_does_not_download_archive(): void
    {
        config(['downloads.password_hash' => Hash::make('correct-password')]);

        $this->from('/downloads')
            ->post('/downloads', ['password' => 'wrong-password'])
            ->assertRedirect('/downloads')
            ->assertSessionHasErrors('password');
    }

    public function test_correct_password_downloads_archive(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('downloads/Site360Pro-test.zip', 'zip-content');

        config([
            'downloads.password_hash' => Hash::make('correct-password'),
            'downloads.archive_path' => Storage::disk('local')->path('downloads/Site360Pro-test.zip'),
            'downloads.filename' => 'Site360Pro-test.zip',
        ]);

        $this->post('/downloads', ['password' => 'correct-password'])
            ->assertDownload('Site360Pro-test.zip');
    }
}
