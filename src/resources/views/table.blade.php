<div id="{{ $view->tableId }}datatable-filters" class="row datatable-filters"></div>
<table id="{{ $view->tableId }}" class="laravel-datatable {{ $view->tableClass }}">
    <thead>
        <tr>
            @foreach($view->columns as $header)
            <th>{{ isset($view->translate[$header['name']]) ? __($view->translate[$header['name']]) : __($header['name']) }}</th>
            @endforeach
        </tr>
    </thead>
    <thead>
        <tr>
            @foreach($view->columns as $header)
                @if(isset($header['columnSearch']) && $header['columnSearch'])
                    <th rowspan="1" colspan="1" class="search-th" style="width: 107px;">
                        <input type="text" data-searchcolumn="{{$header['data']}}" class="search-filter" placeholder="@lang('datatables.columnSearchLabel')">
                    </th>
                @else
                    <th rowspan="1" colspan="1" class="search-th" style="width: 107px;"></th>
                @endif
            @endforeach
        </tr>
    </thead>
</table>
