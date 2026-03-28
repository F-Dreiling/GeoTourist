<?php

require_once __DIR__ . '/../models/LocationModel.php';

class MapController {

    private $config;

    public function __construct($config) {
        $this->config = $config;
    }

    public function index() {
        $model = new LocationModel($this->config['backend_url']);
        $locations = $model->all();

        $viewData = [
            'locations' => $locations,
            'maps_api_key' => $this->config['maps_api_key']
        ];

        require __DIR__ . '/../views/map.php';
    }
}

?>