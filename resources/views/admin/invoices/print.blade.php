<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice — {{ $invoice->number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            background: #e8eaed;
            font-family: ui-sans-serif, system-ui, sans-serif;
            color: #111;
        }
        .inv-print-toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            padding: 0.75rem 1.25rem;
            background: #1e293b;
            color: #f8fafc;
            box-shadow: 0 2px 8px rgba(0,0,0,.15);
        }
        .inv-print-toolbar__title {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
        }
        .inv-print-toolbar__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .inv-print-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.9rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .inv-print-btn--ghost {
            background: transparent;
            color: #e2e8f0;
            border: 1px solid rgba(255,255,255,.25);
        }
        .inv-print-btn--ghost:hover { background: rgba(255,255,255,.08); }
        .inv-print-btn--primary {
            background: #fff;
            color: #1e293b;
        }
        .inv-print-btn--primary:hover { background: #f1f5f9; }
        .inv-print-btn--secondary {
            background: #334155;
            color: #f8fafc;
            border: 1px solid rgba(255,255,255,.15);
        }
        .inv-print-btn--secondary:hover { background: #475569; }
        .inv-print-sheet {
            max-width: 800px;
            margin: 1.5rem auto 2rem;
            padding: 2rem 2.25rem;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,.12);
            border-radius: 4px;
        }
        @media print {
            body { background: #fff; }
            .inv-print-toolbar { display: none !important; }
            .inv-print-sheet {
                margin: 0;
                max-width: none;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }
            @page {
                size: A4;
                margin: 14mm 12mm;
            }
            .inv-doc__lines tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <header class="inv-print-toolbar">
        <p class="inv-print-toolbar__title">Invoice {{ $invoice->number }}</p>
        <div class="inv-print-toolbar__actions">
            <a href="{{ $closeUrl }}" class="inv-print-btn inv-print-btn--ghost">← Back to invoice</a>
            <a href="{{ route('filament.admin.invoices.pdf', $invoice) }}" class="inv-print-btn inv-print-btn--secondary">Download PDF</a>
            <button type="button" class="inv-print-btn inv-print-btn--primary" onclick="window.print()">Print</button>
        </div>
    </header>

    <main class="inv-print-sheet">
        @include('admin.invoices._document')
    </main>

    @if(request()->boolean('autoprint'))
        <script>window.addEventListener('load', () => window.print());</script>
    @endif
</body>
</html>
