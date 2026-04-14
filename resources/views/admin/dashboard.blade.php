@extends('layouts.adminLayout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto mt-8 px-4">
    <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Sales Graph Bulanan</h1>

    <!-- Dropdown untuk memilih tahun dan bulan -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.dashboard') }}">
            <div class="flex flex-wrap justify-center gap-4 mb-6">
                <!-- Total Users -->
                <div class="flex-1 max-w-xs bg-green-500 text-white rounded-lg p-4 text-center">
                    <h5 class="text-lg font-semibold mb-2">Total Users</h5>
                    <p class="text-3xl font-bold">{{ $userCount }}</p>
                </div>
                <!-- Total Events -->
                <div class="flex-1 max-w-xs bg-orange-500 text-white rounded-lg p-4 text-center">
                    <h5 class="text-lg font-semibold mb-2">Total Events</h5>
                    <p class="text-3xl font-bold">{{ $eventCount }}</p>
                </div>
                <!-- Total Orders -->
                <div class="flex-1 max-w-xs bg-blue-500 text-white rounded-lg p-4 text-center">
                    <h5 class="text-lg font-semibold mb-2">Total Orders</h5>
                    <p class="text-3xl font-bold">{{ $orderCount }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Pilihan Tahun -->
                <div>
                    <label for="yearFilter" class="block text-sm font-medium text-gray-700 mb-2">Pilih Tahun:</label>
                    <select id="yearFilter" name="year" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Tahun</option>
                        @foreach($years as $year)
                        <option value="{{ $year->year }}" {{ request()->get('year') == $year->year ? 'selected' : '' }}>
                            {{ $year->year }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilihan Bulan -->
                <div>
                    <label for="monthFilter" class="block text-sm font-medium text-gray-700 mb-2">Pilih Bulan:</label>
                    <select id="monthFilter" name="month" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Bulan</option>
                        @foreach($months as $month)
                        <option value="{{ $month }}" {{ request()->get('month') == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($month)->format('F') }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilihan Event -->
                <div>
                    <label for="eventFilter" class="block text-sm font-medium text-gray-700 mb-2">Pilih Event:</label>
                    <select id="eventFilter" name="eventFilter" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Event</option>
                        @foreach($events as $event)
                        <option value="{{ $event->id_event }}" {{ request()->get('eventFilter') == $event->id_event ? 'selected' : '' }}>
                            {{ $event->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tampilkan Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-semibold">
                        Tampilkan
                    </button>
                </div>

                <!-- Export PDF Button -->
                <div class="flex items-end">
                    <a href="{{ route('admin.exportSalesReport', ['year' => $selectedYear, 'month' => $selectedMonth]) }}" target="_blank" class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-semibold text-center">
                        Export PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Grafik Penjualan Harian -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h4 class="text-lg font-bold text-gray-800 mb-4">Grafik Penjualan Harian</h4>
        <canvas id="dailySalesGraph" class="w-full h-64"></canvas>
    </div>

    <!-- Grafik Popularitas Event -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h4 class="text-lg font-bold text-gray-800 mb-4">Grafik Popularitas Event</h4>
        <canvas id="eventPopularityGraph" class="w-full h-64"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data Grafik Penjualan Harian
        const salesData = @json($salesData);
        const dailyLabels = salesData.map(data => new Date(data.date).toLocaleDateString());
        const dailyValues = salesData.map(data => data.total_sales);

        const dailyCtx = document.getElementById('dailySalesGraph').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyLabels,
                datasets: [{
                    label: 'Total Penjualan Harian (Rp)',
                    data: dailyValues,
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#333'
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal',
                            color: '#333'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Total Penjualan (Rp)',
                            color: '#333'
                        },
                        ticks: {
                            beginAtZero: true
                        }
                    }
                }
            }
        });

        // Data Grafik Popularitas Event
        const eventPopularityData = @json($eventPopularity);
        const eventLabels = eventPopularityData.map(item => item.event_name);
        const eventCounts = eventPopularityData.map(item => item.event_count);

        const eventCtx = document.getElementById('eventPopularityGraph').getContext('2d');
        new Chart(eventCtx, {
            type: 'bar',
            data: {
                labels: eventLabels,
                datasets: [{
                    label: 'Popularitas Event (Jumlah Pembelian)',
                    data: eventCounts,
                    backgroundColor: 'rgba(33, 150, 243, 0.2)',
                    borderColor: '#2196F3',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#333'
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Nama Event',
                            color: '#333'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Jumlah Pembelian',
                            color: '#333'
                        },
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</div>
@endsection
