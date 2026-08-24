<?php

namespace App\Actions\Proposals;

use App\Models\Proposal;
use App\Models\Contact;
use App\Notifications\ProposalSentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class SendProposalAction
{
    public function execute(Proposal $proposal): void
    {
        $email = null;

        if ($proposal->client_id) {
            $client = $proposal->client;
            
            // 1. Primary Contact
            if ($client->primary_contact_id && $client->primaryContact) {
                $email = $client->primaryContact->email;
            }
            
            // 2. Decision Maker
            if (!$email) {
                $decisionMaker = Contact::where('company_id', $client->company_id)->where('is_decision_maker', true)->first();
                if ($decisionMaker) {
                    $email = $decisionMaker->email;
                }
            }
            
            // 3. Any Contact
            if (!$email) {
                $anyContact = Contact::where('company_id', $client->company_id)->first();
                if ($anyContact) {
                    $email = $anyContact->email;
                }
            }
        } elseif ($proposal->opportunity_id) {
            // Fallback to Opportunity's Lead
            $lead = $proposal->opportunity->lead;
            if ($lead) {
                $email = $lead->email;
            }
        }

        if (!$email) {
            Log::warning("Could not resolve email for Proposal {$proposal->id}");
            return;
        }

        Notification::route('mail', $email)->notify(new ProposalSentNotification($proposal));

        $proposal->update(['status' => 'sent']);
    }
}
