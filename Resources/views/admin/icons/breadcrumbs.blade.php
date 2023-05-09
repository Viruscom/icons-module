<div class="breadcrumbs">
    <ul>
        <li>
            <a href="{{ route('admin.index') }}"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="{{ route('admin.icons.index') }}" class="text-black">@lang('icons::admin.icons.index')</a>
        </li>
        @if(url()->current() === route('admin.icons.toManyPagesCreate'))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.icons.toManyPagesCreate') }}" class="text-purple">@lang('icons::admin.icons.to_many_pages_create')</a>
            </li>
        @elseif(!is_null(Request::segment(4)) && url()->current() === route('admin.icons.create', ['path' => Request::segment(4)]))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.icons.create', ['path' => Request::segment(4)]) }}" class="text-purple">@lang('icons::admin.icons.create')</a>
            </li>
        @elseif(Request::segment(3) !== null && url()->current() === route('admin.icons.edit', ['id' => Request::segment(3)]))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.icons.edit', ['id' => Request::segment(3)]) }}" class="text-purple">@lang('icons::admin.icons.edit')</a>
            </li>
        @endif
    </ul>
</div>
