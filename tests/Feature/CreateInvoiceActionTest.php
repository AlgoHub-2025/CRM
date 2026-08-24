<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Client;
use App\Actions\Invoices\CreateInvoiceAction;
use App\Models\Invoice;

class CreateInvoiceActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_invoice_action()
    {
        $client = Client::factory()->create();

        $data = [
            'client_id' => $client->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'discount' => 1000,
            'tax' => 2000,
        ];

        $items = [
            ['description' => 'Item 1', 'quantity' => 2, 'unit_price' => 5000],
            ['description' => 'Item 2', 'quantity' => 1, 'unit_price' => 10000],
        ];

        $action = new CreateInvoiceAction();
        $invoice = $action->execute($data, $items);

        // subtotal = (2 * 5000) + (1 * 10000) = 20000
        // total = 20000 - 1000 + 2000 = 21000

        $this->assertEquals(20000, $invoice->subtotal);
        $this->assertEquals(21000, $invoice->total);
        $this->assertCount(2, $invoice->items);
        $this->assertEquals(20000, $invoice->items->sum('line_total'));
        $this->assertEquals(0, $invoice->items[0]->sort_order);
        $this->assertEquals(1, $invoice->items[1]->sort_order);
    }
}
