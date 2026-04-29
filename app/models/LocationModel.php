<?php

class LocationModel {

    public function __construct() {
    }

    private function fetch(string $url): array {
        $options = [
            'http' => [
                'header' => Security::apiHeaders(),
                'method' => 'GET'
            ]
        ];

        $context = stream_context_create($options);

        $json = @file_get_contents( $url, false, $context );

        if ( $json === false ) return [];

        $data = json_decode( $json, true );

        return is_array( $data ) ? $data : [];
    }

    public function all(): array {
        return $this->fetch( LOCATIONS_URL );
    }

    public function one( string $id ): array {
        return $this->fetch( LOCATIONS_URL . "/" . $id );
    }

    public function search( string $term ): array {
        return $this->fetch( LOCATIONS_URL . "/search?term=" . urlencode( $term ) );
    }

    public function date( string $year ): array {
        return $this->fetch( LOCATIONS_URL . "/date?year=" . urlencode( $year ) );
    }

    public function near( float $lon, float $lat, float $km = 5 ): array {
        return $this->fetch( LOCATIONS_URL . "/near?lon=$lon&lat=$lat&km=$km" );
    }

    public function create( array $data ): array {
        $options = [
            'http' => [
                'header'  => Security::apiHeaders(),
                'method'  => 'POST',
                'content' => json_encode( $data ),
            ],
        ];

        $context = stream_context_create( $options );

        $result = file_get_contents( LOCATIONS_URL, false, $context );

        if ( $result === false ) return [];

        return json_decode( $result, true ) ?? [];
    }

    public function delete( string $id ): bool {
        $options = [
            'http' => [
                'header'=> Security::apiHeaders(),
                'method' => 'DELETE'
            ]
        ];

        $context = stream_context_create( $options );

        $result = file_get_contents( LOCATIONS_URL . '/' . $id, false, $context );

        return $result !== false;
    }

    public function uploadImage( string $id, array $file ): array {
        $ch = curl_init();

        $headers = Security::apiHeadersFile();

        curl_setopt_array($ch, [
            CURLOPT_URL => LOCATIONS_URL . "/$id/image",
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => [
                'file' => new CURLFile( $file['tmp_name'], $file['type'], $file['name'] )
            ]
        ]);

        $result = curl_exec($ch);

        if ( $result === false ) {
            return [
                'status' => 'error',
                'message' => 'Upload request failed'
            ];
        }

        $data = json_decode( $result, true );

        return is_array( $data ) ? $data : [
            'status' => 'error',
            'message' => 'Invalid backend response'
        ];
    }
}

?>