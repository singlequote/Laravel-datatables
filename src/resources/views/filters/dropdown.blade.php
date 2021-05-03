<div id="filter{{ $class->getID() }}" class="{{ $class->getSize() }}">
    <select {{ $class->getString() }} name="{{ $class->getName() }}" class="datatable-filter {{ $class->getClass() }}" id="{{ $class->getID() }}" >
        <option disabled value='**'>{{ $class->getLabel() }}</option>
        @foreach($class->getData() as $data)
        <option {{ isset($data['selected']) && $data['selected'] ? 'selected' : '' }} value="{{ $data['value'] }}">{{ $data['label'] }}</option>
        @endforeach
    </select>
</div>
