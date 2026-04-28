<?php 

class ImageController {

    public function serve() {
        $filename = $_GET['file'] ?? null;

        if ( !$filename ) {
            http_response_code(400);
            exit;
        }

        $url = LOCATIONS_URL . "/images/" . urlencode($filename);

        $context = stream_context_create([
            'http' => [
                'header' => Security::apiHeaders(),
                'method' => 'GET'
            ]
        ]);

        $image = file_get_contents( $url, false, $context );

        if ( $image === false ) {
            http_response_code(404);
            exit;
        }

        $contentType = 'application/octet-stream';

        foreach ( $http_response_header ?? [] as $header ) {
            if ( stripos($header, 'Content-Type:') === 0 ) {
                $contentType = trim( substr($header, 13) );
                break;
            }
        }

        header( "Content-Type: $contentType" );
        echo $image;
    }

    public function thumb() {
        $filename = $_GET['file'] ?? null;

        if ( !$filename ) {
            http_response_code(400);
            exit;
        }

        $url = LOCATIONS_URL . "/images/" . urlencode($filename);

        $context = stream_context_create([
            'http' => [
                'header' => Security::apiHeaders(),
                'method' => 'GET'
            ]
        ]);

        $imageData = file_get_contents( $url, false, $context );

        if ( $imageData === false ) {
            http_response_code(404);
            exit;
        }

        $img = imagecreatefromstring($imageData);

        if ( !$img ) {
            http_response_code(500);
            exit;
        }

        $origW = imagesx($img);
        $origH = imagesy($img);

        $newW = 200;
        $newH = intval( ($origH / $origW) * $newW );

        $thumb = imagecreatetruecolor( $newW, $newH );

        imagecopyresampled(
            $thumb,
            $img,
            0, 0, 0, 0,
            $newW, $newH,
            $origW, $origH
        );

        header("Content-Type: image/jpeg");
        imagejpeg($thumb, null, 75);

        imagedestroy($img);
        imagedestroy($thumb);
    }
}

?>