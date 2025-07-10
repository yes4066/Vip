<?php

declare(strict_types=1);

/**
 * Proxy Subscription Generator (PSG) Page Builder
 *
 * Scans subscription directories and generates a modern, fully functional index.html.
 * This script includes a Search/Filter bar, Last Generated timestamp, a multi-format
 * Subscription Analyzer, custom sorting, and uses the Lucide icon library.
 */

// --- Configuration ---
define('PROJECT_ROOT', __DIR__);
define('GITHUB_REPO_URL', 'https://raw.githubusercontent.com/itsyebekhe/PSG/main');
define('OUTPUT_HTML_FILE', PROJECT_ROOT . '/index.html');
define('SCAN_DIRECTORIES', [
    'Standard' => PROJECT_ROOT . '/subscriptions',
    'Lite' => PROJECT_ROOT . '/lite/subscriptions',
]);

/**
 * Provides a mapping of subscription core/client types to compatible applications
 * for various platforms, including their download URLs.
 */
function get_client_info(): array
{
    return [
        'clash' => [
            'windows' => [
                ['name' => 'Clash Verge (Rev) - x64 Installer', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_x64-setup.exe'],
                ['name' => 'Clash Verge (Rev) - ARM64 Installer', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_arm64-setup.msi']
            ],
            'macos' => [
                ['name' => 'Clash Verge (Rev) - Apple Silicon', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_aarch64.dmg'],
                ['name' => 'ClashX - Universal', 'url' => 'https://github.com/yichengchen/clashX/releases/latest/download/ClashX.dmg']
            ],
            'android' => [
                ['name' => 'Clash for Android (CFA) - arm64-v8a', 'url' => 'https://github.com/Kr328/ClashForAndroid/releases/latest/download/cfa-2.5.12-premium-arm64-v8a-release.apk']
            ],
            'ios' => [
                ['name' => 'Stash (Recommended for Clash)', 'url' => 'https://apps.apple.com/us/app/stash/id1596063349']
            ],
            'linux' => [
                ['name' => 'Clash Verge (Rev) - amd64 (.deb)', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/clash-verge_1.6.8_amd64.deb']
            ]
        ],
        'meta' => [ // Uses the same clients as Clash
            'windows' => [
                ['name' => 'Clash Verge (Rev) - x64 Installer', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_x64-setup.exe'],
                ['name' => 'Clash Verge (Rev) - ARM64 Installer', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_arm64-setup.msi']
            ],
            'macos' => [
                ['name' => 'Clash Verge (Rev) - Apple Silicon', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_aarch64.dmg']
            ],
            'android' => [
                ['name' => 'Clash for Android (CFA) - arm64-v8a', 'url' => 'https://github.com/Kr328/ClashForAndroid/releases/latest/download/cfa-2.5.12-premium-arm64-v8a-release.apk']
            ],
            'ios' => [
                ['name' => 'Stash (Recommended for Clash Meta)', 'url' => 'https://apps.apple.com/us/app/stash/id1596063349']
            ],
            'linux' => [
                ['name' => 'Clash Verge (Rev) - amd64 (.deb)', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/clash-verge_1.6.8_amd64.deb']
            ]
        ],
        'locations' => [ // Uses the same clients as Xray
            'windows' => [
                ['name' => 'v2rayN (with Xray core)', 'url' => 'https://github.com/2dust/v2rayN/releases/latest/download/v2rayN-With-Core.zip']
            ],
            'android' => [
                ['name' => 'v2rayNG - arm64-v8a', 'url' => 'https://github.com/2dust/v2rayNG/releases/latest/download/v2rayNG_1.8.19_arm64-v8a.apk']
            ],
            'ios' => [
                ['name' => 'Shadowrocket (Classic Choice)', 'url' => 'https://apps.apple.com/us/app/shadowrocket/id932747118']
            ]
        ],
        'singbox' => [
            'windows' => [
                ['name' => 'Hiddify-Next - x64 Installer', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Windows-x64-Setup.exe']
            ],
            'macos' => [
                ['name' => 'Hiddify-Next - Universal', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-MacOS.dmg']
            ],
            'android' => [
                ['name' => 'Hiddify-Next - Universal', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Android-universal.apk']
            ],
            'ios' => [
                ['name' => 'Streisand (Recommended for Sing-Box)', 'url' => 'https://apps.apple.com/us/app/streisand/id6450534064']
            ],
            'linux' => [
                ['name' => 'Hiddify-Next - x64 (.AppImage)', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Linux-x64.AppImage']
            ]
        ],
        'surfboard' => [
            'android' => [
                ['name' => 'Surfboard (Google Play)', 'url' => 'https://play.google.com/store/apps/details?id=com.getsurfboard']
            ]
        ],
        'xray' => [
            'windows' => [
                ['name' => 'v2rayN (with Xray core)', 'url' => 'https://github.com/2dust/v2rayN/releases/latest/download/v2rayN-With-Core.zip']
            ],
            'android' => [
                ['name' => 'v2rayNG - arm64-v8a', 'url' => 'https://github.com/2dust/v2rayNG/releases/latest/download/v2rayNG_1.8.19_arm64-v8a.apk']
            ],
            'ios' => [
                ['name' => 'Shadowrocket (Classic Choice)', 'url' => 'https://apps.apple.com/us/app/shadowrocket/id932747118']
            ]
        ]
    ];
}

/**
 * Scans a directory recursively for files, filters out ignored extensions,
 * and returns the original, unmodified relative paths.
 */
function scan_directory(string $dir): array
{
    if (!is_dir($dir)) {
        return [];
    }
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    $ignoreExtensions = ['php', 'md', 'ini', 'txt', 'log', 'conf'];

    foreach ($iterator as $file) {
        if ($file->isFile() && !in_array(strtolower($file->getExtension()), $ignoreExtensions)) {
            $relativePath = str_replace(PROJECT_ROOT . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            $files[] = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
        }
    }
    return $files;
}

/**
 * CORRECTED: Processes file paths into a structured array. This function now acts as a
 * strict filter, only processing files from the 'base64' subfolder for 'xray' and 'locations' types.
 */
function process_files_to_structure(array $files_by_category): array
{
    $structure = [];
    foreach (SCAN_DIRECTORIES as $category_key => $category_dir_path) {
        $base_dir_relative = ltrim(str_replace(PROJECT_ROOT, '', $category_dir_path), DIRECTORY_SEPARATOR);
        $base_dir_relative = str_replace(DIRECTORY_SEPARATOR, '/', $base_dir_relative); 

        if (!isset($files_by_category[$category_key])) {
            continue;
        }

        foreach ($files_by_category[$category_key] as $path) { 
            $relative_path_from_base = str_replace($base_dir_relative . '/', '', $path);
            
            // This temporary path is used for filtering and categorization.
            $path_for_parsing = $relative_path_from_base;
            
            // --- STRICT FILTERING LOGIC ---
            if (strpos($path_for_parsing, 'xray/') === 0) {
                // If it's an 'xray' path, it MUST come from the 'base64' subfolder.
                if (strpos($path_for_parsing, 'xray/base64/') !== 0) {
                    continue; // Skip this file (e.g., from 'xray/normal/').
                }
                // It's valid. Simplify the path for categorization.
                $path_for_parsing = str_replace('xray/base64/', 'xray/', $path_for_parsing);

            } elseif (strpos($path_for_parsing, 'location/') === 0) {
                // If it's a 'locations' path, it MUST come from the 'base64' subfolder.
                if (strpos($path_for_parsing, 'location/base64/') !== 0) {
                    continue; // Skip this file (e.g., from 'locations/normal/').
                }
                // It's valid. Simplify the path for categorization.
                $path_for_parsing = str_replace('location/base64/', 'location/', $path_for_parsing);
            }
            // --- END OF FILTERING ---

            $parts = explode('/', $path_for_parsing);
            if (count($parts) < 2) {
                continue;
            }

            $type = array_shift($parts); 
            $name = pathinfo(implode('/', $parts), PATHINFO_FILENAME);
            
            // The URL is always built from the original, correct path.
            $url = GITHUB_REPO_URL . '/' . $path;

            $structure[$category_key][$type][$name] = $url;
        }
    }
    
    // Sort the final structure
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
 * Generates the complete HTML content for the PSG page, embedding
 * structured subscription data and client information as JSON.
 */
function generate_full_html(array $structured_data, array $client_info_data, string $generation_timestamp): string
{
    $json_structured_data = json_encode($structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    $json_client_info_data = json_encode($client_info_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

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
    </style>
</head>
<body class="bg-slate-50 text-slate-800 leading-relaxed transition-colors duration-300">
    <div class="container max-w-6xl mx-auto px-4 py-8">
        <header class="flex justify-between items-center mb-10">
            <div class="text-left">
                <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 mb-0">Proxy Subscription Generator</h1>
                <p class="text-base sm:text-lg text-slate-500 mt-2">Select your preferences to get a subscription link.</p>
            </div>
        </header>

        <main>
            <div class="bg-white rounded-xl p-6 sm:p-8 shadow-lg border border-slate-200 mb-8 sm:mb-10">
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
                <div id="resultArea" class="hidden bg-slate-50 rounded-lg p-4 sm:p-6 border border-slate-200">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6 items-start">
                        <!-- URL and QR Code Area -->
                        <div id="subscription-details-container" class="hidden">
                            <h3 class="text-lg sm:text-xl font-semibold text-slate-800 mb-4">Your Subscription Link:</h3>
                            <div class="flex items-center mb-4">
                                <input type="text" id="subscriptionUrl" readonly class="flex-grow font-mono text-xs sm:text-sm py-2 px-2.5 sm:py-2.5 sm:px-3 bg-white border border-slate-300 rounded-l-lg outline-none whitespace-nowrap overflow-hidden text-ellipsis" />
                                <button id="copyButton" class="flex-shrink-0 flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 bg-indigo-50 text-indigo-700 border border-l-0 border-indigo-600 rounded-r-lg cursor-pointer transition-colors duration-200 hover:bg-indigo-100" title="Copy URL">
                                    <i data-lucide="copy" class="copy-icon w-5 h-5"></i>
                                    <i data-lucide="check" class="check-icon w-5 h-5 hidden"></i>
                                </button>
                            </div>
                            <div class="flex flex-col items-center justify-center">
                                <p class="text-sm text-slate-600 mb-2">Scan the QR code:</p>
                                <div id="qrcode" class="p-2 bg-white border border-slate-300 rounded-lg shadow-inner"></div>
                            </div>
                            <button id="analyzeButton" class="mt-4 w-full flex items-center justify-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                                <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                                <span>Analyze Subscription</span>
                            </button>
                        </div>
                        
                        <!-- Client Info & Analysis Results Area -->
                        <div class="space-y-4">
                           <div id="client-info-container">
                               <h3 class="text-lg sm:text-xl font-semibold text-slate-800 mb-2">Compatible Clients:</h3>
                               <div id="client-info-list" class="space-y-5"></div>
                           </div>
                           <div id="analysis-container" class="hidden">
                               <h3 class="text-lg sm:text-xl font-semibold text-slate-800 mb-2">Live Analysis:</h3>
                               <div id="analysis-results" class="p-4 bg-slate-100 rounded-lg text-slate-700 space-y-2 text-sm">
                                   <!-- Analysis results will be injected here -->
                               </div>
                           </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="text-center mt-12 sm:mt-16 py-6 sm:py-8 border-t border-slate-200">
            <div class="flex justify-center items-center gap-x-6 text-slate-500 text-sm">
                <p>Created with ❤️ by YEBEKHE</p>
                <div class="flex items-center gap-x-3">
                    <a href="https://t.me/yebekhe" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 transition-colors" title="Telegram"><i data-lucide="send" class="h-5 w-5"></i></a>
                    <a href="https://x.com/yebekhe" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 transition-colors" title="X (Twitter)"><i data-lucide="twitter" class="h-5 w-5"></i></a>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-4">Last Generated: __TIMESTAMP_PLACEHOLDER__</p>
        </footer>
    </div>
    <div id="messageBox" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-lg p-6 shadow-xl max-w-sm w-full text-center">
            <p id="messageBoxText" class="text-lg font-semibold text-slate-800 mb-4"></p>
            <button id="messageBoxClose" class="bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors duration-200">OK</button>
        </div>
    </div>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs@0.0.2/qrcode.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // 1. INITIALIZE LIBRARIES
            lucide.createIcons();

            // 2. DEFINE CONSTANTS
            const structuredData = __JSON_DATA_PLACEHOLDER__;
            const clientInfoData = __CLIENT_INFO_PLACEHOLDER__;
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
            const analyzeButton = document.getElementById('analyzeButton');
            const analysisContainer = document.getElementById('analysis-container');
            const analysisResults = document.getElementById('analysis-results');
            const CUSTOM_SORT_ORDER = ['mix', 'vmess', 'vless', 'reality', 'trojan', 'hysteria', 'hy2', 'tuic'];

            const platformIcons = { windows: 'monitor', macos: 'apple', android: 'smartphone', ios: 'tablet', linux: 'terminal' };

            // 3. DEFINE ALL HELPER FUNCTIONS
            function showMessageBox(message) {
                const box = document.getElementById('messageBox');
                document.getElementById('messageBoxText').textContent = message;
                box.classList.remove('hidden');
                document.getElementById('messageBoxClose').onclick = () => box.classList.add('hidden');
            }

            function populateSelect(selectElement, sortedKeys, defaultOptionText) {
                selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;
                sortedKeys.forEach(key => {
                    const option = document.createElement('option');
                    option.value = key;
                    option.textContent = formatDisplayName(key);
                    selectElement.appendChild(option);
                });
            }

            function resetSelect(selectElement, defaultText) {
                selectElement.innerHTML = `<option value="">${defaultText}</option>`;
                selectElement.disabled = true;
            }

            function getFlagEmoji(countryCode) {
                if (!/^[A-Z]{2}$/.test(countryCode)) return '';
                const codePoints = countryCode.toUpperCase().split('').map(char => 127397 + char.charCodeAt());
                return String.fromCodePoint(...codePoints);
            }

            function formatDisplayName(name) {
                const specialReplacements = { 'ss': 'SHADOWSOCKS' };
                const uppercaseTypes = ['mix', 'vless', 'vmess', 'trojan', 'ssr', 'ws', 'grpc', 'reality', 'hy2', 'hysteria2', 'tuic', 'xhttp'];
                const protocolPrefixes = ['ss', 'ssr'];
                let flag = '';
                let processedName = name;
                const countryCodeMatch = processedName.match(/^([A-Z]{2})[-_]/);
                if (countryCodeMatch) {
                    flag = getFlagEmoji(countryCodeMatch[1]);
                    processedName = processedName.substring(countryCodeMatch[0].length);
                } else {
                    const initialTwoLetterMatch = processedName.match(/(?:^|\W)([A-Z]{2})(?:$|\W)/);
                    if (initialTwoLetterMatch && initialTwoLetterMatch[1] && !protocolPrefixes.includes(initialTwoLetterMatch[1].toLowerCase())) {
                        flag = getFlagEmoji(initialTwoLetterMatch[1]);
                    }
                }
                const parts = processedName.split(/[-_]/).filter(p => p !== '');
                const displayNameParts = parts.map((part) => {
                    const lowerPart = part.toLowerCase();
                    if (specialReplacements[lowerPart]) return specialReplacements[lowerPart];
                    if (uppercaseTypes.includes(lowerPart)) return part.toUpperCase();
                    return part.charAt(0).toUpperCase() + part.slice(1).toLowerCase();
                });
                const textName = displayNameParts.join(' ');
                return flag ? `${flag} ${textName.trim()}` : textName.trim();
            }

            function updateQRCode(url) {
                qrcodeDiv.innerHTML = '';
                if (url) {
                    try {
                        new QRCode(qrcodeDiv, {
                            text: url, width: 128, height: 128,
                            colorDark: "#000000", colorLight: "#00000000",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } catch (error) { console.error('QR code initialization failed:', error); }
                }
            }
            
            // --- ANALYSIS PARSERS ---
            function parseBase64Subscription(content) {
                let decodedContent; try { decodedContent = atob(content); } catch (e) { decodedContent = content; }
                const protocols = new Set(); const countries = new Set(); let nodeCount = 0;
                const nodeRegex = /(vmess|vless|ss|trojan|ssr|hy2|tuic):\/\/[^\s#]*(?:#([A-Z]{2}[-_]?[^&\n]*)|#([^&\n]*))?/gi;
                const lines = decodedContent.split('\n');
                lines.forEach(line => {
                    const trimmedLine = line.trim(); if (trimmedLine.startsWith('#') || trimmedLine.startsWith('//') || trimmedLine.length === 0) return;
                    let match;
                    while ((match = nodeRegex.exec(trimmedLine)) !== null) {
                        nodeCount++; if (match[1]) { protocols.add(match[1].toLowerCase()); }
                        const tag = match[2] || match[3];
                        if (tag) {
                            const countryCodeMatch = tag.match(/^([A-Z]{2})[-_]?/);
                            if (countryCodeMatch && countryCodeMatch[1]) { countries.add(countryCodeMatch[1].toUpperCase()); }
                        }
                    }
                });
                return { nodeCount, protocols: Array.from(protocols).sort(), countries: Array.from(countries).sort() };
            }

            function parseClashSubscription(content) {
                const proxyMatch = content.match(/^\s*proxies:\s*\n((?:\s*-\s*.+\n?)*)/m);
                const proxyCount = proxyMatch && proxyMatch[1] ? (proxyMatch[1].match(/^\s*-\s/gm) || []).length : 0;
                const groupMatch = content.match(/^\s*proxy-groups:\s*\n((?:\s*-\s*name:.*?\n?)*)/m);
                const groupCount = groupMatch && groupMatch[1] ? (groupMatch[1].match(/^\s*-\s*name:/gm) || []).length : 0;
                return { proxyCount, groupCount };
            }

            function parseSingboxSubscription(content) {
                try {
                    const cleanContent = content.replace(/^\s*\/\/.*\r?\n/gm, ''); const data = JSON.parse(cleanContent);
                    if (!data.outbounds || !Array.isArray(data.outbounds)) return 0;
                    const utilityTypes = ['selector', 'urltest', 'direct', 'block', 'dns', 'fallback'];
                    return data.outbounds.filter(o => o.type && !utilityTypes.includes(o.type.toLowerCase())).length;
                } catch (e) { console.error('Sing-box JSON parsing error:', e); return 0; }
            }

            function parseSurfboardSubscription(content) {
                const lines = content.split('\n'); let inProxySection = false; let nodeCount = 0;
                for (const line of lines) {
                    const trimmedLine = line.trim(); if (trimmedLine.toLowerCase() === '[proxy]') { inProxySection = true; continue; }
                    if (trimmedLine.startsWith('[')) { inProxySection = false; }
                    if (inProxySection && trimmedLine.includes('=') && !trimmedLine.toLowerCase().startsWith('direct =')) { nodeCount++; }
                } return nodeCount;
            }

            function updateClientInfo(coreType) {
                const clientInfoContainer = document.getElementById('client-info-container');
                clientInfoList.innerHTML = ''; const platforms = clientInfoData[coreType];
                if (!platforms || Object.keys(platforms).length === 0) { clientInfoContainer.classList.add('hidden'); return; }
                clientInfoContainer.classList.remove('hidden');
                Object.entries(platforms).forEach(([platformName, clients]) => {
                    if (clients.length > 0) {
                        const platformContainer = document.createElement('div');
                        const titleDiv = document.createElement('div');
                        titleDiv.className = 'flex items-center gap-2 text-sm font-semibold text-slate-600 mb-2';
                        const iconName = platformIcons[platformName.toLowerCase()] || 'box';
                        const icon = document.createElement('i'); icon.setAttribute('data-lucide', iconName); icon.className = 'w-4 h-4 text-slate-500'; titleDiv.appendChild(icon);
                        const titleText = document.createElement('span'); titleText.textContent = platformName.charAt(0).toUpperCase() + platformName.slice(1); titleDiv.appendChild(titleText);
                        platformContainer.appendChild(titleDiv);
                        const linksContainer = document.createElement('div'); linksContainer.className = 'flex flex-col gap-2';
                        clients.forEach(client => {
                            const link = document.createElement('a'); link.href = client.url; link.target = '_blank'; link.rel = 'noopener noreferrer';
                            link.className = 'flex items-center justify-between p-2.5 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors duration-200 text-slate-700 hover:text-indigo-600';
                            const nameSpan = document.createElement('span'); nameSpan.className = 'font-medium text-sm'; nameSpan.textContent = client.name;
                            const downloadIcon = document.createElement('i'); downloadIcon.setAttribute('data-lucide', 'download'); downloadIcon.className = 'w-4 h-4 text-slate-500';
                            link.appendChild(nameSpan); link.appendChild(downloadIcon); linksContainer.appendChild(link);
                        });
                        platformContainer.appendChild(linksContainer); clientInfoList.appendChild(platformContainer);
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
                    const getSortIndex = (filename) => {
                        const lowerFilename = filename.toLowerCase();
                        for (let i = 0; i < CUSTOM_SORT_ORDER.length; i++) {
                            if (lowerFilename.includes(CUSTOM_SORT_ORDER[i])) return i;
                        } return CUSTOM_SORT_ORDER.length;
                    };
                    const filteredAndSortedKeys = Object.keys(allElements)
                        .filter(key => formatDisplayName(key).toLowerCase().includes(searchTerm))
                        .sort((a, b) => {
                            const indexA = getSortIndex(a); const indexB = getSortIndex(b);
                            if (indexA !== indexB) return indexA - indexB;
                            return a.localeCompare(b);
                        });
                    populateSelect(otherElementSelect, filteredAndSortedKeys, filteredAndSortedKeys.length > 0 ? 'Select Subscription' : 'No matches found');
                    otherElementSelect.disabled = false;
                }
            }

            // 4. ATTACH EVENT LISTENERS
            analyzeButton.addEventListener('click', async () => {
                const url = subscriptionUrlInput.value;
                const clientCore = ipTypeSelect.value;
                if (!url) { showMessageBox('Please select a subscription URL first.'); return; }
                analysisContainer.classList.remove('hidden');
                analysisResults.innerHTML = '<p class="flex items-center gap-2 text-slate-600"><i data-lucide="loader-2" class="animate-spin w-4 h-4"></i> Analyzing subscription...</p>';
                lucide.createIcons();
                try {
                    const response = await fetch(url);
                    if (!response.ok) {
                        if (response.status === 404) { throw new Error(`Subscription file not found (404). Please ensure the link is correct.`); }
                        throw new Error(`Failed to fetch subscription: HTTP ${response.status} - ${response.statusText}.`);
                    }
                    const content = await response.text();
                    let analysisOutput = '';
                    
                    switch (clientCore.toLowerCase()) {
                        case 'location':
                        case 'xray':
                            const xrayResults = parseBase64Subscription(content);
                            analysisOutput = `<p class="font-medium text-green-700 flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i> Analysis Complete</p> <p><strong>Total Proxies:</strong> <span class="font-bold text-lg">${xrayResults.nodeCount}</span></p> <p><strong>Protocols:</strong> ${xrayResults.protocols.length > 0 ? xrayResults.protocols.join(', ').toUpperCase() : 'N/A'}</p> <p><strong>Countries:</strong> ${xrayResults.countries.length > 0 ? xrayResults.countries.map(c => getFlagEmoji(c) + ' ' + c).join(', ') : 'N/A'}</p>`;
                            break;
                        case 'meta':
                        case 'clash':
                            const clashResults = parseClashSubscription(content);
                            analysisOutput = `<p class="font-medium text-green-700 flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i> Analysis Complete</p> <p><strong>Total Proxies:</strong> <span class="font-bold text-lg">${clashResults.proxyCount}</span></p> <p><strong>Proxy Groups:</strong> <span class="font-bold text-lg">${clashResults.groupCount}</span></p>`;
                            break;
                        case 'singbox':
                            const singboxNodeCount = parseSingboxSubscription(content);
                            analysisOutput = `<p class="font-medium text-green-700 flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i> Analysis Complete</p> <p><strong>Total Proxies:</strong> <span class="font-bold text-lg">${singboxNodeCount}</span></p>`;
                            break;
                        case 'surfboard':
                            const surfboardNodeCount = parseSurfboardSubscription(content);
                            analysisOutput = `<p class="font-medium text-green-700 flex items-center gap-2"><i data-lucide="check-circle" class="w-4 h-4"></i> Analysis Complete</p> <p><strong>Total Proxies:</strong> <span class="font-bold text-lg">${surfboardNodeCount}</span></p>`;
                            break;
                        default:
                            throw new Error('Analysis for this client type is not supported yet.');
                    }
                    analysisResults.innerHTML = analysisOutput;
                } catch (error) {
                    analysisResults.innerHTML = `<p class="font-medium text-red-700 flex items-center gap-2"><i data-lucide="x-circle" class="w-4 h-4"></i> Analysis Failed</p><p class="text-sm text-slate-600">${error.message}</p>`;
                }
                lucide.createIcons();
            });

            configTypeSelect.addEventListener('change', () => {
                resetSelect(ipTypeSelect, 'Select Client/Core'); resetSelect(otherElementSelect, 'Select Subscription');
                searchBar.value = ''; searchBar.disabled = true; resultArea.classList.add('hidden');
                if (configTypeSelect.value && structuredData[configTypeSelect.value]) {
                    populateSelect(ipTypeSelect, Object.keys(structuredData[configTypeSelect.value]), 'Select Client/Core');
                    ipTypeSelect.disabled = false;
                }
            });
            
            ipTypeSelect.addEventListener('change', () => {
                const selectedCore = ipTypeSelect.value; searchBar.value = '';
                if (selectedCore) {
                    updateClientInfo(selectedCore); resultArea.classList.remove('hidden');
                    subscriptionDetailsContainer.classList.add('hidden'); analysisContainer.classList.add('hidden');
                    searchBar.disabled = false; updateOtherElementOptions();
                } else {
                    resultArea.classList.add('hidden'); searchBar.disabled = true;
                    resetSelect(otherElementSelect, 'Select Subscription');
                    clientInfoList.innerHTML = '';
                    document.getElementById('client-info-container').classList.add('hidden');
                }
            });

            searchBar.addEventListener('input', updateOtherElementOptions);

            otherElementSelect.addEventListener('change', () => {
                analysisContainer.classList.add('hidden');
                const url = structuredData[configTypeSelect.value]?.[ipTypeSelect.value]?.[otherElementSelect.value];
                if (url) {
                    subscriptionUrlInput.value = url; updateQRCode(url);
                    subscriptionDetailsContainer.classList.remove('hidden');
                } else {
                    subscriptionDetailsContainer.classList.add('hidden');
                }
            });

            copyButton.addEventListener('click', () => {
                if (!subscriptionUrlInput.value) { showMessageBox('No URL to copy.'); return; }
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(subscriptionUrlInput.value)
                        .then(() => {
                            const copyIcon = copyButton.querySelector('.copy-icon'); const checkIcon = copyButton.querySelector('.check-icon');
                            copyIcon.classList.add('hidden'); checkIcon.classList.remove('hidden');
                            setTimeout(() => { copyIcon.classList.remove('hidden'); checkIcon.classList.add('hidden'); }, 2000);
                        }).catch(err => { console.error('Failed to copy text: ', err); showMessageBox('Failed to copy URL. Please copy manually.'); });
                } else {
                    subscriptionUrlInput.select(); document.execCommand('copy');
                    const copyIcon = copyButton.querySelector('.copy-icon'); const checkIcon = copyButton.querySelector('.check-icon');
                    copyIcon.classList.add('hidden'); checkIcon.classList.remove('hidden');
                    setTimeout(() => { copyIcon.classList.remove('hidden'); checkIcon.classList.add('hidden'); }, 2000);
                }
            });
            
            document.addEventListener('copy', (event) => {
                if (subscriptionUrlInput.value && event.clipboardData && event.target === subscriptionUrlInput) {
                    event.clipboardData.setData('text/plain', subscriptionUrlInput.value); event.preventDefault();
                }
            });

            // 5. INITIALIZE THE PAGE
            populateSelect(configTypeSelect, Object.keys(structuredData), 'Select Config Type');
            configTypeSelect.disabled = false;
        });
    </script>
</body>
</html>
HTML;

    $final_html = str_replace('__JSON_DATA_PLACEHOLDER__', $json_structured_data, $html_template);
    $final_html = str_replace('__CLIENT_INFO_PLACEHOLDER__', $json_client_info_data, $final_html);
    $final_html = str_replace('__TIMESTAMP_PLACEHOLDER__', $generation_timestamp, $final_html);
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
$file_count = array_sum(array_map('count', $all_files));
if ($file_count === 0) { die("Error: No subscription files were found to generate the page. Please check SCAN_DIRECTORIES paths. Exiting." . PHP_EOL); }
echo "Found and categorized {$file_count} subscription files." . PHP_EOL;
$structured_data = process_files_to_structure($all_files);
$client_info = get_client_info();
date_default_timezone_set('Asia/Tehran'); 
$timestamp = date('Y-m-d H:i:s T');
$final_html = generate_full_html($structured_data, $client_info, $timestamp);
file_put_contents(OUTPUT_HTML_FILE, $final_html);
echo "Successfully generated page at: " . realpath(OUTPUT_HTML_FILE) . PHP_EOL;

?>
