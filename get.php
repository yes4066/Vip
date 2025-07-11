<?php

declare(strict_types=1);

/**
 * Stage 2: Config Extractor
 * - Reads channel data and cached HTML from Stage 1.
 * - Extracts proxy configs from both cached HTML files AND the remote private_configs.json.
 * - Processes, enriches, and saves the final subscription files.
 * - Removes channels from channelsAssets.json if they no longer provide valid configs.
 */

// --- Setup ---
ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
error_reporting(E_ALL);

require "functions.php";

// --- Configuration Constants ---
const ASSETS_FILE = __DIR__ . "/channelsData/channelsAssets.json";
const HTML_CACHE_DIR = __DIR__ . "/channelsData/html_cache";
const OUTPUT_DIR = __DIR__ . "/subscriptions";
const LOCATION_DIR = OUTPUT_DIR . "/location";
const CHANNEL_SUBS_DIR = OUTPUT_DIR . "/channel";
const FINAL_CONFIG_FILE = __DIR__ . "/config.txt";

// --- NEW: Separate limits for different outputs ---
const CONFIGS_FOR_MAIN_AGGREGATE = 5; // Process latest 5 configs for the main/location files.
const CONFIGS_FOR_CHANNEL_SUBS = 25; // Process latest 25 configs for the per-channel subscription files.

const PRIVATE_CONFIGS_URL = "https://raw.githubusercontent.com/itsyebekhe/PSGP/main/private_configs.json";

// --- Reusable Processing Function ---
/**
 * Processes a single raw config string and enriches it with metadata.
 * @param string $config The raw config string.
 * @param string $source The source channel name.
 * @param int $key The original index of the config.
 * @param array &$ipInfoCache A reference to the IP information cache.
 * @return array|null The enriched config and its country code, or null if invalid. // MODIFIED: Return type changed
 */
function processAndEnrichConfig(
    string $config,
    string $source,
    int $key,
    array &$ipInfoCache
): ?array { // MODIFIED: Return type hint changed to array
    static $configFields = [
        "vmess" => ["ip" => "add", "name" => "ps"],
        "vless" => ["ip" => "hostname", "name" => "hash"],
        "trojan" => ["ip" => "hostname", "name" => "hash"],
        "tuic" => ["ip" => "hostname", "name" => "hash"],
        "hy2" => ["ip" => "hostname", "name" => "hash"],
        "ss" => ["ip" => "server_address", "name" => "name"],
    ];

    $config = explode("<", $config, 2)[0];
    if (!is_valid($config)) {
        return null;
    }
    $type = detect_type($config);
    if ($type === null || !isset($configFields[$type])) {
        return null;
    }
    $decodedConfig = configParse($config);
    if ($decodedConfig === null) {
        return null;
    }
    if (
        $type === "ss" &&
        (empty($decodedConfig["encryption_method"]) ||
            empty($decodedConfig["password"]))
    ) {
        return null;
    }

    $ipField = $configFields[$type]["ip"];
    $ipOrHost = $decodedConfig[$ipField] ?? null;
    if ($ipOrHost === null) {
        return null;
    }

    if (!isset($ipInfoCache[$ipOrHost])) {
        $info = ip_info($ipOrHost);
        $ipInfoCache[$ipOrHost] = $info ? $info->country : "XX";
    }
    $countryCode = $ipInfoCache[$ipOrHost];

    $flag =
        $countryCode === "XX"
            ? "❔"
            : ($countryCode === "CF"
                ? "🚩"
                : getFlags($countryCode));
    $encryptionStatus = isEncrypted($config) ? "🟢" : "🔴";

    $newName = sprintf(
        "%s %s | %s | %s | @%s | %d",
        $flag,
        $countryCode,
        $encryptionStatus,
        $type,
        $source,
        $key
    );
    $decodedConfig[$configFields[$type]["name"]] = $newName;

    $encodedConfig = reparseConfig($decodedConfig, $type);
    if ($encodedConfig === null) {
        return null;
    }

    $finalConfigString = str_replace("amp%3B", "", $encodedConfig);
    
    // MODIFIED: Return an array containing both the config and the country code
    return [
        'config' => $finalConfigString,
        'country' => $countryCode,
    ];
}

