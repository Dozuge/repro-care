<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Maternal Client List</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 24px; color: #111827; }
        h1, p { margin: 0 0 8px; }
        .meta { margin-bottom: 20px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
        .right { text-align: right; }
        @media print { .no-print { display: none; } body { margin: 12px; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:16px;">
        <button onclick="window.print()">Print</button>
    </div>

    <h1>Maternal Client List</h1>
    <p class="meta">Tab: {{ strtoupper(str_replace('-', ' ', $tab)) }} | Search: {{ $search !== '' ? $search : 'All records' }} | Printed: {{ now()->format('F j, Y g:i A') }}</p>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Client</th>
                <th>Barangay</th>
                <th>Age</th>
                <th>LMP</th>
                <th>EDD</th>
                <th>Profile Summary</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td>{{ $client['number'] }}</td>
                    <td>{{ $client['woman']->name }}</td>
                    <td>{{ $client['woman']->barangay ?? '-' }}</td>
                    <td>{{ $client['age'] ?? '-' }}</td>
                    <td>{{ optional($client['pregnancy']?->lmp)->format('m/d/Y') ?? '-' }}</td>
                    <td>{{ optional($client['pregnancy']?->edd)->format('m/d/Y') ?? '-' }}</td>
                    <td>
                        Gravida: {{ $client['profile']?->gravida ?? '-' }}<br>
                        Parity: {{ $client['profile']?->parity ?? '-' }}<br>
                        1st tri: {{ $client['prenatal']['first'] ? \Illuminate\Support\Carbon::parse($client['prenatal']['first'])->format('m/d/Y') : '-' }}<br>
                        2nd tri: {{ $client['prenatal']['second'] ? \Illuminate\Support\Carbon::parse($client['prenatal']['second'])->format('m/d/Y') : '-' }}<br>
                        3rd tri: {{ $client['prenatal']['third'] ? \Illuminate\Support\Carbon::parse($client['prenatal']['third'])->format('m/d/Y') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="right">No maternal client records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>
    window.addEventListener('load', function () {
        window.print();
    });
    </script>
</body>
</html>
