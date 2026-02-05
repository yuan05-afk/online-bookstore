<?php
/**
 * Simple Load Test Script
 * Simulates concurrent users accessing the homepage.
 */

$concurrent_users = 50; // Basic test, can scale up but PHP CLI might hit limits
$total_requests = 1000;
$url = 'http://localhost/online-bookstore/index.php';

echo "Starting Load Test: $total_requests requests with $concurrent_users concurrency...\n";

$mh = curl_multi_init();
$handles = [];

$active_requests = 0;
$completed_requests = 0;
$successful_requests = 0;
$failed_requests = 0;
$start_time = microtime(true);

function add_request($mh, &$handles, $url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request for speed, or remove for full body
    curl_multi_add_handle($mh, $ch);
    $handles[] = $ch;
}

// Fill initial batch
for ($i = 0; $i < $concurrent_users; $i++) {
    add_request($mh, $handles, $url);
}

$running = null;
do {
    curl_multi_exec($mh, $running);
    curl_multi_select($mh);

    // Check for completed requests
    while (($info = curl_multi_info_read($mh)) !== false) {
        $ch = $info['handle'];
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($http_code >= 200 && $http_code < 400) {
            $successful_requests++;
        } else {
            $failed_requests++;
        }

        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);

        // Remove from handles array (optional cleanup)

        $completed_requests++;

        // Add new request if we haven't reached total
        if ($completed_requests + count($handles) <= $total_requests + $concurrent_users) {
            // Logic simplified: just replenish until total completed matches
        }

        // Actually, simpler logic: just keep pool filled until we fired enough?
        // Let's just do a fixed batch for this simple script or just run the initial batch
        // For a proper 1000 request test:

        if (($total_requests - $concurrent_users) > 0) {
            // add more... implementation complex for simple script
        }
    }
} while ($running > 0);

// For simplicity in this environment, let's just do the batch of 50 and report stats
// Re-implenting a simple loop for 'Total Requests' is better.

$mh = curl_multi_init();
$handles = [];

// Reset stats
$successful_requests = 0;
$failed_requests = 0;
$start_time = microtime(true);

echo "Processing batches...\n";

for ($i = 0; $i < $total_requests; $i += $concurrent_users) {
    $current_batch_size = min($concurrent_users, $total_requests - $i);
    $batch_handles = [];

    // Prepare batch
    for ($j = 0; $j < $current_batch_size; $j++) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_multi_add_handle($mh, $ch);
        $batch_handles[] = $ch;
    }

    // Execute batch
    $running = null;
    do {
        curl_multi_exec($mh, $running);
    } while ($running > 0);

    // Collect results
    foreach ($batch_handles as $ch) {
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code >= 200 && $http_code < 400) {
            $successful_requests++;
        } else {
            $failed_requests++;
        }
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    echo "Completed " . ($i + $current_batch_size) . " requests...\r";
}

$end_time = microtime(true);
$duration = $end_time - $start_time;
$rps = $total_requests / $duration;

echo "\n\nLoad Test Results:\n";
echo "Total Requests: $total_requests\n";
echo "Concurrency: $concurrent_users\n";
echo "Duration: " . number_format($duration, 2) . " seconds\n";
echo "Requests Per Second: " . number_format($rps, 2) . "\n";
echo "Successful: $successful_requests\n";
echo "Failed: $failed_requests\n";
?>