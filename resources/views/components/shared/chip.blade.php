{{-- resources/views/components/shared/chip.blade.php --}}
@props(['details', 'trxId' => null])

@php
    if (is_array($details)) {
        $detailKustom = $details;
    } elseif (is_string($details) && $details !== '' && $details !== '-') {
        $detailKustom = json_decode($details, true) ?? [];
    } else {
        $detailKustom = [];
    }

    if (! is_array($detailKustom)) {
        $detailKustom = [];
    }

    $formatChipKey = function ($key): string {
        if (is_string($key) || is_numeric($key)) {
            return (string) $key;
        }

        return json_encode($key, JSON_UNESCAPED_UNICODE);
    };

    $formatChipValue = function ($value): string {
        if (is_array($value)) {
            if ($value === []) {
                return '-';
            }

            if (array_is_list($value)) {
                return collect($value)->map(function ($item) {
                    if (is_array($item)) {
                        return $item['name'] ?? $item['label'] ?? json_encode($item, JSON_UNESCAPED_UNICODE);
                    }

                    return is_scalar($item) ? (string) $item : json_encode($item, JSON_UNESCAPED_UNICODE);
                })->filter()->implode(', ');
            }

            return collect($value)->map(function ($nested, $nestedKey) {
                $label = is_string($nestedKey) ? str_replace('_', ' ', $nestedKey) . ': ' : '';

                if (is_scalar($nested) || $nested === null) {
                    return $label . (string) ($nested ?? '-');
                }

                return $label . json_encode($nested, JSON_UNESCAPED_UNICODE);
            })->implode('; ');
        }

        if (is_bool($value)) {
            return $value ? 'Ya' : 'Tidak';
        }

        return (string) ($value ?? '-');
    };

    $suffix = $trxId ? "-{$trxId}" : '';
@endphp

<div data-cy="chip-detail{{ $suffix }}" class="flex flex-wrap gap-2 mt-1">
    @forelse($detailKustom as $key => $value)
        @php $chipKey = $formatChipKey($key); @endphp
        <span data-cy="chip-item-{{ Str::slug($chipKey) }}{{ $suffix }}"
            class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-medium bg-white border border-gray-300 text-gray-700 shadow-sm">
            <strong class="text-gray-900 mr-1 capitalize">{{ str_replace('_', ' ', $chipKey) }}:</strong>
            <span class="capitalize">{{ $formatChipValue($value) }}</span>
        </span>
    @empty
        <span data-cy="chip-empty{{ $suffix }}" class="text-[11px] text-gray-400 italic">Tidak ada detail kustomisasi</span>
    @endforelse
</div>
