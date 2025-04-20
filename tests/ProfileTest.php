<?php
use PHPUnit\Framework\TestCase;

class ProfileTest extends TestCase {
    public function testUpdateProfileImage() {
        $this->expectNotToPerformAssertions();
        updateProfileImage(1, ['tmp_name' => '/path/to/temp/file', 'name' => 'profile.jpg', 'size' => 1024, 'type' => 'image/jpeg']);
    }
}
