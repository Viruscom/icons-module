@extends('layouts.admin.app')

@section('content')
    @include('icons::admin.icons.breadcrumbs')
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-danger">{!! __('icons::admin.icons.warning_no_icons_methods_in_eloquent_model') !!}</div>
        </div>
    </div>
@endsection
