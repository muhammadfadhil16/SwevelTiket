<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Notifications\OrderApprovedNotification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $customer = $request->get('customer');
        $event = $request->get('event');

        $orders = Order::with(['user', 'event'])
            ->when($customer, function ($query, $customer) {
                return $query->whereHas('user', function ($q) use ($customer) {
                    $q->where('name_user', 'like', '%' . $customer . '%');
                });
            })
            ->when($event, function ($query, $event) {
                return $query->whereHas('event', function ($q) use ($event) {
                    $q->where('name', 'like', '%' . $event . '%');
                });
            })
            ->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, $id_order)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $order = Order::with('ticket', 'user', 'event')->findOrFail($id_order);

        // Cek jika event sudah selesai (misal ada field status atau date)
        if ($order->event && (
            (isset($order->event->status) && $order->event->status === 'done') ||
            (isset($order->event->date) && $order->event->date < now())
        )) {
            return redirect()->back()->with('error', 'Status tidak dapat diubah karena event sudah selesai.');
        }

        if ($request->status === 'approved' && $order->status === 'pending') {
            $ticket = $order->ticket;
            if ($ticket->quantity >= $order->quantity) {
                $ticket->quantity -= $order->quantity;
                $ticket->save();

                $this->createOrderDetail($order);

                // Kirim notifikasi ke user
                if ($order->user) {
                    $order->user->notify(new OrderApprovedNotification($order, $order->event));
                }
            } else {
                return redirect()->back()->with('error', 'Stok tiket tidak mencukupi.');
            }
        }

        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Status berhasil diperbarui.');
    }

    private function createOrderDetail(Order $order)
    {
        DB::beginTransaction();

        try {
            $ticket = $order->ticket;

            $orderDetail = OrderDetail::create([
                'id_order' => $order->id_order,
                'id_ticket' => $ticket->id_ticket,
            ]);

            // Format QR Code
            $qrCodeContent = "|{$orderDetail->id_order_detail}|{$orderDetail->id_order}|{$orderDetail->id_ticket}|random:" . Str::random(10);
            $qrCode = new QrCode($qrCodeContent);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Simpan QR Code ke storage
            $qrCodeFilename = 'qrcodes/' . Str::random(20) . '.png';
            Storage::disk('public')->put($qrCodeFilename, $result->getString());

            // Simpan path QR Code ke database
            $orderDetail->update(['qr_code' => $qrCodeFilename]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function showPaymentProof(Order $order)
    {
        $filePath = $order->payment_proof;

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'Gambar tidak ditemukan');
        }

        $imagePath = Storage::disk('public')->path($filePath);

        return response()->file($imagePath);
    }
}
