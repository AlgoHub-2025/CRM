<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PipelineStage;
use App\Models\LeadSource;

class PipelineDataSeeder extends Seeder
{
    public function run(): void
    {
        // Lead pipeline stages
        $leadStages = [
            ['name' => 'New', 'type' => 'lead', 'order' => 1],
            ['name' => 'Contacted', 'type' => 'lead', 'order' => 2],
            ['name' => 'Qualified', 'type' => 'lead', 'order' => 3],
            ['name' => 'Proposal Sent', 'type' => 'lead', 'order' => 4],
            ['name' => 'Converted', 'type' => 'lead', 'order' => 5],
            ['name' => 'Lost', 'type' => 'lead', 'order' => 6],
        ];

        foreach ($leadStages as $stage) {
            PipelineStage::firstOrCreate(
                ['name' => $stage['name'], 'type' => $stage['type']],
                ['order' => $stage['order']]
            );
        }

        // Opportunity pipeline stages
        $oppStages = [
            ['name' => 'Discovery', 'type' => 'opportunity', 'order' => 1, 'is_won' => false],
            ['name' => 'Proposal', 'type' => 'opportunity', 'order' => 2, 'is_won' => false],
            ['name' => 'Negotiation', 'type' => 'opportunity', 'order' => 3, 'is_won' => false],
            ['name' => 'Closed Won', 'type' => 'opportunity', 'order' => 4, 'is_won' => true],
            ['name' => 'Closed Lost', 'type' => 'opportunity', 'order' => 5, 'is_won' => false],
        ];

        foreach ($oppStages as $stage) {
            $model = PipelineStage::firstOrCreate(
                ['name' => $stage['name'], 'type' => $stage['type']],
                ['order' => $stage['order']]
            );
            
            if ($model->is_won !== $stage['is_won']) {
                $model->update(['is_won' => $stage['is_won']]);
            }
        }

        // Lead Sources
        $sources = ['Website', 'Referral', 'Social Media', 'Cold Call', 'Email Campaign', 'Exhibition', 'Other'];
        foreach ($sources as $source) {
            LeadSource::firstOrCreate(['name' => $source]);
        }
    }
}
