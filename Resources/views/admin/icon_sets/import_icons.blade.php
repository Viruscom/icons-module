@extends('layouts.app')
@section('scripts')
    <script>
        $('.icon-set-select').on('change', function () {
            if ($(this).val() == 0) {
                alert('Моля, изберете валиден сет.')
                downloadButton.addClass('hidden');
            } else {
                var downloadButton = $('.download-icons-from-set');
                downloadButton.attr('href', $('.base-url').text() + '/admin/icons/importFromIconSet/download/iconSet/' + $(this).val())
                downloadButton.removeClass('hidden');
            }
        });
    </script>
@endsection
@section('content')
    <div class="col-xs-12 p-0">
        <div class="bg-grey top-search-bar">
            <div class="action-mass-buttons pull-right">
                <a href="{{ url()->previous() }}" role="button" class="btn btn-lg back-btn margin-bottom-10"><i class="fa fa-reply"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-xs-12">
            <div class="form form-horizontal">
                <div class="form-body">
                    <div class="form-group">
                        <label class="control-label col-md-3">{{ __('icons::admin.icons_sets.choose_set') }}:</label>
                        <div class="col-md-4">
                            <select class="form-control select2 icon-set-select" name="icon_set_id">
                                <option value="0">{{ __('admin.common.please_select') }}</option>
                                @foreach($iconSets as $key=>$iconSet)
                                    <option value="{{ $iconSet->id }}">{{ $iconSet->name }}</option>
                                @endforeach
                            </select>
                            <a href="#" class="download-icons-from-set btn btn-success m-t-10 hidden"><i class="fas fa-download"></i> {{ __('icons::admin.icons_sets.download_icon_set') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
