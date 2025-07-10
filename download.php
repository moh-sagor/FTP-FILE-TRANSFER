<?php
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $filePath = null;
    $fileName = null;
    $filesToDelete = [];

    // Try to find a single file first, excluding .json files
    $singleFileFound = false;
    $singleFiles = glob('uploads/' . $code . '.*');
    foreach ($singleFiles as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'json') {
            $filePath = $file;
            $fileName = basename($filePath);
            $filesToDelete[] = $filePath; // Mark single file for deletion
            $singleFileFound = true;
            break; // Found the single file, no need to check others
        }
    }

    if (!$singleFileFound) {
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
                            $filesToDelete[] = $fullStoredPath; // Mark individual files for deletion
                        } else {
                            // Log error or handle missing file
                        }
                    }
                    $zip->close();

                    $filePath = $zipPath;
                    $fileName = $zipName;
                    $filesToDelete[] = $manifestPath; // Mark manifest for deletion
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

        // Delete all associated files from the uploads folder
        foreach ($filesToDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        exit;
    } else {
        echo 'Invalid code or file not found.';
    }
} else {
    echo 'No code provided.';
}
?>