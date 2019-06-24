<div id="filter{{ $class->getID() }}" class="{{ $class->getSize() }}">
    <select {{ $class->getString() }} name="{{ $class->getName() }}" class="datatable-filter {{ $class->getClass() }}" id="{{ $class->getID() }}" >
        <option selected disabled value='**'>{{ $class->getLabel() }}</option>
        @foreach($class->getData() as $data)
        <option value="{{ $data['value'] }}">{{ $data['label'] }}</option>
        @endforeach
    </select>
</div>
