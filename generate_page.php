<?php

declare(strict_types=1);

/**
 * Proxy Subscription Generator (PSG) Page Builder
 *
 * Scans subscription directories and generates a modern, fully functional index.html.
 * This script includes a "Simple Mode" for basic users and an advanced
 * "Subscription Composer" for power users, powered by client-side JavaScript.
 */

// --- Configuration ---
define("PROJECT_ROOT", __DIR__);
define(
    "GITHUB_REPO_URL",
    "https://raw.githubusercontent.com/itsyebekhe/PSG/main"
);
define("OUTPUT_HTML_FILE", PROJECT_ROOT . "/index.html");
define("SCAN_DIRECTORIES", [
    "Standard" => PROJECT_ROOT . "/subscriptions",
    "Lite" => PROJECT_ROOT . "/lite/subscriptions",
    "Channels" => PROJECT_ROOT . "/channels",
]);

function get_client_info(): array
{
    // This function remains unchanged.
    return [
        "clash" => [
            "windows" => [
                [
                    "name" => "Clash Verge (Rev) - x64 Installer",
                    "url" =>
                        "https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_x64-setup.exe",
                ],
                [
                    "name" => "Clash Verge (Rev) - ARM64 Installer",
                    "url" =>
                        "https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_arm64-setup.msi",
                ],
            ],
            "macos" => [
                [
                    "name" => "Clash Verge (Rev) - Apple Silicon",
                    "url" =>
                        "https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_aarch64.dmg",
                ],
                [
                    "name" => "ClashX - Universal",
                    "url" =>
                        "https://github.com/yichengchen/clashX/releases/latest/download/ClashX.dmg",
                ],
            ],
            "android" => [
                [
                    "name" => "Clash for Android (CFA) - arm64-v8a",
                    "url" =>
                        "https://github.com/Kr328/ClashForAndroid/releases/latest/download/cfa-2.5.12-premium-arm64-v8a-release.apk",
                ],
            ],
            "ios" => [
                [
                    "name" => "Stash (Recommended for Clash)",
                    "url" => "https://apps.apple.com/us/app/stash/id1596063349",
                ],
            ],
            "linux" => [
                [
                    "name" => "Clash Verge (Rev) - amd64 (.deb)",
                    "url" =>
                        "https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/clash-verge_1.6.8_amd64.deb",
                ],
            ],
        ],
        "meta" => [
            "windows" => [
                [
                    "name" => "Clash Verge (Rev) - x64 Installer",
                    "url" =>
                        "https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_x64-setup.exe",
                ],
            ],
            "macos" => [
                [
                    "name" => "Clash Verge (Rev) - Apple Silicon",
                    "url" =>
                        "https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_aarch64.dmg",
                ],
            ],
            "android" => [
                [
                    "name" => "Clash for Android (CFA) - arm64-v8a",
                    "url" =>
                        "https://github.com/Kr328/ClashForAndroid/releases/latest/download/cfa-2.5.12-premium-arm64-v8a-release.apk",
                ],
            ],
            "ios" => [
                [
                    "name" => "Stash (Recommended for Clash Meta)",
                    "url" =>
                        "https://apps.apple.com/us/app/stash/id1596063349",
                ],
            ],
            "linux" => [
                [
                    "name" => "Clash Verge (Rev) - amd64 (.deb)",
                    "url" =>
                        "https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/clash-verge_1.6.8_amd64.deb",
                ],
            ],
        ],
        "location" => [
            "windows" => [
                [
                    "name" => "v2rayN (with Xray core)",
                    "url" =>
                        "https://github.com/2dust/v2rayN/releases/latest/download/v2rayN-With-Core.zip",
                ],
            ],
            "android" => [
                [
                    "name" => "v2rayNG - arm64-v8a",
                    "url" =>
                        "https://github.com/2dust/v2rayNG/releases/latest/download/v2rayNG_1.8.19_arm64-v8a.apk",
                ],
            ],
            "ios" => [
                [
                    "name" => "Shadowrocket (Classic Choice)",
                    "url" =>
                        "https://apps.apple.com/us/app/shadowrocket/id932747118",
                ],
            ],
        ],
        "singbox" => [
            "windows" => [
                [
                    "name" => "Hiddify-Next - x64 Installer",
                    "url" =>
                        "https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Windows-x64-Setup.exe",
                ],
            ],
            "macos" => [
                [
                    "name" => "Hiddify-Next - Universal",
                    "url" =>
                        "https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-MacOS.dmg",
                ],
            ],
            "android" => [
                [
                    "name" => "Hiddify-Next - Universal",
                    "url" =>
                        "https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Android-universal.apk",
                ],
            ],
            "ios" => [
                [
                    "name" => "Streisand (Recommended for Sing-Box)",
                    "url" =>
                        "https://apps.apple.com/us/app/streisand/id6450534064",
                ],
            ],
            "linux" => [
                [
                    "name" => "Hiddify-Next - x64 (.AppImage)",
                    "url" =>
                        "https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Linux-x64.AppImage",
                ],
            ],
        ],
        "surfboard" => [
            "android" => [
                [
                    "name" => "Surfboard (Google Play)",
                    "url" =>
                        "https://play.google.com/store/apps/details?id=com.getsurfboard",
                ],
            ],
        ],
        "xray" => [
            "windows" => [
                [
                    "name" => "v2rayN (with Xray core)",
                    "url" =>
                        "https://github.com/2dust/v2rayN/releases/latest/download/v2rayN-With-Core.zip",
                ],
            ],
            "android" => [
                [
                    "name" => "v2rayNG - arm64-v8a",
                    "url" =>
                        "https://github.com/2dust/v2rayNG/releases/latest/download/v2rayNG_1.8.19_arm64-v8a.apk",
                ],
            ],
            "ios" => [
                [
                    "name" => "Shadowrocket (Classic Choice)",
                    "url" =>
                        "https://apps.apple.com/us/app/shadowrocket/id932747118",
                ],
            ],
        ],
        "channel" => [
            "windows" => [
                [
                    "name" => "v2rayN (with Xray core)",
                    "url" =>
                        "https://github.com/2dust/v2rayN/releases/latest/download/v2rayN-With-Core.zip",
                ],
            ],
            "android" => [
                [
                    "name" => "v2rayNG - arm64-v8a",
                    "url" =>
                        "https://github.com/2dust/v2rayNG/releases/latest/download/v2rayNG_1.8.19_arm64-v8a.apk",
                ],
            ],
            "ios" => [
                [
                    "name" => "Shadowrocket (Classic Choice)",
                    "url" =>
                        "https://apps.apple.com/us/app/shadowrocket/id932747118",
                ],
            ],
        ],
    ];
}
function scan_directory(string $dir): array
{
    // This function remains unchanged.
    if (!is_dir($dir)) {
        return [];
    }
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $dir,
            RecursiveDirectoryIterator::SKIP_DOTS
        ),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $ignoreExtensions = ["php", "md", "ini", "txt", "log", "conf"];
    foreach ($iterator as $file) {
        if (
            $file->isFile() &&
            !in_array(strtolower($file->getExtension()), $ignoreExtensions)
        ) {
            $relativePath = str_replace(
                PROJECT_ROOT . DIRECTORY_SEPARATOR,
                "",
                $file->getRealPath()
            );
            $files[] = str_replace(DIRECTORY_SEPARATOR, "/", $relativePath);
        }
    }
    return $files;
}

