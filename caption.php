<?php
/**
 * Created by PhpStorm.
 * User: sheldonlynn
 * Date: 2017-12-04
 * Time: 5:22 PM
 */
if (!isset($_POST['submit'])) {
    header("Location: index.php");
    die();
}

$file = $_FILES["fileToUpload"]["tmp_name"];

$check = getimagesize($file);

$errors     = array();
$maxsize    = 4097152;
$acceptable = array(
    'image/jpeg',
    'image/jpg',
    'image/png'
);

$fileSize = filesize($_FILES['fileToUpload']['tmp_name']);

if($fileSize >= $maxsize || $fileSize == 0) {
    echo 'File too large. File must be less than 4mB.';
}

$data = base64_encode(file_get_contents( $_FILES["fileToUpload"]["tmp_name"] ));
echo $data;