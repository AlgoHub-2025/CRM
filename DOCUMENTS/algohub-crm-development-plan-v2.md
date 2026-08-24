# AlgoHub CRM — Laravel Development Specification (v2)

**Prepared for:** AlgoHub SMC Pvt Ltd
**Purpose:** Internal CRM covering the full business lifecycle — Lead → Sales → Proposal → Contract → Client → Project → Invoice → Payment → Support → Retention.

This version tightens the original plan into a build-ready spec: consolidated scope, a normalized schema with types and relationships, a REST API surface, and an RBAC permission matrix — while trimming repeated narrative content.

---

## 1. Tech Stack

| Layer | Choice |
|---|---|
| Language/Framework | PHP 8.2+, Laravel 12 |
| Frontend | Blade, Livewire 3, Alpine.js, Tailwind CSS, Vite |
| Database | PostgreSQL 15+ |
| Cache/Queue | Redis |
| Realtime | Laravel Reverb |
| Storage | Laravel Filesystem (S3-compatible for docs) |
| Auth | Laravel Fortify/Breeze + Spatie `laravel-permission` |
| PDF | `barryvdh/laravel-dompdf` or `spatie/laravel-pdf` |
| Infra | Ubuntu VPS, Nginx, PHP-FPM, Supervisor, Cloudflare, GitHub Actions CI/CD |

**Design principle:** thin controllers. Business logic lives in `app/Actions` and `app/Services`; controllers only orchestrate. Every state transition (lead → opportunity, proposal → contract, etc.) is a single-purpose Action class, which makes it independently testable and reusable from Livewire, jobs, and future API endpoints.

---

## 2. Application Structure

```
app/
├── Actions/          # ConvertLeadToOpportunity, AcceptProposal, IssueInvoice...
├── Console/
├── Events/            # LeadAssigned, ProposalAccepted, PaymentReceived...
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   ├── Requests/
│   └── Resources/
├── Jobs/              # SendFollowUpReminder, GenerateInvoicePdf...
├── Livewire/
├── Listeners/
├── Models/
├── Notifications/
├── Policies/
├── Services/          # PricingService, PdfService, AuditService...
└── Support/

database/{factories,migrations,seeders}/
resources/{css,js,views}/
tests/{Unit,Feature}/
```

---

## 3. Database Schema

UUIDs (`ULID` preferred for sortability) on every externally-referenced table. Timestamps + `deleted_at` (soft deletes) on all business tables. Money stored as integer cents (`bigInteger`) to avoid float rounding errors.

### 3.1 Identity & Access

```
users            id, name, email, password, employee_id FK, remember_token, timestamps
employees        id, user_id FK, employee_code, designation, department, phone, hire_date, status, timestamps
roles            id, name, guard_name              -- spatie/permission
permissions      id, name, guard_name
model_has_roles / model_has_permissions / role_has_permissions   -- spatie pivot tables
```

**Permission naming convention:** `{module}.{action}` — e.g. `leads.view`, `leads.create`, `leads.update`, `leads.delete`, `leads.assign`, `invoices.view.own`, `invoices.view.all`.

### 3.2 CRM Core

```
companies        id, name, industry, website, phone, email, address, city, country,
                 tax_number, account_manager_id FK employees, notes, timestamps

contacts         id, company_id FK, name, designation, email, phone, whatsapp,
                 is_decision_maker bool, notes, timestamps

lead_sources     id, name, timestamps                        -- lookup table
pipeline_stages  id, name, order, type enum(lead,opportunity), timestamps

leads            id, name, company_id FK nullable, email, phone, whatsapp, website,
                 location, industry, source_id FK, interested_service, estimated_budget,
                 priority enum(low,medium,high), assigned_to FK employees,
                 status_id FK pipeline_stages, description, converted_at, timestamps

opportunities    id, lead_id FK nullable, client_id FK nullable, title, service,
                 value bigint, probability tinyint, expected_close_date,
                 assigned_to FK employees, stage_id FK pipeline_stages, notes, timestamps

activities       id, subject_type, subject_id (polymorphic: lead/client/opportunity),
                 employee_id FK, type enum(call,whatsapp,email,meeting,video,sms,note),
                 occurred_at, subject_line, description, result, timestamps

follow_ups       id, activity_id FK nullable, subject_type, subject_id (polymorphic),
                 employee_id FK, due_at, status enum(pending,done,overdue), notes, timestamps
```

### 3.3 Clients, Proposals, Contracts

```
clients          id, company_id FK, primary_contact_id FK contacts,
                 converted_from_opportunity_id FK, status enum(active,inactive), timestamps

proposals        id, proposal_number unique, client_id FK, project_title, status
                 enum(draft,sent,viewed,negotiation,accepted,rejected,expired),
                 subtotal, discount, tax, total, currency, valid_until,
                 payment_terms text, terms_and_conditions text, pdf_path, timestamps

proposal_items   id, proposal_id FK, description, quantity, unit_price, line_total, sort_order

contracts        id, contract_number unique, client_id FK, proposal_id FK nullable,
                 start_date, end_date, value bigint, payment_terms, scope text,
                 status enum(draft,active,completed,terminated), signed_document_path, timestamps
```