// --- 1. Load Source Data and Sanity Check ---
echo "--- STAGE 2: CONFIG EXTRACTOR ---" . PHP_EOL;
echo "1. Loading source list from assets file..." . PHP_EOL;

if (!file_exists(ASSETS_FILE)) {
    die(
        "Error: channelsAssets.json not found. Please run the assets script first." .
            PHP_EOL
    );
}
if (!is_dir(HTML_CACHE_DIR)) {
    echo "Warning: HTML cache directory not found. Will only process subscription-based channels if any." .
        PHP_EOL;
}

$sourcesArray = json_decode(file_get_contents(ASSETS_FILE), true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error: Invalid JSON in assets file." . PHP_EOL);
}

// --- 2. Extract Configs from Cached HTML Files ---
echo "\n2. Extracting configs from local HTML cache..." . PHP_EOL;
$configsList = [];
$totalSources = count($sourcesArray);
$sourceCounter = 0;

foreach ($sourcesArray as $source => $sourceData) {
    print_progress(++$sourceCounter, $totalSources, "Extracting (HTML):");
    if (isset($sourceData["subscription_url"])) {
        continue;
    }

    $htmlFile = HTML_CACHE_DIR . "/" . $source . ".html";
    if (!file_exists($htmlFile)) {
        continue;
    }

    $htmlContent = file_get_contents($htmlFile);
    if (empty($htmlContent)) {
        continue;
    }

    $extractedLinks = extractLinksByType($htmlContent);
    if (!empty($extractedLinks)) {
        $configsList[$source] = array_values(array_unique($extractedLinks));
    }
}
echo PHP_EOL .
    "HTML extraction complete. Found configs from " .
    count($configsList) .
    " sources." .
    PHP_EOL;

// --- 2.5. Integrate configs from the remote private_configs.json file ---
echo "\n2.5. Integrating configs from private source..." . PHP_EOL;
$privateConfigsJson = @file_get_contents(PRIVATE_CONFIGS_URL);

if ($privateConfigsJson === false) {
    echo "  - WARNING: Could not fetch private_configs.json. Skipping this integration." .
        PHP_EOL;
} else {
    $privateConfigsData = json_decode($privateConfigsJson, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "  - WARNING: The fetched private_configs.json is not valid JSON. Skipping." .
            PHP_EOL;
    } else {
        echo "  - Successfully fetched private configs. Merging..." . PHP_EOL;
        foreach ($privateConfigsData as $source => $configs) {
            if (empty($configs) || !is_array($configs)) {
                continue;
            }

            if (isset($configsList[$source])) {
                $configsList[$source] = array_values(
                    array_unique(array_merge($configsList[$source], $configs))
                );
                echo "    - Merged private configs for existing source: '{$source}'" .
                    PHP_EOL;
            } else {
                $configsList[$source] = $configs;
                echo "    - Added configs for private-only source: '{$source}'" .
                    PHP_EOL;
            }
        }
        echo "  - Finished merging private configs." . PHP_EOL;
    }
}

// --- 3. Process and Enrich Configs for MAIN AGGREGATE files ---
echo "\n3. Processing configs for main aggregate files (max " .
    CONFIGS_FOR_MAIN_AGGREGATE .
    " per source)..." .
    PHP_EOL;
$ipInfoCache = [];
$finalOutput = [];
$locationBased = [];
$sourcesWithValidConfigs = []; // This will be populated fully in the next step

$totalConfigsToProcess = 0;
foreach ($configsList as $configs) {
    $totalConfigsToProcess += min(count($configs), CONFIGS_FOR_MAIN_AGGREGATE);
}
$processedCount = 0;

foreach ($configsList as $source => $configs) {
    $configsToProcess = array_slice($configs, -CONFIGS_FOR_MAIN_AGGREGATE);
    $key_offset = count($configs) - count($configsToProcess);

    foreach ($configsToProcess as $key => $config) {
        if ($totalConfigsToProcess > 0) {
            print_progress(
                ++$processedCount,
                $totalConfigsToProcess,
                "Processing (Main):"
            );
        }

        // MODIFIED: Get the returned array from the function
        $processedData = processAndEnrichConfig(
            $config,
            $source,
            $key + $key_offset,
            $ipInfoCache
        );

        // MODIFIED: Use the structured data directly
        if ($processedData !== null) {
            $finalOutput[] = $processedData['config'];
            $countryCode = $processedData['country'];
            $locationBased[$countryCode][] = $processedData['config'];
        }
    }
}
echo PHP_EOL . "Main processing complete." . PHP_EOL;

