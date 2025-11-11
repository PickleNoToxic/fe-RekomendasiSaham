@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-background">
        {{-- Error Handling --}}
        @if (empty($stocksData))
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
                <div class="container mx-auto px-4 py-6">
                    @if (!empty($lastUpdated))
                        <div class="text-right mb-2">
                            <span class="text-sm text-gray-500">
                                Terakhir diperbarui:
                                <span class="font-medium">
                                    {{ \Carbon\Carbon::parse($lastUpdated) }}
                                </span>
                            </span>
                        </div>
                    @endif
                    <div class="text-center">
                        <h1 class="text-3xl font-bold text-primary mb-2">
                            Analisis Saham Indonesia
                        </h1>
                        <p class="text-gray-500">
                            Rekomendasi investasi untuk 5 saham terpilih dengan analisis harga harian
                        </p>
                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="container mx-auto px-4 py-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($stocksData as $stock)
                        <x-stock-card :stock="$stock" />
                    @endforeach
                </div>

                {{-- Footer Info --}}
                <div class="mt-12 text-center text-sm text-gray-500">
                    <p class="mb-2">
                        * Data harga dan rekomendasi ini adalah simulasi untuk tujuan demonstrasi
                    </p>
                    <p>
                        * Selalu lakukan riset mandiri sebelum membuat keputusan investasi
                    </p>
                </div>
            </main>
        @endif
    </div>
@endsection