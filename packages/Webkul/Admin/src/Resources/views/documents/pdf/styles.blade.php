@page { margin: 28px 34px 42px; }
* { box-sizing: border-box; }
body { margin: 0; color: #1f2937; font-family: DejaVu Sans, sans-serif; font-size: 10px; line-height: 1.45; }
table { width: 100%; border-collapse: collapse; }
tr { page-break-inside: avoid; }
.text-right { text-align: right; }
.text-center { text-align: center; }
.muted { color: #6b7280; }
.document-header td { vertical-align: top; }
.brand-column { width: 57%; padding-right: 28px; }
.document-column { width: 43%; text-align: right; }
.logo { display: block; width: 235px; max-height: 64px; object-fit: contain; object-position: left top; margin-bottom: 9px; }
.company-name { color: #111827; font-size: 17px; font-weight: 700; margin-bottom: 7px; }
.company-details { color: #4b5563; font-size: 9px; line-height: 1.55; }
.document-title { color: #111827; font-size: 25px; font-weight: 700; letter-spacing: .4px; line-height: 1.1; margin: 0 0 7px; }
.document-number { color: {{ $brandColor }}; font-size: 12px; font-weight: 700; }
.accent-rule { height: 3px; margin: 16px 0 14px; background: {{ $brandColor }}; }
.meta-table { margin-left: auto; width: 100%; }
.meta-table td { padding: 4px 0 4px 10px; border-bottom: 1px solid #e5e7eb; }
.meta-label { width: 43%; color: #6b7280; font-size: 8px; font-weight: 700; letter-spacing: .3px; text-transform: uppercase; }
.meta-value { color: #111827; font-size: 9px; font-weight: 600; }
.party-table { margin-top: 17px; table-layout: fixed; }
.party-table > tbody > tr > td { width: 50%; vertical-align: top; }
.party-left { padding-right: 7px; }
.party-right { padding-left: 7px; }
.party-box { min-height: 82px; border: 1px solid #d9dee7; }
.box-heading { padding: 6px 9px; border-bottom: 1px solid #d9dee7; background: #f3f4f6; color: #374151; font-size: 8px; font-weight: 700; letter-spacing: .55px; text-transform: uppercase; }
.party-content { padding: 9px 10px; color: #4b5563; }
.party-content div:first-child { color: #111827; font-weight: 700; margin-bottom: 2px; }
.summary-table { margin: 14px 0 17px; table-layout: fixed; }
.summary-table td { padding: 7px 9px; border: 1px solid #d9dee7; vertical-align: top; }
.summary-label { display: block; color: #6b7280; font-size: 7.5px; font-weight: 700; letter-spacing: .35px; text-transform: uppercase; }
.summary-value { display: block; margin-top: 2px; color: #111827; font-size: 9px; font-weight: 600; }
.section-heading { margin: 17px 0 7px; color: #374151; font-size: 9px; font-weight: 700; letter-spacing: .45px; text-transform: uppercase; }
.items-table { table-layout: fixed; }
.items-table th { padding: 7px 6px; border-right: 1px solid #4b5563; background: #28313d; color: #fff; font-size: 7.5px; font-weight: 700; letter-spacing: .35px; text-align: left; text-transform: uppercase; }
.items-table th:last-child { border-right: 0; }
.items-table td { padding: 8px 6px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #d9dee7; color: #374151; vertical-align: top; }
.items-table td:first-child { border-left: 1px solid #d9dee7; }
.items-table tbody tr:nth-child(even) td { background: #fafafa; }
.item-name { color: #111827; font-weight: 600; }
.item-image { width: 38px; height: 38px; border: 1px solid #d9dee7; object-fit: cover; }
.totals-wrap { margin-top: 14px; }
.totals-spacer { width: 52%; }
.totals-cell { width: 48%; }
.totals-table td { padding: 6px 9px; border-bottom: 1px solid #e5e7eb; }
.totals-label { color: #4b5563; }
.totals-value { color: #111827; font-weight: 600; text-align: right; }
.grand-row td { padding-top: 9px; padding-bottom: 9px; border-top: 2px solid #28313d; border-bottom: 0; background: #f3f4f6; color: #111827; font-size: 11px; font-weight: 700; }
.grand-row .totals-label { border-left: 3px solid {{ $brandColor }}; }
.notes-table { margin-top: 17px; table-layout: fixed; }
.notes-table td { vertical-align: top; }
.note-left { padding-right: 7px; }
.note-right { padding-left: 7px; }
.note-box { min-height: 72px; padding: 9px 10px; border: 1px solid #d9dee7; color: #4b5563; }
.note-title { margin-bottom: 5px; color: #374151; font-size: 8px; font-weight: 700; letter-spacing: .5px; text-transform: uppercase; }
.preline { white-space: pre-line; }
.signature-table { margin-top: 58px; table-layout: fixed; }
.signature-cell { width: 29%; vertical-align: bottom; }
.signature-spacer { width: 6.5%; }
.signature-line { border-top: 1px solid #4b5563; padding-top: 6px; color: #6b7280; font-size: 8px; text-align: center; text-transform: uppercase; letter-spacing: .35px; }
.footer { position: fixed; right: 0; bottom: -20px; left: 0; padding-top: 7px; border-top: 1px solid #e5e7eb; color: #9ca3af; font-size: 7.5px; }
.footer-right { text-align: right; }
