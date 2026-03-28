@extends('layout.app')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/search">Search</a></li>
            <li class="breadcrumb-item active" aria-current="page">Live Tracking</li>
        </ol>
    </nav>

    <div class="card shadow-lg border-0 overflow-hidden" style="border-radius: 1.5rem;">
        <div class="card-header bg-primary text-white p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-0"><i class="bi bi-geo-fill me-2"></i>Live Tracking: {{ $bus->bus_name }}</h3>
                    <p class="mb-0 opacity-75">{{ $bus->from }} → {{ $bus->to }}</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-primary p-2">
                        Status: <span id="bus-status">Calculating position...</span>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card-body p-md-5 bg-light">
            @if($bus->stopTimings->count() > 1)
                <!-- Central layout wrapper -->
                <div class="timeline-wrapper bg-white p-5 rounded shadow-sm">
                    <h4 class="text-center mb-5 text-dark fw-bold">Live Route Progress</h4>
                    
                    <div class="timeline-container" id="timeline-container">
                        <!-- Gray line for total route -->
                        <div class="timeline-line" id="timeline-line"></div>
                        <!-- Red line for progress -->
                        <div class="timeline-line-active" id="timeline-line-active"></div>
                        
                        <!-- Bus Icon -->
                        <div class="timeline-bus" id="timeline-bus" style="display: none;">
                            <i class="bi bi-bus-front-fill fs-5"></i>
                        </div>

                        <!-- Stops -->
                        @foreach($bus->stopTimings->sortBy('arrival_time')->values() as $index => $stop)
                            <div class="timeline-stop" id="stop-{{ $index }}">
                                <div class="stop-pin" id="pin-{{ $index }}">
                                    <!-- Using an icon for the pin to match reference slightly -->
                                    <i class="bi bi-geo-alt-fill text-muted" id="pin-icon-{{ $index }}"></i>
                                </div>
                                <div class="stop-content border px-4 py-3 rounded bg-light shadow-xs ms-3">
                                    <h5 class="mb-1 fw-bold text-dark">{{ $stop->stop_name }}</h5>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-clock me-1"></i> 
                                        Scheduled Arrival: <strong>{{ \Carbon\Carbon::parse($stop->arrival_time)->format('h:i A') }}</strong>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-exclamation-circle text-muted fs-1 block mb-3"></i>
                    <h4 class="text-muted">Not enough stops defined for tracking.</h4>
                </div>
            @endif
        </div>
        <div class="card-footer bg-light p-3 text-center border-top-0">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Bus position updates automatically based on the schedule.
            </small>
        </div>
    </div>
</div>

