<?php
// save.php
// Accepts JSON POST data and saves it to assets.csv in the same folder.

header('Content-Type: application/json; charset=utf-8');

$input = file_get_contents('php://input');
if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No input provided']);
    exit;
}

$data = json_decode($input, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

$csvFile = __DIR__ . '/assets.csv';

// Determine headers by using the first object keys if present, otherwise use a preserved header.
$headers = [];
if (count($data) > 0 && is_array($data[0])) {
    $headers = array_keys($data[0]);
} else {
    // Fallback: use existing file header if exists
    if (file_exists($csvFile)) {
        if (($fh = fopen($csvFile, 'r')) !== false) {
            $hdr = fgetcsv($fh);
            fclose($fh);
            if (is_array($hdr)) {
                $headers = $hdr;
            }
        }
    }
}

if (empty($headers)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unable to determine CSV headers']);
    exit;
}

// Write CSV file
if (($fh = fopen($csvFile, 'w')) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Unable to write to CSV file']);
    exit;
}

fputcsv($fh, $headers);
foreach ($data as $row) {
    $line = [];
    foreach ($headers as $key) {
        $line[] = isset($row[$key]) ? $row[$key] : '';
    }
    fputcsv($fh, $line);
}
fclose($fh);

echo json_encode(['success' => true]);
