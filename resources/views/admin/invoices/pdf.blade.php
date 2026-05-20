<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice — {{ $invoice->number }}</title>
    <style>
        @page { margin: 36px 40px; }
    </style>
</head>
<body>
    @include('admin.invoices._document')
</body>
</html>
