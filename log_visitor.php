<?php
// Path to the visitors file
$file = 'visitors.txt';

// Retrieve posted data
$data = json_decode(file_get_contents('php://input'), true);
$userName = $data['user'] ?? 'Anonymous Visitor';

// Read the existing content of the file
if (file_exists($file)) {
    $visitors = file($file, FILE_IGNORE_NEW_LINES);
} else {
    $visitors = [];
}

// Increment visitor count
$count = count($visitors) + 1;

// Add the visitor's entry
$visitors[] = "$count. $userName";

// Save the updated visitors list back to the file
file_put_contents($file, implode(PHP_EOL, $visitors));

// Return a success response
http_response_code(200);
echo json_encode(['success' => true]);
?>