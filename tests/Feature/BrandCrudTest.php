<?php

namespace Tests\Feature;

use App\Http\Controllers\Backend\KV\BrandController;
use App\Http\Requests\Backend\KV\BrandRequest;
use App\Models\Brand;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class BrandCrudTest extends TestCase
{
    public function test_edit_returns_a_json_payload_for_the_brand_modal(): void
    {
        $brand = new Brand([
            'id' => 15,
            'name' => 'Remark',
            'code' => 'RMK',
            'description' => 'Brand details',
            'status' => 1,
            'logo' => 'backend/assets/uploaded-files/brands/remark.png',
        ]);

        $response = app(BrandController::class)->edit($brand);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertSame('Remark', $response->getData(true)['name']);
        $this->assertSame('RMK', $response->getData(true)['code']);
    }

    public function test_it_allows_reusing_soft_deleted_brand_identifiers_when_creating(): void
    {
        $originalDefaultConnection = config('database.default');
        $this->bootSqliteBrandsTable();

        try {
            DB::table('brands')->insert([
                'id' => 1,
                'name' => 'Archived Brand',
                'code' => 'ARC',
                'description' => 'Old record',
                'status' => 1,
                'logo' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => now(),
            ]);

            $request = BrandRequest::create('/brands', 'POST');
            $request->setMethod('POST');

            $validator = Validator::make([
                'name' => 'Archived Brand',
                'code' => 'ARC',
                'description' => 'Replacement record',
                'status' => '1',
            ], $request->rules(), $request->messages());

            $this->assertTrue($validator->passes());
        } finally {
            $this->dropSqliteBrandsTable();
            config(['database.default' => $originalDefaultConnection]);
        }
    }

    public function test_it_ignores_the_current_brand_and_soft_deleted_rows_when_updating(): void
    {
        $originalDefaultConnection = config('database.default');
        $this->bootSqliteBrandsTable();

        try {
            DB::table('brands')->insert([
                [
                    'id' => 1,
                    'name' => 'Active Brand',
                    'code' => 'ACT',
                    'description' => 'Current record',
                    'status' => 1,
                    'logo' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null,
                ],
                [
                    'id' => 2,
                    'name' => 'Active Brand',
                    'code' => 'ACT',
                    'description' => 'Archived duplicate',
                    'status' => 0,
                    'logo' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => now(),
                ],
            ]);

            $brand = Brand::query()->findOrFail(1);

            $request = new class($brand) extends BrandRequest
            {
                public function __construct(private Brand $brand)
                {
                }

                public function route($param = null, $default = null): mixed
                {
                    if ($param === 'brand') {
                        return $this->brand;
                    }

                    return parent::route($param, $default);
                }
            };
            $request->initialize([], [], [], [], [], ['REQUEST_METHOD' => 'PUT']);

            $validator = Validator::make([
                'name' => 'Active Brand',
                'code' => 'ACT',
                'description' => 'Updated record',
                'status' => '1',
            ], $request->rules(), $request->messages());

            $this->assertTrue($validator->passes());
        } finally {
            $this->dropSqliteBrandsTable();
            config(['database.default' => $originalDefaultConnection]);
        }
    }

    private function bootSqliteBrandsTable(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::connection('sqlite')->dropIfExists('brands');
        Schema::connection('sqlite')->create('brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->text('logo')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    private function dropSqliteBrandsTable(): void
    {
        Schema::connection('sqlite')->dropIfExists('brands');
        DB::purge('sqlite');
    }
}
