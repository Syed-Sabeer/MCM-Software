@props([
    'mode' => 'screen',
    'title' => '',
    'number' => '',
    'accentColor' => '#0E90D9',
    'company' => [],
    'meta' => [],
    'partyBlocks' => [],
    'summaryRows' => [],
    'columns' => [],
    'items' => [],
    'totals' => [],
    'notes' => [],
    'signatures' => [],
])

@php
    $isPrint = $mode === 'print';
    $wrapperClass = $isPrint ? 'doc-standard doc-standard--print' : 'doc-standard doc-standard--screen';
    $visibleSummaryRows = collect($summaryRows)->filter(fn ($row) => filled($row['value'] ?? null))->values();
    $visibleTotals = collect($totals)->filter(function ($row) {
        if (($row['always'] ?? false) === true) {
            return true;
        }

        return filled($row['value'] ?? null) && ($row['value'] !== '0.00') && ($row['value'] !== 0) && ($row['value'] !== '0');
    })->values();
    $visibleNotes = collect($notes)->filter(fn ($row) => filled($row['value'] ?? null))->values();
@endphp

<style>
    .doc-standard {
        --doc-accent: {{ $accentColor }};
        --doc-accent-soft: color-mix(in srgb, {{ $accentColor }} 10%, white);
        --doc-border: #d8e0ea;
        --doc-muted: #6b7280;
        --doc-ink: #111827;
        color: var(--doc-ink);
        font-family: DejaVu Sans, sans-serif;
    }

    .doc-standard * {
        box-sizing: border-box;
    }

    .doc-standard--screen {
        border: 1px solid var(--doc-border);
        border-radius: 20px;
        background: #fff;
        padding: 28px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
    }

    .doc-standard--print {
        padding: 0;
        font-size: 11px;
    }

    .doc-standard__header {
        display: table;
        width: 100%;
        border-bottom: 3px solid var(--doc-accent);
        padding-bottom: 18px;
    }

    .doc-standard__header-left,
    .doc-standard__header-right {
        display: table-cell;
        vertical-align: top;
    }

    .doc-standard__header-right {
        width: 40%;
        text-align: right;
    }

    .doc-standard__logo {
        max-width: 120px;
        max-height: 70px;
        margin-bottom: 10px;
    }

    .doc-standard__company-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--doc-accent);
        margin-bottom: 6px;
    }

    .doc-standard__company-line,
    .doc-standard__meta-row,
    .doc-standard__party-line,
    .doc-standard__summary-value,
    .doc-standard__note-value {
        color: var(--doc-ink);
    }

    .doc-standard__muted,
    .doc-standard__meta-label,
    .doc-standard__party-label,
    .doc-standard__summary-label,
    .doc-standard__note-label {
        color: var(--doc-muted);
    }

    .doc-standard__title {
        font-size: 28px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: 2px;
        color: var(--doc-accent);
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .doc-standard__meta {
        display: inline-block;
        min-width: 260px;
        border: 1px solid var(--doc-border);
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
    }

    .doc-standard__meta-row {
        display: table;
        width: 100%;
        border-bottom: 1px solid var(--doc-border);
    }

    .doc-standard__meta-row:last-child {
        border-bottom: 0;
    }

    .doc-standard__meta-label,
    .doc-standard__meta-value {
        display: table-cell;
        padding: 8px 12px;
        vertical-align: top;
    }

    .doc-standard__meta-label {
        width: 42%;
        background: #f8fafc;
        font-weight: 700;
    }

    .doc-standard__hero {
        height: 10px;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--doc-accent), #dbeafe);
        margin: 18px 0 20px;
    }

    .doc-standard__parties {
        display: table;
        width: 100%;
        border-spacing: 0 0;
    }

    .doc-standard__party {
        display: table-cell;
        width: 50%;
        vertical-align: top;
        padding-right: 10px;
    }

    .doc-standard__party:last-child {
        padding-right: 0;
        padding-left: 10px;
    }

    .doc-standard__party-card,
    .doc-standard__summary-card,
    .doc-standard__note-card {
        border: 1px solid var(--doc-border);
        border-radius: 16px;
        padding: 16px;
        background: #fbfdff;
        min-height: 130px;
    }

    .doc-standard__section-title {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: var(--doc-accent);
        margin-bottom: 10px;
    }

    .doc-standard__summary-grid {
        margin-top: 18px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .doc-standard__summary-card {
        min-height: auto;
    }

    .doc-standard__summary-label {
        display: block;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .6px;
        margin-bottom: 4px;
    }

    .doc-standard__summary-value {
        font-size: 13px;
        font-weight: 700;
    }

    .doc-standard__items {
        width: 100%;
        border-collapse: collapse;
        margin-top: 22px;
    }

    .doc-standard__items th {
        background: var(--doc-accent);
        color: #fff;
        padding: 10px 8px;
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .7px;
        border: 1px solid var(--doc-accent);
    }

    .doc-standard__items td {
        border: 1px solid var(--doc-border);
        padding: 10px 8px;
        vertical-align: top;
        font-size: 11px;
    }

    .doc-standard__items tbody tr:nth-child(even) td {
        background: #fbfdff;
    }

    .doc-standard__items .text-right {
        text-align: right;
    }

    .doc-standard__items .text-center {
        text-align: center;
    }

    .doc-standard__image {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border: 1px solid var(--doc-border);
        border-radius: 8px;
    }

    .doc-standard__totals {
        margin-top: 18px;
        width: 340px;
        margin-left: auto;
        border-collapse: collapse;
    }

    .doc-standard__totals td {
        border: 1px solid var(--doc-border);
        padding: 9px 10px;
        font-size: 11px;
    }

    .doc-standard__totals-label {
        background: #f8fafc;
        font-weight: 700;
    }

    .doc-standard__totals-value {
        text-align: right;
        font-weight: 700;
    }

    .doc-standard__totals-row--grand td {
        background: #eef6ff;
        color: var(--doc-accent);
        font-size: 12px;
        font-weight: 800;
    }

    .doc-standard__notes {
        margin-top: 22px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .doc-standard__note-value {
        white-space: pre-line;
    }

    .doc-standard__signatures {
        margin-top: 30px;
        display: table;
        width: 100%;
    }

    .doc-standard__signature {
        display: table-cell;
        width: 33.333%;
        vertical-align: top;
        padding-right: 20px;
        padding-top: 36px;
    }

    .doc-standard__signature:last-child {
        padding-right: 0;
    }

    .doc-standard__signature-line {
        border-top: 1px solid #111827;
        padding-top: 8px;
        font-size: 10px;
        color: var(--doc-muted);
    }

    @media print {
        .doc-standard--screen {
            box-shadow: none;
            border: 0;
            border-radius: 0;
            padding: 0;
        }
    }
</style>

<div {{ $attributes->merge(['class' => $wrapperClass]) }}>
    <div class="doc-standard__header">
        <div class="doc-standard__header-left">
            @if (! empty($company['logo']))
                <img src="{{ $company['logo'] }}" alt="Logo" class="doc-standard__logo">
            @endif

            <div class="doc-standard__company-name">{{ $company['name'] ?? config('app.name') }}</div>

            @foreach (($company['lines'] ?? []) as $line)
                @if (filled($line))
                    <div class="doc-standard__company-line">{{ $line }}</div>
                @endif
            @endforeach
        </div>

        <div class="doc-standard__header-right">
            <div class="doc-standard__title">{{ $title }}</div>

            <div class="doc-standard__meta">
                @foreach ($meta as $row)
                    @if (filled($row['value'] ?? null))
                        <div class="doc-standard__meta-row">
                            <div class="doc-standard__meta-label">{{ $row['label'] }}</div>
                            <div class="doc-standard__meta-value">{{ $row['value'] }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <div class="doc-standard__hero"></div>

    @if (count($partyBlocks))
        <div class="doc-standard__parties">
            @foreach ($partyBlocks as $party)
                <div class="doc-standard__party">
                    <div class="doc-standard__party-card">
                        <div class="doc-standard__section-title">{{ $party['title'] }}</div>

                        @foreach (($party['lines'] ?? []) as $line)
                            @if (filled($line))
                                <div class="doc-standard__party-line">{{ $line }}</div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($visibleSummaryRows->count())
        <div class="doc-standard__summary-grid">
            @foreach ($visibleSummaryRows as $row)
                <div class="doc-standard__summary-card">
                    <span class="doc-standard__summary-label">{{ $row['label'] }}</span>
                    <span class="doc-standard__summary-value">{{ $row['value'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <table class="doc-standard__items">
        <thead>
            <tr>
                @foreach ($columns as $column)
                    <th @if(! empty($column['width'])) style="width: {{ $column['width'] }};" @endif class="{{ $column['align'] ?? '' }}">
                        {{ $column['label'] }}
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse ($items as $row)
                <tr>
                    @foreach ($columns as $column)
                        @php
                            $value = data_get($row, $column['key']);
                            $type = $column['type'] ?? 'text';
                            $align = $column['align'] ?? '';
                        @endphp

                        <td class="{{ $align }}">
                            @if ($type === 'image')
                                @if (filled($value))
                                    <img src="{{ $value }}" alt="Item Image" class="doc-standard__image">
                                @else
                                    -
                                @endif
                            @else
                                {{ filled($value) ? $value : '-' }}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ max(count($columns), 1) }}" class="text-center">No line items available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($visibleTotals->count())
        <table class="doc-standard__totals">
            @foreach ($visibleTotals as $row)
                <tr class="doc-standard__totals-row {{ !empty($row['highlight']) ? 'doc-standard__totals-row--grand' : '' }}">
                    <td class="doc-standard__totals-label">{{ $row['label'] }}</td>
                    <td class="doc-standard__totals-value">{{ $row['value'] }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if ($visibleNotes->count())
        <div class="doc-standard__notes">
            @foreach ($visibleNotes as $row)
                <div class="doc-standard__note-card">
                    <div class="doc-standard__section-title">{{ $row['label'] }}</div>
                    <div class="doc-standard__note-value">{{ $row['value'] }}</div>
                </div>
            @endforeach
        </div>
    @endif

    @if (count($signatures))
        <div class="doc-standard__signatures">
            @foreach ($signatures as $signature)
                <div class="doc-standard__signature">
                    <div class="doc-standard__signature-line">{{ $signature }}</div>
                </div>
            @endforeach
        </div>
    @endif
</div>
