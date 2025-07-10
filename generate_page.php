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
            [
                'name' => 'Clash Verge (Rev) - x64 Installer',
                'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_x64-setup.exe'
            ],
            [
                'name' => 'Clash Verge (Rev) - ARM64 Installer',
                'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_arm64-setup.msi'
            ]
        ],
        'macos' => [
            [
                'name' => 'Clash Verge (Rev) - Apple Silicon',
                'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_aarch64.dmg'
            ],
            [
                'name' => 'ClashX - Universal',
                'url' => 'https://github.com/yichengchen/clashX/releases/latest/download/ClashX.dmg'
            ]
        ],
        'android' => [
            [
                'name' => 'Clash for Android (CFA) - arm64-v8a',
                'url' => 'https://github.com/Kr328/ClashForAndroid/releases/latest/download/cfa-2.5.12-premium-arm64-v8a-release.apk'
            ]
        ],
        'ios' => [
            [
                'name' => 'Stash (Recommended for Clash)',
                'url' => 'https://apps.apple.com/us/app/stash/id1596063349'
            ]
        ],
        'linux' => [
            [
                'name' => 'Clash Verge (Rev) - amd64 (.deb)',
                'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/clash-verge_1.6.8_amd64.deb'
            ]
        ]
    ],
    'singbox' => [
        'windows' => [
            [
                'name' => 'Hiddify-Next - x64 Installer',
                'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Windows-x64-Setup.exe'
            ]
        ],
        'macos' => [
            [
                'name' => 'Hiddify-Next - Universal',
                'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-MacOS.dmg'
            ]
        ],
        'android' => [
            [
                'name' => 'Hiddify-Next - Universal',
                'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Android-universal.apk'
            ]
        ],
        'ios' => [
            [
                'name' => 'Streisand (Recommended for Sing-Box)',
                'url' => 'https://apps.apple.com/us/app/streisand/id6450534064'
            ]
        ],
        'linux' => [
            [
                'name' => 'Hiddify-Next - x64 (.AppImage)',
                'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Linux-x64.AppImage'
            ]
        ]
    ],
    'xray' => [
        'windows' => [
            [
                'name' => 'v2rayN (with Xray core)',
                'url' => 'https://github.com/2dust/v2rayN/releases/latest/download/v2rayN-With-Core.zip'
            ]
        ],
        'android' => [
            [
                'name' => 'v2rayNG - arm64-v8a',
                'url' => 'https://github.com/2dust/v2rayNG/releases/latest/download/v2rayNG_1.8.19_arm64-v8a.apk'
            ]
        ],
        'ios' => [
            [
                'name' => 'Shadowrocket (Classic Choice)',
                'url' => 'https://apps.apple.com/us/app/shadowrocket/id932747118'
            ]
        ]
    ],
    'surfboard' => [
        'android' => [
            [
                'name' => 'Surfboard (Google Play)',
                'url' => 'https://play.google.com/store/apps/details?id=com.getsurfboard'
            ]
        ]
    ]
];
}

