# Complete Laravel 10 + Krayin CRM ERP Project Review

**Document Date:** March 19, 2026  
**Project Stack:** Laravel 10 + Krayin CRM with Concord Modular Framework  
**Status:** Fully Documented & Analyzed

---

## 📋 Executive Summary

This is a **Laravel 10 + Krayin CRM ERP system** built on a modular package architecture using **Concord** as the framework layer. The project is NOT a monolithic Laravel app—it's organized into **18 business packages** under `packages/Webkul/*`, each with its own models, repositories, controllers, views, and migrations.

### Key Characteristics:
- ✅ **Modular:** 18 independent packages composable together
- ✅ **Concord-Driven:** Auto-registers migrations, models, routes, views, translations
- ✅ **ERP-Optimized:** Quote numbering, color variants, material consumption, goods receipts
- ✅ **Dynamic Attributes:** Custom fields system via polymorphic AttributeValue table
- ✅ **Activity Logging:** Auto-tracked entity changes
- ✅ **Admin UI:** Blade + Vue hybrid with DataGrid listings

---

## 📦 Architecture Overview

### Directory Structure

```
crm/
├── app/                           # Minimal app-level code
├── config/                        # Global Laravel config
│   ├── concord.php               # ⭐ Module registration
│   ├── app.php                   # Admin path, timezone
│   ├── filesystems.php           # Storage configuration
│   └── ... (18 config files)
├── database/                      # Base Laravel migrations
├── packages/Webkul/               # ⭐⭐⭐ PRIMARY BUSINESS LOGIC
│   ├── Core/                      # Base classes, utilities
│   ├── Admin/                     # Admin UI, routes, Blade components
│   ├── Contact/                   # Organizations, Persons
│   ├── Product/                   # Product catalog
│   ├── Quote/                     # Quotes & Proforma invoices
│   ├── PurchaseOrder/             # Vendor POs (custom)
│   ├── Lead/                      # Sales leads & pipelines
│   ├── Warehouse/                 # Warehouse management
│   ├── Attribute/                 # Dynamic attributes system
│   ├── Activity/                  # Activity logging
│   ├── Email/ & EmailTemplate/    # Email management
│   ├── DataGrid/                  # Listing framework
│   ├── User/                      # User management
│   ├── Automation/                # Rules engine
│   └── ... (and 5 more)
├── routes/                        # Root routes (minimal)
├── public/                        # Web root
├── storage/                       # Logs, uploads, cache
└── tests/                         # Minimal test suite
```

### Package Structure (Each Package)

```
packages/Webkul/Quote/src/
├── Contracts/                # Interfaces
├── Database/
│   ├── Migrations/          # Package-specific schema
│   └── Seeders/
├── Http/
│   ├── Controllers/         # Business logic
│   ├── Requests/           # Form validation (AttributeForm)
│   ├── Middleware/
│   └── Resources/          # JSON-API resources
├── Models/                  # Eloquent models
├── Providers/              # ModuleServiceProvider (auto-registers everything)
├── Repositories/           # Prettus repositories
├── Routes/
│   └── Admin/
│       ├── web.php        # Main route file
│       └── resources/     # Sub-route files
└── Resources/
    ├── views/            # Blade templates
    ├── lang/            # Translations
    └── assets/
```

---

## 🔄 Request Lifecycle - Complete Flow

### Example: Creating a Quote

