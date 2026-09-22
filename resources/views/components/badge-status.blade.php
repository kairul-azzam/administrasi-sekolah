@props(['status'])

@php
    $config = match(strtolower($status ?? 'draft')) {
        'draft' => [
            'bg' => 'bg-stone-100 text-stone-700 border-stone-200',
            'dot' => 'bg-stone-400',
            'label' => 'Draft',
        ],
        'diajukan' => [
            'bg' => 'bg-amber-50 text-amber-800 border-amber-200',
            'dot' => 'bg-amber-500',
            'label' => 'Diajukan',
        ],
        'diperiksa' => [
            'bg' => 'bg-blue-50 text-blue-800 border-blue-200',
            'dot' => 'bg-blue-500',
            'label' => 'Diperiksa',
        ],
        'dikembalikan' => [
            'bg' => 'bg-orange-50 text-orange-800 border-orange-200',
            'dot' => 'bg-orange-500',
            'label' => 'Dikembalikan',
        ],
        'disetujui' => [
            'bg' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
            'dot' => 'bg-emerald-500',
            'label' => 'Disetujui',
        ],
        'ditolak' => [
            'bg' => 'bg-rose-50 text-rose-800 border-rose-200',
            'dot' => 'bg-rose-500',
            'label' => 'Ditolak',
        ],
        default => [
            'bg' => 'bg-stone-100 text-stone-700 border-stone-200',
            'dot' => 'bg-stone-400',
            'label' => ucfirst($status ?? 'Unknown'),
        ],
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border ' . $config['bg']]) }}>
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}"></span>
    <span>{{ $config['label'] }}</span>
</span>
