<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #222; margin: 0; padding: 20px; }
        h1 { font-size: 16px; margin: 0 0 4px 0; color: #8B0000; }
        .meta { font-size: 10px; color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background: #8B0000; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; white-space: nowrap; }
        td { padding: 5px 8px; border-bottom: 1px solid #e5e5e5; vertical-align: top; }
        tr:nth-child(even) td { background: #fafafa; }
        .empty { text-align: center; color: #999; padding: 20px; }
        .footer { margin-top: 20px; font-size: 9px; color: #aaa; text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">
        Generated: {{ now()->format('m/d/Y H:i') }} &nbsp;|&nbsp;
        Period: {{ $fromCarbon->format('m/d/Y') }} – {{ $toCarbon->format('m/d/Y') }}
    </div>

    @if(count($rows))
    <table>
        <thead>
            <tr>
                @foreach($columns as $col)
                <th>{{ $col }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
            <tr>
                @foreach((array)$row as $cell)
                <td>{{ $cell }}</td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">Bougainvilla Hotel &nbsp;|&nbsp; Total records: {{ count($rows) }}</div>
    @else
    <div class="empty">No records found for the selected period.</div>
    @endif
</body>
</html>
