<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\OrderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_prepare_checkout_from_ticket_selection(): void
    {
        $user = User::factory()->create(['role' => 'User']);
        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'id_event' => $event->id_event,
            'price' => 75000,
            'type' => 'Regular',
        ]);

        $response = $this->actingAs($user)->post(route('order.store'), [
            'id_ticket' => $ticket->id_ticket,
            'quantity' => 3,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('user.order.checkoutOrder');
        $response->assertViewHas('checkoutData', function (array $checkoutData) use ($ticket, $event) {
            return $checkoutData['id_ticket'] === $ticket->id_ticket
                && $checkoutData['id_event'] === $event->id_event
                && $checkoutData['quantity'] === 3
                && $checkoutData['total_price'] === 225000;
        });
    }

    public function test_order_confirm_validates_required_fields(): void
    {
        $user = User::factory()->create(['role' => 'User']);

        $response = $this
            ->actingAs($user)
            ->from(route('order.create'))
            ->post(route('order.confirm'), []);

        $response->assertRedirect(route('order.create'));
        $response->assertSessionHasErrors([
            'payment_proof',
            'id_ticket',
            'id_event',
            'quantity',
            'total_price',
        ]);
    }

    public function test_user_can_confirm_order_and_record_is_saved(): void
    {
        Storage::fake('public');
        Notification::fake();

        $user = User::factory()->create(['role' => 'User']);
        $admin = User::factory()->create(['role' => 'Admin']);
        $event = Event::factory()->create();
        $ticket = Ticket::factory()->create([
            'id_event' => $event->id_event,
            'price' => 100000,
            'type' => 'VIP',
        ]);

        $response = $this->actingAs($user)->post(route('order.confirm'), [
            'payment_proof' => UploadedFile::fake()->image('payment.jpg'),
            'id_ticket' => $ticket->id_ticket,
            'id_event' => $event->id_event,
            'quantity' => 2,
            'total_price' => 200000,
        ]);

        $response->assertRedirect(route('order.index'));

        $this->assertDatabaseHas('orders', [
            'id_user' => $user->id,
            'id_event' => $event->id_event,
            'id_ticket' => $ticket->id_ticket,
            'quantity' => 2,
            'total_price' => 200000,
            'status' => 'pending',
        ]);

        $order = Order::latest('id_order')->firstOrFail();
        Storage::disk('public')->assertExists($order->payment_proof);

        Notification::assertSentTo($admin, OrderNotification::class);
    }
}
