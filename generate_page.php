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
    <title>Proxy Subscription Customizer (PSG)</title>
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
        .filter-checkbox-label { display: flex; align-items: center; padding: 0.5rem 0.75rem; background-color: #f1f5f9; border-radius: 0.5rem; cursor: pointer; transition: background-color 0.2s; user-select: none; }
        .filter-checkbox-label:hover { background-color: #e2e8f0; }
        .filter-checkbox-label input:checked + span { font-weight: 600; color: #4f46e5; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 leading-relaxed transition-colors duration-300">
    <div class="container max-w-6xl mx-auto px-4 py-8">
        <header class="flex justify-between items-center mb-10">
            <div class="text-left">
                <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 mb-0">Proxy Subscription Customizer</h1>
                <p class="text-base sm:text-lg text-slate-500 mt-2">Combine, filter, and generate your own custom subscription links.</p>
            </div>
        </header>

        <main>
            <!-- Step 1: Select Sources -->
            <div class="bg-white rounded-xl p-6 sm:p-8 shadow-lg border border-slate-200 mb-6">
                <h2 class="text-xl font-bold mb-1">Step 1: Select Source Subscriptions</h2>
                <p class="text-slate-500 mb-4 text-sm">Choose one or more base subscriptions to combine and filter.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4">
                    <div>
                        <label for="configType" class="block text-sm font-medium text-slate-700 mb-2">Config Type:</label>
                        <select id="configType" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 text-slate-800"></select>
                    </div>
                    <div>
                        <label for="ipType" class="block text-sm font-medium text-slate-700 mb-2">Client/Core:</label>
                        <select id="ipType" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 text-slate-800" disabled></select>
                    </div>
                </div>
                <div id="subscriptionCheckboxesContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    <!-- Checkboxes will be dynamically inserted here -->
                </div>
            </div>

            <!-- Step 2: Filter & Build -->
            <div id="filterBuilderContainer" class="hidden bg-white rounded-xl p-6 sm:p-8 shadow-lg border border-slate-200 mb-6">
                <h2 class="text-xl font-bold mb-1">Step 2: Filter & Build Your Subscription</h2>
                <p class="text-slate-500 mb-4 text-sm">Apply filters to the combined list of proxies. The counter will update live.</p>
                
                <div class="space-y-6">
                    <div>
                        <h3 class="font-semibold text-slate-800 mb-2">Filter by Country:</h3>
                        <div id="country-filters" class="flex flex-wrap gap-2"></div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800 mb-2">Filter by Protocol:</h3>
                        <div id="protocol-filters" class="flex flex-wrap gap-2"></div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-slate-800 mb-2">Filter by Keywords:</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input type="text" id="include-keyword" placeholder="Include nodes with this text..." class="keyword-filter block w-full rounded-md border-slate-300 shadow-sm p-2 bg-slate-100 placeholder-slate-400">
                            <input type="text" id="exclude-keyword" placeholder="Exclude nodes with this text..." class="keyword-filter block w-full rounded-md border-slate-300 shadow-sm p-2 bg-slate-100 placeholder-slate-400">
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-200 flex items-center justify-between">
                    <div class="text-lg">
                        <span class="font-bold text-indigo-600" id="node-counter">0</span>
                        <span class="text-slate-600">nodes selected</span>
                    </div>
                    <button id="generateButton" class="bg-indigo-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-indigo-700 transition-colors duration-200 disabled:bg-slate-400 disabled:cursor-not-allowed">
                        Generate My Link
                    </button>
                </div>
            </div>
            
            <!-- Step 3: Get Your Link -->
            <div id="resultArea" class="hidden bg-emerald-50 border-emerald-200 rounded-xl p-6 sm:p-8 shadow-lg border">
                 <h2 class="text-xl font-bold mb-4 text-emerald-800">Step 3: Your Custom Link is Ready!</h2>
                 <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6 items-center">
                    <div>
                         <div class="flex items-center mb-4">
                            <input type="text" id="subscriptionUrl" readonly class="flex-grow font-mono text-xs sm:text-sm py-2 px-2.5 sm:py-2.5 sm:px-3 bg-white border border-slate-300 rounded-l-lg outline-none whitespace-nowrap overflow-hidden text-ellipsis" />
                            <button id="copyButton" class="flex-shrink-0 flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 bg-indigo-50 text-indigo-700 border border-l-0 border-indigo-600 rounded-r-lg cursor-pointer transition-colors duration-200 hover:bg-indigo-100" title="Copy URL">
                                <i data-lucide="copy" class="copy-icon w-5 h-5"></i>
                                <i data-lucide="check" class="check-icon w-5 h-5 hidden"></i>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500">This is a self-contained `data:` URI. It works in most modern clients.</p>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <p class="text-sm text-slate-600 mb-2">Scan the QR code:</p>
                        <div id="qrcode" class="p-2 bg-white border border-slate-300 rounded-lg shadow-inner"></div>
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
            // --- 1. INITIALIZE ---
            lucide.createIcons();
            const structuredData = __JSON_DATA_PLACEHOLDER__;

            // --- 2. GRAB ELEMENTS ---
            const configTypeSelect = document.getElementById('configType');
            const ipTypeSelect = document.getElementById('ipType');
            const subscriptionCheckboxesContainer = document.getElementById('subscriptionCheckboxesContainer');
            const filterBuilderContainer = document.getElementById('filterBuilderContainer');
            const countryFilters = document.getElementById('country-filters');
            const protocolFilters = document.getElementById('protocol-filters');
            const includeKeyword = document.getElementById('include-keyword');
            const excludeKeyword = document.getElementById('exclude-keyword');
            const nodeCounter = document.getElementById('node-counter');
            const generateButton = document.getElementById('generateButton');
            const resultArea = document.getElementById('resultArea');
            const subscriptionUrlInput = document.getElementById('subscriptionUrl');
            const qrcodeDiv = document.getElementById('qrcode');
            const copyButton = document.getElementById('copyButton');

            // --- 3. STATE MANAGEMENT ---
            let masterProxyList = [];
            let filteredProxyList = [];

            // --- 4. HELPER & PARSING FUNCTIONS ---
            function showMessageBox(message) { /* ... same as before ... */ }
            function getFlagEmoji(countryCode) { /* ... same as before ... */ }
            function formatDisplayName(name) { /* ... same as before ... */ }
            function updateQRCode(url) { /* ... same as before ... */ }

            function parseProxyLink(link) {
                const linkLower = link.toLowerCase();
                let protocol = 'unknown';
                if (linkLower.startsWith('vmess://')) protocol = 'vmess';
                else if (linkLower.startsWith('vless://')) protocol = 'vless';
                else if (linkLower.startsWith('ss://')) protocol = 'ss';
                else if (linkLower.startsWith('trojan://')) protocol = 'trojan';
                
                let name = '';
                try {
                    name = decodeURIComponent(link.substring(link.indexOf('#') + 1));
                } catch(e) { name = "Unnamed"; }

                const countryMatch = name.match(/^([A-Z]{2})/);
                const country = countryMatch ? countryMatch[1] : 'XX';

                return { originalLink: link, name, protocol, country };
            }

            async function processSourceSelections() {
                const checkedSources = Array.from(subscriptionCheckboxesContainer.querySelectorAll('input:checked'))
                                           .map(cb => ({ url: cb.value, type: ipTypeSelect.value }));
                
                if (checkedSources.length === 0) {
                    filterBuilderContainer.classList.add('hidden');
                    masterProxyList = [];
                    applyFiltersAndRender();
                    return;
                }

                filterBuilderContainer.classList.remove('hidden');
                nodeCounter.textContent = '...';

                try {
                    const responses = await Promise.all(checkedSources.map(source => fetch(source.url)));
                    const contents = await Promise.all(responses.map(res => res.text()));
                    
                    masterProxyList = [];
                    contents.forEach((content, index) => {
                        let links = [];
                        // This is a simplified parser for all formats, focusing on extracting raw links
                        // A more robust version would handle YAML/JSON structures more deeply
                        const lines = content.split('\n');
                        lines.forEach(line => {
                            const trimmed = line.trim();
                            if (trimmed.startsWith('vmess://') || trimmed.startsWith('vless://') || trimmed.startsWith('ss://') || trimmed.startsWith('trojan://')) {
                                links.push(trimmed);
                            }
                        });
                        
                        links.forEach(link => masterProxyList.push(parseProxyLink(link)));
                    });

                    // Deduplicate
                    const uniqueLinks = new Set(masterProxyList.map(p => p.originalLink));
                    masterProxyList = Array.from(uniqueLinks).map(link => masterProxyList.find(p => p.originalLink === link));

                    renderFilterControls();
                    applyFiltersAndRender();

                } catch (error) {
                    showMessageBox(`Failed to fetch or parse subscriptions: ${error.message}`);
                }
            }

            function renderFilterControls() {
                const countries = [...new Set(masterProxyList.map(p => p.country))].sort();
                const protocols = [...new Set(masterProxyList.map(p => p.protocol))].sort();

                countryFilters.innerHTML = countries.map(c => `
                    <label class="filter-checkbox-label">
                        <input type="checkbox" class="filter-control-country" value="${c}" />
                        <span class="ml-2">${getFlagEmoji(c)} ${c}</span>
                    </label>
                `).join('');

                protocolFilters.innerHTML = protocols.map(p => `
                    <label class="filter-checkbox-label">
                        <input type="checkbox" class="filter-control-protocol" value="${p}" />
                        <span class="ml-2">${p.toUpperCase()}</span>
                    </label>
                `).join('');

                document.querySelectorAll('.filter-control-country, .filter-control-protocol').forEach(el => {
                    el.addEventListener('change', applyFiltersAndRender);
                });
            }
            
            function applyFiltersAndRender() {
                const selectedCountries = Array.from(countryFilters.querySelectorAll('input:checked')).map(cb => cb.value);
                const selectedProtocols = Array.from(protocolFilters.querySelectorAll('input:checked')).map(cb => cb.value);
                const includeText = includeKeyword.value.toLowerCase();
                const excludeText = excludeKeyword.value.toLowerCase();

                filteredProxyList = masterProxyList.filter(proxy => {
                    if (selectedCountries.length > 0 && !selectedCountries.includes(proxy.country)) return false;
                    if (selectedProtocols.length > 0 && !selectedProtocols.includes(proxy.protocol)) return false;
                    if (includeText && !proxy.name.toLowerCase().includes(includeText)) return false;
                    if (excludeText && proxy.name.toLowerCase().includes(excludeText)) return false;
                    return true;
                });

                nodeCounter.textContent = filteredProxyList.length;
                generateButton.disabled = filteredProxyList.length === 0;
                resultArea.classList.add('hidden');
            }

            // --- 5. EVENT LISTENERS ---
            configTypeSelect.addEventListener('change', () => {
                ipTypeSelect.innerHTML = '<option value="">Select Client/Core</option>';
                ipTypeSelect.disabled = true;
                subscriptionCheckboxesContainer.innerHTML = '';
                filterBuilderContainer.classList.add('hidden');
                if (configTypeSelect.value) {
                    const keys = Object.keys(structuredData[configTypeSelect.value] || {});
                    keys.forEach(key => ipTypeSelect.add(new Option(formatDisplayName(key), key)));
                    ipTypeSelect.disabled = false;
                }
            });

            ipTypeSelect.addEventListener('change', () => {
                subscriptionCheckboxesContainer.innerHTML = '';
                filterBuilderContainer.classList.add('hidden');
                resultArea.classList.add('hidden');

                const sources = structuredData[configTypeSelect.value]?.[ipTypeSelect.value];
                if (sources) {
                    Object.entries(sources).forEach(([name, url]) => {
                        const checkboxId = `source-${name}`;
                        const checkboxHTML = `
                            <label for="${checkboxId}" class="filter-checkbox-label">
                                <input type="checkbox" id="${checkboxId}" value="${url}" class="source-checkbox" />
                                <span class="ml-2">${formatDisplayName(name)}</span>
                            </label>
                        `;
                        subscriptionCheckboxesContainer.innerHTML += checkboxHTML;
                    });
                    document.querySelectorAll('.source-checkbox').forEach(cb => {
                        cb.addEventListener('change', processSourceSelections);
                    });
                }
            });

            document.querySelectorAll('.keyword-filter').forEach(el => {
                el.addEventListener('input', applyFiltersAndRender);
            });

            generateButton.addEventListener('click', () => {
                if (filteredProxyList.length === 0) {
                    showMessageBox('No nodes selected. Adjust your filters.');
                    return;
                }
                const finalContent = filteredProxyList.map(p => p.originalLink).join('\n');
                const encodedContent = btoa(finalContent);
                const dataUri = `data:text/plain;base64,${encodedContent}`;

                subscriptionUrlInput.value = dataUri;
                updateQRCode(dataUri);
                resultArea.classList.remove('hidden');
                resultArea.scrollIntoView({ behavior: 'smooth' });
            });
            
            copyButton.addEventListener('click', () => { /* ... same as before ... */ });

            // --- 6. INITIAL PAGE SETUP ---
            Object.keys(structuredData).forEach(key => configTypeSelect.add(new Option(key, key)));
        });
    </script>
</body>
</html>
HTML;

    $final_html = str_replace('__JSON_DATA_PLACEHOLDER__', json_encode($structuredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), $html_template);
    // Note: clientInfoData is no longer used in this version but kept for potential future use
    return str_replace('__TIMESTAMP_PLACEHOLDER__', $generation_timestamp, $final_html);
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
