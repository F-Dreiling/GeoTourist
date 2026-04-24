<?php

class UserModel {

    public function __construct() {
    }

    public function login( array $data ): array {
        $options = [
            'http' => [
                'header' => Security::apiHeaders(),
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