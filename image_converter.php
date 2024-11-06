<?php
require 'vendor/autoload.php';

use Intervention\Image\ImageManagerStatic as Image;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {

  $image = $_FILES['image'];
  $uploadDirectory = 'uploads/';

  if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0777, true);
  }

  $imagePath = $uploadDirectory . basename($image['name']);
  move_uploaded_file($image['tmp_name'], $imagePath);

  $img = Image::make($imagePath);

  if (isset($_POST['resize'])) {
    $width = $_POST['width'];
    $height = $_POST['height'];
    $img->resize($width, $height);
  }

  if (isset($_POST['rotate'])) {
    $angle = $_POST['angle'];
    $img->rotate($angle);
  }

  if (isset($_POST['grayscale'])) {
    $img->greyscale();
  }

  if (isset($_POST['brightness'])) {
    $brightness = $_POST['brightness'];
    $img->brightness($brightness);
  }

  if (isset($_POST['contrast'])) {
    $contrast = $_POST['contrast'];
    $img->contrast($contrast);
  }

  if (isset($_FILES['watermark_image']) && $_FILES['watermark_image']['tmp_name'] != '') {
    $watermark = $_FILES['watermark_image']['tmp_name'];
    $watermarkImg = Image::make($watermark);
    $img->insert($watermarkImg, 'bottom-right', 10, 10);
  }

  if (isset($_POST['quality'])) {
    $quality = $_POST['quality'];
    $img->encode('jpg', $quality); // Save as JPG with specified quality
  }

  if (isset($_POST['convert_format'])) {
    $newFormat = $_POST['convert_format'];
    $imagePath = $uploadDirectory . 'converted_' . time() . '.' . $newFormat;
    $img->encode($newFormat)->save($imagePath);
  }

  $newImagePath = $uploadDirectory . 'processed_' . time() . '.jpg';
  $img->save($newImagePath);

  echo json_encode(['path' => $newImagePath, 'message' => 'Image processed successfully']);
}
