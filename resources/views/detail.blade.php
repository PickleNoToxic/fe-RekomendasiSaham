@extends('layouts.app')

@php
    $isPositive = $stock['change'] > 0;
    $isNegative = $stock['change'] < 0;

    $recommendationVariant = match ($stock['recommendation']) {
        'BUY' => 'bg-green-500 text-white',
        'SELL' => 'bg-red-500 text-white',
        default => 'bg-yellow-500 text-white',
    };

    $icon = match ($stock['recommendation']) {
        'BUY' => '<i class="fa-solid fa-circle-up mr-1"></i>',
        'SELL' => '<i class="fa-solid fa-circle-down mr-1"></i>',
        default => '<i class="fa-solid fa-hand mr-1"></i>',
    };
@endphp

@section('content')
    <div class="min-h-screen bg-background">
        {{-- Error Handling --}}
        @if (is_null($stock['price']) && is_null($stock['change']) && is_null($stock['changePercent']) && is_null($stock['recommendation']))
            <div class="flex flex-col items-center justify-center text-center py-16">
                <i class="fa-solid fa-triangle-exclamation text-gray-800 text-5xl mb-4"></i>
                <h2 class="text-2xl font-bold text-gray-700 mb-2">Gagal Mengambil Data</h2>
                <p class="text-gray-500 mb-6">Terjadi kesalahan saat memuat data saham. Silakan coba lagi beberapa saat lagi.
                </p>
                <a href="{{ url()->current() }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-800 text-white rounded hover:bg-gray-900 transition">
                    <i class="fa-solid fa-rotate-right"></i> Coba Lagi
                </a>
            </div>
        @else
            {{-- Header --}}
            <header class="border-b bg-card mx-4">
                <div class="container mx-auto px-4 py-4">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <a href="{{ url('/') }}"
                            class="inline-flex items-center border rounded px-3 py-1 text-sm hover:bg-gray-100">
                            ← Kembali
                        </a>

                        @if (!empty($stock['last_updated']))
                            <span class="text-sm text-gray-500">
                                Terakhir diperbarui:
                                <span class="font-medium">
                                    {{ \Carbon\Carbon::parse($stock['last_updated']) }}
                                </span>
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h1 class="text-3xl font-bold text-primary">{{ $stock['symbol'] }}</h1>
                                <span
                                    class="px-2 py-1 rounded text-sm font-semibold cursor-default {{ $recommendationVariant }}">
                                    {!! $icon !!} {{ $stock['recommendation'] }}
                                </span>
                            </div>
                            <h2 class="text-xl text-foreground mb-2">{{ $stock['name'] }}</h2>
                            <p class="text-gray-500 max-w-2xl">{{ $stock['description'] }}</p>
                        </div>

                        <div class="text-right">
                            <div class="text-3xl font-bold mb-1">
                                Rp {{ number_format($stock['price'], 0, ',', '.') }}
                            </div>
                            <div
                                class="flex items-center gap-1 justify-end text-lg
                                        {{ $isPositive ? 'text-green-600' : ($isNegative ? 'text-red-600' : 'text-gray-500') }}">
                                {{ $isPositive ? '↑' : ($isNegative ? '↓' : '−') }}
                                <span>
                                    {{ $isPositive ? '+' : '' }}{{ number_format($stock['change'], 0, ',', '.') }}
                                    ({{ $isPositive ? '+' : '' }}{{ number_format($stock['changePercent'], 2) }}%)
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="container mx-auto px-4 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    {{-- Chart Section --}}
                    <div class="lg:col-span-2">
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold mb-2">Grafik Harga</h3>
                            <div class="flex flex-wrap gap-2 mb-4">
                                <button class="time-filter px-3 py-1 border rounded text-sm cursor-pointer hover:bg-gray-100"
                                    data-range="1W">1
                                    Minggu</button>
                                <button class="time-filter px-3 py-1 border rounded text-sm cursor-pointer hover:bg-gray-100"
                                    data-range="2W">2
                                    Minggu</button>
                                <button class="time-filter px-3 py-1 border rounded text-sm cursor-pointer hover:bg-gray-100"
                                    data-range="1M">1
                                    Bulan</button>
                                <button class="time-filter px-3 py-1 border rounded text-sm cursor-pointer hover:bg-gray-100"
                                    data-range="3M">3
                                    Bulan</button>
                                <button class="time-filter px-3 py-1 border rounded text-sm cursor-pointer hover:bg-gray-100"
                                    data-range="6M">6
                                    Bulan</button>
                                <button
                                    class="time-filter px-3 py-1 border rounded text-sm bg-gray-800 text-white cursor-pointer hover:bg-gray-900"
                                    data-range="1Y">1
                                    Tahun</button>
                            </div>
                            <canvas id="stockChart"></canvas>
                        </div>
                    </div>

                    {{-- Info Sidebar --}}
                    <div class="space-y-6">
                        {{-- Company Info --}}
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold mb-4 flex items-center gap-2 border-b pb-4">
                                <i class="fa-solid fa-building text-gray-800"></i>
                                Informasi Perusahaan
                            </h3>

                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm text-muted-foreground">Sektor</label>
                                    <p class="font-semibold">{{ $stock['sector'] }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-muted-foreground">Market Cap</label>
                                    <p class="font-semibold">{{ $stock['marketCap'] }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-muted-foreground">Harga Saat Ini</label>
                                    <p class="font-semibold">Rp {{ number_format($stock['price'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Recommendation --}}
                        <div class="border rounded-lg p-4">
                            <h3 class="font-semibold mb-4 flex items-center gap-2 border-b pb-4">
                                <i class="fa-solid fa-coins text-gray-800"></i>
                                Analisis & Rekomendasi
                            </h3>
                            <div class="text-center mb-4">
                                <span
                                    class="px-4 py-2 rounded text-lg font-semibold cursor-default {{ $recommendationVariant }}">
                                    {!! $icon !!} {{ $stock['recommendation'] }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm text-muted-foreground">Perubahan Harian</label>
                                    <p
                                        class="font-semibold {{ $isPositive ? 'text-green-600' : ($isNegative ? 'text-red-600' : 'text-gray-500') }}">
                                        {{ $isPositive ? '+' : '' }}{{ number_format($stock['changePercent'], 2) }}%
                                    </p>
                                </div>

                                <div class="text-sm text-muted-foreground">
                                    <p class="mb-2">* Rekomendasi berdasarkan analisis teknikal dan fundamental</p>
                                    <p>* Selalu lakukan riset mandiri sebelum berinvestasi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        @endif
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const rawData = @json($stock['history']);

        function filterData(range) {
            const now = new Date();
            let startDate;

            switch (range) {
                case '1W': startDate = new Date(now.setDate(now.getDate() - 7)); break;
                case '2W': startDate = new Date(now.setDate(now.getDate() - 14)); break;
                case '1M': startDate = new Date(now.setMonth(now.getMonth() - 1)); break;
                case '3M': startDate = new Date(now.setMonth(now.getMonth() - 3)); break;
                case '6M': startDate = new Date(now.setMonth(now.getMonth() - 6)); break;
                case '1Y': startDate = new Date(now.setFullYear(now.getFullYear() - 1)); break;
                default: startDate = new Date(0);
            }

            return rawData.filter(item => new Date(item.date) >= startDate);
        }

        const ctx = document.getElementById('stockChart').getContext('2d');
        let chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: rawData.map(d => d.date),
                datasets: [{
                    label: 'Harga',
                    data: rawData.map(d => d.close),
                    borderColor: "green",
                    borderWidth: 2,
                    fill: false,
                    tension: 0.3,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                }
            }
        });

        document.querySelectorAll('.time-filter').forEach(btn => {
            btn.addEventListener('click', () => {
                const range = btn.dataset.range;
                const filtered = filterData(range);

                chart.data.labels = filtered.map(d => d.date);
                chart.data.datasets[0].data = filtered.map(d => d.close);
                chart.update();
            });
        });
    </script>
    <script>
        document.querySelectorAll(".time-filter").forEach(button => {
            button.addEventListener("click", () => {
                document.querySelectorAll(".time-filter").forEach(btn => {
                    btn.classList.remove("bg-gray-800", "text-white", "hover:bg-gray-900");
                    btn.classList.add("hover:bg-gray-100", "text-gray-800");
                });

                button.classList.add("bg-gray-800", "text-white", "hover:bg-gray-900");
                button.classList.remove("hover:bg-gray-100", "text-gray-800");
            });
        });
    </script>
@endsection