// --- 4. Write Main and Location-Based Subscription Files ---
echo "\n4. Writing main and location-based subscription files..." . PHP_EOL;
if (is_dir(LOCATION_DIR)) {
    deleteFolder(LOCATION_DIR);
}
mkdir(LOCATION_DIR . "/normal", 0775, true);
mkdir(LOCATION_DIR . "/base64", 0775, true);

foreach ($locationBased as $location => $configs) {
    if (empty(trim($location))) {
        continue;
    }
    $plainText = implode(PHP_EOL, $configs);
    file_put_contents(LOCATION_DIR . "/normal/" . $location, $plainText);
    file_put_contents(
        LOCATION_DIR . "/base64/" . $location,
        base64_encode($plainText)
    );
}
file_put_contents(FINAL_CONFIG_FILE, implode(PHP_EOL, $finalOutput));
echo "Main and location files written." . PHP_EOL;

// --- 4.5. Process and Write Channel-Specific Subscription Files ---
echo "\n4.5. Generating channel-specific subscriptions (max " .
    CONFIGS_FOR_CHANNEL_SUBS .
    " configs)..." .
    PHP_EOL;
if (is_dir(CHANNEL_SUBS_DIR)) {
    deleteFolder(CHANNEL_SUBS_DIR);
}
mkdir(CHANNEL_SUBS_DIR . "/normal", 0775, true);
mkdir(CHANNEL_SUBS_DIR . "/base64", 0775, true);

$totalChannels = count($configsList);
$channelCounter = 0;

foreach ($configsList as $source => $configs) {
    print_progress(++$channelCounter, $totalChannels, "Writing Channels:");

    $channelSpecificConfigs = [];
    $configsToProcess = array_slice($configs, -CONFIGS_FOR_CHANNEL_SUBS);
    $key_offset = count($configs) - count($configsToProcess);

    foreach ($configsToProcess as $key => $config) {
        // MODIFIED: Get the returned array from the function
        $processedData = processAndEnrichConfig(
            $config,
            $source,
            $key + $key_offset,
            $ipInfoCache
        );

        // MODIFIED: Use the config string from the returned array
        if ($processedData !== null) {
            $channelSpecificConfigs[] = $processedData['config'];
            // A source is valid if it produces at least one valid config in this comprehensive check
            if (!isset($sourcesWithValidConfigs[$source])) {
                $sourcesWithValidConfigs[$source] = true;
            }
        }
    }

    if (!empty($channelSpecificConfigs)) {
        $plainText = implode(PHP_EOL, $channelSpecificConfigs);
        $fileName = preg_replace("/[^a-zA-Z0-9_-]/", "", $source);
        file_put_contents(
            CHANNEL_SUBS_DIR . "/normal/" . $fileName,
            $plainText
        );
        file_put_contents(
            CHANNEL_SUBS_DIR . "/base64/" . $fileName,
            base64_encode($plainText)
        );
    }
}
echo PHP_EOL . "Channel-specific files written." . PHP_EOL;

// --- 5. Clean up channelsAssets.json ---
echo "\n5. Cleaning up channelsAssets.json..." . PHP_EOL;
$originalSourceCount = count($sourcesArray);
$updatedSourcesArray = array_filter(
    $sourcesArray,
    function ($sourceData, $key) use ($sourcesWithValidConfigs) {
        if (isset($sourceData["subscription_url"])) {
            return true;
        }
        return isset($sourcesWithValidConfigs[$key]);
    },
    ARRAY_FILTER_USE_BOTH
);
$finalSourceCount = count($updatedSourcesArray);
$removedCount = $originalSourceCount - $finalSourceCount;
if ($removedCount > 0) {
    echo "Removed $removedCount source(s) that had no valid configs and were not subscription links." .
        PHP_EOL;
    file_put_contents(
        ASSETS_FILE,
        json_encode(
            $updatedSourcesArray,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        )
    );
} else {
    echo "No sources needed to be removed." . PHP_EOL;
}

echo "\nDone! All files have been generated successfully." . PHP_EOL;