### 3.4 Projects

```
projects         id, name, client_id FK, contract_id FK nullable, project_manager_id FK employees,
                 start_date, deadline, budget bigint, technology, status
                 enum(not_started,planning,development,testing,client_review,revision,completed,maintenance,cancelled),
                 description, timestamps

project_members  id, project_id FK, employee_id FK, role varchar (e.g. "Backend Developer"), timestamps

milestones       id, project_id FK, name, due_date, order, status, timestamps

tasks            id, project_id FK, milestone_id FK nullable, title, description,
                 assigned_to FK employees, priority, deadline,
                 status enum(todo,in_progress,review,completed,blocked), timestamps

task_comments    id, task_id FK, employee_id FK, comment, timestamps
```

### 3.5 Finance

```
invoices         id, invoice_number unique, client_id FK, project_id FK nullable,
                 issue_date, due_date, subtotal, discount, tax, total, paid_amount,
                 status enum(draft,sent,partially_paid,paid,overdue,cancelled), timestamps

invoice_items    id, invoice_id FK, description, quantity, unit_price, line_total, sort_order

payments         id, invoice_id FK, client_id FK, amount bigint,
                 method enum(bank_transfer,cash,cheque,online,other),
                 transaction_reference, paid_at, received_by FK employees, notes, timestamps
```

*Outstanding balance is computed (`total - paid_amount`), not stored redundantly, and recalculated via an Action every time a payment is recorded — avoids drift between invoice and payment tables.*

### 3.6 Support & Documents

```
support_tickets  id, client_id FK, project_id FK nullable, subject, description,
                 priority enum(low,medium,high,critical),
                 status enum(open,assigned,in_progress,waiting_client,resolved,closed),
                 assigned_to FK employees, resolved_at, timestamps

ticket_messages  id, ticket_id FK, sender_type enum(employee,client), sender_id, message, timestamps

documents        id, documentable_type, documentable_id (polymorphic: lead/client/proposal/
                 contract/project/invoice/ticket), name, path, mime_type, size,
                 uploaded_by FK employees, timestamps
```

### 3.7 System

```
notifications    id (uuid), notifiable_type, notifiable_id, type, data (json), read_at, timestamps  -- Laravel default

audit_logs       id, user_id FK, action, module, record_type, record_id,
                 old_values json, new_values json, ip_address, timestamps

settings         id, key unique, value, timestamps
```

### 3.8 Entity Relationship Summary

```
companies 1──∞ contacts
companies 1──∞ leads (optional)
leads 1──1 opportunities (on conversion)
opportunities ∞──1 clients (on Won)
clients 1──∞ proposals ──1──1 contracts ──1──∞ projects
projects 1──∞ milestones 1──∞ tasks
projects 1──∞ project_members ∞──1 employees
clients 1──∞ invoices 1──∞ payments
clients 1──∞ support_tickets
(leads|clients|proposals|contracts|projects|invoices|tickets) 1──∞ documents (polymorphic)
(leads|clients|opportunities) 1──∞ activities, follow_ups (polymorphic)
```

---

## 4. RBAC Permission Matrix (starting set)

| Role | Leads | Opportunities | Clients | Proposals | Contracts | Projects | Invoices | Payments | Support | Reports | Audit Logs |
|---|---|---|---|---|---|---|---|---|---|---|---|
| CEO / MD | Full | Full | Full | Full | Full | Full | Full | Full | Full | Full | View |
| Sales Manager | Full | Full | Full | Full | View | View | View | View | — | Sales | — |
| Sales Executive | Own | Own | View | Own (draft) | — | — | — | — | — | Own | — |
| Project Manager | — | — | View | View | View | Own | View | — | View | Project | — |
| Developer/Designer | — | — | — | — | — | Assigned tasks | — | — | — | — | — |
| Finance | — | — | View | View | View | View | Full | Full | — | Finance | — |
| Support | — | — | View | — | — | View | — | — | Full | — | — |

"Own" = scoped to records where `assigned_to = auth employee`. Enforce via Laravel **Policies**, never via hiding UI elements alone.

---

## 5. API Surface (for future mobile/portal clients — structure now, ship later)

Resource-based REST under `/api/v1`, token auth via Sanctum, `Laravel API Resources` for shaping output.

```
GET    /leads                 GET    /leads/{id}
POST   /leads                 PUT    /leads/{id}          DELETE /leads/{id}
POST   /leads/{id}/convert
POST   /leads/{id}/activities
POST   /leads/{id}/follow-ups

GET    /opportunities         POST   /opportunities/{id}/won   POST /opportunities/{id}/lost

GET    /clients/{id}          GET    /clients/{id}/timeline

GET    /proposals             POST   /proposals             POST /proposals/{id}/send
POST   /proposals/{id}/accept POST   /proposals/{id}/reject  GET  /proposals/{id}/pdf

GET    /contracts             POST   /contracts

GET    /projects              GET    /projects/{id}/tasks     GET /projects/{id}/members
POST   /tasks                 PATCH  /tasks/{id}/status

GET    /invoices              POST   /invoices               GET /invoices/{id}/pdf
POST   /payments

GET    /support-tickets       POST   /support-tickets         POST /support-tickets/{id}/messages

GET    /search?q=             -- global search across all modules
```

