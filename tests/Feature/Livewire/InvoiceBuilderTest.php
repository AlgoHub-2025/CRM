<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Invoices\InvoiceBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;

class InvoiceBuilderTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_order_persists_to_database()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $invoice = Invoice::factory()->create();
        $item1 = InvoiceItem::factory()->create(['invoice_id' => $invoice->id, 'sort_order' => 0]);
        $item2 = InvoiceItem::factory()->create(['invoice_id' => $invoice->id, 'sort_order' => 1]);
        $item3 = InvoiceItem::factory()->create(['invoice_id' => $invoice->id, 'sort_order' => 2]);

        $newOrder = [$item3->id, $item1->id, $item2->id];

        Livewire::test(InvoiceBuilder::class, ['invoice' => $invoice])
            ->call('updateOrder', $newOrder);

        $this->assertEquals(0, $item3->fresh()->sort_order);
        $this->assertEquals(1, $item1->fresh()->sort_order);
        $this->assertEquals(2, $item2->fresh()->sort_order);
    }

    public function test_it_can_create_a_new_invoice_and_persist_totals()
    {
        $user = User::factory()->create();
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'invoices.create.global']);
        \Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'invoices.create.own']);
        $user->givePermissionTo('invoices.create.global');
        $this->actingAs($user);

        $client = \App\Models\Client::factory()->create();

        // Items: 2x $50 and 1x $100 = $200 subtotal
        // Tax 10% = $20, Discount 5% = $10
        // Total = $210
        Livewire::test(InvoiceBuilder::class, ['clientId' => $client->id])
            ->set('items', [
                ['description' => 'Item 1', 'quantity' => 2, 'unit_price' => 50],
                ['description' => 'Item 2', 'quantity' => 1, 'unit_price' => 100],
            ])
            ->set('taxPercent', 10)
            ->set('discountPercent', 5)
            ->call('save')
            ->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'client_id' => $client->id,
            'tax' => 20,
            'discount' => 10,
            'total' => 210,
            'status' => 'draft',
        ]);

        $invoice = Invoice::where('client_id', $client->id)->first();
        $this->assertCount(2, $invoice->items);
        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'description' => 'Item 1',
            'quantity' => 2,
            'unit_price' => 50,
            'line_total' => 100,
        ]);
    }
}
