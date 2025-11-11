@props(['stock'])

@php
    $isPositive = $stock['change'] > 0;
    $isNegative = $stock['change'] < 0;

    $recommendationVariant = match($stock['recommendation']) {
        'BUY' => 'bg-green-500 text-white',
        'SELL' => 'bg-red-500 text-white',
        default => 'bg-yellow-500 text-white',
    };

    $icon = match($stock['recommendation']) {
        'BUY' => '<i class="fa-solid fa-circle-up mr-1"></i>',
        'SELL' => '<i class="fa-solid fa-circle-down mr-1"></i>',
        default => '<i class="fa-solid fa-hand mr-1"></i>',
    };
@endphp

<a href="{{ url('/stock/' . $stock['symbol']) }}" class="block">
    <div class="rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300 cursor-pointer hover:bg-accent/50">
        {{-- Header --}}
        <div class="p-4">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg font-bold text-primary">
                        {{ $stock['symbol'] }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $stock['name'] }}</p>
                </div>
                <span class="px-2 py-1 rounded text-xs font-semibold {{ $recommendationVariant }}">
                    {!! $icon !!} {{ $stock['recommendation'] }}
                </span>
            </div>
        </div>

        {{-- Content --}}
        <div class="px-4 pb-4 space-y-2">
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-2xl font-bold">
                        Rp {{ number_format($stock['price'], 0, ',', '.') }}
                    </div>
                    <div class="flex items-center gap-1 text-sm
                        {{ $isPositive ? 'text-green-600' : ($isNegative ? 'text-red-600' : 'text-gray-500') }}">
                        {{ $isPositive ? '↑' : ($isNegative ? '↓' : '−') }}
                        <span>
                            {{ $isPositive ? '+' : '' }}{{ number_format($stock['change'], 0, ',', '.') }} 
                            ({{ $isPositive ? '+' : '' }}{{ number_format($stock['changePercent'], 2) }}%)
                        </span>
                    </div>
                </div>

                {{-- Chart Placeholder --}}
                <!-- <div class="w-24 h-12 bg-gray-100 flex items-center justify-center text-xs text-gray-500 rounded">
                    Chart
                </div> -->
            </div>
        </div>
    </div>
</a>
