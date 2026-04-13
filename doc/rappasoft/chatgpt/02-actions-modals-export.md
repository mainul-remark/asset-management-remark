# 02. Actions, Modals, And Export

This file turns the basic table into a real CRUD screen.

Important:

- this package does not give you jQuery-style export buttons out of the box
- the normal pattern is to add your own toolbar buttons and call Livewire methods
- the easiest beginner approach is:
  - render custom toolbar buttons with a configurable area
  - render action buttons from a Blade partial
  - render Bootstrap modals from another Blade partial

## 1. Create an export class

This project already has `maatwebsite/excel` installed, so use it.

Create `app/Exports/ProductsExport.php`:

```php
<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected Collection $products
    ) {}

    public function collection(): Collection
    {
        return $this->products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->price,
                'status' => $product->is_active ? 'Active' : 'Inactive',
                'created_at' => optional($product->created_at)->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'SKU',
            'Price',
            'Status',
            'Created At',
        ];
    }
}
```

## 2. Upgrade the table component

Replace `app/Livewire/ProductsTable.php` with this full example:

```php
<?php

namespace App\Livewire;

use App\Exports\ProductsExport;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;
use Rappasoft\LaravelLivewireTables\Views\Filters\TextFilter;

class ProductsTable extends DataTableComponent
{
    protected $model = Product::class;

    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $name = '';
    public string $sku = '';
    public string $price = '';
    public bool $is_active = true;

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

        $this->setBulkActions([
            'exportSelected' => 'Export selected',
            'activateSelected' => 'Mark selected as active',
            'deactivateSelected' => 'Mark selected as inactive',
        ]);

        $this->setHideBulkActionsWhenEmptyEnabled();

        $this->setConfigurableAreas([
            'toolbar-right-start' => 'livewire.products-table.toolbar',
            'after-tools' => 'livewire.products-table.modals',
        ]);
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

            Column::make('Actions')
                ->label(fn ($row, $column) => view('livewire.products-table.actions', [
                    'row' => $row,
                ]))
                ->html()
                ->excludeFromColumnSelect(),
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

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'sku' => [
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($this->editingId),
            ],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->resetValidation();

        $this->dispatch('show-product-form-modal');
    }

    public function edit(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->editingId = $product->id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->price = (string) $product->price;
        $this->is_active = (bool) $product->is_active;

        $this->resetValidation();

        $this->dispatch('show-product-form-modal');
    }

    public function save(): void
    {
        $validated = $this->validate();

        if ($this->editingId) {
            $product = Product::findOrFail($this->editingId);
            $product->update($validated);
            $message = 'Product updated successfully.';
        } else {
            Product::create($validated);
            $message = 'Product created successfully.';
        }

        $this->dispatch('hide-product-form-modal');
        $this->dispatch('refreshDatatable');

        session()->flash('message', $message);

        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;

        $this->dispatch('show-delete-modal');
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        Product::findOrFail($this->deletingId)->delete();

        $this->dispatch('hide-delete-modal');
        $this->dispatch('refreshDatatable');

        session()->flash('message', 'Product deleted successfully.');

        $this->deletingId = null;
    }

    public function exportSelected()
    {
        if (! $this->hasSelected()) {
            return null;
        }

        $products = Product::query()
            ->whereKey($this->getSelected())
            ->orderBy('id')
            ->get(['id', 'name', 'sku', 'price', 'is_active', 'created_at']);

        return Excel::download(
            new ProductsExport($products),
            'products-selected-'.now()->format('Y-m-d_H-i-s').'.xlsx'
        );
    }

    public function exportAll()
    {
        $products = Product::query()
            ->orderBy('id')
            ->get(['id', 'name', 'sku', 'price', 'is_active', 'created_at']);

        return Excel::download(
            new ProductsExport($products),
            'products-all-'.now()->format('Y-m-d_H-i-s').'.xlsx'
        );
    }

    public function activateSelected(): void
    {
        $count = $this->getSelectedCount();

        Product::whereKey($this->getSelected())->update([
            'is_active' => true,
        ]);

        $this->clearSelected();
        $this->dispatch('refreshDatatable');

        session()->flash('message', $count.' products marked as active.');
    }

    public function deactivateSelected(): void
    {
        $count = $this->getSelectedCount();

        Product::whereKey($this->getSelected())->update([
            'is_active' => false,
        ]);

        $this->clearSelected();
        $this->dispatch('refreshDatatable');

        session()->flash('message', $count.' products marked as inactive.');
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingId',
            'name',
            'sku',
            'price',
        ]);

        $this->is_active = true;
    }
}
```

## 3. Create the toolbar buttons

Create `resources/views/livewire/products-table/toolbar.blade.php`:

