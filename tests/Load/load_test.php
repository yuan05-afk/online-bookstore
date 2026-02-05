<?php
/**
 * Load Test Script - 1000 Concurrent Users
 * Note: PHP CLI might struggle with 1000 truly simultaneous requests.
 * This script will send 1000 requests in a single batch using curl_multi.
 */

$concurrent_users = 1000; // Number of concurrent users
$url = 'http://localhost/online-bookstore/index.php';

$successful_requests = 0;
$failed_requests = 0;

echo "Starting Load Test: $concurrent_users concurrent users...\n";

$mh = curl_multi_init();
$handles = [];

// Create all 1000 handles
for ($i = 0; $i < $concurrent_users; $i++) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Remove the next line to fetch full page instead of HEAD
    // curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_multi_add_handle($mh, $ch);
    $handles[] = $ch;
}

$start_time = microtime(true);

// Execute all requests concurrently
$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);
} while ($running > 0);

// Collect results
foreach ($handles as $ch) {
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code >= 200 && $http_code < 400) {
        $successful_requests++;
    } else {
        $failed_requests++;
    }
    curl_multi_remove_handle($mh, $ch);
    curl_close($ch);
}

curl_multi_close($mh);

$end_time = microtime(true);
$duration = $end_time - $start_time;
$rps = $concurrent_users / $duration;

echo "\n\nLoad Test Results:\n";
echo "Concurrent Users: $concurrent_users\n";
echo "Duration: " . number_format($duration, 2) . " seconds\n";
echo "Requests Per Second: " . number_format($rps, 2) . "\n";
echo "Successful: $successful_requests\n";
echo "Failed: $failed_requests\n";
?>