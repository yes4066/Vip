<?php

declare(strict_types=1);

/**
 * Stage 1: Asset & HTML/Subscription Fetcher (Fully Parallel Version)
 * - Fetches all channel HTML pages and subscription links in a single, large parallel batch.
 * - This is faster but has a higher risk of being rate-limited by Telegram.
 * - Caches raw HTML and processes assets as before.
 * - NEW: Supports `subscription_url` in channelsAssets.json for channels without a public page.
 */

// --- Setup ---
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/functions.php';

// --- Configuration Constants ---
const INPUT_FILE = __DIR__ . '/channelsData/channelsAssets.json';
const FINAL_ASSETS_DIR = __DIR__ . '/channelsData';
const TEMP_BUILD_DIR = __DIR__ . '/temp_build';
const HTML_CACHE_DIR = TEMP_BUILD_DIR . '/html_cache';
const LOGOS_DIR_NAME = 'logos';
const GITHUB_LOGO_BASE_URL = 'https://raw.githubusercontent.com/yebekhe/TVC/main/channelsData/logos';

// --- 1. Initial Checks and Setup ---

echo "--- STAGE 1: ASSET & CONTENT FETCHER (FULLY PARALLEL) ---" . PHP_EOL;
echo "1. Initializing and loading source data..." . PHP_EOL;

if (!file_exists(INPUT_FILE)) {
    die('Error: channelsAssets.json not found.' . PHP_EOL);
}
$sourcesData = json_decode(file_get_contents(INPUT_FILE), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die('Error: Failed to decode channelsAssets.json.' . PHP_EOL);
}
$sourcesToProcess = array_keys($sourcesData);
$totalSources = count($sourcesToProcess);

if (is_dir(TEMP_BUILD_DIR)) {
    deleteFolder(TEMP_BUILD_DIR);
}
mkdir(HTML_CACHE_DIR, 0775, true);
mkdir(TEMP_BUILD_DIR . '/' . LOGOS_DIR_NAME, 0775, true);

// --- 2. Build URL List and Fetch All Content in a Single Parallel Batch ---

echo "2. Building content list and fetching all {$totalSources} sources in parallel..." . PHP_EOL;

$urls_to_fetch = [];
foreach ($sourcesToProcess as $source) {
    // NEW: Check if a subscription link is provided
    if (isset($sourcesData[$source]['subscription_url']) && !empty($sourcesData[$source]['subscription_url'])) {
        $urls_to_fetch[$source] = $sourcesData[$source]['subscription_url'];
        echo "  - {$source} (Subscription URL)" . PHP_EOL;
    } else {
        // Fallback to the standard Telegram scraping method
        $urls_to_fetch[$source] = "https://t.me/s/" . $source;
    }
}
// Call the parallel fetcher with the entire list of mixed URLs at once.
$fetched_content_data = fetch_multiple_urls_parallel($urls_to_fetch);
echo "\nFinished fetching content. Total successful fetches: " . count($fetched_content_data) . " of {$totalSources}" . PHP_EOL;


// --- 3. Save HTML to Cache and Process Assets ---
echo "\n3. Caching content and processing assets..." . PHP_EOL;

$channelArray = [];
$logo_urls_to_fetch = [];
$processedCount = 0;

foreach ($sourcesToProcess as $source) {
    print_progress(++$processedCount, $totalSources, 'Processing:');

    if (!isset($fetched_content_data[$source]) || empty($fetched_content_data[$source])) {
        // Carry over old data if the fetch failed for this specific channel
        $channelArray[$source] = $sourcesData[$source] ?? ['types' => [], 'title' => 'Unknown (Fetch Failed)', 'logo' => ''];
        // Remove subscription_url from final output
        unset($channelArray[$source]['subscription_url']);
        continue;
    }

    $content = $fetched_content_data[$source];
    $foundTypes = [];

    // NEW: Logic to handle both subscription links and regular HTML pages
    if (isset($sourcesData[$source]['subscription_url']) && !empty($sourcesData[$source]['subscription_url'])) {
        // --- THIS IS A SUBSCRIPTION LINK ---
        $decoded_content = base64_decode($content, true);
        if ($decoded_content === false) {
            echo "Warning: Failed to base64-decode subscription content for '{$source}'. Skipping type detection." . PHP_EOL;
            // Preserve existing data but mark types as empty
            $channelArray[$source]['types'] = [];
        } else {
            // Extract links from the decoded content (assuming one link per line)
            $links = preg_split('/\r\n|\r|\n/', $decoded_content);
            foreach ($links as $link) {
                $type = detect_type(trim($link));
                if ($type) {
                    $foundTypes[$type] = true; // Use keys for automatic deduplication
                }
            }
            $channelArray[$source]['types'] = array_keys($foundTypes);
        }

        // For subscription links, we preserve the title and logo from the input file
        $channelArray[$source]['title'] = $sourcesData[$source]['title'] ?? $source;
        $channelArray[$source]['logo'] = $sourcesData[$source]['logo'] ?? '';

    } else {
        // --- THIS IS A REGULAR HTML PAGE ---
        $html = $content;
        file_put_contents(HTML_CACHE_DIR . '/' . $source . '.html', $html);

        // Dynamic Type Discovery using the robust extractLinksByType function
        $links = extractLinksByType($html);
        foreach ($links as $link) {
            $type = detect_type($link);
            if ($type) {
                $foundTypes[$type] = true; // Use keys for automatic deduplication
            }
        }
        $channelArray[$source]['types'] = array_keys($foundTypes);

        // Asset Extraction
        preg_match('#<meta property="twitter:title" content="(.*?)">#', $html, $title_match);
        preg_match('#<meta property="twitter:image" content="(.*?)">#', $html, $image_match);

        $channelArray[$source]['title'] = $title_match[1] ?? 'Unknown Title';

        if (isset($image_match[1]) && !empty($image_match[1])) {
            $logo_urls_to_fetch[$source] = $image_match[1];
            $channelArray[$source]['logo'] = GITHUB_LOGO_BASE_URL . '/' . $source . ".jpg";
        } else {
            $channelArray[$source]['logo'] = '';
        }
    }
}
echo PHP_EOL;

// --- 4. Fetch All Logo Images in a Single Parallel Batch ---
if (!empty($logo_urls_to_fetch)) {
    echo "\n4. Fetching " . count($logo_urls_to_fetch) . " new logo images in parallel..." . PHP_EOL;
    $fetched_logo_data = fetch_multiple_urls_parallel($logo_urls_to_fetch);

    foreach ($fetched_logo_data as $source => $imageData) {
        file_put_contents(TEMP_BUILD_DIR . '/' . LOGOS_DIR_NAME . '/' . $source . '.jpg', $imageData);
    }
    echo "\nLogo downloads complete." . PHP_EOL;
} else {
    echo "\n4. No new logos to fetch." . PHP_EOL;
}


// --- 5. Finalize, Write JSON, and Perform Atomic Swap ---
echo "\n5. Writing new assets file and swapping directories..." . PHP_EOL;
$jsonOutput = json_encode($channelArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
file_put_contents(TEMP_BUILD_DIR . '/channelsAssets.json', $jsonOutput);
if (is_dir(FINAL_ASSETS_DIR)) {
    deleteFolder(FINAL_ASSETS_DIR);
}
rename(TEMP_BUILD_DIR, FINAL_ASSETS_DIR);
echo "Done! Channel assets and HTML cache have been successfully updated." . PHP_EOL;
