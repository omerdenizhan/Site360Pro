<?php
namespace Tests\Unit;
use App\Services\Installer\EnvironmentWriter;
use PHPUnit\Framework\TestCase;
class EnvironmentWriterTest extends TestCase
{
    private string $directory;
    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir().'/env-writer-'.bin2hex(random_bytes(4));
        mkdir($this->directory);
        file_put_contents($this->directory.'/.env.example', "APP_NAME=Site360Pro\nAPP_KEY=\n#DB_HOST=127.0.0.1\nDB_CONNECTION=sqlite\n");
    }
    protected function tearDown(): void
    {
        foreach (glob($this->directory.'/{,.}*', GLOB_BRACE) as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        rmdir($this->directory);
        parent::tearDown();
    }
    private function writer(): EnvironmentWriter
    {
        return new EnvironmentWriter($this->directory.'/.env', $this->directory.'/.env.example', $this->directory.'/config.php');
    }
    public function test_it_creates_env_from_example(): void
    {
        $writer = $this->writer();
        $this->assertFalse($writer->exists());
        $this->assertTrue($writer->ensureExists());
        $this->assertSame('Site360Pro', $writer->get('APP_NAME'));
    }
    public function test_it_replaces_uncomments_and_appends_values(): void
    {
        $writer = $this->writer();
        $writer->ensureExists();
        $writer->set(['APP_KEY' => 'base64:abc==', 'DB_HOST' => 'db.local', 'EXTRA' => 'x']);
        $contents = $writer->contents();
        $this->assertStringContainsString("APP_KEY=base64:abc==\n", $contents);
        $this->assertStringContainsString("DB_HOST=db.local\n", $contents);
        $this->assertStringNotContainsString('#DB_HOST', $contents);
        $this->assertStringContainsString("EXTRA=x\n", $contents);
        $this->assertSame(1, substr_count($contents, 'APP_KEY='));
    }
    public function test_special_characters_round_trip(): void
    {
        $writer = $this->writer();
        $writer->ensureExists();
        $password = "p@ss #w'o\\rd \$x \"q\"";
        $writer->set(['DB_PASSWORD' => $password, 'MAIL_FROM_NAME' => 'Site 360 Pro']);
        $this->assertSame($password, $writer->get('DB_PASSWORD'));
        $this->assertSame('Site 360 Pro', $writer->get('MAIL_FROM_NAME'));
    }
    public function test_forget_comments_out_a_key_and_clears_config_cache(): void
    {
        $writer = $this->writer();
        $writer->ensureExists();
        file_put_contents($this->directory.'/config.php', '<?php return [];');
        $writer->set(['DB_HOST' => 'db.local']);
        $writer->forget(['DB_HOST']);
        $this->assertNull($writer->get('DB_HOST'));
        $this->assertStringContainsString('#DB_HOST=db.local', $writer->contents());
        $this->assertFileDoesNotExist($this->directory.'/config.php');
    }
    public function test_booleans_and_empty_values_are_formatted(): void
    {
        $this->assertSame('true', EnvironmentWriter::format(true));
        $this->assertSame('false', EnvironmentWriter::format(false));
        $this->assertSame('', EnvironmentWriter::format(''));
        $this->assertSame('null', EnvironmentWriter::format(null));
        $this->assertSame('https://site.example.com', EnvironmentWriter::format('https://site.example.com'));
    }
}