```
1. GET /admin/quotes/create (Browser)
   ↓
   QuoteController::create()
   ├─ Load customer organizations
   ├─ Initialize empty quote form with defaults
   └─ Return view('admin::quotes.create', ['quote' => $quote])

2. POST /admin/quotes/create (Form Submit)
   ↓
   QuoteController::store(AttributeForm $request)
   ├─ ✅ VALIDATION (via AttributeForm request class)
   │  └─ Dynamically builds rules from Attribute definitions
   ├─ Call quoteRepository.create($request->all())
   │  ├─ prepareQuoteData() - Extract & normalize items
   │  ├─ Recalculate totals SERVER-SIDE (critical!)
   │  ├─ parent::create($productData) - Insert quote record
   │  ├─ Save custom attributes via attributeValueRepository
   │  ├─ Create child QuoteItem records for each line
   │  └─ Dispatch 'quote.create.after' event
   ├─ LogsActivity trait auto-creates Activity record
   ├─ Flash success message
   └─ Redirect to admin.quotes.index

3. GET /admin/quotes (Browser)
   ↓
   QuoteController::index()
   ├─ If AJAX: datagrid(QuoteDataGrid::class)->process()
   │  ├─ prepareQueryBuilder() - SELECT from quotes (with joins)
   │  ├─ Apply filters (status, date range, etc)
   │  ├─ Sort & paginate
   │  └─ Return JSON with formatted columns + display links
   └─ Return view('admin::quotes.index')
      └─ Vue component auto-calls AJAX to populate table

4. GET /admin/quotes/view/{id} (View Detail)
   ↓
   QuoteController::view($id)
   └─ Return view('admin::quotes.view', ['quote' => $quote])

5. GET /admin/quotes/edit/{id} (Edit Form)
   ↓
   QuoteController::edit($id)
   ├─ Load quote with relations (items, custom attributes)
   └─ Return view('admin::quotes.edit', ['quote' => $quote])

6. PUT /admin/quotes/edit/{id} (Update)
   ↓
   QuoteController::update(AttributeForm $request, $id)
   ├─ Validation
   ├─ quoteRepository.update($request->all(), $id)
   │  ├─ Compare existing items vs submitted items
   │  ├─ Update existing items
   │  ├─ Create new items (keys: 'item_0', 'item_1', ...)
   │  ├─ Delete items no longer in form
   │  └─ Recalculate totals & update quote
   └─ Redirect to view or index

7. GET /admin/quotes/{id}/print (PDF Download)
   ↓
   QuoteController::print($id)
   ├─ Load quote with all relations
   ├─ dompdf wrapper renders view('admin::quotes.pdf', ['quote' => $quote])
   │  ├─ Convert image paths to local filesystem (not HTTP URLs)
   │  ├─ Generate HTML table with items, totals, signature area
   │  └─ Serialize to PDF
   └─ Download file
```

### Key Principle: Request → Controller → Repository → Model → DB

```
Request (FormRequest validation)
   ↓
Controller (orchestrates flow, renders views)
   ↓
Repository (Prettus pattern, handles create/update/delete, child sync, totals)
   ↓
Model (Eloquent, relations, casts, traits for CustomAttribute + LogsActivity)
   ↓
Database (migrations define schema, queries executed)
```

---

## 🗄️ Data Model & Core Entities

### Key Tables & Relationships

#### Organizations (Customers/Vendors/Partners)
```sql
organizations {
  id, name, type (customer/vendor/partner),
  address, billing/shipping addresses (JSON),
  phone, website, industry, parent_organization_id,
  user_id (assigned owner),
  created_at, updated_at
}
→ hasMany Person (contacts within org)
→ hasMany Activity (polymorphic)
→ hasMany AttributeValue (dynamic fields)
→ hasMany OrganizationFile
```

#### Persons (Contacts)
```sql
persons {
  id, first_name, last_name, name, type (customer/vendor/employee),
  email, contact_numbers (JSON), phone, cell_phone,
  organization_id, user_id
}
```

#### Products
```sql
products {
  id, name, sku, internal_code, description,
  cost_price, selling_price, quantity,
  customer_organization_id (customer-specific product),
  cover_image (path),
  created_at, updated_at
}
→ hasMany ProductColor        # color_code, sort_order
→ hasMany ProductOtherImage   # image paths linked to colors
→ hasMany ProductConsumption  # BOM: materials needed
→ hasMany ProductProductionSection  # production steps
→ hasMany ProductPricingChart # dynamic pricing tiers
```

#### Quotes
```sql
quotes {
  id, quote_number (auto-generated),
  organization_id, person_id, user_id (sales owner),
  subject, description, quote_date,
  status (draft/sent/approved/rejected/expired/cancelled),
  sub_total, tax_amount, grand_total,
  tariff_percent, freight_percent,
  payment_term, shipping_method, production_time, transit_time,
  etd (estimated delivery), eta,
  billing_address, shipping_address (JSON),
  notes, terms, attachment_path,
  created_at, updated_at
}
→ hasMany QuoteItem
  └─ Each item: product_id, qty, unit_price, tax_amount, 
     color_variant_id, preview_image, final_total
```

