<?php
$uploadDir = 'uploads/';
$allowedFileTypes = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'video/mp4',
    'video/avi',
    'application/pdf',
    'application/zip',
];

// Max size (10 MB for example)
$maxFileSize = 10 * 1024 * 1024; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];

    $fileName    = basename($file['name']);
    $fileTmpName = $file['tmp_name'];
    $fileSize    = $file['size'];
    $fileError   = $file['error'];
    $fileType    = mime_content_type($fileTmpName); // more reliable than $_FILES['type']

    // 1. Error check
    if ($fileError !== UPLOAD_ERR_OK) {
        echo json_encode(['error' => 'File upload error']);
        exit;
    }

    // 2. Size check
    if ($fileSize > $maxFileSize) {
        echo json_encode(['error' => 'File too large']);
        exit;
    }

    // 3. MIME type validation
    if (!in_array($fileType, $allowedFileTypes)) {
        echo json_encode(['error' => 'Invalid file type']);
        exit;
    }

    // 4. Double check file extension matches MIME (defence against renamed files)
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $mimeMap = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'mp4' => 'video/mp4',
        'avi' => 'video/avi',
        'pdf' => 'application/pdf',
        'zip' => 'application/zip',
    ];

    if (!isset($mimeMap[$extension]) || $mimeMap[$extension] !== $fileType) {
        echo json_encode(['error' => 'File extension mismatch']);
        exit;
    }

    // 5. Create uploads directory if not exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    // 6. Generate safe unique filename
    $safeName = preg_replace("/[^A-Za-z0-9_\-\.]/", "_", $fileName);
    $filePath = $uploadDir . uniqid("suhail_chatzone_", true) . "-" . $safeName;

    // 7. Optional: Scan file with ClamAV (antivirus) if installed
    // Example: `clamscan` command line
    // $scanResult = shell_exec("clamscan --stdout " . escapeshellarg($fileTmpName));
    // if (strpos($scanResult, "OK") === false) {
    //     echo json_encode(['error' => 'File flagged as malicious']);
    //     exit;
    // }

    // 8. Move uploaded file
    if (move_uploaded_file($fileTmpName, $filePath)) {
        // Make uploaded file non-executable
        chmod($filePath, 0644);
        echo json_encode(['fileUrl' => $filePath]);
    } else {
        echo json_encode(['error' => 'Failed to move uploaded file']);
    }
} else {
    echo json_encode(['error' => 'Network error']);
}
?>