<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Ambil data bulan dan tahun yang tersedia
        $years = Order::select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year', 'asc')
            ->get();

        $months = range(1, 12); // Daftar bulan Januari sampai Desember

        // Ambil data bulan dan tahun yang dipilih (default ke bulan dan tahun sekarang)
        $selectedYear = request()->get('year', date('Y')); // Default tahun saat ini
        $selectedMonth = request()->get('month', date('m')); // Default bulan saat ini

        // Ambil data total sales per hari berdasarkan bulan dan tahun yang dipilih
        $salesData = Order::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_price) as total_sales')
        )
            ->whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->groupBy(DB::raw('DATE(created_at)')) // Group by tanggal
            ->orderBy('date', 'asc')
            ->get();

        // Ambil data event popularity per bulan dan tahun yang dipilih
        $events = Event::all(); // Ambil semua event
        $eventFilter = request()->get('eventFilter'); // Ambil filter event dari request

        // Ambil data event popularity sesuai bulan dan tahun yang dipilih
        $query = Order::select(
            DB::raw('tickets.id_event'),
            DB::raw('events.name as event_name'),
            DB::raw('COUNT(*) as event_count')
        )
            ->join('tickets', 'orders.id_ticket', '=', 'tickets.id_ticket')
            ->join('events', 'tickets.id_event', '=', 'events.id_event')
            ->whereYear('orders.created_at', $selectedYear)
            ->whereMonth('orders.created_at', $selectedMonth)
            ->groupBy('tickets.id_event', 'events.name');

        if ($eventFilter) {
            $query->where('tickets.id_event', $eventFilter);
        }

        $eventPopularity = $query->get();
    // Menghitung jumlah user, event, dan order
        $userCount = User::count();
        $eventCount = Event::count();
        $orderCount = Order::count();

        // Return view dengan data sales dan event popularity
        return view('admin.dashboard', compact(
            'salesData',
            'eventPopularity',
            'years',
            'months',
            'events',
            'selectedYear',
            'selectedMonth',
            'eventFilter',
            'userCount',
            'eventCount',
            'orderCount'
        ));
    }

    // sales report pdf export
    public function exportSalesReport()
    {
        // Ambil bulan dan tahun dari request
        $selectedYear = request()->get('year', date('Y')); // Default tahun saat ini
        $selectedMonth = request()->get('month', date('m')); // Default bulan saat ini

        // Filter data order berdasarkan bulan dan tahun yang dipilih
        $orders = Order::whereYear('created_at', $selectedYear)
            ->whereMonth('created_at', $selectedMonth)
            ->get();

        // Jika tidak ada data, beri pesan atau return dengan informasi kosong
        if ($orders->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk bulan dan tahun yang dipilih.');
        }

        // Buat data yang akan diteruskan ke view
        $data = [
            'orders' => $orders,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
        ];

        // Render PDF dengan DOMPDF menggunakan view khusus
        $pdf = Pdf::loadView('admin.SalesReport', $data);

        // Unduh file PDF
        $fileName = "sales_report_{$selectedYear}_{$selectedMonth}.pdf";
        return $pdf->download($fileName);
    }

}