<style>
    .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.05); }

    .timeline-wrapper {
        max-width: 800px;
        margin: 0 auto;
    }

    .timeline-container {
        position: relative;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    /* Vertical lines */
    .timeline-line {
        position: absolute;
        left: 48px; /* Offset to center under pins: pin is at 32, width is 32. Center is 48. */
        width: 4px;
        background: #dee2e6;
        z-index: 1;
        border-radius: 4px;
    }
    .timeline-line-active {
        position: absolute;
        left: 48px;
        width: 4px;
        background: #dc3545; /* Red progress line */
        z-index: 2;
        transition: height 1s linear;
        border-radius: 4px;
    }

    /* Bus Icon */
    .timeline-bus {
        position: absolute;
        left: 28px; /* line at 48. Bus width 40. 48 - 20 = 28 */
        width: 40px;
        height: 40px;
        background: #fff;
        border: 2px solid #dc3545;
        border-radius: 50%;
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        display: flex; 
        align-items: center; 
        justify-content: center;
        color: #dc3545;
        z-index: 4;
        transition: top 1s linear;
    }

    /* Stops layout */
    .timeline-stop {
        position: relative;
        padding-left: 100px;
        margin-bottom: 50px;
        min-height: 50px;
        display: flex;
        align-items: center;
        z-index: 3;
    }
    .timeline-stop:last-child {
        margin-bottom: 0;
    }

    /* Target Pins */
    .stop-pin {
        position: absolute;
        left: 32px; /* Center of pin is 32 + 16 = 48 */
        width: 32px;
        height: 32px;
        background: #fff;
        border: 3px solid #6c757d;
        border-radius: 50%;
        z-index: 3;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Icon inside pin */
    .stop-pin i {
        font-size: 14px;
    }

    /* States */
    .stop-pin.passed {
        border-color: #dc3545;
        background: #ffeeba; /* slight yellow or just white */
    }
    .stop-pin.passed i {
        color: #dc3545 !important;
    }
    
    .stop-pin.current {
        border-color: #dc3545;
        background: #dc3545;
        box-shadow: 0 0 0 5px rgba(220, 53, 69, 0.2);
    }
    .stop-pin.current i {
        color: #fff !important;
    }

    .stop-content {
        width: 100%;
        transition: transform 0.2s;
    }
    .stop-content:hover {
        transform: translateX(5px);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stops = @json($bus->stopTimings->sortBy('arrival_time')->values());
        if(stops.length < 2) return;

        const busIcon = document.getElementById('timeline-bus');
        const activeLine = document.getElementById('timeline-line-active');
        const statusText = document.getElementById('bus-status');

        function timeToMinutes(timeStr) {
            let parts = timeStr.split(':');
            return parseInt(parts[0]) * 60 + parseInt(parts[1]);
        }

        let stopData = [];
        let containerRect = document.getElementById('timeline-container').getBoundingClientRect();
        
        // Setup base positions
        for(let i=0; i<stops.length; i++) {
            let pin = document.getElementById('pin-' + i);
            let pinRect = pin.getBoundingClientRect();
            
            // Y center of pin relative to container
            let yPos = (pinRect.top - containerRect.top) + (pinRect.height / 2);

            stopData.push({
                index: i,
                name: stops[i].stop_name,
                minutes: timeToMinutes(stops[i].arrival_time),
                y: yPos,
                pinEl: pin
            });
        }

        // Draw the background inactive line
        let bgLine = document.getElementById('timeline-line');
        bgLine.style.top = stopData[0].y + 'px';
        bgLine.style.height = (stopData[stopData.length - 1].y - stopData[0].y) + 'px';
        activeLine.style.top = stopData[0].y + 'px';

        function updateTracking() {
            let now = new Date();
            let currentMinutes = now.getHours() * 60 + now.getMinutes() + (now.getSeconds() / 60);
            
            let currentY = stopData[0].y;
            let statusObj = "";

            if (currentMinutes < stopData[0].minutes) {
                // Bus hasn't started
                currentY = stopData[0].y;
                statusObj = `Waiting to depart from ${stopData[0].name}`;
            } else if (currentMinutes >= stopData[stopData.length - 1].minutes) {
                // Bus reached destination
                currentY = stopData[stopData.length - 1].y;
                statusObj = `Arrived at ${stopData[stopData.length - 1].name}`;
                stopData.forEach(s => {
                    s.pinEl.className = 'stop-pin passed';
                });
            } else {
                // Bus is currently between two stops
                for (let i = 0; i < stopData.length - 1; i++) {
                    let start = stopData[i];
                    let end = stopData[i+1];
                    
                    // Mark pins before current segment as passed
                    for(let j=0; j<=i; j++) {
                        stopData[j].pinEl.className = 'stop-pin passed';
                    }
                    // Reset future pins
                    for(let j=i+1; j<stopData.length; j++) {
                        stopData[j].pinEl.className = 'stop-pin';
                    }

                    if (currentMinutes >= start.minutes && currentMinutes < end.minutes) {
                        end.pinEl.className = 'stop-pin current'; // Pulse the next stop
                        
                        let duration = end.minutes - start.minutes;
                        let passed = currentMinutes - start.minutes;
                        let fraction = passed / duration;
                        
                        currentY = start.y + (end.y - start.y) * fraction;
                        statusObj = `En route to ${end.name}`;
                        break;
                    }
                }
            }

            // Sync visual positions
            busIcon.style.display = 'flex';
            busIcon.style.top = (currentY - 20) + 'px'; // Center bus icon height 40
            activeLine.style.height = (currentY - stopData[0].y) + 'px';
            statusText.innerText = statusObj;
        }

        updateTracking();
        // Recalculate container and positions on window resize
        window.addEventListener('resize', function() {
            containerRect = document.getElementById('timeline-container').getBoundingClientRect();
            stopData.forEach(s => {
                let pinRect = s.pinEl.getBoundingClientRect();
                s.y = (pinRect.top - containerRect.top) + (pinRect.height / 2);
            });
            bgLine.style.top = stopData[0].y + 'px';
            bgLine.style.height = (stopData[stopData.length - 1].y - stopData[0].y) + 'px';
            activeLine.style.top = stopData[0].y + 'px';
            updateTracking();
        });

        setInterval(updateTracking, 5000);
    });
</script>
@endsection
