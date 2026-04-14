<?php

namespace App\Http\Controllers\User;

    use App\Models\Order;
    use App\Models\Ticket;
    use App\Models\User;
    use App\Notifications\OrderApprovedNotification;
    use App\Notifications\OrderNotification;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    class OrderController extends Controller
    {
        public function index()
        {
            $userId = Auth::id();

            // Mengambil order dengan relasi 'ticket', 'event', dan 'orderDetails' yang sesuai dengan user_id
            $orders = Order::with(['ticket', 'event', 'orderDetails'])
                ->where('id_user', $userId)
                ->get();

            return view('user.order.index', compact('orders'));
        }

        public function create()
        {
            $tickets = Ticket::all();
            return view('user.catalogue.detailevent', compact('tickets'));
        }

        public function store(Request $request)
        {
            // Validasi input awal
            $request->validate([
                'id_ticket' => 'required|exists:tickets,id_ticket',
                'quantity' => 'required|integer|min:1',
            ]);

            // Ambil data tiket
            $ticket = Ticket::findOrFail($request->id_ticket);

            // Hitung total harga
            $totalPrice = $ticket->price * $request->quantity;

            // Simpan data sementara untuk ditampilkan di checkout
            $checkoutData = [
                'id_ticket' => $ticket->id_ticket,
                'id_event' => $ticket->event->id_event,
                'ticket_type' => $ticket->type,
                'ticket_price' => $ticket->price,
                'quantity' => $request->quantity,
                'total_price' => $totalPrice,
            ];

            // Arahkan ke halaman checkoutOrder
            return view('user.order.checkoutOrder', compact('checkoutData'));
        }

        public function confirm(Request $request)
        {
            // Validasi input
            $request->validate([
                'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120',
                'id_ticket' => 'required|exists:tickets,id_ticket',
                'id_event' => 'required|exists:events,id_event',
                'quantity' => 'required|integer|min:1',
                'total_price' => 'required|integer',
            ]);

            // Simpan bukti pembayaran
            $paymentProofPath = $request->file('payment_proof')->store('payments', 'public');

            // Ambil user yang sedang login
            $user = Auth::user();

            // Simpan data order ke database
            $order = Order::create([
                'id_user' => $user->id,
                'id_event' => $request->id_event,
                'id_ticket' => $request->id_ticket,
                'quantity' => $request->quantity,
                'total_price' => $request->total_price,
                'payment_proof' => $paymentProofPath,
                'status' => 'pending',
            ]);

            // Kirim notifikasi ke admin
            $admins = User::where('role', 'Admin')->get(); // Ambil semua admin
            foreach ($admins as $admin) {
                $admin->notify(new OrderNotification([
                    'id_order' => $order->id_order,
                    'user_name' => $user->name_user,
                    'event_name' => $order->event->name,
                    'quantity' => $order->quantity,
                    'total_price' => $order->total_price,
                ]));
            }

            return redirect()->route('order.index')->with('status', 'Order successfully created!');
        }

        public function show(Order $order)
        {
            return view('user.order.Ticketshow', compact('order'));
        }

        public function showEventOrder($id_order)
        {
            // Pastikan order ada sebelum mengakses propertinya
            $order = Order::with(['ticket', 'event'])->findOrFail($id_order);

            // Ambil event yang terkait dengan tiket
            $event = $order->ticket->event;

            return redirect()->route('user.catalogue.showEvent', ['id_event' => $event->id_event]);
        }

        public function edit(Order $order)
        {
            $tickets = Ticket::all();
            return view('orders.edit', compact('order', 'tickets'));
        }

        public function update(Request $request, Order $order)
        {
            $request->validate([
                'id_ticket' => 'required|exists:tickets,id_ticket',
                'quantity' => 'required|integer|min:1',
            ]);

            $ticket = Ticket::findOrFail($request->id_ticket);
            $totalPrice = $ticket->price * $request->quantity;

            $order->update([
                'id_ticket' => $request->id_ticket,
                'quantity' => $request->quantity,
                'total_price' => $totalPrice,
            ]);

            return redirect()->route('order.index')->with('success', 'Order successfully updated!');
        }

        public function destroy(Order $order)
        {
            // Hapus bukti pembayaran jika ada
            if ($order->payment_proof) {
                Storage::disk('public')->delete($order->payment_proof);
            }

            $order->delete();
            return redirect()->route('order.index')->with('success', 'Order successfully deleted!');
        }

        public function approveOrder($orderId)
        {
            // Cari order berdasarkan ID
            $order = Order::with('user')->findOrFail($orderId);

            // Perbarui status order menjadi "approved"
            $order->status = 'approved';
            $order->save();

            // Kirim notifikasi ke user terkait order yang disetujui
            if ($order->user) {
                $order->user->notify(new OrderApprovedNotification($order, 'Your custom message or second argument here'));
            }

            return redirect()->back()->with('success', 'Order approved and notification sent!');
        }
    }
