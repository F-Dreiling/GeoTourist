    const locations = window.APP_DATA.locations;
    const searchParams = window.APP_DATA.search;
    const uploadStatus = window.APP_DATA.uploadStatus;
    const uploadMessage = window.APP_DATA.uploadMessage;

    const btnSearch = document.getElementById("btn-search");
    const btnDate = document.getElementById("btn-date");
    const btnNew = document.getElementById("btn-new");
    const btnHome = document.getElementById("btn-home");
    const btnLogout = document.getElementById("btn-logout");
    const searchPanel = document.getElementById("search-panel");
    const datePanel = document.getElementById("date-panel");
    const newPanel = document.getElementById("new-panel");
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const fileInput = document.querySelector('input[name="image"]');
    const preview = document.getElementById("image-preview");

    const MAX_SIZE = 2 * 1024 * 1024;
    const ALLOWED_TYPES = ["image/jpeg", "image/png", "image/gif", "image/webp"];

    const showToast = ( message, type = "info" ) => {
        const colors = {
            success: "#28a745",
            error: "#dc3545",
            info: "#0d6efd"
        };

        Toastify({
            text: message,
            duration: 3500,
            gravity: "top",
            position: "center",
            offset: { y: 100 },
            style: {
                background: colors[type]
            }
        }).showToast();
    };

    if ( uploadStatus === "success" ) {
        showToast( uploadMessage, "success" );
    }

    if ( uploadStatus === "error" ) {
        showToast( uploadMessage, "error" );
    }

    btnSearch.addEventListener( "click", () => {
        searchPanel.style.top = btnHome.offsetTop + "px";
        searchPanel.classList.toggle("active");
        newPanel.classList.remove("active");
        datePanel.classList.remove("active");
    });

    btnDate.addEventListener( "click", () => {
        datePanel.style.top = btnHome.offsetTop + "px";
        datePanel.classList.toggle("active");
        searchPanel.classList.remove("active");
        newPanel.classList.remove("active");
    });

    btnNew.addEventListener( "click", () => {
        newPanel.style.top = btnHome.offsetTop + "px";
        newPanel.classList.toggle("active");
        searchPanel.classList.remove("active");
        datePanel.classList.remove("active");
    });

    fileInput.addEventListener( "change", () => {
        const file = fileInput.files[0];

        if ( !file ) {
            preview.style.display = "none";
            return;
        }

        if ( file.size > MAX_SIZE ) {
            showToast( "Image too large (max 2MB)", "error" );
            fileInput.value = "";
            preview.style.display = "none";
            return;
        }

        if ( !ALLOWED_TYPES.includes( file.type ) ) {
            showToast( "Unsupported format (jpg, png, gif, webp)", "error" );
            fileInput.value = "";
            preview.style.display = "none";
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = "block";
        };
        reader.readAsDataURL(file);
    });

    window.initMap = function () {
        let openInfoWindow = null;

        const map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 49.76, lng: 6.64 },
            mapTypeId: 'hybrid',
            mapId: '458f84a5d3127d64d19da50c',
            tilt: 45,
            zoom: 5,
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
                <div class="info-header">
                    <div class="info-title">${loc.name}</div>
                    <button class="info-delete" data-id="${loc.id}" title="Delete">⛔</button>
                </div>
                ${loc.address ? `<div>${loc.address}</div>` : ''}
                ${loc.dateVisited ? `<div>${loc.dateVisited}</div>` : ''}
                ${loc.imageUrl ? `<img class="info-image" src="/thumbs?file=${encodeURIComponent(loc.imageUrl)}" data-full="${encodeURIComponent(loc.imageUrl)}" />` : ''}
            `;

            infoContent.addEventListener("click", (e) => {
                const img = e.target.closest(".info-image");
                if (!img) return;

                const fullUrl = "/images?file=" + img.dataset.full;

                window.open( fullUrl, "_blank" );
            });

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

                // Delete Button - Wait for DOM render
                setTimeout(() => {
                    const btn = document.querySelector(".info-delete[data-id='" + loc.id + "']");
                    if (!btn) return;

                    btn.addEventListener("click", async (e) => {
                        e.stopPropagation();

                        if (!confirm("Delete this location?")) return;

                        try {
                            const res = await fetch(`/delete/${loc.id}`, {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json"
                                },
                                body: JSON.stringify({
                                    csrf_token: csrfToken
                                })
                            });

                            if (res.ok) {
                                marker.map = null;

                                if (openInfoWindow) {
                                    openInfoWindow.close();
                                    openInfoWindow = null;
                                }
                            }
                            else {
                                alert("Delete failed");
                            }
                        }
                        catch (err) {
                            console.error(err);
                            alert("Error deleting location");
                        }
                    });
                }, 0);
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
                map.setZoom(8);
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