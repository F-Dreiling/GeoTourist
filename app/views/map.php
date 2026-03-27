<!DOCTYPE html>
<html>
<head>
    <title>Geo Map</title>
    <style>
        #map {
            height: 100vh;
            width: 100%;
        }
    </style>
</head>
<body>

<div id="map"></div>

<script>
    const locations = <?php echo json_encode($viewData['locations']); ?>;

    function initMap() {
        const center = { lat: 48.8584, lng: 2.2945 };

        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 4,
            center: center,
        });

        locations.forEach(loc => {
            if (!loc.geo) return;

            const coords = loc.geo.coordinates;

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

<!-- ⚠️ Replace YOUR_API_KEY -->
<script async
    src="https://maps.googleapis.com/maps/api/js?key=API_KEY&callback=initMap">
</script>

</body>
</html>