<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment receipt — {{ $payment->number }}</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            padding: 0;
            background: #e8eaed;
            font-family: ui-sans-serif, system-ui, sans-serif;
            color: #111;
        }
        .rcp-print-toolbar {
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
        .rcp-print-toolbar__title {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 600;
        }
        .rcp-print-toolbar__actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .rcp-print-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.45rem 0.9rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }
        .rcp-print-btn--ghost {
            background: transparent;
            color: #e2e8f0;
            border: 1px solid rgba(255,255,255,.25);
        }
        .rcp-print-btn--ghost:hover { background: rgba(255,255,255,.08); }
        .rcp-print-btn--primary {
            background: #fff;
            color: #1e293b;
        }
        .rcp-print-btn--primary:hover { background: #f1f5f9; }
        .rcp-print-sheet {
            max-width: 720px;
            margin: 1.5rem auto 2rem;
            padding: 2rem 2.25rem;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0,0,0,.12);
            border-radius: 4px;
        }
        @media print {
            body { background: #fff; }
            .rcp-print-toolbar { display: none !important; }
            .rcp-print-sheet {
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
            .rcp-doc__lines tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body>
    <header class="rcp-print-toolbar">
        <p class="rcp-print-toolbar__title">Receipt {{ $payment->number }}</p>
        <div class="rcp-print-toolbar__actions">
            @if($closeUrl ?? null)
                <a href="{{ $closeUrl }}" class="rcp-print-btn rcp-print-btn--ghost">← Back to payments</a>
            @endif
            <button type="button" class="rcp-print-btn rcp-print-btn--primary" onclick="window.print()">Print receipt</button>
        </div>
    </header>

    <main class="rcp-print-sheet">
        @include('admin.payments._receipt-document')
    </main>

    @if(request()->boolean('autoprint'))
        <script>window.addEventListener('load', () => window.print());</script>
    @endif
</body>
</html>
