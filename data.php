<?php
// data.php
// Reads assets data from a CSV file in the same folder and returns it as JSON.

header('Content-Type: application/json; charset=utf-8');

$csvFile = __DIR__ . '/assets.csv';

// If file does not exist, create an empty file with a default header row.
if (!file_exists($csvFile)) {
    $defaultHeaders = ['id', 'Location', 'Location Description', 'Employee Number', 'Full Name', 'Asset Number', 'Asset Key', 'Asset Type', 'Asset Description', 'S/N', 'Status', 'Comments', 'QR Code', 'Check status', 'تعليق المخازن'];
    $fp = fopen($csvFile, 'w');
    if ($fp) {
        fputcsv($fp, $defaultHeaders);
        fclose($fp);
    }
}

$data = [];
if (($handle = fopen($csvFile, 'r')) !== false) {
    $headers = fgetcsv($handle);
    if ($headers !== false) {
        while (($row = fgetcsv($handle)) !== false) {
            // Ensure row length matches headers length
            if (count($row) !== count($headers)) {
                // Pad or trim row to match header length
                $row = array_pad($row, count($headers), '');
                $row = array_slice($row, 0, count($headers));
            }
            $item = [];
            foreach ($headers as $index => $key) {
                $item[$key] = isset($row[$index]) ? $row[$index] : '';
            }
            $data[] = $item;
        }
    }
    fclose($handle);
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);
