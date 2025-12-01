<li>
    <div
        class="unit-card p-2 mb-1 rounded shadow-sm 
        {{ request()->session()->get('theme', 'light') === 'dark' ? 'bg-dark text-white border-secondary' : 'bg-white text-dark border' }}">
        <strong>{{ $unit['nombre'] }}</strong>
        <br>
        <span class="text-muted">Jefe: {{ $unit['jefe'] ?? '-' }}</span>
    </div>

    @if(!empty($unit['children']))
        <ul class="ms-3">
            @foreach($unit['children'] as $child)
                @include('admin.unidades.partials.unit-node', ['unit' => $child])
            @endforeach
        </ul>
    @endif
</li>