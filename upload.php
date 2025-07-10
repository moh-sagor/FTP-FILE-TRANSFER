<?php

// This block handles the case where the uploaded files exceed post_max_size in php.ini
// In this case, the $_FILES and $_POST arrays are empty.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_FILES) && empty($_POST)) {
    $post_max_size = ini_get('post_max_size');
    echo "Error: The total size of the uploaded files exceeds the server limit of {$post_max_size}.";
    exit;
}

if (isset($_FILES['files'])) {
    $files = $_FILES['files'];
    $uploadedFiles = [];
    $errors = [];

    foreach ($files['name'] as $key => $name) {
        $fileError = $files['error'][$key];

        if ($fileError === UPLOAD_ERR_OK) {
            $tmp_name = $files['tmp_name'][$key];
            $uploadedFiles[] = [
                'name' => $name,
                'tmp_name' => $tmp_name,
                'size' => $files['size'][$key],
                'type' => $files['type'][$key]
            ];
        } else {
            $errorMessage = "Error uploading file '$name': ";
            switch ($fileError) {
                case UPLOAD_ERR_INI_SIZE:
                    $upload_max_filesize = ini_get('upload_max_filesize');
                    $errorMessage .= "The file is larger than the server's allowed size of {$upload_max_filesize}.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMessage .= 'The file exceeds the MAX_FILE_SIZE directive specified in the HTML form.';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMessage .= 'The file was only partially uploaded.';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMessage .= 'No file was uploaded.';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorMessage .= 'Missing a temporary folder.';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMessage .= 'Failed to write file to disk.';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errorMessage .= 'A PHP extension stopped the file upload.';
                    break;
                default:
                    $errorMessage .= 'Unknown upload error.';
                    break;
            }
            $errors[] = $errorMessage;
        }
    }

    if (!empty($uploadedFiles) && empty($errors)) {
        $code = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
        $uploadedFileDetails = [];

        if (count($uploadedFiles) === 1) {
            // Handle single file upload
            $file = $uploadedFiles[0];
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = $code . '.' . $fileExt;
            $fileDestination = 'uploads/' . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $fileDestination)) {
                echo $code;
            } else {
                echo "Error: Could not move the uploaded file.";
            }
        } else {
            // Handle multiple file upload: save individually and create manifest
            foreach ($uploadedFiles as $index => $file) {
                $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
                $uniqueFileName = $code . '_' . $index . '.' . $fileExt; // e.g., ABCDEF_0.jpg, ABCDEF_1.png
                $fileDestination = 'uploads/' . $uniqueFileName;

                if (move_uploaded_file($file['tmp_name'], $fileDestination)) {
                    $uploadedFileDetails[] = [
                        'original_name' => $file['name'],
                        'stored_name' => $uniqueFileName
                    ];
                } else {
                    $errors[] = "Error: Could not move uploaded file '" . $file['name'] . "'.";
                }
            }

            if (empty($errors)) {
                $manifestPath = 'uploads/' . $code . '.json';
                if (file_put_contents($manifestPath, json_encode($uploadedFileDetails))) {
                    echo $code;
                } else {
                    echo "Error: Could not create manifest file.";
                }
            } else {
                echo implode("\n", $errors);
            }
        }
    } else {
        if (!empty($errors)) {
            echo implode("\n", $errors);
        } else {
             echo 'No files were uploaded successfully.';
        }
    }
} else {
    echo 'No files were sent with the request.';
}
?>