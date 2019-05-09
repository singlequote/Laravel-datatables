<table id="{{ $view->tableId }}" class="laravel-datatable {{ $view->tableClass }}">
    <thead>
        <tr>
            @foreach($view->columns as $header)
            <th>{{ isset($view->translate[$header['name']]) ? __($view->translate[$header['name']]) : __($header['name']) }}</th>
            @endforeach
        </tr>
    </thead>
</table>