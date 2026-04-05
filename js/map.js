    const locations = window.APP_DATA.locations;
    const searchParams = window.APP_DATA.search;

    const btnSearch = document.getElementById("btn-search");
    const btnNew = document.getElementById("btn-new");

    const searchPanel = document.getElementById("search-panel");
    const newPanel = document.getElementById("new-panel");

    btnSearch.addEventListener("click", () => {
        searchPanel.style.top = btnSearch.offsetTop + "px";
        searchPanel.classList.toggle("active");
        newPanel.classList.remove("active");
    });

    btnNew.addEventListener("click", () => {
        newPanel.style.top = btnNew.offsetTop + "px";
        newPanel.classList.toggle("active");
        searchPanel.classList.remove("active");
    });

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

        if (locations.length > 0) {
            const bounds = new google.maps.LatLngBounds();
            let count = 0;

            locations.forEach(loc => {
                if (!loc.geoPoint) return;

                const coords = loc.geoPoint.coordinates;

                bounds.extend({
                    lat: coords[1],
                    lng: coords[0]
                });

                count++;
            });

            if (count === 1) {
                map.setCenter(bounds.getCenter());
                map.setZoom(Math.min(map.getZoom(), 14));
            } 
            else if (count > 1) {
                map.fitBounds(bounds);
            }
        }

        let clickMarker = null;
        let clickCircle = null;

        map.addListener("click", (e) => {
            const lat = e.latLng.lat();
            const lon = e.latLng.lng();

            // Fill form fields
            document.getElementById("lat").value = lat.toFixed(6);
            document.getElementById("lon").value = lon.toFixed(6);

            // Fill NEW form fields
            document.getElementById("new-lat").value = lat.toFixed(6);
            document.getElementById("new-lon").value = lon.toFixed(6);

            // Close panels
            searchPanel.classList.remove("active");
            newPanel.classList.remove("active");

            const position = { lat: lat, lng: lon };

            // Remove old marker/circle if exists
            if (clickMarker) clickMarker.setMap(null);
            if (clickCircle) clickCircle.setMap(null);

            // 🔴 Small red center marker
            clickMarker = new google.maps.Marker({
                position: position,
                map: map,
                title: "Selected location",
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 6,
                    fillColor: "#dc3545", // Bootstrap red
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: "#fff"
                }
            });

            // 🔴 Small subtle radius (optional, looks nice)
            clickCircle = new google.maps.Circle({
                strokeColor: "#dc3545",
                strokeOpacity: 0.6,
                strokeWeight: 2,
                fillColor: "#dc3545",
                fillOpacity: 0.15,
                map: map,
                center: position,
                radius: 100 // 100 meters, tweak as you like
            });
        });

        if (searchParams.lat !== null && searchParams.lon !== null) {
            const center = {
                lat: searchParams.lat,
                lng: searchParams.lon
            };

            const radius = (searchParams.km || 5) * 1000; // meters

            // Optional: center map on search
            map.setCenter(center);

            // Draw circle
            const circle = new google.maps.Circle({
                strokeColor: "#0d6efd",
                strokeOpacity: 0.6,
                strokeWeight: 2,
                fillColor: "#0d6efd",
                fillOpacity: 0.15,
                map: map,
                center: center,
                radius: radius,
                clickable: false
            });

            map.fitBounds(circle.getBounds());

            // Optional: marker in center
            new google.maps.Marker({
                position: center,
                map: map,
                title: "Search Center",
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 6,
                    fillColor: "#0d6efd",
                    fillOpacity: 1,
                    strokeWeight: 2,
                    strokeColor: "#fff"
                }
            });
        }
    }