#### ProformaInvoice (Quote-adjacent invoice)
```sql
proforma_invoices {
  id, proforma_number, quote_id,
  organization_id, person_id, user_id,
  issue_date, due_date, status,
  subtotal, tax, grand_total,
  received_amount, remaining_amount (tracked via ProformaReceipt),
  created_at, updated_at
}
→ hasMany ProformaInvoiceItem
→ hasMany ProformaReceipt (track payments)
```

#### PurchaseOrder (Vendor quotes)
```sql
purchase_orders {
  id, po_number (auto PO-#####),
  job_order_id, vendor_quote_id,
  organization_id (vendor), user_id (buyer),
  status (draft/pending/approved/partial/complete),
  sub_total, tax, grand_total,
  expected_receive_date,
  created_at, updated_at
}
→ hasMany PurchaseOrderItem
  └─ qty vs received_quantity vs pending_quantity
→ hasMany GoodsReceipt (track incoming shipments)
→ hasMany VendorPayable (payment obligations)
```

#### Leads
```sql
leads {
  id, case_no, title, lead_value, status,
  priority, expected_close_date, closed_at,
  user_id (assigned owner),
  person_id, organization_id,
  lead_source_id, lead_pipeline_id, lead_pipeline_stage_id,
  created_at, updated_at
}
```

### Polymorphic Relations (Morphmap)

```php
Relation::morphMap([
    'organizations' → Organization::class,
    'persons'       → Person::class,
    'products'      → Product::class,
    'quotes'        → Quote::class,
    'leads'         → Lead::class,
    'warehouses'    → Warehouse::class,
]);
```

#### Activity Table (Audit Trail)
```sql
activities (polymorphic)
  entity_type: 'organizations|persons|products|quotes|...'
  entity_id: foreign key to that entity
  type: 'created|updated|deleted'
  additional: JSON with field changes
  user_id: who made the change
  created_at
```

#### AttributeValue Table (Dynamic Fields)
```sql
attribute_values (polymorphic)
  attribute_id, entity_type, entity_id, attribute_code,
  text_value, float_value, boolean_value, integer_value,
  datetime_value, date_value, json_value, ...
```

### Attribute System (Dynamic Fields)

Allows organizations, leads, products, quotes to have custom fields:

```php
Attribute {
  code: 'custom_field_name',
  entity_type: 'organizations|products|quotes|...',
  type: 'text|textarea|price|select|checkbox|email|address|datetime|file|...',
  is_required: boolean,
  sort_order: integer,
}

// Usage in code:
$org = Organization::find(1);
$org->custom_field_name  // Trait intercepts, loads from attribute_values
```

---

## 🛣️ Routing Architecture

### Route Entry Points

**Root:** `routes/web.php`  
Minimal, mostly redirects.

**Admin Routes:** `packages/Webkul/Admin/src/Routes/Admin/`
```
web.php (main file, requires sub-files)
├── auth-routes.php
├── contacts-routes.php
├── products-routes.php
├── quote-routes.php
├── proforma-invoice-routes.php
├── purchase-order-routes.php
├── leads-routes.php
├── warehouse-routes.php
├── settings-routes.php
└── ... (and more)
```

### Route Naming Convention

`admin.<feature>.<action>`

Examples:
- `admin.quotes.index` → GET /admin/quotes
- `admin.quotes.create` → GET /admin/quotes/create
- `admin.quotes.store` → POST /admin/quotes/create
- `admin.quotes.view` → GET /admin/quotes/view/{id}
- `admin.quotes.edit` → GET /admin/quotes/edit/{id}
- `admin.quotes.update` → PUT /admin/quotes/edit/{id}
- `admin.quotes.delete` → DELETE /admin/quotes/{id}
- `admin.purchase_orders.index` → GET /admin/purchase-orders

### Middleware Stack

All admin routes wrapped with:
```php
Route::middleware(['web', 'admin_locale', 'user'])
    ->prefix(config('app.admin_path'))  // default: 'admin'
```

**web** - Laravel cookies, CSRF, sessions, routing  
**admin_locale** - Set app locale from session  
**user** (Bouncer) - Authentication + authorization check  

---

## 🏗️ Module Registration & Bootstrapping

### How Concord Works

1. **Laravel boots** with `config/app.php` providers
2. **Concord discovers modules** from `config/concord.php`:
   ```php
   'modules' => [
       Webkul\Activity\Providers\ModuleServiceProvider::class,
       Webkul\Admin\Providers\ModuleServiceProvider::class,
       // ... 16 more
   ]
   ```
