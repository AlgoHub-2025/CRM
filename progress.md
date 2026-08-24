# AlgoHub CRM: Project Progress Document

This document serves as a comprehensive record of the modules, architecture, and features successfully implemented in the AlgoHub CRM project to date.

## Module 1: Foundation & Authentication
- **Framework Setup**: Initialized the Laravel 11.x project with a PostgreSQL database connection.
- **Authentication**: Implemented base authentication scaffolding.
- **RBAC Infrastructure**: Integrated `spatie/laravel-permission` for robust Role-Based Access Control.

## Module 2: Employees, Roles & Permissions
- **Data Models**: Created the `Employee` model mapped to the `User` model.
- **Granular Permissions**: Defined strict, granular permissions (e.g., `view.own`, `view.all`, `update.own`, `update.all`) across all major entities to enforce data isolation.
- **Roles & Seeding**: Implemented `RolePermissionSeeder` establishing the core organizational hierarchy. Core permissions were fully seeded for the Sales teams, while other roles were created as empty shells (permissions to be defined module-by-module):
  - CEO / Managing Director (Global Access)
  - Sales Manager (View/Update All within Sales)
  - Sales Executive (View/Update Own within Sales)
  - Project Manager, Developer, Designer, Finance, HR, Support (Roles created; permissions deferred).

## Module 3: Pre-Sales (Leads, Opportunities, Companies, Contacts)
- **Data Architecture Insight (The Opportunity Link)**:
  - An `Opportunity` is designed to flow directly from a `Lead` (pre-sales) or an existing `Client` (upsell). As such, an `Opportunity` does **not** have a direct `company_id` column.
  - For pre-sales opportunities, the company relationship is resolved via the parent lead (`Opportunity -> Lead -> Company`). If an opportunity lacks a valid lead/company linkage, it cannot be converted.
- **Companies & Contacts**: 
  - Full CRUD functionality with `.own`/`.all` visibility scoping.
  - Implemented cascading soft-deletes and extensive database indexing for performance.
- **Leads Management**: 
  - Created dynamic table views with real-time filtering.
  - Built robust creation forms featuring inline company creation (via Alpine.js toggles).
- **Sales Pipeline (Opportunities)**: 
  - Built an interactive Kanban board utilizing Livewire.
  - Implemented drag-and-drop reordering with a dedicated `order` column.
  - Added automated forecast value calculations based on probability.
- **UI/UX Refactoring**: 
  - Refactored the application shell to use a fixed left-sidebar layout (removing the top-navbar).
  - Applied the **AlgoHub Brand Guidelines** (`#2376D6` brand-blue, `#2B333E` brand-charcoal).

## Module 4: Post-Sales (Clients)
- **Database Constraints & Schema**:
  - Added `is_won` boolean to `pipeline_stages` to programmatically identify winning stages.
  - Enforced a partial unique index on `clients.company_id` (`WHERE deleted_at IS NULL`) at the database level to prevent duplicate active clients.
- **Atomic Conversions (`MarkOpportunityWonAction`)**:
  - Implemented race-safe, transactional logic to convert Won Opportunities into Clients.
  - Features `withTrashed` restoration checks to elegantly handle re-conversions.
  - Catches `UniqueConstraintViolationException` during high-concurrency race conditions to ensure data integrity without application crashes.
  - **Fallback Resolution**: If a request loses the race condition and hits the catch block, it successfully links its Opportunity to the winner's newly created Client and populates `primary_contact_id` if missing, while correctly leaving `converted_from_opportunity_id` untouched (as it belongs to the winning request's opportunity).
  - Automatically maps the Company's decision-maker as the Client's `primary_contact_id`.
- **Security & Policies**:
  - Developed `ClientPolicy` to enforce `.own` access by dynamically joining through the `Lead` and `Opportunity` relationships.
- **Automated Testing**:
  - Wrote and passed three targeted PHPUnit test suites: `MarkOpportunityWonActionTest` (verified race-condition fallback linking), `ClientPolicyTest` (verified complex RBAC `.own`/`.all` scoping via the Lead relation), and `ClientFactoryTest`.
  - These tests successfully caught a structural bug in the Opportunity-Company relation chain, which was subsequently fixed to route through the Lead.
- **Client UI**:
  - Built the `ClientList` index and the `clients.show` profile view, fully styled to the brand guidelines with placeholders for upcoming Modules.

---

### Current Status
**5 out of 7 Core Modules Completed.** 
The CRM's core foundation, pre-sales pipeline, and post-sales client conversion engine are fully operational, tested, and styled. 

### Next Up
**Module 5: Proposals & Contracts**
