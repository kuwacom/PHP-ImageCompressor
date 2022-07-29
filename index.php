<?php
$imagePath = $_GET['path'];
$imageSize = $_GET['size'];
if (!isset($imageSize) || $imageSize == '' || intval($imageSize) > 1000){
    $imageSize = 'full';
}
if (isset($imagePath) && $imagePath != ''){
    try {
        // $imageData = file_get_contents(__DIR__ . '/' . $imagePath);
        
        list($originalX, $originalY, $type) = getimagesize(__DIR__ . '/' . $imagePath);
        switch ($type) {
            case IMAGETYPE_JPEG:
                $originalImage = imagecreatefromjpeg(__DIR__ . '/' . $imagePath);
                break;
            case IMAGETYPE_PNG:
                $originalImage = imagecreatefrompng(__DIR__ . '/' . $imagePath);
                break;
            case IMAGETYPE_GIF:
                $originalImage = imagecreatefromgif(__DIR__ . '/' . $imagePath);
                break;
            default:
                echo '{"status":"error"}';
                return;
        }
    } catch ( Exception $e ) {
        echo '{"status":"error"}';
        return;
    }
    if ($imageSize == 'full') {
        header('Content-type: image/jpg');
        imagewebp($originalImage);
        imagedestroy($originalImage);
        return;
    }
    $originalSize = $originalX / $originalY;
    $imageData = imagecreatetruecolor($originalSize*$imageSize, $imageSize);
    imagealphablending($imageData, false);
    imagesavealpha($imageData, true);
    imagecopyresized($imageData, $originalImage, 0, 0, 0, 0, $originalSize*$imageSize, $imageSize, $originalX, $originalY);

    header('Content-type: image/jpg');
    imagewebp($imageData);
    imagedestroy($originalImage);
    imagedestroy($imageData);
} else {
    echo '{"status":"error"}';
}
?>
