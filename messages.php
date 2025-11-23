<?php
$messagesFile = 'messages.json';

// Set the correct timezone
date_default_timezone_set('Asia/Kolkata'); // Adjust the timezone as needed

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (file_exists($messagesFile)) {
        $messages = json_decode(file_get_contents($messagesFile), true);
        echo json_encode($messages);
    } else {
        echo json_encode([]);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (file_exists($messagesFile)) {
        $messages = json_decode(file_get_contents($messagesFile), true);
    } else {
        $messages = [];
    }

    // Add the current timestamp in correct format
    $data['timestamp'] = date('Y-m-d H:i:s');
    
    // Append the new message to the existing messages array
    $messages[] = $data;
    
    // Save the updated messages array back to the file
    file_put_contents($messagesFile, json_encode($messages, JSON_PRETTY_PRINT));
}
?>