```blade
<div class="d-flex flex-wrap gap-2">
    <button type="button" class="btn btn-primary" wire:click="create">
        Add Product
    </button>

    <button type="button" class="btn btn-outline-success" wire:click="exportAll">
        Export All
    </button>

    <button
        type="button"
        class="btn btn-outline-secondary"
        wire:click="exportSelected"
        @disabled($this->getSelectedCount() === 0)
    >
        Export Selected
    </button>
</div>
```

## 4. Create the row action buttons

Create `resources/views/livewire/products-table/actions.blade.php`:

```blade
<div class="btn-group btn-group-sm" role="group">
    <button type="button" class="btn btn-outline-primary" wire:click="edit({{ $row->id }})">
        Edit
    </button>

    <button type="button" class="btn btn-outline-danger" wire:click="confirmDelete({{ $row->id }})">
        Delete
    </button>
</div>
```

This is the easiest beginner-friendly way to build action buttons.

## 5. Create the Bootstrap 5 modals

Create `resources/views/livewire/products-table/modals.blade.php`:

```blade
<div>
    <div
        wire:ignore.self
        class="modal fade"
        id="productFormModal"
        tabindex="-1"
        aria-labelledby="productFormModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog">
            <form wire:submit="save" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productFormModalLabel">
                        {{ $editingId ? 'Edit Product' : 'Add Product' }}
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">SKU</label>
                        <input type="text" class="form-control" wire:model="sku">
                        @error('sku')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" wire:model="price">
                        @error('price')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
                        <label class="form-check-label" for="is_active">
                            Active
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        {{ $editingId ? 'Update' : 'Save' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div
        wire:ignore.self
        class="modal fade"
        id="deleteModal"
        tabindex="-1"
        aria-labelledby="deleteModalLabel"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete this product?
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="button" class="btn btn-danger" wire:click="delete">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        const getBsModal = (id) => {
            const element = document.getElementById(id);

            if (! element) {
                return null;
            }

            return bootstrap.Modal.getOrCreateInstance(element);
        };

        $wire.on('show-product-form-modal', () => {
            getBsModal('productFormModal')?.show();
        });

        $wire.on('hide-product-form-modal', () => {
            getBsModal('productFormModal')?.hide();
        });

        $wire.on('show-delete-modal', () => {
            getBsModal('deleteModal')?.show();
        });

        $wire.on('hide-delete-modal', () => {
            getBsModal('deleteModal')?.hide();
        });

        document.getElementById('productFormModal')?.addEventListener('hidden.bs.modal', () => {
            $wire.resetForm();
        });
    </script>
    @endscript
</div>
```

## 6. Show flash messages on the page

Update `resources/views/products/index.blade.php`:

```blade
<x-app-layout>
    <div class="container py-4">
        <h1 class="h3 mb-3">Products</h1>

        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <livewire:products-table />
    </div>
</x-app-layout>
```

## 7. How the modal flow works

Create flow:

1. click `Add Product`
2. `create()` runs in the component
3. the component dispatches `show-product-form-modal`
4. the script listens and opens the Bootstrap modal
5. submit the form
6. `save()` validates and stores
7. the component dispatches `hide-product-form-modal`
8. the component dispatches `refreshDatatable`

Edit flow:

1. click `Edit`
2. `edit($id)` loads the row into Livewire properties
3. the component dispatches `show-product-form-modal`
4. user updates values
5. `save()` updates the existing row because `editingId` is filled

Delete flow:

1. click `Delete`
2. `confirmDelete($id)` stores the row id and opens a confirmation modal
3. user confirms
4. `delete()` removes the row and refreshes the table

## 8. If you want export to follow active filters

The simple `exportAll()` example exports all rows from the model.

If you want to export the filtered table result instead, adapt the query. A common pattern is:

```php
$products = (clone $this->baseQuery())
    ->select('products.*')
    ->get();
```

Use this carefully if your table query joins relationships.

## 9. Why `wire:ignore.self` is used on modal roots

Bootstrap modals manage their own DOM state. Without `wire:ignore.self`, Livewire re-renders can sometimes break open/close behavior, focus handling, or the backdrop.

Use `wire:ignore.self` on the root modal element, not on the whole form contents.

## 10. Minimum files in this example

- `app/Exports/ProductsExport.php`
- `app/Livewire/ProductsTable.php`
- `resources/views/livewire/products-table/toolbar.blade.php`
- `resources/views/livewire/products-table/actions.blade.php`
- `resources/views/livewire/products-table/modals.blade.php`
- `resources/views/products/index.blade.php`

Continue with [03-common-gotchas.md](./03-common-gotchas.md).
