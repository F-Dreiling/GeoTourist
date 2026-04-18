<?php

class UserModel {

    private string $backendUrl;

    public function __construct( string $backendUrl ) {
        $this->backendUrl = $backendUrl . "/auth";
    }

    public function login( array $data ): array {
        $options = [
            'http' => [
                'header'  => "Content-type: application/json\r\n",
                'method'  => 'POST',
                'content' => json_encode( $data ),
            ],
        ];

        $context = stream_context_create( $options );
        $result = file_get_contents( $this->backendUrl . '/login', false, $context );

        if ( $result === false ) return [];

        return json_decode( $result, true ) ?? [];
    }
}

?>