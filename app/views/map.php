<?php require __DIR__ . '/layout/header.php'; ?>

<!--<pre><?php //print_r($viewData['locations']); ?></pre>-->

<div id="map-controls">

    <form method="GET" action="/search" class="d-flex mb-2">
        <input type="text" name="name" class="form-control me-2" placeholder="Search location..." value="<?= htmlspecialchars( $_GET['name'] ?? '' ) ?>">
        <button class="btn btn-primary">Search</button>
    </form>

    <form method="GET" action="/near" class="row g-2">
        <div class="col-3">
            <input type="number" step="any" name="lon" id="lon" class="form-control" placeholder="Lon" value="<?= htmlspecialchars( $_GET['lon'] ?? '' ) ?>">
        </div>

        <div class="col-3">
            <input type="number" step="any" name="lat" id="lat" class="form-control" placeholder="Lat" value="<?= htmlspecialchars( $_GET['lat'] ?? '' ) ?>">
        </div>

        <div class="col-3">
            <input type="number" step="any" name="km" class="form-control" placeholder="km" value="<?= htmlspecialchars( $_GET['km'] ?? '' ) ?>">
        </div>

        <div class="col-3">
            <button class="btn btn-success w-100">Find Nearby</button>
        </div>
    </form>

</div>

<div id="map"></div>

<script>
    const locations = <?php echo json_encode($viewData['locations']); ?>;

    function initMap() {
        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 49.76, lng: 6.64 },
            mapTypeId: 'hybrid',
            tilt: 45,
            zoom: 10,
        });

        locations.forEach(loc => {
            if (!loc.geoPoint) return;

            const coords = loc.geoPoint.coordinates;

            const marker = new google.maps.Marker({
                position: {
                    lat: coords[1],
                    lng: coords[0]
                },
                map: map,
                title: loc.name
            });

            const info = new google.maps.InfoWindow({
                content: `<strong>${loc.name}</strong><br>${loc.address ?? ''}`
            });

            marker.addListener("click", () => {
                info.open(map, marker);
            });
        });

        map.addListener("click", (e) => {
            document.getElementById("lat").value = e.latLng.lat().toFixed(6);
            document.getElementById("lon").value = e.latLng.lng().toFixed(6);
        });
    }
</script>

<script async
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $viewData['maps_api_key']; ?>&callback=initMap&loading=async">
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>