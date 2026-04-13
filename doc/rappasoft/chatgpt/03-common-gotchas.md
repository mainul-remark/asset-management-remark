# 03. Common Gotchas

These are the mistakes beginners usually make first.

## 1. Mixing `v3` package code with `v4` docs

Your installed package here is `v3.7.3`.

That means:

- prefer `v3` examples
- verify method names before copying from `v4/master`
- do not assume every new doc example exists in your installed version

## 2. Forgetting `setPrimaryKey('id')`

Always set the primary key in `configure()`:

```php
$this->setPrimaryKey('id');
```

If you skip this, selection, actions, or row handling can behave badly.

## 3. Treating this package like jQuery DataTables

This package is Livewire-first.

That means:

- columns are PHP objects
- filters are PHP objects
- actions are usually Livewire methods
- exports are usually custom methods
- modals are usually Bootstrap or Alpine markup you wire yourself

Do not expect a ready-made jQuery plugin ecosystem.

## 4. Using `emit()` instead of `dispatch()` in Livewire 3

In Livewire 3, use:

```php
$this->dispatch('event-name');
```

Do not write old Livewire 2 style code unless you are intentionally working with legacy examples.

## 5. Forgetting that filter values are strings

For example:

```php
SelectFilter::make('Status')
```

returns string values from the browser, so cast when needed:

```php
$builder->where('products.is_active', (int) $value);
```

## 6. Putting action buttons directly into plain text columns

The easiest safe pattern is:

- create an `Actions` label column
- render a Blade partial
- call `wire:click` methods from that partial

Example:

```php
Column::make('Actions')
    ->label(fn ($row, $column) => view('livewire.products-table.actions', [
        'row' => $row,
    ]))
    ->html()
```

## 7. Forgetting `->html()` on custom action columns

If your column returns a Blade view or HTML string, add:

```php
->html()
```

Without it, your buttons may render as escaped text.

## 8. Breaking Bootstrap modals during Livewire re-renders

Use:

```blade
wire:ignore.self
```

on the modal root element.

Then open and close the modal from JavaScript after a Livewire event.

## 9. Forgetting to refresh the table after create, update, or delete

After saving or deleting, dispatch:

```php
$this->dispatch('refreshDatatable');
```

This is the simplest refresh pattern for this package.

## 10. Exporting everything when you wanted only selected rows

Keep two export methods if your UX needs both:

- `exportAll()`
- `exportSelected()`

If you only keep one method, users will eventually be surprised.

## 11. Query issues with relationships

If you add relationship columns later, use a custom `builder()` and test carefully.

Common symptoms:

- duplicate rows
- ambiguous column names
- broken sorting
- broken export queries

When joins become complex, explicitly select your base table:

```php
return Product::query()->select('products.*');
```

## 12. Doing too much in the first version

Best order:

1. render table
2. add search and sort
3. add filters
4. add action buttons
5. add create and edit modal
6. add delete confirmation
7. add export
8. add bulk actions

## Suggested next improvements

After the guide examples work, you can add:

- validation messages per field
- authorization checks in action methods
- relationship columns
- image columns
- status toggle actions
- reusable action partials
- tests for create, update, delete, and export methods