function process_files_to_structure(array $files_by_category): array
{
    // This function remains unchanged.
    $structure = [];
    foreach (SCAN_DIRECTORIES as $category_key => $category_dir_path) {
        $base_dir_relative = ltrim(
            str_replace(PROJECT_ROOT, "", $category_dir_path),
            DIRECTORY_SEPARATOR
        );
        $base_dir_relative = str_replace(
            DIRECTORY_SEPARATOR,
            "/",
            $base_dir_relative
        );

        if (!isset($files_by_category[$category_key])) {
            continue;
        }

        foreach ($files_by_category[$category_key] as $path) {
            $relative_path_from_base = str_replace(
                $base_dir_relative . "/",
                "",
                $path
            );
            $path_for_parsing = $relative_path_from_base;

            if (
                strpos($path_for_parsing, "xray/") === 0 ||
                strpos($path_for_parsing, "channel/") === 0 ||
                strpos($path_for_parsing, "location/") === 0
            ) {
                $parts = explode('/', $path_for_parsing, 3);
                $type_prefix = $parts[0];

                if (count($parts) < 3 || $parts[1] !== 'base64') {
                    continue; 
                }
                $path_for_parsing = $type_prefix . '/' . $parts[2];
            }

            $parts = explode("/", $path_for_parsing);
            if (count($parts) < 2) {
                continue;
            }

            $type = array_shift($parts);
            $remaining_path = implode("/", $parts);
            $name = preg_replace('/\\.[^.\\/]+$/', '', $remaining_path);

            $url = GITHUB_REPO_URL . "/" . $path;

            $structure[$category_key][$type][$name] = $url;
        }
    }

    foreach ($structure as &$categories) {
        ksort($categories);
        foreach ($categories as &$elements) {
            ksort($elements);
        }
    }
    ksort($structure);

    return $structure;
}

/**
 * Generates the complete HTML content for the PSG page.
 */