3. **Each module's ModuleServiceProvider extends BaseModuleServiceProvider:**
   ```php
   // BaseModuleServiceProvider automatically:
   // - Registers migrations from src/Database/Migrations/
   // - Registers models (if defined in $models array)
   // - Registers enums from src/Enums/
   // - Registers form requests from src/Http/Requests/
   // - Registers views from src/Resources/views/ (as package.*)
   // - Registers routes from src/Routes/<Name>/web.php (as package.*)
   // - Registers language files from src/Resources/lang/
   ```

### AdminServiceProvider Special Role

[packages/Webkul/Admin/src/Providers/AdminServiceProvider.php](packages/Webkul/Admin/src/Providers/AdminServiceProvider.php)

- Registers admin-specific middleware (`user`, `admin_locale`)
- Registers admin routes at `/admin` prefix
- Loads ACL (access control lists)
- Sets up Morphmap for polymorphic relations
- Handles admin-specific exceptions

---

## 🎨 Frontend Architecture

### Blade Component System

Location: `packages/Webkul/Admin/src/Resources/views/components/`

Registered as anonymous components with `admin::` prefix.

**Usage Pattern:**
```blade
<x-admin::layouts>
    <x-slot:title>Page Title</x-slot>
    
    <x-admin::form :action="route('admin.quotes.store')">
        <x-admin::form.control-group>
            <x-admin::form.control-group.label class="required">
                Quote #
            </x-admin::form.control-group.label>
            
            <x-admin::form.control-group.control 
                type="text" 
                name="quote_number" 
                value="{{ old('quote_number') }}"
                rules="required"
            />
            
            <x-admin::form.control-group.error control-name="quote_number" />
        </x-admin::form.control-group>
    </x-admin::form>
</x-admin::layouts>
```

### Vue.js Integration (Hybrid Pattern)

Vue components embedded directly in Blade files:

```blade
<!-- Script template defined in Blade -->
@pushOnce('scripts')
    <script type="text/x-template" id="v-quote-template">
        <div>
            <input v-model="form.quote_number" />
            <button @click="saveQuote">Save</button>
        </div>
    </script>
    
    <!-- Component registration -->
    <script type="module">
        app.component('v-quote', {
            template: '#v-quote-template',
            props: ['errors', 'customers'],
            data() {
                return {
                    form: {
                        quote_number: '{{ old("quote_number") }}'
                    }
                };
            },
            computed: {
                // ...
            },
            methods: {
                saveQuote() {
                    // Handle save
                }
            },
            mounted() {
                // Component mounted
            }
        });
    </script>
@endPushOnce

<!-- Component usage -->
<v-quote :errors="errors" :customers='@json($customers)' />
```

### DataGrid System (List Page Framework)

Location: `packages/Webkul/Admin/src/DataGrids/`

Each list page uses a DataGrid class:

```php
class QuoteDataGrid extends DataGrid {
    // 1. Build query
    public function prepareQueryBuilder(): Builder {
        return DB::table('quotes')
            ->select('quotes.id', 'quotes.quote_number', ...)
            ->leftJoin('organizations', 'quotes.organization_id', 'organizations.id');
    }
    
    // 2. Define filters
    public function prepareFilters(): void {
        $this->addFilter('quote_number', 'quotes.quote_number');
        $this->addFilter('status', 'quotes.status');
    }
    
    // 3. Define columns for display
    public function prepareColumns(): void {
        $this->addColumn([
            'index' => 'id',
            'label' => 'ID',
            'type' => 'integer',
            'sortable' => true,
            'closure' => fn($row) => $row->id
        ]);
        $this->addColumn([
            'index' => 'quote_number',
            'label' => 'Quote #',
            'searchable' => true,
        ]);
    }
    
    // 4. Define row actions
    public function prepareActions(): void {
        $this->addAction([
            'index' => 'edit',
            'title' => 'Edit',
            'method' => 'GET',
            'route' => 'admin.quotes.edit',
        ]);
    }
}
```

**Controller usage:**
```php
public function index() {
    if (request()->ajax()) {
        return datagrid(QuoteDataGrid::class)->process();
    }
    return view('admin::quotes.index');  // Vue component calls AJAX
}
```

---

## 💾 File Storage & Uploads

### Configuration

[config/filesystems.php](config/filesystems.php)

