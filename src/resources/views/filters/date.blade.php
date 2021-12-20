<div id="filter{{ $class->getID() }}" class="{{ $class->getSize() }}">
    @if($class->getLabel())
    <label>{{ $class->getLabel() }}</label>
    @endif
    <input type="date" placeholder="{{ $class->getLabel() }}" value="{{ $class->getValue() }}" name="{{ $class->getName() }}" class="datatable-filter {{ $class->getClass() }}" id="{{ $class->getID() }}" {{ $class->getString() }}>
</div>