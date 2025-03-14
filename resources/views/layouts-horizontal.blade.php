@extends('layouts.master-layouts')

@section('title') @lang('translation.Horizontal') @endsection

@section('body')
    <body data-topbar="dark" data-layout="horizontal">
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') Utility @endslot
        @slot('title') Dashboard @endslot
    @endcomponent



@endsection
@section('script')
    <!-- apexcharts -->

    <!-- dashboard init -->
@endsection
