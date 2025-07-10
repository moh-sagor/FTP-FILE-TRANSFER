<?php
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $files = glob('uploads/' . $code . '.*');

    if (count($files) > 0) {
        $file = $files[0];
        $fileName = basename($file);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($file));

        readfile($file);
        unlink($file); // Delete the file after download
        exit;
    } else {
        echo 'Invalid code.';
    }
}
?>