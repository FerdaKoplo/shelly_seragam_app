{{-- resources/views/components/shared/chip.blade.php --}}
@props(['details', 'trxId' => null])

@php
    $detailKustom = json_decode($details, true) ?? [];
    $suffix = $trxId ? "-{$trxId}" : '';
@endphp

<div data-cy="chip-detail{{ $suffix }}" class="flex flex-wrap gap-2 mt-1">
    @forelse($detailKustom as $key => $value)
        <span data-cy="chip-item-{{ Str::slug($key) }}{{ $suffix }}"
            class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium bg-white border border-gray-300 text-gray-700 shadow-sm">
            <strong class="text-gray-900 mr-1 capitalize">{{ str_replace('_', ' ', $key) }}:</strong>
            <span class="capitalize">{{ $value }}</span>
        </span>
    @empty
        <span data-cy="chip-empty{{ $suffix }}" class="text-[11px] text-gray-400 italic">Tidak ada detail kustomisasi</span>
    @endforelse
</div>