function generate_full_html(
    array $structured_data,
    array $client_info_data,
    string $generation_timestamp
): string {
    $json_structured_data = json_encode(
        $structured_data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    );
    $json_client_info_data = json_encode(
        $client_info_data,
        JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
    );

    // The entire HTML template with the composer and full JS logic is now here.
    $html_template = <<<'HTML'
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proxy Subscription Generator (PSG)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-yaml@4.1.0/dist/js-yaml.min.js"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['Inter', 'sans-serif'],
            },
          }
        }
      }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .mode-button-active { background-color: #4f46e5; color: white; border-color: #e2e8f0; border-bottom-color: transparent !important; }
        .mode-button-inactive { background-color: transparent; color: #475569; border-color: transparent; }
        .composer-list::-webkit-scrollbar { width: 5px; }
        .composer-list::-webkit-scrollbar-track { background: #f1f5f9; }
        .composer-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .composer-list::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 leading-relaxed transition-colors duration-300">
    <div class="container max-w-6xl mx-auto px-4 py-8">
        <!-- Main Header -->
        <header class="flex justify-between items-center mb-10">
            <div class="text-left">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-slate-900 mb-0">Proxy Subscription Generator</h1>
                <p class="text-base sm:text-lg text-slate-500 mt-2">Your central hub for proxy subscriptions.</p>
            </div>
        </header>

        <main>
            <!-- Main Control Panel -->
            <div class="bg-white rounded-xl p-4 sm:p-6 lg:p-8 shadow-lg border border-slate-200 mb-8 sm:mb-10">
                
                <!-- Mode Toggle Buttons -->
                <div class="flex border-b border-slate-200 mb-6">
                    <button id="simpleModeButton" class="px-4 py-2 text-sm font-semibold rounded-t-md -mb-px border mode-button-active">Simple Mode</button>
                    <button id="composerModeButton" class="px-4 py-2 text-sm font-semibold rounded-t-md hover:bg-slate-100 mode-button-inactive">✨ Subscription Composer</button>
                </div>

                <!-- Container for "Simple Mode" -->
                <div id="simpleModeContainer">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6">
                        <div>
                            <label for="configType" class="block text-sm font-medium text-slate-700 mb-2">Config Type:</label>
                            <select id="configType" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 text-slate-800"></select>
                        </div>
                        <div>
                            <label for="ipType" class="block text-sm font-medium text-slate-700 mb-2">Client/Core:</label>
                            <select id="ipType" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 text-slate-800" disabled></select>
                        </div>
                        <div>
                            <label for="otherElement" class="block text-sm font-medium text-slate-700 mb-2">Subscription:</label>
                            <input type="search" id="searchBar" placeholder="Filter subscriptions..." class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 mb-2 bg-slate-100 text-slate-800 placeholder-slate-400" disabled>
                            <select id="otherElement" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 text-slate-800" disabled></select>
                        </div>
                    </div>
                </div>

                <!-- Container for "Subscription Composer Mode" -->
                <div id="composerModeContainer" class="hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Step 1: Select Sources -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                                <span class="bg-indigo-100 text-indigo-700 w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">1</span>
                                Select Sources
                            </h3>
                            <div id="composerSourceList">
                                <!-- Source categories will be dynamically generated here -->
                            </div>
                        </div>

                        <!-- Step 2: Filter & Refine -->
                        <div class="space-y-6">
                           <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                               <span class="bg-indigo-100 text-indigo-700 w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">2</span>
                               Filter & Refine
                           </h3>
                           <div>
                                <label for="filterCountry" class="block text-sm font-medium text-slate-700 mb-2">Filter by Country Code (optional)</label>
                                <input type="text" id="filterCountry" placeholder="e.g. DE,US,JP (comma-separated)" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 text-slate-800 placeholder-slate-400">
                           </div>
                           <div>
                                <label class="block text-sm font-medium text-slate-700 mb-2">Filter by Protocol (optional)</label>
                                <div id="composerProtocolFilters" class="grid grid-cols-2 gap-2">
                                    <!-- Protocol checkboxes will be inserted here -->
                                </div>
                           </div>
                           <div>
                                <label for="nodeLimit" class="block text-sm font-medium text-slate-700 mb-2">Max Nodes in Subscription</label>
                                <input type="number" id="nodeLimit" value="50" min="1" max="500" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 text-slate-800">
                           </div>
                        </div>

                        <!-- Step 3: Generate Output -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-slate-800 flex items-center gap-2">
                                <span class="bg-indigo-100 text-indigo-700 w-6 h-6 rounded-full flex items-center justify-center font-bold text-sm">3</span>
                                Generate Output
                            </h3>
                            <div>
                                <label for="composerTargetClient" class="block text-sm font-medium text-slate-700 mb-2">Target Client Format</label>
                                <select id="composerTargetClient" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 text-slate-800">
                                    <option value="clash">Clash / Meta</option>
                                    <option value="singbox">Sing-box / Hiddify</option>
                                    <option value="base64">Base64 (for v2rayN, etc.)</option>
                                </select>
                            </div>
                            <button id="generateCompositionButton" class="w-full flex items-center justify-center gap-2 bg-emerald-600 text-white px-4 py-3 rounded-md hover:bg-emerald-700 transition-colors duration-200 disabled:bg-emerald-300 disabled:cursor-not-allowed">
                                <i data-lucide="git-merge" class="w-5 h-5"></i>
                                <span id="generateCompositionButtonText" class="font-semibold">Generate Composed Subscription</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Result Area for Simple Mode -->
                <div id="resultArea" class="hidden bg-slate-50 rounded-lg p-4 sm:p-6 border border-slate-200 mt-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-8 items-start">
                        <div id="subscription-details-container" class="hidden">
                            <h3 class="text-lg sm:text-xl font-semibold text-slate-800 mb-4">Your Subscription Link:</h3>
                            <div class="flex items-center mb-4">
                                <input type="text" id="subscriptionUrl" readonly class="flex-grow font-mono text-xs sm:text-sm py-2.5 px-3 bg-white border border-slate-300 rounded-l-lg outline-none whitespace-nowrap overflow-hidden text-ellipsis" />
                                <button id="copyButton" class="flex-shrink-0 flex items-center justify-center w-11 h-11 bg-indigo-50 text-indigo-700 border border-l-0 border-indigo-600 rounded-r-lg cursor-pointer transition-colors duration-200 hover:bg-indigo-100" title="Copy URL">
                                    <i data-lucide="copy" class="copy-icon w-5 h-5"></i>
                                    <i data-lucide="check" class="check-icon w-5 h-5 hidden"></i>
                                </button>
                            </div>
                            <div class="flex flex-col sm:flex-row items-center justify-center sm:justify-start gap-4">
                                <div id="qrcode" class="p-2 bg-white border border-slate-300 rounded-lg shadow-inner"></div>
                                <button id="analyzeButton" class="w-full sm:w-auto flex-grow flex items-center justify-center gap-2 bg-blue-600 text-white px-4 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                    <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                                    <span class="font-semibold">Analyze Subscription DNA</span>
                                </button>
                            </div>
                        </div>
                        <div id="client-info-container">
                           <h3 class="text-lg sm:text-xl font-semibold text-slate-800 mb-2">Compatible Clients:</h3>
                           <div id="client-info-list" class="space-y-5"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Result Area for Composer Mode -->
                <div id="composerResultArea" class="hidden bg-slate-50 rounded-lg p-4 sm:p-6 border border-slate-200 mt-6">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-800 mb-4">Your Composed Subscription:</h3>
                     <div class="grid grid-cols-1 gap-y-8 items-start">
                        <div>
                             <textarea id="composedResultText" readonly class="w-full h-48 font-mono text-xs bg-white border border-slate-300 rounded-lg p-3 outline-none resize-vertical"></textarea>
                             <div class="flex items-center gap-2 mt-2">
                                <button id="copyComposedButton" class="flex-grow flex items-center justify-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors duration-200">
                                    <i data-lucide="copy"></i> Copy
                                </button>
                                <button id="downloadComposedButton" class="flex-grow flex items-center justify-center gap-2 bg-slate-600 text-white px-4 py-2 rounded-md hover:bg-slate-700 transition-colors duration-200">
                                    <i data-lucide="download"></i> Download
                                </button>
                            </div>
                        </div>
                     </div>
                </div>

            </div>
        </main>
        
        <footer class="text-center mt-12 sm:mt-16 py-6 sm:py-8 border-t border-slate-200">
            <div class="flex flex-col sm:flex-row justify-center items-center gap-y-4 gap-x-6 text-slate-500 text-sm">
                <p>Created with ❤️ by YEBEKHE</p>
                <div class="flex items-center gap-x-3">
                    <a href="https://t.me/yebekhe" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 transition-colors" title="Telegram"><i data-lucide="send" class="h-5 w-5"></i></a>
                    <a href="https://x.com/yebekhe" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 transition-colors" title="X (Twitter)"><i data-lucide="twitter" class="h-5 w-5"></i></a>
                </div>
            </div>
            <p id="lastGenerated" class="text-xs text-slate-400 mt-4">Last Generated: __TIMESTAMP_PLACEHOLDER__</p>
        </footer>
    </div>
    
    <div id="messageBox" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-lg p-6 shadow-xl max-w-sm w-full text-center">
            <p id="messageBoxText" class="text-lg font-semibold text-slate-800 mb-4"></p>
            <button id="messageBoxClose" class="bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors duration-200">OK</button>
        </div>
    </div>
    
    <div id="dnaModal" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center p-4 z-50 hidden">
        <div id="dnaModalContent" class="bg-white rounded-xl p-4 sm:p-6 lg:p-8 shadow-2xl max-w-5xl w-full text-slate-800 transform transition-all scale-95 opacity-0 overflow-y-auto max-h-[90vh]">
            <div class="flex justify-between items-center mb-6 border-b border-slate-200 pb-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Subscription DNA</h2>
                    <p id="modalSubscriptionName" class="text-sm text-slate-500"></p>
                </div>
                <button id="dnaModalCloseButton" class="p-2 rounded-full hover:bg-slate-100">
                    <i data-lucide="x" class="w-6 h-6 text-slate-600"></i>
                </button>
            </div>
            <div id="dnaLoadingState" class="text-center py-10"><p class="flex items-center justify-center gap-2 text-slate-600"><i data-lucide="loader-2" class="animate-spin w-5 h-5"></i>Analyzing... Please wait.</p></div>
            <div id="dnaResultsContainer" class="hidden grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-8">
                    <div><h3 class="font-semibold mb-3 text-center text-slate-700">Protocol Distribution</h3><div class="max-w-[200px] sm:max-w-[250px] mx-auto relative"><canvas id="protocolChart"></canvas><div id="protocolTotal" class="absolute inset-0 flex items-center justify-center text-center leading-none"><div><span class="text-3xl font-bold text-slate-800"></span><span class="text-sm text-slate-500 block">Nodes</span></div></div></div></div>
                    <div><h3 class="font-semibold mb-3 text-center text-slate-700">Security Profile</h3><div class="max-w-[200px] sm:max-w-[250px] mx-auto"><canvas id="securityChart"></canvas></div></div>
                </div>
                <div class="space-y-8">
                     <div><h3 class="font-semibold mb-3 text-center text-slate-700">Top Countries</h3><div id="countryBarChartContainer"><canvas id="countryBarChart"></canvas></div></div>
                     <div><h3 class="font-semibold mb-3 text-center text-slate-700">Top Transports</h3><div id="transportChartContainer"><canvas id="transportChart"></canvas></div></div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs@0.0.2/qrcode.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- DATA (Injected by PHP) ---
        const structuredData = __JSON_DATA_PLACEHOLDER__;
        const clientInfoData = __CLIENT_INFO_PLACEHOLDER__;
        
        // --- DOM REFERENCES ---
        // Simple Mode
        const configTypeSelect = document.getElementById('configType');
        const ipTypeSelect = document.getElementById('ipType');
        const otherElementSelect = document.getElementById('otherElement');
        const searchBar = document.getElementById('searchBar');
        const resultArea = document.getElementById('resultArea');
        const subscriptionUrlInput = document.getElementById('subscriptionUrl');
        const copyButton = document.getElementById('copyButton');
        const qrcodeDiv = document.getElementById('qrcode');
        const clientInfoList = document.getElementById('client-info-list');
        const subscriptionDetailsContainer = document.getElementById('subscription-details-container');
        // DNA Modal
        const analyzeButton = document.getElementById('analyzeButton');
        const dnaModal = document.getElementById('dnaModal');
        const dnaModalCloseButton = document.getElementById('dnaModalCloseButton');
        // Message Box
        const messageBox = document.getElementById('messageBox');
        const messageBoxText = document.getElementById('messageBoxText');
        const messageBoxClose = document.getElementById('messageBoxClose');
        
        // Composer Mode
        const simpleModeButton = document.getElementById('simpleModeButton');
        const composerModeButton = document.getElementById('composerModeButton');
        const simpleModeContainer = document.getElementById('simpleModeContainer');
        const composerModeContainer = document.getElementById('composerModeContainer');
        const composerSourceList = document.getElementById('composerSourceList');
        const composerProtocolFilters = document.getElementById('composerProtocolFilters');
        const generateCompositionButton = document.getElementById('generateCompositionButton');
        const composerResultArea = document.getElementById('composerResultArea');
        const composedResultText = document.getElementById('composedResultText');
        const copyComposedButton = document.getElementById('copyComposedButton');
        const downloadComposedButton = document.getElementById('downloadComposedButton');

        let charts = {};

        // --- UTILITY FUNCTIONS ---
        const countryCodeMap = { US: 'United States', SG: 'Singapore', JP: 'Japan', KR: 'S. Korea', DE: 'Germany', NL: 'Netherlands', GB: 'UK', FR: 'France', CA: 'Canada', AU: 'Australia', HK: 'Hong Kong', TW: 'Taiwan', RU: 'Russia', IN: 'India', TR: 'Turkey', IR: 'Iran', AE: 'UAE' };
        function getCountryName(code) { return countryCodeMap[code.toUpperCase()] || code.toUpperCase(); }
        function getFlagEmoji(countryCode) { if (!/^[A-Z]{2}$/.test(countryCode)) return '🏳️'; return String.fromCodePoint(...countryCode.toUpperCase().split('').map(char => 127397 + char.charCodeAt())); }
        function showMessageBox(message) { messageBoxText.textContent = message; messageBox.classList.remove('hidden'); }
        function formatDisplayName(name) {
            let flag = '';
            const countryCodeMatch = name.match(/\[([A-Z]{2})\]|^([A-Z]{2})[-_]|\b([A-Z]{2})\b/);
            if (countryCodeMatch) {
                const code = countryCodeMatch[1] || countryCodeMatch[2] || countryCodeMatch[3];
                if (code) flag = getFlagEmoji(code);
            }
            const specialReplacements = { 'ss': 'SHADOWSOCKS' };
            const uppercaseTypes = ['mix', 'vless', 'vmess', 'trojan', 'ssr', 'ws', 'grpc', 'reality', 'hy2', 'hysteria2', 'tuic', 'xhttp'];
            const parts = name.replace(/\//g, '-').split(/[-_]/).filter(p => p !== '');
            const displayNameParts = parts.map((part) => {
                if (/^[A-Z]{2}$/.test(part)) return part.toUpperCase();
                const lowerPart = part.toLowerCase();
                if (specialReplacements[lowerPart]) return specialReplacements[lowerPart];
                if (uppercaseTypes.includes(lowerPart)) return part.toUpperCase();
                return part.charAt(0).toUpperCase() + part.slice(1).toLowerCase();
            });
            const textName = displayNameParts.join(' ');
            return flag ? `${flag} ${textName.trim()}` : textName.trim();
        }

        // --- SIMPLE MODE & DNA LOGIC (largely unchanged) ---
        function populateSelect(selectElement, sortedKeys, defaultOptionText) { selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`; sortedKeys.forEach(key => { const option = document.createElement('option'); option.value = key; option.textContent = formatDisplayName(key); selectElement.appendChild(option); }); }
        function resetSelect(selectElement, defaultText) { selectElement.innerHTML = `<option value="">${defaultText}</option>`; selectElement.disabled = true; }
        
        function updateQRCode(element, url) {
            element.innerHTML = '';
            const MAX_QR_CODE_LENGTH = 2500;
            if (!url) return;
            if (url.length > MAX_QR_CODE_LENGTH) {
                element.innerHTML = `<div class="w-[128px] h-[128px] flex items-center justify-center text-center text-xs text-slate-500 bg-slate-100 rounded-md p-2">Content is too large for a QR code. Please copy the URL.</div>`;
                return;
            }
            try { new QRCode(element, { text: url, width: 128, height: 128, colorDark: "#000000", colorLight: "#FFFFFF", correctLevel: QRCode.CorrectLevel.H }); } catch (error) { console.error('QR code init failed:', error); }
        }

        function updateClientInfo(coreType) {
            clientInfoList.innerHTML = '';
            const platforms = clientInfoData[coreType];
            if (!platforms || Object.keys(platforms).length === 0) { clientInfoList.closest('#client-info-container').classList.add('hidden'); return; }
            clientInfoList.closest('#client-info-container').classList.remove('hidden');
            Object.entries(platforms).forEach(([platformName, clients]) => {
                if (clients.length > 0) {
                    const platformContainer = document.createElement('div');
                    const titleDiv = document.createElement('div');
                    titleDiv.className = 'flex items-center gap-2 text-sm font-semibold text-slate-600 mb-2';
                    const iconName = { windows: 'monitor', macos: 'apple', android: 'smartphone', ios: 'tablet', linux: 'terminal' }[platformName.toLowerCase()] || 'box';
                    titleDiv.innerHTML = `<i data-lucide="${iconName}" class="w-4 h-4 text-slate-500"></i><span>${platformName.charAt(0).toUpperCase() + platformName.slice(1)}</span>`;
                    platformContainer.appendChild(titleDiv);
                    const linksContainer = document.createElement('div');
                    linksContainer.className = 'flex flex-col gap-2';
                    clients.forEach(client => { linksContainer.innerHTML += `<a href="${client.url}" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between p-2.5 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors duration-200 text-slate-700 hover:text-indigo-600"><span class="font-medium text-sm">${client.name}</span><i data-lucide="download" class="w-4 h-4 text-slate-500"></i></a>`; });
                    platformContainer.appendChild(linksContainer);
                    clientInfoList.appendChild(platformContainer);
                }
            });
            lucide.createIcons();
        }
        function updateOtherElementOptions() {
            const selectedConfigType = configTypeSelect.value;
            const selectedIpType = ipTypeSelect.value;
            const searchTerm = searchBar.value.toLowerCase();
            
            resetSelect(otherElementSelect, 'Select Subscription');
            subscriptionDetailsContainer.classList.add('hidden');
            
            if (selectedIpType && structuredData[selectedConfigType]?.[selectedIpType]) {
                const allElements = structuredData[selectedConfigType][selectedIpType];
                const filteredAndSortedKeys = Object.keys(allElements)
                    .filter(key => formatDisplayName(key).toLowerCase().includes(searchTerm))
                    .sort((a, b) => a.localeCompare(b));
                    
                populateSelect(otherElementSelect, filteredAndSortedKeys, filteredAndSortedKeys.length > 0 ? 'Select Subscription' : 'No matches found');
                otherElementSelect.disabled = false;
            }
        }
                function getUniversalDna(content, coreType) {
            const dna = { protocols: {}, countries: {}, transports: {}, security: {tls: 0, reality: 0, insecure: 0}, total: 0 };
            
            const processNodeDetails = (protocol, name, transport, security) => {
                if (!protocol || !name) return;
                dna.total++;
                const lowerName = name.toLowerCase();

                // Normalize protocol names
                let normalizedProtocol = protocol.toLowerCase();
                if (normalizedProtocol === 'ss') normalizedProtocol = 'shadowsocks';
                if (normalizedProtocol === 'hysteria2') normalizedProtocol = 'hy2';

                dna.protocols[normalizedProtocol] = (dna.protocols[normalizedProtocol] || 0) + 1;
                
                let normalizedTransport = transport ? transport.toLowerCase() : 'tcp';
                dna.transports[normalizedTransport] = (dna.transports[normalizedTransport] || 0) + 1;
                
                if (security === 'tls') dna.security.tls++;
                else if (security === 'reality') dna.security.reality++;
                else dna.security.insecure++;

                const countryMatch = lowerName.match(/\[([a-z]{2})\]|\b([a-z]{2})\b|([a-z]{2})[-_]/);
                if (countryMatch) {
                    const code = (countryMatch[1] || countryMatch[2] || countryMatch[3]).toUpperCase();
                    dna.countries[code] = (dna.countries[code] || 0) + 1;
                }
            };
            
            try {
                switch (coreType.toLowerCase()) {
                    case 'clash':
                    case 'meta':
                        const parsedYaml = jsyaml.load(content);
                        if (parsedYaml && Array.isArray(parsedYaml.proxies)) {
                            parsedYaml.proxies.forEach(p => {
                                let security = 'insecure';
                                if (p.tls) security = 'tls';
                                if (p['reality-opts']) security = 'reality';
                                processNodeDetails(p.type, p.name, p.network || 'tcp', security);
                            });
                        }
                        break;

                    case 'singbox':
                        const parsedJson = JSON.parse(content);
                        const utilityTypes = ['selector', 'urltest', 'direct', 'block', 'dns'];
                        if (parsedJson && Array.isArray(parsedJson.outbounds)) {
                            parsedJson.outbounds
                                .filter(o => o.type && !utilityTypes.includes(o.type))
                                .forEach(o => {
                                    let security = 'insecure';
                                    if (o.tls?.enabled) {
                                        security = o.tls.reality?.enabled ? 'reality' : 'tls';
                                    }
                                    processNodeDetails(o.type, o.tag, o.transport?.type || 'tcp', security);
                                });
                        }
                        break;
                    
                    case 'xray':
                    case 'location':
                    case 'channel':
                    case 'surfboard': // Surfboard is often base64
                    default: // Fallback for base64 with expanded regex
                        const decoded = atob(content);
                        const vmessRegex = /^vmess:\/\/(.+)$/;
                        const standardRegex = /^(vless|trojan|ss|hy2|tuic|hysteria|hysteria2):\/\/([^@]+@)?([^:?#]+):(\d+)\??([^#]+)?#(.+)$/;

                        decoded.split(/[\n\r]+/).forEach(line => {
                            line = line.trim();
                            if (!line) return;
                            
                            let match;
                            if (match = line.match(vmessRegex)) {
                                try {
                                    const vmessConfig = JSON.parse(atob(match[1]));
                                    processNodeDetails(
                                        'vmess',
                                        vmessConfig.ps || 'vmess_node',
                                        vmessConfig.net || 'tcp',
                                        vmessConfig.tls || 'insecure'
                                    );
                                } catch (e) { /* Malformed vmess, skip */ }
                            } else if (match = line.match(standardRegex)) {
                                const protocol = match[1];
                                const params = new URLSearchParams(match[5] || '');
                                const name = decodeURIComponent(match[6] || `${protocol}_node`);
                                processNodeDetails(
                                    protocol,
                                    name,
                                    params.get('type') || 'tcp',
                                    params.get('security') || 'insecure'
                                );
                            }
                        });
                        break;
                }
            } catch (e) {
                console.error(`Parsing failed for type ${coreType}:`, e);
                throw new Error(`Could not parse subscription. It may be invalid or malformed for the '${coreType}' type.`);
            }
            return dna;
        }

        analyzeButton.addEventListener('click', async () => {
            const url = subscriptionUrlInput.value;
            if (!url) { showMessageBox('Please select a subscription URL first.'); return; }
            
            const modalContent = document.getElementById('dnaModalContent');
            document.getElementById('dnaLoadingState').classList.remove('hidden');
            document.getElementById('dnaResultsContainer').classList.add('hidden');
            document.getElementById('modalSubscriptionName').textContent = `For: ${formatDisplayName(otherElementSelect.value)}`;
            dnaModal.classList.remove('hidden');
            setTimeout(() => modalContent.classList.remove('scale-95', 'opacity-0'), 50);

            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error(`Fetch failed (${response.status})`);
                const content = await response.text();
                const dna = getUniversalDna(content, ipTypeSelect.value);

                if (dna.total === 0) throw new Error('No compatible proxy nodes found to analyze.');
                Object.values(charts).forEach(chart => { if (chart) chart.destroy(); });
                
                charts.protocol = new Chart(document.getElementById('protocolChart'), {
                    type: 'doughnut', data: { labels: Object.keys(dna.protocols), datasets: [{ data: Object.values(dna.protocols), backgroundColor: ['#4f46e5', '#16a34a', '#f97316', '#0ea5e9', '#dc2626', '#d946ef', '#65a30d'], borderWidth: 0 }] },
                    options: { responsive: true, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 }}}}
                });
                document.querySelector('#protocolTotal div span:first-child').textContent = dna.total;

                charts.security = new Chart(document.getElementById('securityChart'), {
                    type: 'doughnut', data: { labels: ['TLS', 'REALITY', 'Insecure'], datasets: [{ data: [dna.security.tls, dna.security.reality, dna.security.insecure], backgroundColor: ['#34d399', '#a78bfa', '#fbbf24'], borderWidth: 0 }] },
                    options: { responsive: true, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15 }}}}
                });

                const sortedCountries = Object.entries(dna.countries).sort((a, b) => b[1] - a[1]).slice(0, 7);
                charts.country = new Chart(document.getElementById('countryBarChart'), {
                    type: 'bar', data: { labels: sortedCountries.map(c => `${getFlagEmoji(c[0])} ${getCountryName(c[0])}`), datasets: [{ label: '# Nodes', data: sortedCountries.map(c => c[1]), backgroundColor: '#60a5fa', borderRadius: 4 }] },
                    options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
                });

                const sortedTransports = Object.entries(dna.transports).sort((a, b) => b[1] - a[1]).slice(0, 7);
                charts.transport = new Chart(document.getElementById('transportChart'), {
                    type: 'bar', data: { labels: sortedTransports.map(t => t[0]), datasets: [{ label: '# Nodes', data: sortedTransports.map(t => t[1]), backgroundColor: '#f472b6', borderRadius: 4 }] },
                    options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
                });
                
                document.getElementById('dnaLoadingState').classList.add('hidden');
                document.getElementById('dnaResultsContainer').classList.remove('hidden');
            } catch (error) {
                const modalContent = document.getElementById('dnaModalContent');
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => dnaModal.classList.add('hidden'), 200);
                showMessageBox(`Analysis Failed: ${error.message}`);
            }
        });

        dnaModalCloseButton.addEventListener('click', () => {
            const modalContent = document.getElementById('dnaModalContent');
            modalContent.classList.add('scale-95', 'opacity-0');
            setTimeout(() => dnaModal.classList.add('hidden'), 200);
            Object.values(charts).forEach(chart => { if (chart) chart.destroy(); });
        });

        // --- COMPOSER LOGIC ---
        function populateComposerSources() {
            composerSourceList.innerHTML = '';
            
            Object.entries(structuredData).forEach(([configType, clientCores]) => {
                let sourcesForConfigType = [];
                 // We only care about base64 sources for the composer
                Object.entries(clientCores).forEach(([clientCore, subscriptions]) => {
                    if (['location', 'channel', 'xray'].includes(clientCore.toLowerCase())) {
                        Object.entries(subscriptions).forEach(([name, url]) => {
                            sourcesForConfigType.push({ name: `${clientCore}/${name}`, url, configType });
                        });
                    }
                });

                if (sourcesForConfigType.length > 0) {
                    const container = document.createElement('div');
                    container.className = 'mb-4';
                    container.innerHTML = `
                        <div class="flex justify-between items-center mb-2">
                            <label class="font-medium text-slate-700">${configType} Sources</label>
                            <button data-target="source-list-${configType}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 select-all-btn">Select All</button>
                        </div>
                        <div id="source-list-${configType}" class="composer-list bg-slate-50 border border-slate-200 rounded-md p-3 max-h-40 overflow-y-auto space-y-2"></div>
                    `;
                    const listDiv = container.querySelector(`#source-list-${configType}`);
                    sourcesForConfigType.sort((a,b) => a.name.localeCompare(b.name)).forEach(source => {
                        const checkboxDiv = document.createElement('div');
                        checkboxDiv.className = 'flex items-center';
                        checkboxDiv.innerHTML = `<input type="checkbox" id="source_${source.url}" data-url="${source.url}" class="composer-source h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"><label for="source_${source.url}" class="ml-2 block text-sm text-slate-900">${formatDisplayName(source.name)}</label>`;
                        listDiv.appendChild(checkboxDiv);
                    });
                    composerSourceList.appendChild(container);
                }
            });

            document.querySelectorAll('.select-all-btn').forEach(btn => btn.addEventListener('click', handleSelectAll));

            const protocols = ['VLESS', 'VMess', 'Trojan', 'Shadowsocks', 'REALITY', 'Hysteria2'];
            composerProtocolFilters.innerHTML = protocols.map(p => `
                <div class="flex items-center">
                    <input type="checkbox" id="proto_${p.toLowerCase()}" data-protocol="${p.toLowerCase()}" class="composer-protocol h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="proto_${p.toLowerCase()}" class="ml-2 block text-sm text-slate-900">${p}</label>
                </div>
            `).join('');
        }

        function handleSelectAll(e) {
            const button = e.target;
            const targetId = button.dataset.target;
            const container = document.getElementById(targetId);
            const checkboxes = container.querySelectorAll('input[type="checkbox"]');
            const isSelectAll = button.textContent === 'Select All';
            checkboxes.forEach(cb => cb.checked = isSelectAll);
            button.textContent = isSelectAll ? 'Deselect All' : 'Select All';
        }
        
        // ====================================================================
        // NEW, ROBUST PARSING LOGIC (Directly converted from your PHP)
        // ====================================================================

        /**
         * Parses a configuration link into an object, mirroring the PHP logic.
         * @param {string} uri The configuration link.
         * @returns {object|null} The parsed configuration or null on failure.
         */
        function configParse(uri) {
            try {
                const protocolMatch = uri.match(/^([a-z0-9]+):\/\//);
                if (!protocolMatch) return null;
                const protocol = protocolMatch[1];

                switch (protocol) {
                    case 'vmess': {
                        const b64 = uri.substring(8);
                        const decoded = JSON.parse(atob(b64));
                        decoded.protocol = 'vmess';
                        return decoded;
                    }

                    case 'vless':
                    case 'trojan':
                    case 'tuic':
                    case 'hy2': {
                        let url;
                        try {
                            url = new URL(uri);
                        } catch (e) {
                            console.warn("Skipping malformed URI:", uri, e.message);
                            return null;
                        }

                        const params = {};
                        url.searchParams.forEach((value, key) => {
                            params[key.toLowerCase()] = value;
                        });

                        const output = {
                            protocol: protocol,
                            username: decodeURIComponent(url.username),
                            hostname: url.hostname,
                            port: parseInt(url.port, 10),
                            params: params,
                            hash: decodeURIComponent(url.hash.substring(1)) || `PSG_${Math.random().toString(36).substring(2, 8)}`,
                        };

                        if (protocol === 'tuic') {
                            output.password = decodeURIComponent(url.password);
                        }
                        return output;
                    }

                    case 'ss': {
                        let url;
                        try {
                           url = new URL(uri);
                        } catch (e) {
                            console.warn("Skipping malformed SS URI:", uri, e.message);
                            return null;
                        }

                        let userInfo = decodeURIComponent(url.username);
                        // Check if the user info part might be base64 encoded
                        try {
                            // A common pattern is base64(method:password)
                            const decodedUserInfo = atob(userInfo);
                            if (decodedUserInfo.includes(':')) {
                                userInfo = decodedUserInfo;
                            }
                        } catch (e) {
                            // Not valid base64, proceed as is
                        }
                        
                        if (!userInfo.includes(':')) return null;

                        const [method, password] = userInfo.split(':', 2);

                        return {
                            protocol: 'ss',
                            encryption_method: method,
                            password: password,
                            hostname: url.hostname,
                            port: parseInt(url.port, 10),
                            hash: decodeURIComponent(url.hash.substring(1)) || `PSG_${Math.random().toString(36).substring(2, 8)}`,
                        };
                    }
                    default:
                        return null;
                }
            } catch (e) {
                console.error("Fatal error parsing config:", uri, e);
                return null;
            }
        }

        async function generateClashOutput(nodes) {
            const templateURL = 'https://raw.githubusercontent.com/itsyebekhe/PSG/main/templates/clash.yaml';
            const response = await fetch(templateURL);
            if (!response.ok) throw new Error('Could not fetch Clash template.');
            let templateContent = await response.text();
            
            const proxyDetails = nodes.map(node => {
                const p = node.parsed;
                let clashNode = null;
                switch(p.protocol) {
                    case 'vmess': clashNode = { type: 'vmess', name: p.ps, server: p.add, port: parseInt(p.port), uuid: p.id, alterId: parseInt(p.aid) || 0, cipher: 'auto', udp: true, network: p.net, 'ws-opts': p.net === 'ws' ? { path: p.path.split('?')[0], headers: { Host: p.host } } : undefined }; break;
                    case 'vless': clashNode = { type: 'vless', name: p.hash, server: p.hostname, port: p.port, uuid: p.username, udp: true, network: p.params.type, tls: p.params.security === 'tls' || p.params.security === 'reality', 'client-fingerprint': 'chrome', 'ws-opts': p.params.type === 'ws' ? { path: p.params.path } : undefined, 'reality-opts': p.params.security === 'reality' ? { 'public-key': p.params.pbk, 'short-id': p.params.sid } : undefined, 'servername': p.params.sni }; break;
                    case 'trojan': clashNode = { type: 'trojan', name: p.hash, server: p.hostname, port: p.port, password: p.username, udp: true, sni: p.params.sni }; break;
                    case 'ss': clashNode = { type: 'ss', name: p.hash, server: p.hostname, port: p.port, cipher: p.encryption_method, password: p.password, udp: true }; break;
                }
                return clashNode;
            }).filter(Boolean);

            const proxiesYAML = jsyaml.dump(proxyDetails, { indent: 2, noArrayIndent: true }).trim();
            const proxyNamesYAML = proxyDetails.map(p => `      - ${p.name}`).join('\n');
            templateContent = templateContent.replace('##PROXIES##', proxiesYAML);
            templateContent = templateContent.replace('##PROXY_NAMES##', proxyNamesYAML);
            return templateContent;
        }
        
        async function generateSingboxOutput(nodes) {
            const templateURL = 'https://raw.githubusercontent.com/itsyebekhe/PSG/main/templates/structure.json';
            const response = await fetch(templateURL);
            if (!response.ok) throw new Error('Could not fetch Sing-box template.');
            const templateString = await response.text();
            const jsonString = templateString.replace(/\\"|"(?:\\"|[^"])*"|(\/\/.*|\/\*[\s\S]*?\*\/)/g, (m, g) => g ? "" : m);
            const templateJson = JSON.parse(jsonString);

            const outbounds = nodes.map(node => {
                const p = node.parsed;
                let singboxNode = null;
                switch (p.protocol) {
                    case 'vmess': singboxNode = { tag: p.ps, type: 'vmess', server: p.add, server_port: parseInt(p.port), uuid: p.id, alter_id: parseInt(p.aid), security: 'auto', transport: p.net ? { type: p.net, path: p.path.split('?')[0], headers: { Host: p.host } } : undefined }; break;
                    case 'vless': 
                        const transport = p.params.type ? { type: p.params.type, path: p.params.path } : undefined;
                        const tls = (p.params.security === 'tls' || p.params.security === 'reality') ? { enabled: true, server_name: p.params.sni, reality: p.params.security === 'reality' ? { enabled: true, public_key: p.params.pbk, short_id: p.params.sid } : undefined } : undefined;
                        singboxNode = { tag: p.hash, type: 'vless', server: p.hostname, server_port: p.port, uuid: p.username, transport, tls }; break;
                    case 'trojan': singboxNode = { tag: p.hash, type: 'trojan', server: p.hostname, server_port: p.port, password: p.username }; break;
                    case 'ss': singboxNode = { tag: p.hash, type: 'shadowsocks', server: p.hostname, server_port: p.port, method: p.encryption_method, password: p.password }; break;
                }
                return singboxNode;
            }).filter(Boolean);

            const urlTestGroup = templateJson.outbounds.find(o => o.tag === 'auto');
            if (urlTestGroup) { urlTestGroup.outbounds = outbounds.map(o => o.tag); }
            templateJson.outbounds.unshift(...outbounds);
            return JSON.stringify(templateJson, null, 2);
        }

        async function handleGenerateComposition() {
            const button = generateCompositionButton;
            const buttonText = document.getElementById('generateCompositionButtonText');
            button.disabled = true;
            buttonText.textContent = 'Generating...';
            composerResultArea.classList.add('hidden');

            const selectedCheckboxes = document.querySelectorAll('.composer-source:checked');
            if (selectedCheckboxes.length === 0) {
                showMessageBox('Please select at least one proxy source.');
                button.disabled = false; buttonText.textContent = 'Generate Composed Subscription'; return;
            }

            const urls = Array.from(selectedCheckboxes).map(cb => cb.dataset.url);
            let allNodes = [];
            const responses = await Promise.allSettled(urls.map(url => fetch(url)));
            
            for (const response of responses) {
                if (response.status === 'fulfilled' && response.value.ok) {
                    try {
                        const decoded = atob(await response.value.text());
                        const uris = decoded.split(/[\n\r]+/).filter(Boolean);
                        uris.forEach(uri => {
                            const parsed = configParse(uri);
                            if (parsed) {
                                const name = parsed.ps || parsed.hash;
                                const countryMatch = name.match(/\[([A-Z]{2})\]|\b([A-Z]{2})\b/i);
                                allNodes.push({
                                    parsed: parsed,
                                    protocol: parsed.protocol,
                                    name: name,
                                    country: countryMatch ? (countryMatch[1] || countryMatch[2])?.toUpperCase() : null,
                                });
                            }
                        });
                    } catch (e) { console.warn(`Failed to process source:`, e); }
                }
            }
            
            const countryFilter = document.getElementById('filterCountry').value.toUpperCase().split(',').map(c => c.trim()).filter(Boolean);
            const protocolFilter = Array.from(document.querySelectorAll('.composer-protocol:checked')).map(cb => cb.dataset.protocol);
            let filteredNodes = allNodes;
            if (countryFilter.length > 0) { filteredNodes = filteredNodes.filter(node => node.country && countryFilter.includes(node.country)); }
            if (protocolFilter.length > 0) { filteredNodes = filteredNodes.filter(node => node.protocol && protocolFilter.includes(node.protocol)); }

            filteredNodes.sort(() => 0.5 - Math.random());
            const limit = parseInt(document.getElementById('nodeLimit').value, 10) || 50;
            const finalNodes = filteredNodes.slice(0, limit);

            if (finalNodes.length === 0) {
                showMessageBox('No nodes found matching your criteria. Try different filters or sources.');
                button.disabled = false; buttonText.textContent = 'Generate Composed Subscription'; return;
            }

            const targetClient = document.getElementById('composerTargetClient').value;
            let outputContent = '', fileExtension = 'txt';

            try {
                if (targetClient === 'clash') { outputContent = await generateClashOutput(finalNodes); fileExtension = 'yaml'; }
                else if (targetClient === 'singbox') { outputContent = await generateSingboxOutput(finalNodes); fileExtension = 'json'; }
                else { outputContent = generateBase64Output(finalNodes.map(n => n.parsed)); } // Base64 still needs raw URIs, let's re-evaluate this if needed
            } catch (e) {
                showMessageBox(`Error generating config: ${e.message}`);
                console.error(e);
                button.disabled = false; buttonText.textContent = 'Generate Composed Subscription'; return;
            }
            
            composedResultText.value = outputContent;
            composedResultText.setAttribute('data-filename', `PSG-composed-config.${fileExtension}`);
            composerResultArea.classList.remove('hidden');
            button.disabled = false; buttonText.textContent = 'Generate Composed Subscription';
        }

        // --- EVENT LISTENERS ---
        simpleModeButton.addEventListener('click', () => { simpleModeContainer.classList.remove('hidden'); composerModeContainer.classList.add('hidden'); composerResultArea.classList.add('hidden'); simpleModeButton.classList.replace('mode-button-inactive', 'mode-button-active'); composerModeButton.classList.replace('mode-button-active', 'mode-button-inactive'); });
        composerModeButton.addEventListener('click', () => { simpleModeContainer.classList.add('hidden'); composerModeContainer.classList.remove('hidden'); resultArea.classList.add('hidden'); simpleModeButton.classList.replace('mode-button-active', 'mode-button-inactive'); composerModeButton.classList.replace('mode-button-inactive', 'mode-button-active'); });
        configTypeSelect.addEventListener('change', () => { resetSelect(ipTypeSelect, 'Select Client/Core'); resetSelect(otherElementSelect, 'Select Subscription'); searchBar.value = ''; searchBar.disabled = true; resultArea.classList.add('hidden'); if (configTypeSelect.value && structuredData[configTypeSelect.value]) { populateSelect(ipTypeSelect, Object.keys(structuredData[configTypeSelect.value]), 'Select Client/Core'); ipTypeSelect.disabled = false; } });
        ipTypeSelect.addEventListener('change', () => { searchBar.value = ''; if (ipTypeSelect.value) { updateClientInfo(ipTypeSelect.value); resultArea.classList.remove('hidden'); subscriptionDetailsContainer.classList.add('hidden'); searchBar.disabled = false; updateOtherElementOptions(); } else { resultArea.classList.add('hidden'); searchBar.disabled = true; resetSelect(otherElementSelect, 'Select Subscription'); } });
        searchBar.addEventListener('input', updateOtherElementOptions);
        otherElementSelect.addEventListener('change', () => { const url = structuredData[configTypeSelect.value]?.[ipTypeSelect.value]?.[otherElementSelect.value]; if (url) { subscriptionUrlInput.value = url; updateQRCode(qrcodeDiv, url); subscriptionDetailsContainer.classList.remove('hidden'); } else { subscriptionDetailsContainer.classList.add('hidden'); } });
        copyButton.addEventListener('click', () => { navigator.clipboard.writeText(subscriptionUrlInput.value).then(() => { const icon = copyButton.querySelector('.copy-icon'), check = copyButton.querySelector('.check-icon'); icon.classList.add('hidden'); check.classList.remove('hidden'); setTimeout(() => { icon.classList.remove('hidden'); check.classList.add('hidden'); }, 2000); }); });
        messageBoxClose.addEventListener('click', () => messageBox.classList.add('hidden'));
        generateCompositionButton.addEventListener('click', handleGenerateComposition);
        copyComposedButton.addEventListener('click', () => { navigator.clipboard.writeText(composedResultText.value).then(() => showMessageBox('Copied to clipboard!')); });
        downloadComposedButton.addEventListener('click', () => { const blob = new Blob([composedResultText.value], { type: 'text/plain' }); const url = URL.createObjectURL(blob); const a = document.createElement('a'); a.href = url; a.download = composedResultText.dataset.filename || 'psg-config.txt'; document.body.appendChild(a); a.click(); document.body.removeChild(a); URL.revokeObjectURL(url); });

        // --- INITIALIZATION ---
        populateSelect(configTypeSelect, Object.keys(structuredData), 'Select Config Type');
        configTypeSelect.disabled = false;
        populateComposerSources();
        lucide.createIcons();
    });
    </script>
