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
    <script src="https://cdn.jsdelivr.net/npm/jsonc-parser@3.2.1/lib/umd/main.min.js"></script>
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
        
        function parseProxyUri(uri) {
            try {
                const protocolMatch = uri.match(/^([a-z0-9]+):\/\//);
                if (!protocolMatch) return null;
                const protocol = protocolMatch[1];
                const nameMatch = uri.match(/#(.+)$/);
                const name = nameMatch ? decodeURIComponent(nameMatch[1].trim()) : 'Unnamed Node';
                const countryMatch = name.match(/\[([A-Z]{2})\]|\b([A-Z]{2})\b/i);
                const country = countryMatch ? (countryMatch[1] || countryMatch[2])?.toUpperCase() : null;
                const isReality = (uri.includes('security=reality') || name.toLowerCase().includes('reality'));
                
                return { uri, protocol: isReality ? 'reality' : protocol, name, country };
            } catch { return null; }
        }

        function generateBase64Output(nodes) { return btoa(nodes.map(n => n.uri).join('\\n')); }

        function generateClashOutput(nodes) {
            const proxyNames = nodes.map(p => p.name);
            const clashConfig = {
                'mixed-port': 7890,
                'allow-lan': false,
                'mode': 'rule',
                'log-level': 'info',
                'proxies': nodes.map(node => {
                    const params = new URL(node.uri);
                    const common = { name: node.name, server: params.hostname, port: parseInt(params.port), udp: true };
                    switch (node.protocol) {
                        case 'vmess': return { ...common, type: 'vmess', uuid: params.username, alterId: 0, cipher: 'auto', network: params.searchParams.get('type') || 'tcp', 'ws-opts': { path: params.searchParams.get('path') } };
                        case 'vless': case 'reality': return { ...common, type: 'vless', uuid: params.username, network: params.searchParams.get('type') || 'tcp', tls: params.searchParams.get('security') === 'tls', 'ws-opts': { path: params.searchParams.get('path') }, 'reality-opts': node.protocol === 'reality' ? { 'public-key': params.searchParams.get('pbk'), 'short-id': params.searchParams.get('sid') } : undefined };
                        case 'trojan': return { ...common, type: 'trojan', password: params.username };
                        default: return common;
                    }
                }),
                'proxy-groups': [{'name': 'PROXY', 'type': 'select', 'proxies': proxyNames }, { 'name': '🚀 Auto-Select', 'type': 'url-test', 'proxies': proxyNames, 'url': 'http://www.gstatic.com/generate_204', 'interval': 300 }],
                'rules': [ 'MATCH,PROXY' ]
            };
            return jsyaml.dump(clashConfig, { noArrayIndent: true });
        }
        
        function generateSingboxOutput(nodes) { return JSON.stringify({"outbounds": nodes.map(node => ({ "type": node.protocol, "tag": node.name }))}, null, 2); }

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
                        uris.forEach(uri => { const parsedNode = parseProxyUri(uri); if (parsedNode) allNodes.push(parsedNode); });
                    } catch (e) { console.warn(`Failed to parse source:`, e); }
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
            if (targetClient === 'clash') { outputContent = generateClashOutput(finalNodes); fileExtension = 'yaml'; }
            else if (targetClient === 'singbox') { outputContent = generateSingboxOutput(finalNodes); fileExtension = 'json'; }
            else { outputContent = generateBase64Output(finalNodes); }
            
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
        searchBar.addEventListener('input', () => { document.getElementById('otherElement').dispatchEvent(new Event('change')); updateOtherElementOptions(); });
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
