@extends('backend.master')

@section('title', 'Asset Assign to Store')

@section('body')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-xl-9 col-lg-10 col-md-11 col-sm-12 mx-auto">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Assign Asset To Store</div>
{{--                        <button type="button" class="btn btn-sm btn-primary btn-wave" id="btn-add-asset-type">--}}
{{--                            <i class="ri-add-line me-1"></i> Add Asset Type--}}
{{--                        </button>--}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="filter-card">

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table id="data-table" class="table table-bordered text-nowrap w-100">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Store</th>
                                            <th>Assets</th>
                                            <th>Default Fee</th>
                                            <th>Dimensions</th>
                                            <th>Properties</th>
                                            <th>Status</th>
                                            <th width="110">Actions</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')



@endsection

@push('styles')

@endpush

@push('scripts')
    @include('backend.includes.plugins.datatable')
    @include('backend.includes.plugins.select2')
@endpush