Every mutating endpoint fires a domain Event (`LeadConverted`, `ProposalAccepted`, `PaymentReceived`, etc.) so notifications, audit logging, and realtime broadcasts stay decoupled from controller logic.

---

## 6. Core Business Workflow

```
Lead → Qualification → Requirements → Opportunity → Proposal → Negotiation
     → Contract → Client → Project → Tasks/Milestones → Delivery
     → Invoice → Payment → Support → Retention → Repeat Business
```

Automated transitions worth building as Actions from day one:
- `ConvertLeadToOpportunity` — copies relevant lead fields, keeps `lead_id` for traceability.
- `MarkOpportunityWon` — creates/links `Client`, fires `ClientOnboarded`.
- `AcceptProposal` — locks proposal, creates draft `Contract`.
- `ActivateContract` → creates draft `Project` with milestones pre-seeded from a template.
- `IssueInvoice` → snapshot of project/contract value, generates PDF.
- `RecordPayment` → recalculates invoice status (`partially_paid`/`paid`), fires `PaymentReceived`.

---

## 7. Notifications

| Event | Channels |
|---|---|
| Lead assigned | Database, Email |
| Follow-up due / overdue | Database, Email, Realtime |
| Proposal accepted/rejected | Database, Email |
| Contract signed | Database, Email |
| Task assigned / deadline approaching | Database, Realtime |
| Invoice created / payment received / overdue | Database, Email |
| Support ticket created / assigned | Database, Realtime |

WhatsApp channel to be added post-MVP via a queued driver (Twilio/WhatsApp Business API) — build the `Notification` classes channel-agnostic now so this is a config change later, not a rewrite.

---

## 8. Security Baseline

- CSRF protection, hashed passwords, rate-limited auth routes
- Laravel Policies on every model — authorize server-side regardless of UI state
- Signed/temporary URLs for private document downloads (never public S3 links)
- Input validation via Form Requests on every write endpoint
- Eloquent/query builder only — no raw SQL string concatenation
- Full audit trail on financial and permission-related actions, read-restricted to CEO/MD
- HTTPS via Cloudflare, encrypted backups, `.env` secrets never committed

---

## 9. MVP Scope (V1)

**Focus: Sales + Client Management + Basic Project Management.**

| Included in V1 | Deferred to V2 | Deferred to V3 |
|---|---|---|
| Auth, employees, roles, permissions | Invoices, payments | WhatsApp integration |
| Leads, pipeline, activities, follow-ups | Support tickets | Email automation |
| Companies, contacts, clients | Advanced reports | Mobile app |
| Proposals, basic contracts | Document management | AI features |
| Projects, tasks, dashboard | Client portal | Automated workflows |
| Notifications, audit logs | | |

---

## 10. Build Order

```
1. Foundation + Auth        6. Companies/Contacts    11. Documents      16. Reports
2. Employees/Roles/Perms    7. Clients                12. Invoices      17. Audit Logs
3. Leads                    8. Proposals               13. Payments      18. Testing
4. Activities/Follow-ups    9. Contracts               14. Support       19. Deployment
5. Sales Pipeline           10. Projects/Tasks         15. Notifications  20. AI (post-MVP)
```

---

## 11. Testing Checklist

- Unauthorized users blocked at policy level, not just UI
- Sales Executives cannot see other reps' "own"-scoped records
- Financial record edits require explicit permission
- Soft-deleted records excluded from active queries but recoverable
- Invoice totals, discounts, and tax calculate correctly
- Payment recording updates invoice `paid_amount`/status atomically
- `ConvertLeadToOpportunity`, `AcceptProposal`, `ActivateContract` Actions produce correct downstream records
- Audit log entries generated for every state-changing Action

---

## 12. Deployment Topology

```
Internet → Cloudflare → Nginx → PHP-FPM → Laravel
                                    │
                          PostgreSQL ─ Redis
                                    │
                              Supervisor (queues, Reverb)
```

CI/CD via GitHub Actions: lint → test → deploy on merge to `main`. Automated nightly PostgreSQL backups, encrypted and off-site.

---

## 13. Success Criteria

V1 ships when AlgoHub management can answer the following from the CRM alone (no spreadsheets, no WhatsApp digging):

1. Active lead count and ownership
2. Today's/overdue follow-ups
3. Pipeline value and expected close revenue
4. Active client and project counts
5. Project delay status and team assignment
6. Complete relationship history for any client

Financial reporting (payments, overdue invoices) becomes answerable once V2 ships.
