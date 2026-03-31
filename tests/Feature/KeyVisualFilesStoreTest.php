<?php

namespace Tests\Feature;

use App\Http\Controllers\Backend\KV\KeyVisualFilesController;
use App\Http\Requests\Backend\KV\KeyVisualFileRequest;
use App\Models\KeyVisualSize;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class KeyVisualFilesStoreTest extends TestCase
{
    public function test_it_prepares_upload_payload_without_reading_size_after_move(): void
    {
        $originalDefaultConnection = config('database.default');
        $this->bootSqliteKeyVisualSizesTable();

        try {
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

                public function input($key = null, $default = null): mixed
                {
                    return $default;
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
        } finally {
            $this->dropSqliteKeyVisualSizesTable();
            config(['database.default' => $originalDefaultConnection]);
        }
    }

    public function test_it_creates_a_missing_key_visual_size_from_uploaded_media_dimensions(): void
    {
        $originalDefaultConnection = config('database.default');
        $this->bootSqliteKeyVisualSizesTable();

        try {
            $controller = new KeyVisualFilesController();
            $upload = UploadedFile::fake()->image('missing-size.png', 320, 180)->size(256);

            $request = new class($upload) extends KeyVisualFileRequest
            {
                public function __construct(private UploadedFile $upload)
                {
                }

                public function validated($key = null, $default = null): array
                {
                    return [
                        'name' => 'Auto Size File',
                        'key_visual_id' => 1,
                        'key_visual_size_id' => null,
                        'media_width' => 320,
                        'media_height' => 180,
                        'kv_size' => 256,
                        'aspect_ratio' => '',
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

                public function input($key = null, $default = null): mixed
                {
                    $data = [
                        'media_width' => 320,
                        'media_height' => 180,
                    ];

                    if ($key === null) {
                        return $data;
                    }

                    return $data[$key] ?? $default;
                }
            };

            $method = new \ReflectionMethod($controller, 'preparePayload');
            $method->setAccessible(true);
            $payload = $method->invoke($controller, $request);

            $size = KeyVisualSize::query()->firstOrFail();

            $this->assertSame((int) $size->id, $payload['key_visual_size_id']);
            $this->assertSame('320 x 180', $size->name);
            $this->assertSame('320', (string) $size->width);
            $this->assertSame('180', (string) $size->height);
            $this->assertSame('px', $size->unit_name);
            $this->assertSame(1, (int) $size->status);

            File::delete(public_path($payload['kv_file']));
        } finally {
            $this->dropSqliteKeyVisualSizesTable();
            config(['database.default' => $originalDefaultConnection]);
        }
    }

    public function test_it_filters_the_index_by_query_key_visual_and_passes_the_preselected_id(): void
    {
        $originalDefaultConnection = config('database.default');
        $this->bootSqliteIndexTables();

        try {
            DB::table('key_visuals')->insert([
                [
                    'id' => 1,
                    'name' => 'KV One',
                    'unique_code' => 'KV-001',
                    'deleted_at' => null,
                ],
                [
                    'id' => 2,
                    'name' => 'KV Two',
                    'unique_code' => 'KV-002',
                    'deleted_at' => null,
                ],
            ]);

            DB::table('key_visual_sizes')->insert([
                'id' => 10,
                'name' => '320 x 180',
                'width' => 320,
                'height' => 180,
                'unit_name' => 'px',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('key_visual_files')->insert([
                [
                    'name' => 'KV One File',
                    'key_visual_id' => 1,
                    'key_visual_size_id' => 10,
                    'kv_file' => 'backend/assets/uploaded-files/key-visual-files/one.png',
                    'kv_size' => 100,
                    'aspect_ratio' => 1.7778,
                    'file_type' => 'image/png',
                    'file_duration' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ],
                [
                    'name' => 'KV Two File',
                    'key_visual_id' => 2,
                    'key_visual_size_id' => 10,
                    'kv_file' => 'backend/assets/uploaded-files/key-visual-files/two.png',
                    'kv_size' => 100,
                    'aspect_ratio' => 1.7778,
                    'file_type' => 'image/png',
                    'file_duration' => null,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ],
            ]);

            $controller = new KeyVisualFilesController();
            $view = $controller->index(Request::create('/key-visual-files', 'GET', ['kv' => 1]));
            $data = $view->getData();

            $this->assertSame(1, $data['selectedKeyVisualId']);
            $this->assertCount(1, $data['kvFiles']);
            $this->assertSame(1, $data['kvFiles']->first()->key_visual_id);
        } finally {
            $this->dropSqliteIndexTables();
            config(['database.default' => $originalDefaultConnection]);
        }
    }

    private function bootSqliteKeyVisualSizesTable(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::connection('sqlite')->dropIfExists('key_visual_sizes');
        Schema::connection('sqlite')->create('key_visual_sizes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->decimal('height', 12, 0)->default(0);
            $table->decimal('width', 12, 0)->default(0);
            $table->string('unit_name')->default('px');
            $table->tinyInteger('status')->default(1)->nullable();
            $table->timestamps();
        });
    }

    private function dropSqliteKeyVisualSizesTable(): void
    {
        Schema::connection('sqlite')->dropIfExists('key_visual_sizes');
        DB::purge('sqlite');
    }

    private function bootSqliteIndexTables(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::connection('sqlite')->dropIfExists('key_visual_files');
        Schema::connection('sqlite')->dropIfExists('key_visual_sizes');
        Schema::connection('sqlite')->dropIfExists('key_visuals');

        Schema::connection('sqlite')->create('key_visuals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('unique_code')->nullable();
            $table->softDeletes();
        });

        Schema::connection('sqlite')->create('key_visual_sizes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->decimal('height', 12, 0)->default(0);
            $table->decimal('width', 12, 0)->default(0);
            $table->string('unit_name')->default('px');
            $table->tinyInteger('status')->default(1)->nullable();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('key_visual_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->unsignedBigInteger('key_visual_id');
            $table->unsignedBigInteger('key_visual_size_id');
            $table->longText('kv_file');
            $table->mediumInteger('kv_size')->default(0)->nullable();
            $table->float('aspect_ratio')->default(0)->nullable();
            $table->string('file_type')->nullable();
            $table->string('file_duration')->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function dropSqliteIndexTables(): void
    {
        Schema::connection('sqlite')->dropIfExists('key_visual_files');
        Schema::connection('sqlite')->dropIfExists('key_visual_sizes');
        Schema::connection('sqlite')->dropIfExists('key_visuals');
        DB::purge('sqlite');
    }
}