```php
'default' => 'public',

'disks' => [
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL') . '/storage',
        'visibility' => 'public',
    ],
]
```

**Upload Path:** `storage/app/public/`  
**Web URL:** `/storage/...`  
*App symlinks `public/storage/` → `storage/app/public/`*

### Upload Patterns

**Product Images:**
- `products/{product_id}/cover.jpg` - Main cover image
- `products/{product_id}/color-{color_id}.jpg` - Color-specific images
- `products/{product_id}/{generated_uuid}.jpg` - Other images

**Quote Attachments:**
- `quotes/{quote_id}/quote-{quote_number}.pdf` - Generated PDF
- `quotes/{quote_id}/{filename}` - Customer file attachments

### Polymorphic File Relations

**Activity Files** (`activity_files` table):
```
Activity hasMany File
File belongsTo Activity (polymorphic to entity via Activity)
```

**Organization Files:**
```
Organization hasMany OrganizationFile
OrganizationFile { organization_id, path, original_name }
```

---

## 🔐 Authentication & Authorization

### Authentication

**Guard:** `user` (defined in `config/auth.php`)

**Provider:** `users` table

**Login Endpoint:** `admin.session.create` (form) → `admin.session.store` (process)

### Authorization (Bouncer)

[Admin/Bouncer.php](packages/Webkul/Admin/src/Bouncer.php)

**Checks in middleware:**
1. User authenticated via `auth()->guard('user')->check()`
2. User active: `user->status == 1`
3. User has permissions: `role->permissions` not empty
4. User has route ACL in `Admin/Config/acl.php`

**Data Filtering:**
```php
if ($userIds = bouncer()->getAuthorizedUserIds()) {
    $queryBuilder->whereIn('user_id', $userIds);
}
```
Users only see data assigned to them.

---

## 🏥 Repository Pattern & Business Logic

### Base Repository Class

[packages/Webkul/Core/src/Eloquent/Repository.php](packages/Webkul/Core/src/Eloquent/Repository.php)

Extends Prettus `BaseRepository`:

```php
class Repository extends BaseRepository {
    // Standard methods:
    find($id)
    findOrFail($id)
    findWhere([...])
    findOneWhere([...])
    create($data)
    update($data, $id)
    delete($id)
    
    // Repository-specific:
    public function model() {
        return MyModel::class;  // Define in child class
    }
}
```

### Custom Repository Example

[packages/Webkul/Quote/src/Repositories/QuoteRepository.php](packages/Webkul/Quote/src/Repositories/QuoteRepository.php)

```php
class QuoteRepository extends Repository {
    public function model() {
        return Quote::class;
    }
    
    // Override create() for complex sync
    public function create($data) {
        $data = $this->prepareQuoteData($data);
        
        $quote = parent::create($data);  // Insert quote
        
        // Create child items
        foreach ($data['items'] as $itemData) {
            $this->quoteItemRepository->create(
                array_merge($itemData, ['quote_id' => $quote->id])
            );
        }
        
        // Save custom attributes
        $this->attributeValueRepository->save([...]);
        
        return $quote;
    }
    
    // Helper: Normalize & validate submitted data
    private function prepareQuoteData($data) {
        // Extract items
        $items = $data['items'] ?? [];
        
        // Recalculate totals (server-side, never trust frontend)
        $subTotal = 0;
        $taxAmount = 0;
        
        foreach ($items as &$item) {
            $qty = (float) ($item['qty'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $lineTotal = $qty * $unitPrice;
            $lineTax = $lineTotal * ((float) ($item['tax_percent'] ?? 0) / 100);
            
            $subTotal += $lineTotal;
            $taxAmount += $lineTax;
        }
        
        $data['sub_total'] = $subTotal;
        $data['tax_amount'] = $taxAmount;
        $data['grand_total'] = $subTotal + $taxAmount + 
                               (float) ($data['adjustment_amount'] ?? 0) - 
                               (float) ($data['discount_amount'] ?? 0);
        
        return $data;
    }
}
```

### Critical Rule: Server-Side Totals

**NEVER trust frontend totals.** Always recalculate on server:

```php
// CORRECT ✅
$subTotal = 0;
foreach ($items as $item) {
    $subTotal += (float)$item['qty'] * (float)$item['unit_price'];
}
$data['grand_total'] = $subTotal + $tax - $discount;

// WRONG ❌
$data['grand_total'] = (float) request('grand_total');  // Frontend could be manipulated
```

