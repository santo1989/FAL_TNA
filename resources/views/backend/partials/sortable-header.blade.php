@php
    $sortDirection = request('direction') === 'asc' ? 'desc' : 'asc';
    $isCurrentSort = request('sort') === $column;
@endphp

<a href="{{ route('tnas_dashboard', [
    'sort' => $column,
    'direction' => $sortDirection,
    'buyer' => request('buyer'),
    'search' => request('search')
]) }}" class="text-white">
    {{ $label }}
    @if($isCurrentSort)
        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
    @else
        <i class="fas fa-sort"></i>
    @endif
</a>