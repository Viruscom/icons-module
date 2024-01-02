@extends('layouts.app')
@section('scripts')
    <script>
        $('.icon-set-select').on('change', function () {
            if ($(this).val() == 0) {
                alert('Моля, изберете валиден сет.')
                downloadButton.addClass('hidden');
            } else {
                $.ajax({
                    url: $('.base-url').text() + '/admin/icons/importFromIconSet/load/iconSet/' + $(this).val(),
                    type: "GET",
                    async: false,
                    success: function (response) {
                        $('.icons-wrapper').html('');
                        console.log(response[0])
                        $.each(response[0].icons, function (index, item) {
                            console.log(item)
                            $('.icons-wrapper').append('<div class="set-icon">' +
                                '<img src="' + $('.base-url').text() + '/images/icons/adminSets/' + response[0].id + '/' + item.filename + '" /><p class="check btn btn-xs green" icon="' + item.id + '">Маркирай</p><p class="uncheck btn btn-xs purple-a hidden" icon="' + item.id + '">Демаркирай</p></div>');
                        })

                        actions();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.log(jqXHR);
                        console.log(textStatus);
                        console.log(errorThrown);
                    }
                });
            }
        });

        function actions() {
            var icons = [];

            $('.check').on('click', function () {
                if (icons.indexOf($(this).attr('icon')) < 0) {
                    icons.push($(this).attr('icon'));
                }

                $(this).addClass('hidden');
                $(this).siblings('p.uncheck').removeClass('hidden');
                $('input[name="icons"]').val(icons);
            });

            $('.uncheck').on('click', function () {
                if (icons.indexOf($(this).attr('icon')) >= 0) {
                    icons.splice($(this).attr('icon'), 1);
                }

                $(this).addClass('hidden');
                $(this).siblings('p.check').removeClass('hidden');
                $('input[name="icons"]').val(icons);
            });

        }
    </script>
@endsection
@section('content')
    <div class="col-xs-12 p-0">
        <form class="my-form" action="{{ url('/admin/icons/importFromIconSet/'.Request::segment(4).'/'.Request::segment(5).'/store') }}" method="POST" data-form-type="store" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="position" value="{{old('position')}}">
            <input type="hidden" name="parent_type_id" value="{{Request::segment(4)}}">
            <input type="hidden" name="parent_id" value="{{Request::segment(5)}}">
            <input type="hidden" name="main_catalog_id" value="0">
            <input type="hidden" name="icons">
            <div class="bg-grey top-search-bar">
                <div class="action-mass-buttons pull-right">
                    <button type="submit" name="submitaddnew" value="submitaddnew" class="btn btn-lg green saveplusicon margin-bottom-10"></button>
                    <button type="submit" name="submit" value="submit" class="btn btn-lg save-btn margin-bottom-10"><i class="fas fa-save"></i></button>
                    <a href="{{ url('/admin/catalogs') }}" role="button" class="btn btn-lg back-btn margin-bottom-10"><i class="fa fa-reply"></i></a>
                </div>
            </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="form form-horizontal">
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">Страница:</label>
                        <div class="col-md-4">
                            <select class="form-control" disabled>
                                @foreach($navigations as $nav)
                                    <optgroup label="{{$nav->translations->where('language_id', 1)->first()->title}}">
                                        @php
                                            $contentPages = $nav->content_pages()->orderBy('position', 'asc')->get();
                                        @endphp
                                        @foreach($contentPages as $contPage)
                                            <option data-parentTypeId="{{$parentTypeContent}}" value="{{$contPage->id}}" {{ ($parentTypeContent==Request::segment(4) && $contPage->id==Request::segment(5)) ? 'selected':''}}> - - {{
							$contPage->translations->where('language_id', 1)->first()->title}}</option>
                                        @endforeach</optgroup>

                                    @if($nav->isHotelModule())
                                        <optgroup label="@lang('administration_messages.module_11')">
                                            @php $hotels = $nav->hotels()->orderBy('position', 'asc')->get(); @endphp
                                            @foreach($hotels as $hotel)
                                                <option data-parentTypeId="{{$parentTypeHotel}}" value="{{$hotel->id}}"{{ ($parentTypeHotel==Request::segment(4) && $hotel->id==Request::segment(5)) ? 'selected':''}}> - - {{
                                $hotel->translations->where('language_id', 1)->first()->title}}</option>
                                                @php $rooms = $hotel->rooms()->orderBy('position', 'asc')->get(); @endphp
                                                @foreach($rooms as $room)
                                                    <option data-parentTypeId="{{$parentTypeHotelRoom}}" value="{{$room->id}}"{{ ($parentTypeHotelRoom==Request::segment(4) && $room->id==Request::segment(5)) ? 'selected':''}}> - - {{
                                $room->translations->where('language_id', 1)->first()->title}}</option>
                                                @endforeach
                                            @endforeach
                                        </optgroup>
                                    @endif

                                    @php
                                        $productCategories = $nav->product_categories()->orderBy('position', 'asc')->get();
                                    @endphp
                                    @foreach($productCategories as $prodCateg)
                                        <optgroup label=" - - {{$prodCateg->translations->where('language_id', 1)->first()->title}}">
                                            @php
                                                $products = $prodCateg->products()->orderBy('position', 'asc')->get();
                                            @endphp
                                            @foreach($products as $prod)
                                                <option data-parentTypeId="{{$parentTypeProduct}}" value="{{$prod->id}}"{{ ($parentTypeProduct==Request::segment(4) && $prod->id==Request::segment(5)) ? 'selected':''}}> - - - -{{
								            $prod->translations->where('language_id', 1)->first()->title}}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Избор на сет:</label>
                        <div class="col-md-4">
                            <select class="form-control select2 icon-set-select" name="icon_set_id">
                                <option value="0">--- Моля, изберете ---</option>
                                @foreach($iconSets as $key=>$iconSet)
                                    <option value="{{ $iconSet->id }}">{{ $iconSet->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 icons-wrapper">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">Основна позиция на иконата:</label>
                        <div class="col-md-4">
                            <select class="form-control select2" name="main_position">
                                <option value="0">{{ trans('administration_messages.additional_gallery_main_position_0') }}</option>
                                <option value="1">{{ trans('administration_messages.additional_gallery_main_position_1') }}</option>
                                <option value="2">{{ trans('administration_messages.additional_gallery_main_position_2') }}</option>
                                <option value="3">{{ trans('administration_messages.additional_gallery_main_position_3') }}</option>
                                <option value="4">{{ trans('administration_messages.additional_gallery_main_position_4') }}</option>
                                <option value="4">{{ trans('administration_messages.additional_gallery_main_position_5') }}</option>
                                <option value="4">{{ trans('administration_messages.additional_gallery_main_position_6') }}</option>
                            </select>
                        </div>
                    </div>

                    {{--                    <hr>--}}
                    {{--                    <div class="form-group">--}}
                    {{--                        <label class="control-label col-md-3">Активен (видим) в сайта:</label>--}}
                    {{--                        <div class="col-md-6">--}}
                    {{--                            <label class="switch pull-left">--}}
                    {{--                                <input type="checkbox" name="active" class="success" data-size="small" checked {{(old('active') ? 'checked' : 'active')}}>--}}
                    {{--                                <span class="slider"></span>--}}
                    {{--                            </label>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                </div>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" name="submit" value="submit" class="btn save-btn margin-bottom-10"><i class="fas fa-save"></i> запиши</button>
                            <a href="{{ url()->previous() }}" role="button" class="btn back-btn margin-bottom-10"><i class="fa fa-reply"></i> назад</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
@endsection
