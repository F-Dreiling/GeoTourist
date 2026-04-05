<?php require __DIR__ . '/layout/header.php'; ?>

<!--<pre><?php //print_r($viewData['locations']); ?></pre>-->

<div id="map-controls">

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

<div id="map-ui">

    <div id="map-buttons">
        <button type="button" id="btn-search" class="btn btn-primary mb-2 w-100">
            <i class="fa fa-search"></i>
        </button>
        <button type="button" id="btn-new" class="btn btn-success w-100">
            <i class="fa fa-plus"></i>
        </button>
    </div>

    <div id="search-panel" class="map-panel">
        <form method="GET" action="/search">
            <input type="text" name="term" class="form-control mb-2" placeholder="Search location..." value="<?= htmlspecialchars( $_GET['term'] ?? '' ) ?>">
            <button class="btn btn-primary w-100">Search</button>
        </form>
    </div>

    <div id="new-panel" class="map-panel">
        <form method="POST" action="/create">
            <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
            <input type="text" name="address" class="form-control mb-2" placeholder="Address">

            <div class="row g-2 mb-2">
                <div class="col">
                    <input type="number" step="any" id="new-lat" name="lat" class="form-control" placeholder="Lat" required>
                </div>
                <div class="col">
                    <input type="number" step="any" id="new-lon" name="lon" class="form-control" placeholder="Lon" required>
                </div>
            </div>

            <input type="date" name="date" class="form-control mb-2" placeholder="Date visited">

            <button class="btn btn-success w-100">Save Location</button>
        </form>
    </div>

</div>

<div id="map"></div>

<script>
    window.APP_DATA = {
        locations: <?php echo json_encode($viewData['locations']); ?>,
        search: {
            lat: <?= isset($_GET['lat']) ? (float)$_GET['lat'] : 'null' ?>,
            lon: <?= isset($_GET['lon']) ? (float)$_GET['lon'] : 'null' ?>,
            km: <?= isset($_GET['km']) ? (float)$_GET['km'] : 'null' ?>
        }
    };
</script>

<script src="/js/map.js"></script>

<script async
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo $viewData['maps_api_key']; ?>&callback=initMap&loading=async">
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>