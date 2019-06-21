<div id="filter{{ $class->getID() }}" class="datatable-filters {{ $class->getSize() }}">
    <select {{ $class->getString() }} name="{{ $class->getName() }}" class="datatable-filter {{ $class->getClass() }}" id="{{ $class->getID() }}" >
        @if(strlen($class->getLabel()) > 0)
        <option value="lfalse">{{ $class->getLabel() }}</option>
        @endif
        @foreach($class->getData() as $data)
        <option value="{{ $data['value'] }}">{{ $data['label'] }}</option>
        @endforeach
    </select>
</div>