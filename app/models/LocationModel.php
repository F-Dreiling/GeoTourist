<?php

class LocationModel {

    private string $backendUrl;

    public function __construct(string $backendUrl) {
        $this->backendUrl = $backendUrl;
    }

    public function all(): array {
        $json = @file_get_contents($this->backendUrl);

        if ($json === false) {
            return [];
        }

        $data = json_decode($json, true);

        return is_array($data) ? $data : [];
    }
    
}

?>