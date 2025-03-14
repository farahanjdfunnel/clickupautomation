@extends('layouts.master-layouts')

@section('title')
    @lang('translation.Data_Tables')
@endsection

@section('css')
    <!-- DataTables -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            SPIn
        @endslot
        @slot('title')
            List
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body" style="font-size: large;">
                    <p>{!! $paragraphText !!}</p>
                    <a href="{{ $buttonLink }}" target="_blank" class="btn btn-primary">
                        {{ $buttonText }}
                    </a>
                </div>
            </div>
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection

@section('script')
@endsection
