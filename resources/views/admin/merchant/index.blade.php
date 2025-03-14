@extends('layouts.master-layouts')

@section('title')
    @lang('translation.Data_Tables')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    {{-- @component('components.breadcrumb')
        @slot('li_1')
            Merchant
        @endslot
        @slot('title')
            List
        @endslot
    @endcomponent --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title d-flex justify-content-between align-items-center">
                        <span>Merchants</span>
                        {{-- <button class="btn btn-primary float-end" id="btn-add">Add</button> --}}
                    </h4>
                    <table id="merchant-table" class="table table-bordered dt-responsive  nowrap w-100">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Email</th>
                                <th>location Id</th>
                                <th>Location Name</th>
                                <th>Company Id</th>
                                <th>Phone</th>
                                <th>Country</th>
                                <th>State</th>
                                <th>City</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
    @include('admin.merchant.modal.setup-hpp')
    @include('admin.merchant.modal.payment-provider-setup-crm')

@endsection
@section('script')
    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    @include('admin.merchant.datatable')
    @include('admin.merchant.script')
@endsection
