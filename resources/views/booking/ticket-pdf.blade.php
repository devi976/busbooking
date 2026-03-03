<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; }
        .ticket { border: 2px solid #333; padding: 20px; width: 600px; margin: auto; }
        .header { text-align: center; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; color: #0d6efd; text-transform: uppercase; }
        .section { margin-bottom: 15px; }
        .label { font-weight: bold; color: #555; text-transform: uppercase; font-size: 12px; }
        .value { font-size: 16px; margin-top: 2px; }
        .row { display: table; width: 100%; }
        .col { display: table-cell; width: 50%; }
        .footer { border-top: 2px solid #eee; padding-top: 10px; margin-top: 20px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="ticket">

        <div class="header">
            <h1>{{ $booking->bus->bus_name }}</h1>
            <p>Booking Confirmed - Ticket #{{ $booking->id }}</p>
        </div>

        <div class="row section">
            <div class="col">
                <div class="label">Passenger Name</div>
                <div class="value">{{ $booking->name }}</div>
            </div>
            <div class="col">
                <div class="label">Travel Date</div>
                <div class="value">{{ $booking->travel_date }}</div>
            </div>
        </div>

        <div class="row section">
            <div class="col">
                <div class="label">From (Entry Point)</div>
                <div class="value">{{ $booking->entry_point }}</div>
            </div>
            <div class="col">
                <div class="label">To (Exit Point)</div>
                <div class="value">{{ $booking->exit_point }}</div>
            </div>
        </div>

        <div class="section">
            <div class="label">Selected Seats</div>
            <div class="value" style="color: #0d6efd; font-weight: bold;">
                @foreach($booking->seats as $bookingSeat)
    {{ $bookingSeat->seat->seat_number }}{{ !$loop->last ? ',' : '' }}
@endforeach
            </div>
        </div>

        <div class="row section">
            <div class="col">
                <div class="label">Amount Paid</div>
                <div class="value" style="font-weight: bold;">
                    Rs. {{ number_format($booking->total_amount, 2) }}
                </div>
            </div>
            <div class="col">
                <div class="label">Payment Method</div>
                <div class="value">{{ $booking->payment_method }}</div>
            </div>
        </div>

        <div class="section">
            <div class="label">Bus Contact</div>
            <div class="value">{{ $booking->bus->contact_number }}</div>
        </div>

        <div class="footer">
            <div class="row">
                <div class="col">
                    For support, contact: <strong>{{ $booking->bus->contact_number }}</strong>
                </div>
                <div class="col" style="text-align: right;">
                    Generated on: {{ now()->format('d M Y, h:i A') }}
                </div>
            </div>
        </div>

    </div>
</body>
</html>