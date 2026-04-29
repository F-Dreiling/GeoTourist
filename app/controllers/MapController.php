<?php

require_once __DIR__ . '/../models/LocationModel.php';

class MapController {

    private LocationModel $model;
    private array $viewData;

    public function __construct() {
        $this->model = new LocationModel();

        $this->viewData = [
            'locations' => [],
            'uploadStatus' => null,
            'uploadMessage' => null
        ];
    }

    private function getLocationsIfAuthenticated( callable $callback ): array {
        if ( !isset( $_SESSION['user_id'] ) ) {
            return [];
        }

        return $callback();
    }

    private function render( array $locations ) {
        $this->viewData['locations'] = $locations;

        require __DIR__ . '/../views/map.php';
    }

    public function all() {
        $locations = $this->getLocationsIfAuthenticated( fn() => 
            $this->model->all() 
        );

        $this->render( $locations );
    }

    public function one() {
        $id = $_GET['id'] ?? null;

        $locations = $this->model->one( $id );

        $this->render( $locations );
    }

    public function search() {
        $term = $_GET['term'] ?? null;

        if ( $term === null ) {
            $this->all();
            return;
        }

        $locations = $this->model->search( $term );

        $this->render( $locations );
    }

    public function date() {
        $year = $_GET['year'] ?? null;

        if ( !is_numeric( $year ) ) {
            $this->all();
            return;
        }

        $locations = $this->model->date( $year );

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
            ],
            'dateVisited' => $_POST['date']
        ];

        $locations = $this->model->create( $data );

        $id = $locations[0]['id'] ?? null;

        if ( $id && !empty( $_FILES['image'] ) && !empty( $_FILES['image']['tmp_name'] ) ) {
            $uploadResult = $this->model->uploadImage( $id, $_FILES['image'] );

            $this->viewData['uploadStatus'] = $uploadResult['status'] ?? null;
            $this->viewData['uploadMessage'] = $uploadResult['message'] ?? null;

            $locations = $this->model->one( $id );
        }

        $this->render( $locations );
    }

    public function delete() {
        $id = $_GET['id'] ?? null;

        if ( !$id ) {
            http_response_code(400);
            echo "Missing ID";
            return;
        }

        $this->model->delete( $id );

        echo json_encode( ['status' => 'ok'] );
    }
}

?>