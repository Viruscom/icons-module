@php
    use Modules\Icons\Models\Icon;
@endphp@extends('layouts.admin.app')
@section('styles')
    <link href="{{ asset('admin/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('scripts')
    <script src="{{ asset('admin/js/select2.min.js') }}"></script>
    <script>
        $(".select2").select2({language: "bg"});

        $(document).ready(function () {
            @foreach($languages as $language)
            //Load external links AdBoxes
            $('input[name="external_url_{{$language->code}}"]').on('click', function () {
                adBoxExternalLinkToggle($(this), '{{$language->code}}');
            });
            @endforeach
            function adBoxExternalLinkToggle(el, languageCode) {
                var select = $('.select2-' + languageCode + '');
                if (el.val() == "on" && $('.select2-' + languageCode).hasClass('hidden')) {
                    select.removeClass('hidden').removeAttr('disabled');
                    select.parents('.form-group').removeClass('hidden');
                    $('input[name="url_' + languageCode + '"]').parent().addClass('hidden');
                } else {
                    select.addClass('hidden').attr('disabled', 'disabled');
                    select.parents('.form-group').addClass('hidden');
                    $('input[name="url_' + languageCode + '"]').parent().removeClass('hidden');
                }
            }
        });
    </script>
@endsection

@section('content')
    @include('icons::admin.icons.breadcrumbs')
    @include('admin.notify')
    <div class="col-xs-12 p-0">
        <form class="my-form" action="{{ route('admin.icons.store', ['path' => $path]) }}" method="POST" data-form-type="store" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="position" value="{{old('position')}}">
            <input type="hidden" name="icon_set_id" value="0">
            <div class="bg-grey top-search-bar">
                <div class="action-mass-buttons pull-right">
                    <button type="submit" name="submitaddnew" value="submitaddnew" class="btn btn-lg green saveplusicon margin-bottom-10"></button>
                    <button type="submit" name="submit" value="submit" class="btn btn-lg save-btn margin-bottom-10"><i class="fas fa-save"></i></button>
                    <a href="{{ url('/admin/icons') }}" role="button" class="btn btn-lg back-btn margin-bottom-10"><i class="fa fa-reply"></i></a>
                </div>
            </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <ul class="nav nav-tabs">
                @foreach($languages as $language)
                    <li @if($language->code === config('default.app.language.code')) class="active" @endif><a data-toggle="tab" href="#{{$language->code}}">{{$language->code}} <span class="err-span-{{$language->code}} hidden text-purple"><i class="fas fa-exclamation"></i></span></a></li>
                @endforeach
            </ul>
            <div class="tab-content">
                @foreach($languages as $language)
                        <?php
                        $langTitle      = 'title_' . $language->code;
                        $langShortDescr = 'short_description_' . $language->code;
                        $langLink       = 'url_' . $language->code; $langExternalUrl = 'external_url_' . $language->code;
                        ?>
                    <div id="{{$language->code}}" class="tab-pane fade in @if($language->code === config('default.app.language.code')) active @endif">
                        <div class="form-group @if($errors->has($langTitle)) has-error @endif">
                            <label class="control-label p-b-10"><span class="text-purple">* </span> {{ __('icons::admin.icons.title') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>
                            <input class="form-control" type="text" name="{{$langTitle}}" value="{{ old($langTitle) }}">
                            @if($errors->has($langTitle))
                                <span class="help-block">{{ trans($errors->first($langTitle)) }}</span>
                            @endif
                        </div>
                        <div id="{{$language->code}}" class="tab-pane fade in @if($language->code === config('default.app.language.code')) active @endif">
                            <div class="form-group @if($errors->has($langShortDescr)) has-error @endif">
                                <label class="control-label p-b-10">{{ __('icons::admin.icons.short_description') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                <input class="form-control" type="text" name="{{$langShortDescr}}" value="{{ old($langShortDescr) }}">
                                @if($errors->has($langShortDescr))
                                    <span class="help-block">{{ trans($errors->first($langShortDescr)) }}</span>
                                @endif
                            </div>
                        </div>

                        {{--                        <div class="form-group">--}}
                        {{--                            <label class="control-label">{{ __('admin.common.intenal_link') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>--}}
                        {{--                            <div>--}}
                        {{--                                <select name="{{$langLink}}" class="form-control select2 select2-{{$language->code}}" style="width: 100%;">--}}
                        {{--                                    @include('admin.partials.on_create.select_tag_internal_links', ['language' => $language->code, 'internalLinks' => $internalLinks])--}}
                        {{--                                </select>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                    </div>
                @endforeach
            </div>
            <div class="form form-horizontal">
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">{{ __('icons::admin.icons.main_position') }}:</label>
                        <div class="col-md-4">
                            <select class="form-control select2" name="main_position">
                                <option value="{{ Icon::ICONS_AFTER_DESCRIPTION }}">{{ trans('icons::admin.icons.after_main_description') }}</option>
                                <option value="{{ Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_1 }}">{{ trans('icons::admin.icons.after_additional_description_1') }}</option>
                                <option value="{{ Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_2 }}">{{ trans('icons::admin.icons.after_additional_description_2') }}</option>
                                <option value="{{ Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_3 }}">{{ trans('icons::admin.icons.after_additional_description_3') }}</option>
                                <option value="{{ Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_4 }}">{{ trans('icons::admin.icons.after_additional_description_4') }}</option>
                                <option value="{{ Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_5 }}">{{ trans('icons::admin.icons.after_additional_description_5') }}</option>
                                <option value="{{ Icon::ICONS_AFTER_ADDITIONAL_DESCRIPTION_6 }}">{{ trans('icons::admin.icons.after_additional_description_6') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group @if($errors->has('file')) has-error @endif">
                        <label class="control-label col-md-3"><span class="text-purple">* </span>{{ __('icons::admin.icons.icon') }}:</label>
                        <div class="col-md-6">
                            <input type="file" name="image" class="filestyle form-control" data-buttonText="{{trans('admin.browse_file')}}" data-iconName="fas fa-upload" data-buttonName="btn green" data-badge="true">
                            <p class="help-block">{!! $fileRulesInfo !!}</p>
                        </div>
                    </div>
                    <hr>
                    @include('admin.partials.on_create.active_checkbox')

                </div>
                @include('admin.partials.on_create.form_actions_bottom')

            </div>
        </div>
        </form>
    </div>
@endsection