function scan_directory(string $dir): array
{
    if (!is_dir($dir)) return [];
    $files = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
    $ignoreExtensions = ['php', 'md', 'yml', 'yaml', 'ini'];
    foreach ($iterator as $file) {
        if ($file->isFile() && !in_array(strtolower($file->getExtension()), $ignoreExtensions)) {
            $relativePath = str_replace(PROJECT_ROOT . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            $files[] = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
        }
    }
    return $files;
}

function process_files_to_structure(array $files_by_category): array
{
    $structure = [];
    foreach (SCAN_DIRECTORIES as $category_key => $category_dir_path) {
        $base_dir_relative = ltrim(str_replace(DIRECTORY_SEPARATOR, '/', str_replace(PROJECT_ROOT, '', $category_dir_path)), '/');
        if (!isset($files_by_category[$category_key])) continue;

        foreach ($files_by_category[$category_key] as $path) {
            $relative_path_from_base = str_replace($base_dir_relative . '/', '', $path);
            $path_for_parsing = $relative_path_from_base;

            if (strpos($path_for_parsing, 'xray/') === 0) {
                if (strpos($path_for_parsing, 'xray/base64/') !== 0) {
                    continue; 
                }
                $path_for_parsing = str_replace('xray/base64/', 'xray/', $path_for_parsing);
            }

            $parts = explode('/', $path_for_parsing);
            if (count($parts) < 2) continue;
            
            $type = array_shift($parts);
            $name = pathinfo(implode('/', $parts), PATHINFO_FILENAME);
            
            $url = GITHUB_REPO_URL . '/' . $path;
            
            $structure[$category_key][$type][$name] = $url;
        }
    }
    foreach ($structure as &$categories) { ksort($categories); foreach ($categories as &$elements) { ksort($elements); } }
    ksort($structure);
    return $structure;
}

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
            // --- 1. INITIALIZE & GRAB ELEMENTS ---
            lucide.createIcons();
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

            // --- 2. HELPER & PARSING FUNCTIONS ---
            function showMessageBox(message) {
                const box = document.getElementById('messageBox');
                document.getElementById('messageBoxText').textContent = message;
                box.classList.remove('hidden');
                document.getElementById('messageBoxClose').onclick = () => box.classList.add('hidden');
            }

            function populateSelect(selectElement, keys, defaultText) {
                selectElement.innerHTML = `<option value="">${defaultText}</option>`;
                keys.forEach(key => selectElement.add(new Option(formatDisplayName(key), key)));
            }

            function resetSelect(selectElement, defaultText) {
                selectElement.innerHTML = `<option value="">${defaultText}</option>`;
                selectElement.disabled = true;
            }

            function getFlagEmoji(countryCode) {
                if (!/^[A-Z]{2}$/.test(countryCode)) return '';
                return String.fromCodePoint(...countryCode.toUpperCase().split('').map(c => 127397 + c.charCodeAt(0)));
            }
            function formatDisplayName(name) {
                const specialReplacements = { 'ss': 'SHADOWSOCKS' };
                const uppercaseTypes = ['vless', 'vmess', 'trojan', 'ssr', 'ws', 'grpc', 'reality', 'hy2', 'hysteria2', 'tuic'];
                const protocolPrefixes = ['ss', 'ssr'];
                const parts = name.split(/[-_]/);
                let flag = '';
                if (parts.length > 0 && !protocolPrefixes.includes(parts[0].toLowerCase())) {
                    flag = getFlagEmoji(parts[0].toUpperCase());
                }
                const displayNameParts = parts.map((part, index) => {
                    const lowerPart = part.toLowerCase();
                    if (specialReplacements[lowerPart]) return specialReplacements[lowerPart];
                    if (uppercaseTypes.includes(lowerPart)) return part.toUpperCase();
                    if (index === 0 && flag) return part.toUpperCase();
                    return part.charAt(0).toUpperCase() + part.slice(1).toLowerCase();
                });
                const textName = displayNameParts.join(' ');
                return flag ? `${flag} ${textName}` : textName;
            }
            function updateQRCode(url) {
                qrcodeDiv.innerHTML = '';
                if (url) {
                    try { new QRCode(qrcodeDiv, { text: url, width: 128, height: 128 }); } catch (e) { console.error(e); }
                }
            }
            function parseBase64Subscription(content) {
                let decodedContent; try { decodedContent = atob(content); } catch (e) { decodedContent = content; }
                const protocols = ['vmess://', 'vless://', 'ss://', 'trojan://', 'ssr://', 'hy2://', 'tuic://'];
                return decodedContent.split('\n').filter(line => {
                    const trimmedLine = line.trim();
                    if (trimmedLine.startsWith('#') || trimmedLine.startsWith('//') || trimmedLine.length === 0) return false;
                    return protocols.some(proto => trimmedLine.startsWith(proto));
                }).length;
            }
            function parseClashSubscription(content) {
                const match = content.match(/^\s*proxies:\s*\n((?:\s*-\s*.+\n?)*)/m);
                if (!match || !match[1]) return 0;
                const nodeMatches = match[1].match(/^\s*-\s/gm);
                return nodeMatches ? nodeMatches.length : 0;
            }
            function parseSingboxSubscription(content) {
                try {
                    const data = JSON.parse(content.replace(/^\s*\/\/.*\r?\n/gm, ''));
                    if (!data.outbounds || !Array.isArray(data.outbounds)) return 0;
                    const utilityTypes = ['selector', 'urltest', 'direct', 'block', 'dns', 'fallback'];
                    return data.outbounds.filter(o => o.type && !utilityTypes.includes(o.type.toLowerCase())).length;
                } catch (e) { return 0; }
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
                clientInfoList.innerHTML = '';
                const platforms = clientInfoData[coreType];
                if (!platforms || Object.keys(platforms).length === 0) {
                    clientInfoContainer.classList.add('hidden');
                    return;
                }
                clientInfoContainer.classList.remove('hidden');
                Object.entries(platforms).forEach(([platformName, clients]) => {
                    if (clients.length > 0) {
                        const platformContainer = document.createElement('div');
                        const title = document.createElement('h4');
                        title.className = 'text-sm font-semibold text-slate-600 mb-2';
                        title.textContent = platformName.charAt(0).toUpperCase() + platformName.slice(1);
                        platformContainer.appendChild(title);
                        const linksContainer = document.createElement('div');
                        linksContainer.className = 'flex flex-col gap-2';
                        clients.forEach(client => {
                            const link = document.createElement('a');
                            link.href = client.url;
                            link.target = '_blank';
                            link.rel = 'noopener noreferrer';
                            link.className = 'flex items-center justify-between p-2.5 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors duration-200 text-slate-700 hover:text-indigo-600';
                            const nameSpan = document.createElement('span');
                            nameSpan.className = 'font-medium text-sm';
                            nameSpan.textContent = client.name;
                            const icon = document.createElement('i');
                            icon.setAttribute('data-lucide', 'download');
                            icon.className = 'w-4 h-4 text-slate-500';
                            link.appendChild(nameSpan);
                            link.appendChild(icon);
                            linksContainer.appendChild(link);
                        });
                        platformContainer.appendChild(linksContainer);
                        clientInfoList.appendChild(platformContainer);
                    }
                });
                try {
                    lucide.createIcons();
                } catch (e) {
                    console.error(e);
                }
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
                        }
                        return CUSTOM_SORT_ORDER.length;
                    };
                    const filteredAndSortedKeys = Object.keys(allElements)
                        .filter(key => formatDisplayName(key).toLowerCase().includes(searchTerm))
                        .sort((a, b) => {
                            const indexA = getSortIndex(a);
                            const indexB = getSortIndex(b);
                            if (indexA !== indexB) return indexA - indexB;
                            return a.localeCompare(b);
                        });

                    populateSelect(otherElementSelect, filteredAndSortedKeys, filteredAndSortedKeys.length > 0 ? 'Select Subscription' : 'No matches found');
                    otherElementSelect.disabled = false;
                }
            }
            
            // --- 3. EVENT LISTENERS SETUP ---
            analyzeButton.addEventListener('click', async () => {
                const url = subscriptionUrlInput.value;
                const clientCore = ipTypeSelect.value;
                if (!url) return;
                analysisContainer.classList.remove('hidden');
                analysisResults.innerHTML = '<p class="flex items-center gap-2"><i data-lucide="loader-2" class="animate-spin"></i> Analyzing subscription...</p>';
                lucide.createIcons();
                try {
                    const response = await fetch(url);
                    if (!response.ok) throw new Error(`Network error: ${response.statusText}`);
                    const content = await response.text();
                    let nodeCount = 0;
                    switch (clientCore.toLowerCase()) {
                        case 'xray': nodeCount = parseBase64Subscription(content); break;
                        case 'clash': nodeCount = parseClashSubscription(content); break;
                        case 'singbox': nodeCount = parseSingboxSubscription(content); break;
                        case 'surfboard': nodeCount = parseSurfboardSubscription(content); break;
                        default: throw new Error('Analysis for this client type is not supported.');
                    }
                    analysisResults.innerHTML = `<p class="font-medium text-green-700">✅ Analysis Complete</p><p><strong>Total Proxies Found:</strong> <span class="font-bold text-lg">${nodeCount}</span></p>`;
                } catch (error) {
                    analysisResults.innerHTML = `<p class="font-medium text-red-700">❌ Analysis Failed</p><p>${error.message}</p>`;
                }
            });

            configTypeSelect.addEventListener('change', () => {
                resetSelect(ipTypeSelect, 'Select Client/Core');
                resetSelect(otherElementSelect, 'Select Subscription');
                searchBar.value = '';
                searchBar.disabled = true;
                resultArea.classList.add('hidden');
                if (configTypeSelect.value) {
                    const keys = Object.keys(structuredData[configTypeSelect.value] || {});
                    populateSelect(ipTypeSelect, keys, 'Select Client/Core');
                    ipTypeSelect.disabled = false;
                }
            });
            
            ipTypeSelect.addEventListener('change', () => {
                const selectedCore = ipTypeSelect.value;
                searchBar.value = '';
                if (selectedCore) {
                    updateClientInfo(selectedCore);
                    resultArea.classList.remove('hidden');
                    subscriptionDetailsContainer.classList.add('hidden');
                    analysisContainer.classList.add('hidden');
                    searchBar.disabled = false;
                    updateOtherElementOptions();
                } else {
                    resultArea.classList.add('hidden');
                    searchBar.disabled = true;
                    resetSelect(otherElementSelect, 'Select Subscription');
                }
            });

            searchBar.addEventListener('input', updateOtherElementOptions);

            otherElementSelect.addEventListener('change', () => {
                analysisContainer.classList.add('hidden');
                const url = structuredData[configTypeSelect.value]?.[ipTypeSelect.value]?.[otherElementSelect.value];
                if (url) {
                    subscriptionUrlInput.value = url;
                    updateQRCode(url);
                    subscriptionDetailsContainer.classList.remove('hidden');
                } else {
                    subscriptionDetailsContainer.classList.add('hidden');
                }
            });

            copyButton.addEventListener('click', () => {
                if (!subscriptionUrlInput.value) { showMessageBox('No URL to copy.'); return; }
                document.execCommand('copy');
                const copyIcon = copyButton.querySelector('.copy-icon');
                const checkIcon = copyButton.querySelector('.check-icon');
                copyIcon.classList.add('hidden');
                checkIcon.classList.remove('hidden');
                setTimeout(() => {
                    copyIcon.classList.remove('hidden');
                    checkIcon.classList.add('hidden');
                }, 2000);
            });
            document.addEventListener('copy', (event) => {
                if (subscriptionUrlInput.value && event.clipboardData) {
                    event.clipboardData.setData('text/plain', subscriptionUrlInput.value);
                    event.preventDefault();
                }
            });

            // --- 4. INITIAL PAGE SETUP ---
            populateSelect(configTypeSelect, Object.keys(structuredData), 'Select Config Type');
            configTypeSelect.disabled = false;
        });
    </script>
</body>
</html>
HTML;

    return str_replace('__JSON_DATA_PLACEHOLDER__', $json_structured_data, $html_template);
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
if ($file_count === 0) { die("Error: No subscription files were found to generate the page. Exiting." . PHP_EOL); }
echo "Found and categorized {$file_count} subscription files." . PHP_EOL;
$structured_data = process_files_to_structure($all_files);
$client_info = get_client_info();
date_default_timezone_set('Asia/Tehran'); 
$timestamp = date('Y-m-d H:i:s T');
$final_html = generate_full_html($structured_data, $client_info, $timestamp);
file_put_contents(OUTPUT_HTML_FILE, $final_html);
echo "Successfully generated page at: " . realpath(OUTPUT_HTML_FILE) . PHP_EOL;
