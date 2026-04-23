<?php require __DIR__ . '/layout/header.php'; ?>

<?php if ( !isset($_SESSION['user_id']) ): ?>
    <div id="login-overlay">
        <form method="POST" action="/login" class="login-box">
            <h4>Login</h4>

            <input type="text" name="username" placeholder="Username" required class="form-control mb-2">
            <input type="password" name="password" placeholder="Password" required class="form-control mb-2">

            <button class="btn btn-primary w-100">Login</button>

            <?php if ( !empty($_SESSION['login_error']) ): ?>
                <div class="text-danger mt-2">Invalid login</div>
                <?php unset( $_SESSION['login_error'] ); ?>
            <?php endif; ?>

            <?= csrf_field() ?>
        </form>
    </div>
<?php endif; ?>

<div id="map-controls">

    <form method="GET" action="/near" class="row g-2">
        <div class="col-4">
            <input type="number" step="any" name="lon" id="lon" class="form-control" placeholder="Lon" value="<?= htmlspecialchars( $_GET['lon'] ?? '' ) ?>">
        </div>

        <div class="col-4">
            <input type="number" step="any" name="lat" id="lat" class="form-control" placeholder="Lat" value="<?= htmlspecialchars( $_GET['lat'] ?? '' ) ?>">
        </div>

        <div class="col-2">
            <input type="number" step="any" name="km" class="form-control" placeholder="km" value="<?= htmlspecialchars( $_GET['km'] ?? '' ) ?>">
        </div>

        <div class="col-2">
            <button class="btn btn-warning w-100">Find</button>
        </div>
    </form>

</div>

<div id="map-ui">

    <div id="map-buttons">
        <a href="/" class="btn btn-dark mb-2 w-100 text-decoration-none">
            <i class="fa fa-home"></i>
        </a>
        <button type="button" id="btn-search" class="btn btn-primary mb-2 w-100">
            <i class="fa fa-search"></i>
        </button>
        <button type="button" id="btn-date" class="btn btn-success mb-2 w-100">
            <i class="fa fa-calendar"></i>
        </button>
        <button type="button" id="btn-new" class="btn btn-danger w-100">
            <i class="fa fa-plus"></i>
        </button>
    </div>

    <div id="search-panel" class="map-panel">
        <form method="GET" action="/search">
            <input type="text" name="term" class="form-control mb-2" placeholder="Search location..." value="<?= htmlspecialchars( $_GET['term'] ?? '' ) ?>">
            <button class="btn btn-primary w-100">Search</button>
        </form>
    </div>

    <div id="date-panel" class="map-panel">
        <form method="GET" action="/date">
            <select name="year" id="year-select" class="form-control mb-2">
                <option value="">All years</option>
                <?php 
                    $currentYear = (int)date("Y");
                    for($y = $currentYear; $y >= 2000; $y--) {
                        $selected = ( isset( $_GET['year'] ) && (int)$_GET['year'] === $y ) ? 'selected' : '';
                        echo "<option value='$y' $selected>$y</option>";
                    }
                ?>
            </select>
            <button class="btn btn-success w-100">Filter</button>
        </form>
    </div>

    <div id="new-panel" class="map-panel">
        <form method="POST" action="/create">
            <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
            <input type="text" name="address" class="form-control mb-2" placeholder="Address">

            <div class="row g-2 mb-2">
                <div class="col">
                    <input type="number" step="any" id="new-lon" name="lon" class="form-control" placeholder="Lon" required>
                </div>
                <div class="col">
                    <input type="number" step="any" id="new-lat" name="lat" class="form-control" placeholder="Lat" required>
                </div>
            </div>

            <input type="date" name="date" class="form-control mb-2">

            <button class="btn btn-danger w-100">Save Location</button>

            <?= csrf_field() ?>
        </form>
    </div>

</div>

<div id="map"></div>

<script>
    window.APP_DATA = {
        locations: <?php echo json_encode( $viewData['locations'] ); ?>,
        search: {
            lat: <?= isset( $_GET['lat'] ) ? (float)$_GET['lat'] : 'null' ?>,
            lon: <?= isset( $_GET['lon'] ) ? (float)$_GET['lon'] : 'null' ?>,
            km: <?= isset( $_GET['km'] ) ? (float)$_GET['km'] : 'null' ?>,
            year: <?= isset( $_GET['year'] ) ? (int)$_GET['year'] : 'null' ?>
        }
    };
</script>

<script src="/js/map.js"></script>

<script async
    src="https://maps.googleapis.com/maps/api/js?key=<?php echo MAPS_KEY; ?>&callback=initMap&libraries=marker&loading=async">
</script>

<?php require __DIR__ . '/layout/footer.php'; ?>