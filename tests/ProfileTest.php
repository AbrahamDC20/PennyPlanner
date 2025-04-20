<?php
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__) . '/controllers/profileController.php'; // Incluir el archivo necesario

class ProfileTest extends TestCase {
    public function testUpdateProfileImage() {
        $this->expectNotToPerformAssertions();
        $tempFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($tempFile, 'test content');
        updateProfileImage(1, ['tmp_name' => $tempFile, 'name' => 'profile.jpg', 'size' => 1024, 'type' => 'image/jpeg']);
        unlink($tempFile);
    }
}