---

## 📝 Validation & Forms

### AttributeForm (Dynamic Validation)

[packages/Webkul/Admin/src/Http/Requests/AttributeForm.php](packages/Webkul/Admin/src/Http/Requests/AttributeForm.php)

Dynamically builds validation rules from Attribute definitions:

```php
public function rules() {
    $entity_type = request('entity_type');  // 'organizations|products|quotes|...'
    
    $rules = [];
    
    $attributes = Attribute::where('entity_type', $entity_type)->get();
    
    foreach ($attributes as $attribute) {
        if ($attribute->type === 'email') {
            // Email array validation
            $rules["{$attribute->code}.*.value"] = 'email';
            $rules["{$attribute->code}.*.label"] = 'required';
        } elseif ($attribute->type === 'address') {
            // Address sub-fields
            $rules["{$attribute->code}.address"] = $attribute->is_required ? 'required' : 'nullable';
            $rules["{$attribute->code}.country"] = $attribute->is_required ? 'required' : 'nullable';
        } elseif ($attribute->is_required) {
            $rules[$attribute->code] = 'required';
        } else {
            $rules[$attribute->code] = 'nullable';
        }
    }
    
    return $rules;
}
```

### Usage in Controller

```php
public function store(AttributeForm $request) {
    // Request automatically validates via dynamic rules
    $validated = $request->all();  // Already validated
    
    $entity = $this->repository->create($validated);
    
    return redirect()->route('admin.entity.index')->with('success', 'Created!');
}
```

---

## 📊 DataGrid & Listing Pages

### Complete Flow

1. **GET /admin/products** (Browser)
   - Controller loads view with empty placeholder
   - Vue component automatically calls AJAX

2. **AJAX /admin/products?_datagrid_index=1** (DataGrid request)
   - Controller detects AJAX
   - `datagrid(ProductDataGrid::class)->process()`
   - Returns JSON with table data

3. **DataGrid Process:**
   ```
   prepareQueryBuilder()   → SELECT ... WHERE ... (unfiltered)
   ↓
   prepareFilters()        → Add filter methods
   ↓
   Apply request filters   → WHERE status = 'active' (from ?status=active)
   ↓
   prepareColumns()        → Define display columns
   ↓
   Apply sorting/pagination → ORDER BY, LIMIT, OFFSET
   ↓
   Format for JSON         → Return paginated, column-formatted data
   ```

### Example: ProductDataGrid

```php
class ProductDataGrid extends DataGrid {
    private $productRepository;
    
    public function __construct(ProductRepository $repository) {
        parent::__construct();
        $this->productRepository = $repository;
    }
    
    public function prepareQueryBuilder(): Builder {
        return DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.cost_price',
                'products.selling_price',
                'categories.name AS category_name'
            )
            ->leftJoin('product_categories', 'products.category_id', 'categories.id');
    }
    
    public function prepareFilters(): void {
        $this->addFilter('sku', 'products.sku');
        $this->addFilter('name', 'products.name');
        $this->addFilter('category_id', 'products.category_id');
    }
    
    public function prepareColumns(): void {
        // Column 1: ID
        $this->addColumn([
            'index' => 'id',
            'label' => trans('admin::app.products.datagrid.id'),
            'type' => 'integer',
            'sortable' => true,
            'searchable' => true,
        ]);
        
        // Column 2: SKU
        $this->addColumn([
            'index' => 'sku',
            'label' => trans('admin::app.products.datagrid.sku'),
            'type' => 'string',
            'sortable' => true,
            'searchable' => true,
            'closure' => fn($row) => $row->sku
        ]);
        
        // Column 3: Name (with link)
        $this->addColumn([
            'index' => 'name',
            'label' => trans('admin::app.products.datagrid.name'),
            'type' => 'string',
            'sortable' => true,
            'closure' => fn($row) => 
                '<a href="' . route('admin.products.edit', $row->id) . '">' . 
                htmlspecialchars($row->name) . 
                '</a>'
        ]);
        
        // Column 4: Price (formatted)
        $this->addColumn([
            'index' => 'selling_price',
            'label' => trans('admin::app.products.datagrid.price'),
            'type' => 'decimal',
            'closure' => fn($row) => 
                format_price($row->selling_price)  // Custom helper
        ]);
    }
    
    public function prepareActions(): void {
        // Edit action
        $this->addAction([
            'index' => 'edit',
            'title' => trans('admin::app.products.datagrid.edit'),
            'method' => 'GET',
            'route' => 'admin.products.edit',
            'icon' => 'fab-pencil'
        ]);
        
        // Delete action
        $this->addAction([
            'index' => 'delete',
            'title' => trans('admin::app.products.datagrid.delete'),
            'method' => 'DELETE',
            'route' => 'admin.products.delete',
            'icon' => 'fab-trash',
        ]);
    }
    
    public function prepareMassActions(): void {
        $this->addMassAction([
            'index' => 'delete',
            'title' => trans('admin::app.products.datagrid.mass-delete'),
            'method' => 'POST',
            'route' => 'admin.products.mass_delete',
            'action' => 'delete'
        ]);
    }
}
```

