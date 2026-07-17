# Document UI Engine

## Shared Blade Components
- `admin::components.documents.standard`
  - shared commercial document renderer for screen and print modes
  - accepts company, meta, party blocks, summary rows, line item columns, totals, notes, and signatures
- `admin::components.documents.form-styles`
  - shared create/edit styling layer for document entry forms

## Current Modules Using It
- Quote print view
- Proforma Invoice detail + print views
- Final Invoice detail + print views
- Purchase Order detail + print views
- Quote / Proforma / Purchase Order create-edit forms use the shared form style include

## Data Mapping Pattern
Each document view prepares:
- `company`
- `meta`
- `partyBlocks`
- `summaryRows`
- `columns`
- `items`
- `totals`
- `notes`
- `signatures`

The component renders the document consistently while allowing each module to choose labels and visible sections.

## Final Invoice Flow
- Final invoices are immutable commercial snapshots created from issued, non-cancelled proforma invoices.
- Proforma advances are carried as `advance_applied`; later receipts are stored separately in `invoice_receipts`.
- Invoice PDFs use the shared mature PDF document partials and show total, advance, subsequent payments, and balance due.
