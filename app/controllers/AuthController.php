<?php

require_once __DIR__ . '/../models/UserModel.php';

class AuthController {

    private UserModel $model;

    public function __construct() {
        $this->model = new UserModel();
    }

    public function login() {
        $data = [
            'username' => $_POST['username'] ?? '',
            'password' => $_POST['password'] ?? ''
        ];

        $result = $this->model->login( $data );

        if ( !empty( $result['success'] ) && $result['success'] === true ) {
            $_SESSION['user_id'] = $result['userId'];
            header( "Location: /" );
            exit;
        }

        $_SESSION['login_error'] = true;
        header( "Location: /" );
        exit;
    }

    public function logout() {
        session_start();
        session_destroy();
        header( "Location: /" );
        exit;
    }
}

?>