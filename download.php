<?php
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $filePath = null;
    $fileName = null;

    // Try to find a single file first
    $singleFiles = glob('uploads/' . $code . '.*');
    if (count($singleFiles) > 0) {
        $filePath = $singleFiles[0];
        $fileName = basename($filePath);
    } else {
        // If not a single file, try to find a manifest file for multiple files
        $manifestPath = 'uploads/' . $code . '.json';
        if (file_exists($manifestPath)) {
            $manifestContent = file_get_contents($manifestPath);
            $uploadedFileDetails = json_decode($manifestContent, true);

            if ($uploadedFileDetails) {
                $zip = new ZipArchive();
                $zipName = $code . '.zip';
                $zipPath = sys_get_temp_dir() . '/' . $zipName; // Create zip in temp directory

                if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
                    foreach ($uploadedFileDetails as $fileDetail) {
                        $originalName = $fileDetail['original_name'];
                        $storedName = $fileDetail['stored_name'];
                        $fullStoredPath = 'uploads/' . $storedName;

                        if (file_exists($fullStoredPath)) {
                            $zip->addFile($fullStoredPath, $originalName);
                        } else {
                            // Log error or handle missing file
                        }
                    }
                    $zip->close();

                    $filePath = $zipPath;
                    $fileName = $zipName;
                } else {
                    echo "Error: Could not create the zip file on the fly.";
                    exit;
                }
            } else {
                echo "Error: Invalid manifest file.";
                exit;
            }
        }
    }

    if ($filePath && file_exists($filePath)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));

        readfile($filePath);

        // Delete the temporary zip file if it was created on the fly
        if (isset($zipPath) && file_exists($zipPath)) {
            unlink($zipPath);
        }
        // Do NOT delete individual files after download
        exit;
    } else {
        echo 'Invalid code or file not found.';
    }
} else {
    echo 'No code provided.';
}
?>