---

## 📑 Models & Traits

### CustomAttribute Trait

[packages/Webkul/Attribute/src/Traits/CustomAttribute.php](packages/Webkul/Attribute/src/Traits/CustomAttribute.php)

Allows dynamic fields on models:

```php
class Organization extends Model {
    use CustomAttribute;  // ← Add this
    
    protected $fillable = ['name', 'type', ...];
}

// Usage:
$org = Organization::find(1);

// Access dynamic field (intercepts getAttribute):
echo $org->custom_field_name;
// → Loads from attribute_values table

// Set dynamic field:
$org->custom_field_name = 'value';
$org->save();
// → Stores in attribute_values via eloquent hook
```

### LogsActivity Trait

[packages/Webkul/Activity/src/Traits/LogsActivity.php](packages/Webkul/Activity/src/Traits/LogsActivity.php)

Auto-tracks model changes:

```php
class Quote extends Model {
    use LogsActivity;  // ← Add this
    
    protected $fillable = ['quote_number', 'status', ...];
}

// On create:
$quote = Quote::create([...]);
// → Activity record created with type='created'

// On update:
$quote->update(['status' => 'approved']);
// → Activity record created with type='updated'
// → Includes old value, new value, field name

// On delete:
$quote->delete();
// → Activity record created with type='deleted'
```

---

## 🎯 How to Add a New CRUD Feature

### Complete Checklist

```
1. ✅ Create Route File
   File: packages/Webkul/Admin/src/Routes/Admin/feature-routes.php
   
2. ✅ Include Route File
   File: packages/Webkul/Admin/src/Routes/Admin/web.php
   Add: require 'feature-routes.php';
   
3. ✅ Create Controller
   File: packages/Webkul/Admin/src/Http/Controllers/Feature/FeatureController.php
   Methods: index, create, store, view, edit, update, destroy
   
4. ✅ Create Repository
   File: packages/Webkul/Feature/src/Repositories/FeatureRepository.php
   
5. ✅ Create Migration
   File: packages/Webkul/Feature/src/Database/Migrations/YYYY_MM_DD_create_features_table.php
   
6. ✅ Create Model
   File: packages/Webkul/Feature/src/Models/Feature.php
   
7. ✅ Create Service Provider
   File: packages/Webkul/Feature/src/Providers/ModuleServiceProvider.php
   
8. ✅ Register Provider
   File: config/concord.php
   Add: \Webkul\Feature\Providers\ModuleServiceProvider::class,
   
9. ✅ Create Views
   Files:
   - packages/Webkul/Admin/src/Resources/views/features/create.blade.php
   - packages/Webkul/Admin/src/Resources/views/features/edit.blade.php
   - packages/Webkul/Admin/src/Resources/views/features/view.blade.php
   - packages/Webkul/Admin/src/Resources/views/features/index.blade.php
   
10. ✅ Create DataGrid
    File: packages/Webkul/Admin/src/DataGrids/Feature/FeatureDataGrid.php
    
11. ✅ Add Translations
    File: packages/Webkul/Admin/src/Resources/lang/en/app.php
    Add: 'features' => [...]
    
12. ✅ (Optional) Add Morphmap Entry
    File: AdminServiceProvider.php
    If activity logging needed:
    Relation::morphMap(['features' => Feature::class]);
    
13. ✅ Tests
    File: tests/Feature/FeatureTest.php
    
14. ✅ Run Migrations
    Command: php artisan migrate
```

---

## 🔍 Debugging & Troubleshooting

### Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| **Blade template won't update** | Run `php artisan view:clear` |
| **Route not found** | Run `php artisan route:list \| grep feature-name` |
| **Stale SQL join errors** | Check DataGrid query matches current schema via `php artisan migrate:status` |
| **Attribute values not saving** | Ensure model has `CustomAttribute` trait |
| **Activity not logging** | Ensure model has `LogsActivity` trait |
| **PDF images not showing** | Use local path not HTTP URL: `public_path('storage/' \. \$path)` |
| **CORS/AJAX errors** | Check `config/cors.php` and middleware order |
| **User sees no data** | Check `bouncer()->getAuthorizedUserIds()` filtering |

### Debugging Commands

```bash
# Clear all caches
php artisan optimize:clear

# Clear compiled views
php artisan view:clear

# Check Laravel logs
tail -f storage/logs/laravel.log

# List all routes
php artisan route:list

# Check migrations
php artisan migrate:status

# Access tinker REPL
php artisan tinker
>>> \Webkul\Product\Models\Product::with('colors')->first();
```

---

## 🚀 Key Performance Tips

1. **Eager-load relations** to avoid N+1 queries:
   ```php
   $quotes = Quote::with(['organization', 'items.product', 'user'])->get();
   ```

2. **Cache attribute lookups:**
   ```php
   $attributes = Cache::remember('attributes.products', 3600, function() {
       return Attribute::where('entity_type', 'products')->get();
   });
   ```

3. **Index frequently filtered columns:**
   - user_id, status, created_at, organization_id

4. **Paginate large results:**
   ```php
   $quotes = Quote::paginate(20);  // or simplePaginate(20)
   ```

5. **Use DataGrid pagination** instead of loading all rows

---

## 📚 Quick Reference Table

| Layer | Location | Example |
|-------|----------|---------|
| **Route** | `Admin/Routes/Admin/*-routes.php` | `admin.quotes.create` |
| **Controller** | `Admin/Http/Controllers/Quote/` | `QuoteController::store()` |
| **Repository** | `Quote/Repositories/` | `QuoteRepository::create()` |
| **Model** | `Quote/Models/Quote.php` | `Quote::generateNextQuoteNumber()` |
| **Migration** | `Quote/Database/Migrations/` | `create_quotes_table` |
| **View** | `Admin/Resources/views/quotes/` | `create.blade.php` |
| **DataGrid** | `Admin/DataGrids/Quote/` | `QuoteDataGrid::class` |
| **Validation** | `Admin/Http/Requests/` | `AttributeForm::class` |
| **Trait** | Package feature | `CustomAttribute`, `LogsActivity` |

---

## 📖 Essential Files (Bookmarks)

Core Configuration:
- `config/concord.php` - Module registration
- `config/app.php` - Admin path, timezone
- `config/auth.php` - Auth guards

Core Classes:
- `packages/Webkul/Core/src/Eloquent/Repository.php` - Base repository
- `packages/Webkul/Admin/src/Bouncer.php` - Authorization

Admin Setup:
- `packages/Webkul/Admin/src/Providers/AdminServiceProvider.php` - Admin bootstrap
- `packages/Webkul/Admin/src/Routes/Admin/web.php` - Route composition

Key Services:
- `packages/Webkul/Contact/Repositories/OrganizationRepository.php` - Contact management
- `packages/Webkul/Quote/Repositories/QuoteRepository.php` - Quote management
- `packages/Webkul/Product/Repositories/ProductRepository.php` - Product management

---

## 💡 Final Principles

1. **Route → Controller → Repository → Model → DB** - Follow this flow
2. **Server recalculates totals** - Never trust frontend math
3. **Custom attributes via trait** - Use `CustomAttribute` for dynamic fields
4. **Activity auto-tracking** - Use `LogsActivity` for audit trails
5. **DataGrid for lists** - List pages use DataGrid, not raw Blade loops
6. **Blade components reused** - Use `x-admin::*` components consistently
7. **Morphmap for polymorphism** - Register all entities needing activities/files
8. **Package-local logic** - Keep business logic in `packages/Webkul/*`, not in `app/`
9. **Route helpers always** - Never hard-code `/admin/...` URLs
10. **Concord registration** - New features need provider + config/concord.php entry

---

**Document Complete** ✅  
Use this as your reference guide for the entire project architecture and flows.
