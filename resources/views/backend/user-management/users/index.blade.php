@extends('backend.master')
@section('title','User')
@push('styles')
    @include('backend.user-management.datatables.datatable-style')
    <link rel="modulepreload" href="{{asset('backend/build/assets/date_time_pickers-CfSDcSmz.js')}}" />
    <link rel="stylesheet" href="{{asset('backend/reza-custom/css/custom.css')}}"/>
{{--    <style>--}}
{{--        /* Bottom info + pagination same line */--}}
{{--        .dt-container .dt-info,--}}
{{--        .dt-container .dt-paging {--}}
{{--            display: inline-flex;--}}
{{--            align-items: center;--}}
{{--        }--}}
{{--        /* Wrap both in one row */--}}
{{--        .dt-container {--}}
{{--            position: relative;--}}
{{--        }--}}

{{--        /* Info (Showing...) left */--}}
{{--        .dt-container .dt-info {--}}
{{--            float: left;--}}
{{--        }--}}

{{--        /* Pagination right */--}}
{{--        .dt-container .dt-paging {--}}
{{--            float: right;--}}
{{--        }--}}


{{--        .dt-buttons .btn-group .flex-wrap .gap-2 { float: left }--}}
{{--        .dt-search {float: right}--}}
{{--        .dt-length {margin-left: 10px!important; float: left}--}}

{{--        /* Mobile fix for dt-search and dt-length (100px to 488px) */--}}
{{--        @media (min-width: 100px) and (max-width: 488px) {--}}
{{--            .dt-length,--}}
{{--            .dt-search {--}}
{{--                float: left !important;--}}
{{--                width: 50% !important;--}}
{{--                margin: 0 !important;--}}
{{--                box-sizing: border-box !important;--}}
{{--            }--}}
{{--            .dt-length select,--}}
{{--            .dt-search input {--}}
{{--                width: auto !important;--}}
{{--            }--}}
{{--            /*.dt-length {margin-left: 5px!important;}*/--}}
{{--        }--}}
{{--    </style>--}}
@endpush

@section('body')
    <div class="container-fluid pt-3">
        <div class="d-md-flex d-block align-items-center justify-content-between page-header-breadcrumb mb-3">
            <div class="my-auto">
                <h4 class="mb-sm-0 text-uppercase" style="font-family: 'Bell MT';font-size: 16px"><i class="mdi mdi-checkbox-marked-outline me-2"></i>Users</h4>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{url('/admin/dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Users</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="row mb-4">
            <div class="col-xl-7 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form id="filter_form" class="form-inline justify-content-center">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text text-muted"><i class="ri-calendar-line"></i></span>
                                        <input type="text" name="from_date"  max="{{date('Y-m-d H:i:s')}}"  class="form-control py-2" id="from_date" placeholder="From date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <span class="input-group-text text-muted"><i class="ri-calendar-line"></i></span>
                                        <input type="text" name="to_date"  max="{{date('Y-m-d H:i:s')}}"  class="form-control py-2" id="to_date" placeholder="To date">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="btn-group" role="group" aria-label="Filter actions">
                                        <button type="submit" class="btn btn-outline-primary " id="filterBtn" title="Filter">
                                            <i class="ri-search-line"></i> Filter
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary  ajax_reload" id="resetBtn" title="Refresh">
                                            <i class="ri-refresh-line"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="mdi mdi-view-list me-1"></i> User List
                        </h5>
                        <a href="{{ route('users.create') }}" class="btn btn-sm btn-outline-primary">
                            <i class="mdi mdi-plus-circle me-1"></i> Create
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table  class="table table-bordered text-nowrap w-100 mb-3" id="userDataTable"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('backend/build/assets/libs/flatpickr/flatpickr.min.js')}}"></script>
    @include('backend.user-management.datatables.datatable-script')
    @include('backend.user-management.toasts')
    @include('backend.user-management.partials.user.user-index-script')
@endpush