</body>
</html>
HTML;

    // Inject the JSON data and timestamp into the final HTML
    $final_html = str_replace(
        "__JSON_DATA_PLACEHOLDER__",
        $json_structured_data,
        $html_template
    );
    $final_html = str_replace(
        "__CLIENT_INFO_PLACEHOLDER__",
        $json_client_info_data,
        $final_html
    );
    $final_html = str_replace(
        "__TIMESTAMP_PLACEHOLDER__",
        $generation_timestamp,
        $final_html
    );
    return $final_html;
}

// --- Main Execution ---
echo "Starting PSG page generator..." . PHP_EOL;
$all_files = [];
foreach (SCAN_DIRECTORIES as $category => $dir) {
    if (is_dir($dir)) {
        echo "Scanning directory: {$dir}" . PHP_EOL;
        $all_files[$category] = scan_directory($dir);
    } else {
        echo "Warning: Directory not found, skipping: {$dir}" . PHP_EOL;
    }
}
$file_count = array_sum(array_map("count", $all_files));
if ($file_count === 0) {
    die(
        "Error: No subscription files were found to generate the page. Please check SCAN_DIRECTORIES paths. Exiting." .
            PHP_EOL
    );
}
echo "Found and categorized {$file_count} subscription files." . PHP_EOL;
$structured_data = process_files_to_structure($all_files);
$client_info = get_client_info();
date_default_timezone_set("Asia/Tehran");
$timestamp = date("Y-m-d H:i:s T");
$final_html = generate_full_html($structured_data, $client_info, $timestamp);
file_put_contents(OUTPUT_HTML_FILE, $final_html);
echo "Successfully generated page at: " . realpath(OUTPUT_HTML_FILE) . PHP_EOL;
