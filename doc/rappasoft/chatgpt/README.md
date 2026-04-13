# Rappasoft Laravel Livewire Tables Guide

This guide is for the package version installed in this project:

- `rappasoft/laravel-livewire-tables` `v3.7.3`
- `livewire/livewire` `v3.7.13`
- Laravel `12.x`

Important:

- Use the `v3` package style in this guide.
- Do not blindly follow the `v4/master` docs unless you upgrade on purpose.
- This package is not the same as jQuery DataTables. Export buttons, modal forms, and custom action buttons are usually added by you with Livewire methods and small Blade partials.

## Read in this order

1. [01-setup-and-first-table.md](./01-setup-and-first-table.md)
2. [02-actions-modals-export.md](./02-actions-modals-export.md)
3. [03-common-gotchas.md](./03-common-gotchas.md)

## What this local guide teaches

- Install and configure the package for Bootstrap 5
- Generate your first table component
- Add searchable and sortable columns
- Add filters
- Add row action buttons
- Open and close Bootstrap 5 modals from Livewire
- Store and update data from modal forms
- Delete rows with a confirmation modal
- Add export buttons with `maatwebsite/excel`
- Add bulk actions

## Official references

- Rappasoft docs: `https://rappasoft.com/docs/laravel-livewire-tables`
- Package repo: `https://github.com/rappasoft/laravel-livewire-tables`
- Livewire docs: `https://livewire.laravel.com/docs/3.x`

## Fast recommendation for beginners

If you are new:

- start with one simple model
- get the table rendering first
- then add filters
- then add one action button
- then add the Bootstrap modal form
- add export last

Do not start by trying to build every feature at once.
