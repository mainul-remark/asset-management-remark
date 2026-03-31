<?php

namespace Tests\Feature;

use App\Http\Requests\Backend\KV\KeyVisualFileRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class KeyVisualFileRequestTest extends TestCase
{
    public function test_it_rejects_images_larger_than_five_megabytes(): void
    {
        $request = new KeyVisualFileRequest();
        $request->setMethod('POST');

        $validator = Validator::make([
            'kv_file_upload' => UploadedFile::fake()->image('too-large.png')->size(6000),
        ], [
            'kv_file_upload' => $request->rules()['kv_file_upload'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertSame('Image files must not exceed 5 MB.', $validator->errors()->first('kv_file_upload'));
    }

    public function test_it_rejects_videos_larger_than_ten_megabytes(): void
    {
        $request = new KeyVisualFileRequest();
        $request->setMethod('POST');

        $validator = Validator::make([
            'kv_file_upload' => UploadedFile::fake()->create('too-large.mp4', 11000, 'video/mp4'),
        ], [
            'kv_file_upload' => $request->rules()['kv_file_upload'],
        ]);

        $this->assertTrue($validator->fails());
        $this->assertSame('Video files must not exceed 10 MB.', $validator->errors()->first('kv_file_upload'));
    }

    public function test_it_allows_files_within_the_type_specific_size_limit(): void
    {
        $request = new KeyVisualFileRequest();
        $request->setMethod('POST');

        $validator = Validator::make([
            'kv_file_upload' => UploadedFile::fake()->image('allowed.png')->size(5000),
        ], [
            'kv_file_upload' => $request->rules()['kv_file_upload'],
        ]);

        $this->assertTrue($validator->passes());
    }
}
