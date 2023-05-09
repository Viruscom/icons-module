@php
    use Modules\Icons\Models\Icon;
@endphp@extends('layouts.admin.app')

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.myadmin-alert .closed').click(function (e) {
                e.preventDefault();
                $(this).parent().addClass('hidden');
            });

            $('[data-toggle="popover"]').popover({
                placement: 'auto',
                trigger: 'hover',
                html: true
            });
        });
    </script>
@endsection
@section('content')
    @include('icons::admin.icons.breadcrumbs')
    @include('admin.notify')

    <div class="row">
        <div class="col-xs-12">
            <h3>{{ __('icons::admin.icons.after_main_description') }} {{ __('admin.gallery.for') }} {{ $model->title }}</h3>
            @include('icons::admin.icons.top_buttons', ['formId' => 'headerForm', 'mainPosition' => Icon::ICONS_AFTER_DESCRIPTION])
            @include('icons::admin.icons.table', ['icons' => $model['Icons'][Icon::ICONS_AFTER_DESCRIPTION], 'tableClass' => 'table-headerForm'])
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <h3>{{ __('icons::admin.icons.after_additional_description_1') }} {{ __('admin.gallery.for') }} {{ $model->title }}</h3>
            @include('icons::admin.icons.top_buttons', ['formId' => 'additionalTextOneForm', 'mainPosition' => Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_1])
            @include('icons::admin.icons.table', ['icons' => $model['Icons'][Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_1], 'tableClass' => 'table-additionalTextOneForm'])
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <h3>{{ __('icons::admin.icons.after_additional_description_2') }} {{ __('admin.gallery.for') }} {{ $model->title }}</h3>
            @include('icons::admin.icons.top_buttons', ['formId' => 'additionalTextTwoForm', 'mainPosition' => Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_2])
            @include('icons::admin.icons.table', ['icons' => $model['Icons'][Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_2], 'tableClass' => 'table-additionalTextTwoForm'])
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <h3>{{ __('icons::admin.icons.after_additional_description_3') }} {{ __('admin.gallery.for') }} {{ $model->title }}</h3>
            @include('icons::admin.icons.top_buttons', ['formId' => 'additionalTextThreeForm', 'mainPosition' => Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_3])
            @include('icons::admin.icons.table', ['icons' => $model['Icons'][Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_3], 'tableClass' => 'table-additionalTextThreeForm'])
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <h3>{{ __('icons::admin.icons.after_additional_description_4') }} {{ __('admin.gallery.for') }} {{ $model->title }}</h3>
            @include('icons::admin.icons.top_buttons', ['formId' => 'additionalTextFourForm', 'mainPosition' => Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_4])
            @include('icons::admin.icons.table', ['icons' => $model['Icons'][Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_4], 'tableClass' => 'table-additionalTextFourForm'])
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <h3>{{ __('icons::admin.icons.after_additional_description_5') }} {{ __('admin.gallery.for') }} {{ $model->title }}</h3>
            @include('icons::admin.icons.top_buttons', ['formId' => 'additionalTextFiveForm', 'mainPosition' => Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_5])
            @include('icons::admin.icons.table', ['icons' => $model['Icons'][Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_5], 'tableClass' => 'table-additionalTextFiveForm'])
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <h3>{{ __('icons::admin.icons.after_additional_description_6') }} {{ __('admin.gallery.for') }} {{ $model->title }}</h3>
            @include('icons::admin.icons.top_buttons', ['formId' => 'additionalTextSixForm', 'mainPosition' => Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_6])
            @include('icons::admin.icons.table', ['icons' => $model['Icons'][Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_6], 'tableClass' => 'table-additionalTextSixForm'])
        </div>
    </div>

    @include('admin.partials.modals.delete_confirm')
@endsection
