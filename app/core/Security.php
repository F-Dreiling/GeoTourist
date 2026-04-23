<?php

class Security {
    public static function getCsrfToken() {
        if ( isset($_POST['csrf_token']) ) {
            return $_POST['csrf_token'];
        }

        $raw = file_get_contents( 'php://input' );
        if ( !$raw ) return null;

        $data = json_decode( $raw, true );
        if ( !is_array($data) ) return null;

        return $data['csrf_token'] ?? null;
    }

    public static function requireAuth() {
        if ( !isset( $_SESSION['user_id'] ) ) {
            http_response_code(401);
            exit( 'Unauthorized' );
        }
    }

    public static function requireCsrf() {
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        $requestToken = self::getCsrfToken() ?? '';

        if ( !$sessionToken || !hash_equals( $sessionToken, $requestToken ) ) {
            http_response_code(403);
            exit( 'Invalid CSRF token' );
        }
    }

}
?>