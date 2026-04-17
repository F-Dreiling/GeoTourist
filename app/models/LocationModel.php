<?php

class LocationModel {

    private string $backendUrl;

    public function __construct( string $backendUrl ) {
        $this->backendUrl = $backendUrl;
    }

    private function fetch( string $url ): array {
        $json = @file_get_contents( $url );

        if ( $json === false ) return [];

        $data = json_decode( $json, true );

        return is_array( $data ) ? $data : [];
    }

    public function all(): array {
        return $this->fetch( $this->backendUrl );
    }

    public function one( string $id ): array {
        return $this->fetch( $this->backendUrl . "/" . $id );
    }

    public function search( string $term ): array {
        return $this->fetch( $this->backendUrl . "/search?term=" . urlencode( $term ) );
    }

    public function date( string $year ): array {
        return $this->fetch( $this->backendUrl . "/date?year=" . urlencode( $year ) );
    }

    public function near( float $lon, float $lat, float $km = 5 ): array {
        return $this->fetch( $this->backendUrl . "/near?lon=$lon&lat=$lat&km=$km" );
    }

    public function create( array $data ): array {
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode( $data ),
            ],
        ];

        $context = stream_context_create( $options );

        $result = file_get_contents( $this->backendUrl, false, $context );

        if ( $result === false ) return [];

        return json_decode( $result, true ) ?? [];
    }

    public function delete( string $id ): bool {
        $options = [
            'http' => [
                'method' => 'DELETE'
            ]
        ];

        $context = stream_context_create( $options );

        $result = file_get_contents( $this->backendUrl . '/' . $id, false, $context );

        return $result !== false;
    }
}

?>