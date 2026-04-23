<?php

class UserModel {

    public function __construct() {
    }

    private function apiHeaders(): array {
        return [
            "Content-Type: application/json",
            "X-API-KEY: " . BACKEND_KEY
        ];
    }

    public function login( array $data ): array {
        $options = [
            'http' => [
                'header' => $this->apiHeaders(),
                'method'  => 'POST',
                'content' => json_encode( $data ),
            ],
        ];

        $context = stream_context_create( $options );
        $result = file_get_contents( AUTH_URL . '/login', false, $context );

        if ( $result === false ) return [];

        return json_decode( $result, true ) ?? [];
    }
}

?>