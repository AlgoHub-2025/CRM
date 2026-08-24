<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyDeletionTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_it_soft_deletes_and_restores_associated_contacts()
    {
        $company = \App\Models\Company::factory()->create();
        $contact1 = \App\Models\Contact::factory()->create(['company_id' => $company->id]);
        $contact2 = \App\Models\Contact::factory()->create(['company_id' => $company->id]);

        // Assert contacts exist
        $this->assertDatabaseHas('contacts', ['id' => $contact1->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('contacts', ['id' => $contact2->id, 'deleted_at' => null]);

        // Delete the company
        $company->delete();

        // Assert company is soft deleted
        $this->assertSoftDeleted($company);

        // Assert contacts are soft deleted
        $this->assertSoftDeleted($contact1);
        $this->assertSoftDeleted($contact2);

        // Restore the company
        $company->restore();

        // Assert company and contacts are restored
        $this->assertNotSoftDeleted($company);
        $this->assertNotSoftDeleted($contact1);
        $this->assertNotSoftDeleted($contact2);
    }
}
