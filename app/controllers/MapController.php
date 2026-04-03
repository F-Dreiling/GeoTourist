<?php

require_once __DIR__ . '/../models/LocationModel.php';

class MapController {

    private $config;
    private $model;

    public function __construct($config) {
        $this->config = $config;
        $this->model = new LocationModel( $this->config['backend_url'] );
    }

    private function render(array $locations) {
        $viewData = [
            'locations' => $locations,
            'maps_api_key' => $this->config['maps_api_key']
        ];

        require __DIR__ . '/../views/map.php';
    }

    public function all() {
        $locations = $this->model->all();

        $this->render( $locations );
    }

    public function one() {
        $id = $_GET['id'] ?? null;

        if ( $id === null ) {
            $this->all();
            return;
        }

        $locations = $this->model->one( $id );

        $this->render( $locations );
    }

    public function search() {
        $name = $_GET['name'] ?? null;

        if ( $name === null ) {
            $this->all();
            return;
        }
        
        $locations = $this->model->search( $name );

        $this->render( $locations );
    }
    
    public function near() {
        $lon = isset( $_GET['lon'] ) ? (float)$_GET['lon'] : null;
        $lat = isset( $_GET['lat'] ) ? (float)$_GET['lat'] : null;

        if ( !( $_GET['km'] > 0 ) ) {
            $_GET['km'] = 5;
        }
        $km = (float)$_GET['km'];

        if ( $lon === null || $lat === null ) {
            $this->all();
            return;
        }

        $locations = $this->model->near( $lon, $lat, $km );

        $this->render( $locations );
    }

    public function create() {
        $data = [
            'name' => $_POST['name'],
            'address' => $_POST['address'],
            'geoPoint' => [
                'type' => 'Point',
                'coordinates' => [
                    (float)$_POST['lon'],
                    (float)$_POST['lat']
                ]
            ]
        ];

        $this->model->create( $data );

        header( "Location: /" );
        exit;
    }
}

?>