<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminEventAndTicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_event(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this->actingAs($admin)->post(route('events.store'), [
            'name' => 'Konser Musim Panas',
            'date' => now()->addDays(2)->toDateString(),
            'start_time' => '19:00',
            'end_time' => '21:00',
            'location' => 'Yogyakarta',
            'venue' => 'Balairung',
            'description' => 'Acara konser tahunan.',
            'capacity' => 300,
            'category' => 'Music',
            'whatsapp_group_link' => 'https://chat.whatsapp.com/example',
        ]);

        $response->assertRedirect(route('events.index'));

        $this->assertDatabaseHas('events', [
            'name' => 'Konser Musim Panas',
            'location' => 'Yogyakarta, Balairung',
            'capacity' => 300,
            'category' => 'Music',
            'status' => 'Upcoming',
        ]);
    }

    public function test_event_store_validates_required_fields(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $response = $this
            ->actingAs($admin)
            ->from(route('events.create'))
            ->post(route('events.store'), []);

        $response->assertRedirect(route('events.create'));
        $response->assertSessionHasErrors([
            'name',
            'date',
            'start_time',
            'end_time',
            'location',
            'venue',
            'capacity',
            'category',
        ]);
    }

    public function test_admin_can_create_tickets_when_total_is_within_capacity(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $event = Event::factory()->create(['capacity' => 100]);

        $response = $this->actingAs($admin)->post(route('tickets.store'), [
            'id_event' => $event->id_event,
            'types' => ['Regular', 'VIP'],
            'prices' => [
                'Regular' => 100000,
                'VIP' => 200000,
            ],
            'quantity' => [
                'Regular' => 50,
                'VIP' => 20,
            ],
        ]);

        $response->assertRedirect(route('events.index'));

        $this->assertDatabaseHas('tickets', [
            'id_event' => $event->id_event,
            'type' => 'Regular',
            'price' => 100000,
            'quantity' => 50,
        ]);

        $this->assertDatabaseHas('tickets', [
            'id_event' => $event->id_event,
            'type' => 'VIP',
            'price' => 200000,
            'quantity' => 20,
        ]);
    }

    public function test_ticket_store_rejects_if_total_exceeds_event_capacity(): void
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $event = Event::factory()->create(['capacity' => 10]);

        Ticket::create([
            'id_event' => $event->id_event,
            'type' => 'Regular',
            'price' => 100000,
            'quantity' => 8,
        ]);

        $response = $this
            ->actingAs($admin)
            ->from(route('tickets.create', ['event_id' => $event->id_event]))
            ->post(route('tickets.store'), [
                'id_event' => $event->id_event,
                'types' => ['VIP', 'VVIP'],
                'prices' => [
                    'VIP' => 200000,
                    'VVIP' => 300000,
                ],
                'quantity' => [
                    'VIP' => 2,
                    'VVIP' => 2,
                ],
            ]);

        $response->assertRedirect(route('tickets.create', ['event_id' => $event->id_event]));
        $response->assertSessionHas('error');
    }
}
