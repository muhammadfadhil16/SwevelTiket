<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\Api\CityController;
use App\Http\Controllers\Admin\TicketsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Exports\OrdersExport;
use App\Exports\EventsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Admin\TicketValidationController;
// user
use App\Http\Controllers\User\CatalogueController;
use App\Http\Controllers\User\TicketController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\OrderDetailController;
use App\Http\Controllers\User\UserController as UserSettingsController;
use App\Http\Controllers\User\NotificationController as UserNotificationController;


// -------------------Main--------------------------
Route::get('/', [CatalogueController::class, 'index'])->name('catalogue.index');
Route::get('/catalogue/event', [CatalogueController::class, 'showEvent'])->name('catalogue.event');
Route::get('/catalogue', [CatalogueController::class, 'showAllEvents'])->name('user.catalogue.showAllEvents');

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------Admin--------------------------
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // User Management
    Route::resource('users', UserController::class);

    // Event Management
    Route::resource('events', EventController::class);

    // Tickets Management
    Route::get('/tickets/create/{event_id}', [TicketsController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketsController::class, 'store'])->name('tickets.store');
    Route::get('/events/{event}/tickets', [TicketsController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [TicketsController::class, 'show'])->name('tickets.show');
    Route::get('/tickets/{ticket}/edit', [TicketsController::class, 'edit'])->name('tickets.edit');
    Route::put('/tickets/{ticket}', [TicketsController::class, 'update'])->name('tickets.update');
    Route::delete('/tickets/{ticket}', [TicketsController::class, 'destroy'])->name('tickets.destroy');

    // Orders Management
    Route::get('orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::post('orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    Route::get('orders/{order}/payment-proof', [AdminOrderController::class, 'showPaymentProof'])->name('admin.orders.showPaymentProof');
    Route::post('orders/{order}/send-receipt', [AdminOrderController::class, 'sendReceipt'])->name('admin.orders.sendReceipt');

    // Export Excel
    Route::get('/orders/export', function () {
        return Excel::download(new OrdersExport, 'orders.xlsx');
    })->name('admin.orders.export');
    Route::get('/events/export', function () {
        return Excel::download(new EventsExport, 'events.xlsx');
    })->name('admin.events.export');
    Route::get('/export-sales-report', [AdminController::class, 'exportSalesReport'])->name('admin.exportSalesReport');

    // Ticket Validation
    Route::get('/ticket-validation', [TicketValidationController::class, 'showTicketValidationPage'])->name('admin.ticketValidation');
    Route::post('/ticketValidation/validate', [TicketValidationController::class, 'validateTicket'])->name('admin.ticketValidation.validate');
    Route::post('/ticketValidation/fetchTicketData', [TicketValidationController::class, 'fetchTicketData'])->name('admin.ticketValidation.fetchTicketData');

    // Notifications
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications/read', [AdminNotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::delete('/admin/notifications/deleteAll', [AdminNotificationController::class, 'deleteAll'])->name('admin.notifications.deleteAll');


});

// --------------------User-------------------------
Route::middleware('auth')->group(function () {
    // Catalogue
    Route::get('/catalogue/{id_event}', [CatalogueController::class, 'showEvent'])->name('user.catalogue.showEvent');

    // Settings
    Route::prefix('user')->group(function () {
        Route::get('/settings', [UserSettingsController::class, 'settings'])->name('user.settings');
        Route::post('/settings', [UserSettingsController::class, 'updateSettings'])->name('user.settings.update');
    });

    // Tickets
    Route::get('/ticket', [TicketController::class, 'index'])->name('ticket.index');
    Route::get('/ticket/create', [TicketController::class, 'create'])->name('ticket.create');
    Route::post('/ticket', [TicketController::class, 'store'])->name('ticket.store');
    Route::get('/ticket/{ticket}', [TicketController::class, 'show'])->name('ticket.show');
    Route::get('/ticket/{ticket}/edit', [TicketController::class, 'edit'])->name('ticket.edit');
    Route::put('/ticket/{ticket}', [TicketController::class, 'update'])->name('ticket.update');
    Route::delete('/ticket/{ticket}', [TicketController::class, 'destroy'])->name('ticket.destroy');

    // Orders
    Route::get('/order', [OrderController::class, 'index'])->name('order.index');
    Route::get('/order/create', [OrderController::class, 'create'])->name('order.create');
    Route::post('/order', [OrderController::class, 'store'])->name('order.store');
    Route::post('/order/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
    Route::get('/order/event/{id_order}', [OrderController::class, 'showEventOrder'])->name('order.ShowEventOrder');
    Route::get('/order/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/order/{order}/edit', [OrderController::class, 'edit'])->name('order.edit');
    Route::put('/order/{order}', [OrderController::class, 'update'])->name('order.update');
    Route::delete('/order/{order}', [OrderController::class, 'destroy'])->name('order.destroy');
    Route::get('orders', [OrderController::class, 'index'])->name('user.orders.index');
    Route::get('orderDetail/{id_order_detail}', [OrderDetailController::class, 'show'])->name('user.orderDetail.show');

    // Notifications
    Route::prefix('user')->group(function () {
        Route::get('/notifications', [UserNotificationController::class, 'index'])->name('user.notifications.index');
        Route::post('/notifications/read', [UserNotificationController::class, 'markAsRead'])->name('user.notifications.read');
        Route::post('/notifications/mark-as-read', [UserNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::delete('/notifications/delete-all', [UserNotificationController::class, 'deleteAll'])->name('notifications.deleteAll');
    });
});

