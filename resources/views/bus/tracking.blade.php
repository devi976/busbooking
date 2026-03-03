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
                    <span class="badge bg-light text-primary p-2" id="last-update-badge">
                        Last Update: <span id="last-update-time">{{ $bus->last_location_update ? $bus->last_location_update->diffForHumans() : 'Never' }}</span>
                    </span>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div id="map" style="height: 500px; width: 100%;"></div>
        </div>

        <div class="card-footer bg-light p-3 text-center">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Location updates automatically every 10 seconds.
            </small>
        </div>
    </div>
</div>

{{-- Leaflet CSS/JS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Initialize Map
    let defaultLat = {{ $bus->current_lat ?? '20.5937' }}; // Default to India center if null
    let defaultLng = {{ $bus->current_lng ?? '78.9629' }};
    
    const map = L.map('map').setView([defaultLat, defaultLng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Custom Bus Icon
    const busIcon = L.icon({
        iconUrl: 'https://cdn-icons-png.flaticon.com/512/3448/3448339.png', // Or any bus marker icon
        iconSize: [40, 40],
        iconAnchor: [20, 40],
        popupAnchor: [0, -40]
    });

    let marker = L.marker([defaultLat, defaultLng], {icon: busIcon}).addTo(map)
        .bindPopup('<b>{{ $bus->bus_name }}</b><br>Live Location').openPopup();

    // Polling function
    async function updateLocation() {
        try {
            const response = await fetch('/bus/{{ $bus->id }}/get-location');
            const data = await response.json();

            if (data.lat && data.lng) {
                const newPos = [data.lat, data.lng];
                marker.setLatLng(newPos);
                map.panTo(newPos);
                
                document.getElementById('last-update-time').innerText = data.last_update;
                document.getElementById('last-update-badge').classList.replace('bg-warning-soft', 'bg-light');
            }
        } catch (error) {
            console.error('Failed to update location:', error);
            document.getElementById('last-update-badge').classList.replace('bg-light', 'bg-warning-soft');
        }
    }

    // Update every 10 seconds
    setInterval(updateLocation, 10000);
</script>

<style>
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.2); }
</style>
@endsection
