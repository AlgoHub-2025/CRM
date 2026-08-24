<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Explicit permissions list avoiding blanket .own/.all scoping
        $permissions = [
            // Leads
            'leads.view.own', 'leads.view.all', 'leads.create', 'leads.update.own', 'leads.update.all', 'leads.delete', 'leads.assign', 'leads.convert',
            
            // Opportunities
            'opportunities.view.own', 'opportunities.view.all', 'opportunities.create', 'opportunities.update.own', 'opportunities.update.all', 'opportunities.delete',
            
            // Companies
            'companies.view', 'companies.create', 'companies.update', 'companies.delete',
            
            // Contacts
            'contacts.view.own', 'contacts.view.all', 'contacts.create', 'contacts.update.own', 'contacts.update.all', 'contacts.delete',
            
            // Clients
            'clients.view.own', 'clients.view.all', 'clients.create', 'clients.update.own', 'clients.update.all', 'clients.delete',
            
            // Projects
            'projects.view.own', 'projects.view.all', 'projects.create', 'projects.update.own', 'projects.update.all', 'projects.delete',
            
            // Milestones
            'milestones.view.own', 'milestones.view.all', 'milestones.create', 'milestones.update.own', 'milestones.update.all', 'milestones.delete',

            // Tasks
            'tasks.view.own', 'tasks.view.all', 'tasks.create', 'tasks.update.own', 'tasks.update.all', 'tasks.delete',
            
            // Invoices
            'invoices.view.own', 'invoices.view.all', 'invoices.create', 'invoices.update.own', 'invoices.update.all', 'invoices.delete',
            
            // Payments
            'payments.view', 'payments.create', 'payments.update', 'payments.delete',
            
            // Proposals
            'proposals.view.own', 'proposals.view.all', 'proposals.create', 'proposals.update.own', 'proposals.update.all', 'proposals.delete',
            
            // Contracts
            'contracts.view.own', 'contracts.view.all', 'contracts.create', 'contracts.update.own', 'contracts.update.all', 'contracts.delete',
            
            // Documents
            'documents.view.own', 'documents.view.all', 'documents.create', 'documents.update.own', 'documents.update.all', 'documents.delete',
            
            // Tickets
            'tickets.view.own', 'tickets.view.all', 'tickets.create', 'tickets.update.own', 'tickets.update.all', 'tickets.delete',
            
            // Globals
            'reports.view', 'settings.manage', 'users.manage'
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        // Roles from section 7.2
        $roles = [
            'CEO',
            'Managing Director',
            'Sales Manager',
            'Sales Executive',
            'Project Manager',
            'Developer',
            'Designer',
            'Finance',
            'HR',
            'Support'
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            // Assign specific permissions based on role
            if ($roleName === 'Sales Manager') {
                $role->givePermissionTo([
                    'companies.view', 'companies.create', 'companies.update', 'companies.delete',
                    'contacts.view.all', 'contacts.create', 'contacts.update.all', 'contacts.delete',
                    'leads.view.all', 'leads.create', 'leads.update.all',
                    'opportunities.view.all', 'opportunities.create', 'opportunities.update.all',
                    'clients.view.all', 'clients.create', 'clients.update.all', 'clients.delete',
                    'proposals.view.all', 'proposals.create', 'proposals.update.all', 'proposals.delete',
                    'contracts.view.all', 'contracts.create', 'contracts.update.all', 'contracts.delete'
                ]);
            } elseif ($roleName === 'Sales Executive') {
                $role->givePermissionTo([
                    'companies.view', 'companies.create', 'companies.update',
                    'contacts.view.own', 'contacts.create', 'contacts.update.own',
                    'leads.view.own', 'leads.create', 'leads.update.own',
                    'opportunities.view.own', 'opportunities.create', 'opportunities.update.own',
                    'clients.view.own', 
                    'proposals.view.own', 'proposals.create', 'proposals.update.own',
                    'contracts.view.own', 'contracts.create', 'contracts.update.own',
                    'projects.view.own', 'milestones.view.own', 'tasks.view.own'
                ]);
            } elseif ($roleName === 'Project Manager') {
                $role->givePermissionTo([
                    'projects.view.own', 'projects.create', 'projects.update.own', 'projects.delete',
                    'milestones.view.own', 'milestones.create', 'milestones.update.own', 'milestones.delete',
                    'tasks.view.own', 'tasks.create', 'tasks.update.own', 'tasks.delete',
                    'companies.view', 'contacts.view.all', 'clients.view.all'
                ]);
            } elseif (in_array($roleName, ['Developer', 'Designer'])) {
                $role->givePermissionTo([
                    'projects.view.own',
                    'tasks.view.own', 'tasks.update.own',
                    'documents.view.own', 'documents.create'
                ]);
            } elseif ($roleName === 'Finance') {
                $role->givePermissionTo([
                    'invoices.view.all', 'invoices.create', 'invoices.update.all', 'invoices.delete',
                    'payments.view', 'payments.create', 'payments.update',
                    'companies.view', 'contracts.view.all', 'reports.view'
                ]);
            } elseif ($roleName === 'Support') {
                $role->givePermissionTo([
                    'tickets.view.all', 'tickets.create', 'tickets.update.all',
                    'companies.view', 'clients.view.all', 'projects.view.all'
                ]);
            } elseif (in_array($roleName, ['CEO', 'Managing Director'])) {
                // CEO and MD get all permissions
                $role->givePermissionTo(Permission::all());
            }
        }

        // Create professional accounts
        $accounts = [
            'ceo@algohubsmc.com' => ['name' => 'Chief Executive Officer', 'role' => 'CEO'],
            'md@algohubsmc.com' => ['name' => 'Managing Director', 'role' => 'Managing Director'],
            'sales.manager@algohubsmc.com' => ['name' => 'Sales Manager', 'role' => 'Sales Manager'],
            'sales.exec@algohubsmc.com' => ['name' => 'Sales Executive', 'role' => 'Sales Executive'],
            'pm@algohubsmc.com' => ['name' => 'Project Manager', 'role' => 'Project Manager'],
            'developer@algohubsmc.com' => ['name' => 'Lead Developer', 'role' => 'Developer'],
            'finance@algohubsmc.com' => ['name' => 'Finance Controller', 'role' => 'Finance'],
            'support@algohubsmc.com' => ['name' => 'Support Lead', 'role' => 'Support'],
        ];

        foreach ($accounts as $email => $data) {
            $user = User::firstOrCreate([
                'email' => $email,
            ], [
                'name' => $data['name'],
                'password' => bcrypt('AlgoHub@2026!'),
            ]);
            $user->assignRole($data['role']);
        }
    }
}
