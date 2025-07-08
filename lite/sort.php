<?php

declare(strict_types=1);

/**
 * This script reads a list of proxy configurations, sorts them by protocol type
 * AND address type (IPv4, IPv6, Domain), and generates separate subscription
 * files for each combination. It also includes a special category for "reality" configs.
 */

// --- Setup ---
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Ensure the optimized functions.php is available
require_once __DIR__ . '/functions.php';

// --- Configuration Constants ---
const CONFIG_FILE = __DIR__ . '/config.txt';
const SUBS_DIR_NORMAL = __DIR__ . '/subscriptions/xray/normal';
const SUBS_DIR_BASE64 = __DIR__ . '/subscriptions/xray/base64';

// NEW: Helper function to determine the address type (IPv4, IPv6, or Domain).
// It's good practice to place this in your functions.php, but for clarity, it's here.
/**
 * Detects if the host in a config URI is an IPv4, IPv6, or a domain name.
 *
 * @param string $config The configuration URI.
 * @return string|null 'ipv4', 'ipv6', 'domain', or null if host is not found.
 */
function get_address_type(string $config): ?string
{
    $host = parse_url($config, PHP_URL_HOST);

    if (empty($host)) {
        return null;
    }

    // Trim brackets for IPv6 addresses like [::1]
    $ip = trim($host, '[]');

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return 'ipv4';
    }
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        return 'ipv6';
    }
    // If it's not a valid IP, it's a domain name.
    return 'domain';
}


// --- 1. Load Input File ---

echo "1. Loading configurations from " . basename(CONFIG_FILE) . "..." . PHP_EOL;

if (!file_exists(CONFIG_FILE)) {
    die('Error: config.txt not found. Please run the previous scripts first.' . PHP_EOL);
}

// Use file() to read into an array directly. It's clean and efficient.
// The flags automatically skip empty lines and trim newlines from the end of each line.
$configsArray = file(CONFIG_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if (empty($configsArray)) {
    die('Warning: config.txt is empty. No files will be generated.' . PHP_EOL);
}

echo "Loaded " . count($configsArray) . " configs." . PHP_EOL;


// --- 2. Sort Configurations into Groups ---

echo "2. Sorting configs by protocol and address type..." . PHP_EOL;

// MODIFIED: The structure is now a nested array: $sortedConfigs[protocol][address_type]
$sortedConfigs = [];

foreach ($configsArray as $config) {
    $trimmedConfig = trim($config);
    if (empty($trimmedConfig)) {
        continue;
    }

    $configType = detect_type($config);
    // NEW: Detect the address type (ipv4, ipv6, or domain)
    $addressType = get_address_type($config);

    // Skip any malformed or unknown lines
    if ($configType === null || $addressType === null) {
        continue;
    }

    // MODIFIED: Add the config to its nested group
    // The urldecode is kept as it was in the original script's intent
    $sortedConfigs[$configType][$addressType][] = urldecode($config);

    // **Optimization**: Only check for 'reality' if the type is 'vless'.
    if ($configType === 'vless' && is_reality($config)) {
        // MODIFIED: Also sort 'reality' configs by their address type
        $sortedConfigs['reality'][$addressType][] = urldecode($config);
    }
}

echo "Sorting complete. Found " . count($sortedConfigs) . " unique protocol types." . PHP_EOL;


// --- 3. Write Subscription Files ---

echo "3. Writing subscription files..." . PHP_EOL;

// **Robustness**: Ensure the output directories exist, create them if they don't.
if (!is_dir(SUBS_DIR_NORMAL)) {
    mkdir(SUBS_DIR_NORMAL, 0775, true);
}
if (!is_dir(SUBS_DIR_BASE64)) {
    mkdir(SUBS_DIR_BASE64, 0775, true);
}

$filesWritten = 0;
// MODIFIED: Nested loops to handle the new structure [protocol][address_type]
foreach ($sortedConfigs as $type => $addressGroups) {
    foreach ($addressGroups as $addressType => $configs) {
        // NEW: Create a combined filename like 'vless_ipv4', 'trojan_domain', etc.
        $fileName = "{$type}_{$addressType}";
        
        // NEW: Create a more descriptive header for the subscription file
        $header = hiddifyHeader("PSG | " . strtoupper($type) . " " . strtoupper($addressType));
        
        $plainTextContent = $header . implode(PHP_EOL, $configs);
        $base64Content = base64_encode($plainTextContent);

        // Define file paths using the new combined filename
        $normalFilePath = SUBS_DIR_NORMAL . '/' . $fileName;
        $base64FilePath = SUBS_DIR_BASE64 . '/' . $fileName;

        // Write both the plain text and Base64 encoded files
        file_put_contents($normalFilePath, $plainTextContent);
        file_put_contents($base64FilePath, $base64Content);
        
        $filesWritten++;
    }
}

echo "Done! Wrote {$filesWritten} total subscription files." . PHP_EOL;