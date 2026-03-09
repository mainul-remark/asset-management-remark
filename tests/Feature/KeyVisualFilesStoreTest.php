<?php

namespace Tests\Feature;

use App\Http\Controllers\Backend\KV\KeyVisualFilesController;
use App\Http\Requests\Backend\KV\KeyVisualFileRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class KeyVisualFilesStoreTest extends TestCase
{
    public function test_it_prepares_upload_payload_without_reading_size_after_move(): void
    {
        $controller = new KeyVisualFilesController();
        $upload = UploadedFile::fake()->image('controller-size-test.png', 200, 100)->size(256);

        $request = new class($upload) extends KeyVisualFileRequest
        {
            public function __construct(private UploadedFile $upload)
            {
            }

            public function validated($key = null, $default = null): array
            {
                return [
                    'name' => 'Homepage Hero File',
                    'key_visual_id' => 1,
                    'key_visual_size_id' => 1,
                    'kv_size' => 256,
                    'aspect_ratio' => 2.0,
                    'file_type' => 'image/png',
                    'file_duration' => '',
                    'status' => '1',
                ];
            }

            public function hasFile($key): bool
            {
                return $key === 'kv_file_upload';
            }

            public function file($key = null, $default = null): UploadedFile|null|array
            {
                return $key === 'kv_file_upload' ? $this->upload : $default;
            }
        };

        $method = new \ReflectionMethod($controller, 'preparePayload');
        $method->setAccessible(true);
        $payload = $method->invoke($controller, $request);

        $this->assertSame(256, $payload['kv_size']);
        $this->assertSame('image/png', $payload['file_type']);
        $this->assertStringStartsWith('backend/assets/uploaded-files/key-visual-files/', $payload['kv_file']);
        $this->assertFileExists(public_path($payload['kv_file']));

        File::delete(public_path($payload['kv_file']));
    }
}
