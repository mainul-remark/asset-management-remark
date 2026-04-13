# 01. Setup And First Table

This file shows the smallest clean path to get your first table working.

## 1. Install the package

If the package is not installed yet:

```bash
composer require rappasoft/laravel-livewire-tables
```

In this project it is already installed.

## 2. Publish the configuration

Publish Livewire config if you do not already have it:

```bash
php artisan livewire:publish --config
```

Publish the Rappasoft config:

```bash
php artisan vendor:publish --tag=livewire-tables-config
```

Optional:

- publish views if you want to deeply customize package blades
- publish public assets if you disable asset injection

```bash
php artisan vendor:publish --tag=livewire-tables-views
php artisan vendor:publish --tag=livewire-tables-public
```

## 3. Switch the table theme to Bootstrap 5

Edit `config/livewire-tables.php`:

```php
'theme' => 'bootstrap-5',
```

This package supports:

- `tailwind`
- `bootstrap-4`
- `bootstrap-5`

## 4. Make sure Livewire assets exist in your layout

Your main layout should include:

```blade
@livewireStyles
```

inside `<head>`, and:

```blade
@livewireScripts
```

before `</body>`.

If your app already uses Livewire, this is probably already done.

## 5. Create a simple model if you need a demo table

For learning, use a small model. Example:

```bash
php artisan make:model Product -m
```

Example migration:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->decimal('price', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

Example model:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
```

Run the migration:

```bash
php artisan migrate
```

## 6. Generate a datatable component

Use the package command:

```bash
php artisan make:datatable ProductsTable Product
```

The installed package exposes the `make:datatable` command.

## 7. Build your first table component

Create or update `app/Livewire/ProductsTable.php`:

```php
<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class ProductsTable extends DataTableComponent
{
    protected $model = Product::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');

        $this->setTableAttributes([
            'class' => 'table table-striped table-bordered align-middle mb-0',
        ]);

        $this->setDefaultSort('id', 'desc');
        $this->setSearchPlaceholder('Search products...');
        $this->setPerPageAccepted([10, 25, 50, 100]);
        $this->setDefaultPerPage(10);
        $this->setPaginationTheme('bootstrap-5');
        $this->setFilterLayoutSlideDown();
        $this->setColumnSelectEnabled();
        $this->setRememberColumnSelectionEnabled();
    }

    public function builder(): Builder
    {
        return Product::query()->select('products.*');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->sortable()
                ->collapseOnMobile(),

            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('SKU', 'sku')
                ->searchable()
                ->sortable(),

            Column::make('Price', 'price')
                ->sortable()
                ->format(fn ($value) => number_format((float) $value, 2)),

            Column::make('Status', 'is_active')
                ->sortable()
                ->format(fn ($value) => $value ? 'Active' : 'Inactive'),

            Column::make('Created', 'created_at')
                ->sortable()
                ->format(fn ($value) => optional($value)->format('Y-m-d')),
        ];
    }

    public function filters(): array
    {
        return [
            TextFilter::make('Name')
                ->config([
                    'placeholder' => 'Search by name',
                    'maxlength' => 100,
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('products.name', 'like', '%'.$value.'%');
                }),

            SelectFilter::make('Status')
                ->options([
                    '' => 'All',
                    '1' => 'Active',
                    '0' => 'Inactive',
                ])
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('products.is_active', (int) $value);
                }),
        ];
    }
}
```

## 8. Render the table on a page

Add a route in `routes/web.php`:

```php
use Illuminate\Support\Facades\Route;

Route::get('/products', function () {
    return view('products.index');
})->name('products.index');
```

Create `resources/views/products/index.blade.php`:

```blade
<x-app-layout>
    <div class="container py-4">
        <h1 class="h3 mb-3">Products</h1>

        <livewire:products-table />
    </div>
</x-app-layout>
```

If you are not using Jetstream layout components, use your normal Bootstrap layout instead.

## 9. Seed some rows if the table is empty

Quick example from Tinker:

```bash
php artisan tinker
```

```php
\App\Models\Product::create([
    'name' => 'Laptop',
    'sku' => 'LP-001',
    'price' => 1200,
    'is_active' => true,
]);
```

## 10. Test checklist

Your first table is correct when:

- the page loads without JavaScript errors
- the table shows rows
- search works
- sorting works
- filters work
- pagination works
- Bootstrap 5 table styling looks correct

## Notes

- `setPrimaryKey('id')` is required in practice. Do not skip it.
- `SelectFilter` values come from HTML, so treat them as strings and cast inside the callback when needed.
- If your model is simple, `protected $model = Product::class;` is enough.
- If your query is more complex, define a custom `builder()` method.

Once this works, continue with [02-actions-modals-export.md](./02-actions-modals-export.md).
