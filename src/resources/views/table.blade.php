<div id="{{ $view->tableId }}datatable-filters" class="row datatable-filters"></div>
<table id="{{ $view->tableId }}" class="laravel-datatable {{ $view->tableClass }}">
    <thead class="{{ $view->tableHeadClass ?? '' }}">
        <tr>
            @foreach($view->columns as $header)
            <th>{{ isset($view->translate[$header['name']]) ? __($view->translate[$header['name']]) : __($header['name']) }}</th>
            @endforeach
        </tr>
    </thead>
</table>
