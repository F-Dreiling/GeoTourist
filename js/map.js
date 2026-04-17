    const locations = window.APP_DATA.locations;
    const searchParams = window.APP_DATA.search;

    const btnSearch = document.getElementById("btn-search");
    const btnDate = document.getElementById("btn-date");
    const btnNew = document.getElementById("btn-new");
    const searchPanel = document.getElementById("search-panel");
    const datePanel = document.getElementById("date-panel");
    const newPanel = document.getElementById("new-panel");

    btnSearch.addEventListener( "click", () => {
        searchPanel.style.top = btnSearch.offsetTop + "px";
        searchPanel.classList.toggle("active");
        newPanel.classList.remove("active");
        datePanel.classList.remove("active");
    });

    btnDate.addEventListener( "click", () => {
        datePanel.style.top = btnDate.offsetTop + "px";
        datePanel.classList.toggle("active");
        searchPanel.classList.remove("active");
        newPanel.classList.remove("active");
    });

    btnNew.addEventListener( "click", () => {
        newPanel.style.top = btnNew.offsetTop + "px";
        newPanel.classList.toggle("active");
        searchPanel.classList.remove("active");
        datePanel.classList.remove("active");
    });

    window.initMap = function () {
        let openInfoWindow = null;

        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 49.76, lng: 6.64 },
            mapTypeId: 'hybrid',
            mapId: '458f84a5d3127d64d19da50c',
            tilt: 45,
            zoom: 10,
            clickableIcons: false
        });

        // Markers for Locations
        locations.forEach( loc => {
            if ( !loc.geoPoint ) return;

            const coords = loc.geoPoint.coordinates;

            const markerContent = document.createElement("div");
            markerContent.className = "marker-location";

            const marker = new google.maps.marker.AdvancedMarkerElement({
                map: map,
                position: {
                    lat: coords[1],
                    lng: coords[0]
                },
                content: markerContent
            });

            const infoContent = document.createElement("div");
            infoContent.className = "info-window";
            infoContent.innerHTML = `
                <div class="info-title">${loc.name}</div>
                ${loc.address ? `<div>${loc.address}</div>` : ''}
                ${loc.dateVisited ? `<div>${loc.dateVisited}</div>` : ''}
            `;

            const info = new google.maps.InfoWindow({
                shouldFocus: false,
                content: infoContent
            });

            google.maps.event.addListener(marker, "click", () => {
                if (openInfoWindow) {
                    openInfoWindow.close();
                }

                info.open(map, marker);
                openInfoWindow = info;
            });
        });

        // Fit all Markers
        if ( locations.length > 0 ) {
            const bounds = new google.maps.LatLngBounds();
            let count = 0;

            locations.forEach(loc => {
                if ( !loc.geoPoint ) return;

                const coords = loc.geoPoint.coordinates;

                bounds.extend({
                    lat: coords[1],
                    lng: coords[0]
                });

                count++;
            });

            if ( count === 1 ) {
                map.setCenter(bounds.getCenter());
                map.setZoom(Math.min(map.getZoom(), 14));
            } 
            else if ( count > 1 ) {
                map.fitBounds(bounds);
            }
        }

        // Map Click
        let clickMarker = null;

        map.addListener( "click", (e) => {
            if (openInfoWindow) { 
                openInfoWindow.close(); 
                openInfoWindow = null; 
            }

            const lat = e.latLng.lat();
            const lon = e.latLng.lng();

            document.getElementById("lat").value = lat.toFixed(6);
            document.getElementById("lon").value = lon.toFixed(6);
            document.getElementById("new-lat").value = lat.toFixed(6);
            document.getElementById("new-lon").value = lon.toFixed(6);

            searchPanel.classList.remove("active");
            datePanel.classList.remove("active");
            newPanel.classList.remove("active");

            const position = { lat: lat, lng: lon };

            if ( clickMarker ) clickMarker.setMap(null);

            const markerContent = document.createElement("div");
            markerContent.className = "marker-click";

            clickMarker = new google.maps.marker.AdvancedMarkerElement({
                map: map,
                position: position,
                content: markerContent
            });
        });

        // After Search
        if ( searchParams.lat !== null && searchParams.lon !== null ) {
            const center = {
                lat: searchParams.lat,
                lng: searchParams.lon
            };

            const radius = ( searchParams.km || 5 ) * 1000;

            map.setCenter(center);

            const circle = new google.maps.Circle({
                strokeColor: "#0d6efd",
                strokeOpacity: 0.6,
                strokeWeight: 2,
                fillColor: "#0d6efd",
                fillOpacity: 0.10,
                map: map,
                center: center,
                radius: radius,
                clickable: false
            });

            map.fitBounds(circle.getBounds());

            const markerContent = document.createElement("div");
            markerContent.className = "marker-search";

            new google.maps.marker.AdvancedMarkerElement({
                map: map,
                position: center,
                content: markerContent
            });
        }
    }