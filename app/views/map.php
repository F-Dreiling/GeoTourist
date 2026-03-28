<?php require __DIR__ . '/layout/header.php'; ?>

<!--<pre><?php //print_r($viewData['locations']); ?></pre>-->

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
    }
</script>

<script async
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $viewData['maps_api_key']; ?>&callback=initMap&loading=async">
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>