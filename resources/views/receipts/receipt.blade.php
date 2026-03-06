<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Courier New', monospace; font-size: 12px; color: #000; margin: 0; padding: 20px; }
        .receipt { max-width: 300px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 12px; margin-bottom: 12px; }
        .title { font-size: 16px; font-weight: bold; letter-spacing: 1px; }
        .subtitle { font-size: 10px; color: #555; margin-top: 2px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 3px; }
        .section { margin-bottom: 12px; }
        .divider { border-top: 1px dashed #000; margin: 8px 0; }
        .total-row { font-weight: bold; font-size: 13px; border-top: 2px solid #000; padding-top: 6px; margin-top: 6px; display: flex; justify-content: space-between; }
        .footer { text-align: center; border-top: 2px dashed #000; padding-top: 12px; margin-top: 12px; font-size: 11px; color: #555; }
        .thank-you { font-weight: bold; font-size: 13px; text-transform: uppercase; margin-bottom: 4px; }
    </style>
</head>
<body>
<div class="receipt">
    <div class="header">
        <div class="title">Bougainvilla Hotel</div>
        <div class="subtitle">Accommodation Services</div>
        <div class="subtitle">Tel: (02) 123-4567</div>
    </div>

    <div class="section">
        <div class="row"><span>Receipt No:</span><span>#{{ $receipt->id }}</span></div>
        <div class="row"><span>Date &amp; Time:</span><span>{{ $receipt->created_at->format('m/d/Y H:i') }}</span></div>
        <div class="row"><span>Cashier:</span><span>{{ $cashier }}</span></div>
        <div class="row"><span>Type:</span><span>{{ $receipt->status_type }}</span></div>
    </div>

    <div class="divider"></div>

    <div class="section">
        <div class="row"><span>Room:</span><span>{{ $room }}</span></div>
        <div class="row"><span>Level:</span><span>{{ $level }}</span></div>
        <div class="row"><span>Accommodation:</span><span>{{ $accommodation }}</span></div>
        <div class="row"><span>Rate:</span><span>{{ $rate }}</span></div>
        <div class="row"><span>Duration:</span><span>{{ $duration }}</span></div>
        <div class="row"><span>Check-in:</span><span>{{ $checkIn }}</span></div>
        <div class="row"><span>Check-out:</span><span>{{ $checkOut }}</span></div>
        <div class="row"><span>Guests:</span><span>{{ $guestCount }} person(s)</span></div>
    </div>

    @if($guests->count())
    <div class="section">
        <div style="font-weight:bold; margin-bottom:4px;">Guests:</div>
        @foreach($guests as $guest)
        <div class="row"><span>{{ $loop->iteration }}. {{ $guest->firstName }} {{ $guest->lastName }}</span></div>
        @endforeach
    </div>
    @endif

    <div class="divider"></div>

    <div class="section">
        <div class="row"><span>Subtotal:</span><span>&#8369;{{ number_format($subtotal, 2) }}</span></div>
        <div class="row"><span>Tax (12%):</span><span>&#8369;{{ number_format($tax, 2) }}</span></div>
        <div class="total-row"><span>TOTAL:</span><span>&#8369;{{ number_format($total, 2) }}</span></div>
    </div>

    <div class="footer">
        <div class="thank-you">Thank You!</div>
        <div>Please keep this receipt for your records.</div>
        <div>Visit us again soon!</div>
    </div>
</div>
</body>
</html>
