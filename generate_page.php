<?php

declare(strict_types=1);

/**
 * Proxy Subscription Generator (PSG) Page Builder
 *
 * Scans subscription directories and generates a modern, fully functional index.html.
 * This script includes a Search/Filter bar, Last Generated timestamp,
 * and uses the Lucide icon library for a clean, modern look.
 */

// --- Configuration ---
define('PROJECT_ROOT', __DIR__);
define('GITHUB_REPO_URL', 'https://raw.githubusercontent.com/itsyebekhe/PSG/main');
define('OUTPUT_HTML_FILE', PROJECT_ROOT . '/index.html');
define('SCAN_DIRECTORIES', [
    'Standard' => PROJECT_ROOT . '/subscriptions',
    'Lite' => PROJECT_ROOT . '/lite/subscriptions',
]);

// --- NEW: Client Information Data Source (REVISED) ---
/**
 * Provides a mapping of subscription core/client types to compatible applications
 * for various platforms, including their download URLs.
 *
 * The keys (e.g., 'clash', 'sing-box') should match the subdirectory names
 * under '/subscriptions/' and '/lite/subscriptions/'.
 *
 * @return array
 */
function get_client_info(): array
{
    return [
        'clash' => [
            'windows' => [
                ['name' => 'Clash Verge', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest'],
                ['name' => 'Clash for Windows', 'url' => 'https://github.com/Fndroid/clash_for_windows_pkg/releases/latest'],
            ],
            'macos' => [
                ['name' => 'Clash Verge', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest'],
                ['name' => 'ClashX', 'url' => 'https://github.com/yichengchen/clashX/releases/latest'],
            ],
            'android' => [
                ['name' => 'Clash for Android', 'url' => 'https://github.com/Kr328/ClashForAndroid/releases/latest'],
            ],
            'ios' => [
                ['name' => 'Stash', 'url' => 'https://apps.apple.com/us/app/stash/id1596063349'],
            ],
            'linux' => [
                ['name' => 'Clash Verge', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest'],
            ]
        ],
        'sing-box' => [
            'windows' => [
                ['name' => 'NekoRay', 'url' => 'https://github.com/MatsuriDayo/nekoray/releases/latest'],
                ['name' => 'Hiddify-Next', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest'],
            ],
            'macos' => [
                ['name' => 'NekoRay', 'url' => 'https://github.com/MatsuriDayo/nekoray/releases/latest'],
                ['name' => 'Hiddify-Next', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest'],
            ],
            'android' => [
                ['name' => 'NekoBox', 'url' => 'https://github.com/MatsuriDayo/NekoBoxForAndroid/releases/latest'],
                ['name' => 'Hiddify-Next', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest'],
            ],
            'ios' => [
                ['name' => 'Stash', 'url' => 'https://apps.apple.com/us/app/stash/id1596063349'],
                ['name' => 'Streisand', 'url' => 'https://apps.apple.com/us/app/streisand/id6450534064'],
            ],
            'linux' => [
                ['name' => 'NekoRay', 'url' => 'https://github.com/MatsuriDayo/nekoray/releases/latest'],
                ['name' => 'Hiddify-Next', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest'],
            ]
        ],
        'v2ray' => [ // For V2Ray/Xray-core compatible subscription links (standard base64)
            'windows' => [
                ['name' => 'v2rayN', 'url' => 'https://github.com/2dust/v2rayN/releases/latest'],
                ['name' => 'NekoRay', 'url' => 'https://github.com/MatsuriDayo/nekoray/releases/latest'],
            ],
            'macos' => [
                ['name' => 'V2RayU', 'url' => 'https://github.com/yanue/V2rayU/releases/latest'],
                ['name' => 'NekoRay', 'url' => 'https://github.com/MatsuriDayo/nekoray/releases/latest'],
            ],
            'android' => [
                ['name' => 'v2rayNG', 'url' => 'https://github.com/2dust/v2rayNG/releases/latest'],
                ['name' => 'NekoBox', 'url' => 'https://github.com/MatsuriDayo/NekoBoxForAndroid/releases/latest'],
            ],
            'ios' => [
                ['name' => 'Shadowrocket', 'url' => 'https://apps.apple.com/us/app/shadowrocket/id932747118'],
                ['name' => 'Stash', 'url' => 'https://apps.apple.com/us/app/stash/id1596063349'],
                ['name' => 'Streisand', 'url' => 'https://apps.apple.com/us/app/streisand/id6450534064'],
            ],
            'linux' => [
                ['name' => 'NekoRay', 'url' => 'https://github.com/MatsuriDayo/nekoray/releases/latest'],
                ['name' => 'Qv2ray', 'url' => 'https://github.com/Qv2ray/Qv2ray/releases/latest'],
            ]
        ],
        'surfboard' => [
            'android' => [
                ['name' => 'Surfboard', 'url' => 'https://play.google.com/store/apps/details?id=com.getsurfboard'],
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
            $parts = explode('/', $relative_path_from_base);
            if (count($parts) < 2) continue;
            // The 'type' is now the core/client type, e.g., 'clash', 'sing-box'
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
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script type="text/javascript">
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
                        <div>
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
                        </div>
                        <div class="space-y-4">
                           <h3 class="text-lg sm:text-xl font-semibold text-slate-800 mb-2">Compatible Clients & Downloads:</h3>
                           <div id="client-info-list" class="space-y-5">
                               <!-- Client links will be grouped by platform and injected here -->
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
            try { lucide.createIcons(); } catch (error) { console.error('Lucide icons initialization failed:', error); }

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

            function showMessageBox(message) {
                const box = document.getElementById('messageBox');
                document.getElementById('messageBoxText').textContent = message;
                box.classList.remove('hidden');
                document.getElementById('messageBoxClose').onclick = () => box.classList.add('hidden');
            }
            function populateSelect(selectElement, data, defaultOptionText) {
                selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`;
                Object.keys(data).forEach(key => {
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
            function formatDisplayName(name) {
                return name.split(/[-_]/).map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
            }
            function updateQRCode(url) {
                qrcodeDiv.innerHTML = '';
                if (url) {
                    try {
                        new QRCode(qrcodeDiv, {
                            text: url, width: 128, height: 128,
                            colorDark: "#000000", colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } catch (error) { console.error('QR code initialization failed:', error); }
                }
            }
            
            // --- NEW: Function to display compatible clients, grouped by platform ---
            function updateClientInfo(coreType) {
                clientInfoList.innerHTML = '';
                const platforms = clientInfoData[coreType];

                if (!platforms || Object.keys(platforms).length === 0) {
                    clientInfoList.innerHTML = '<p class="text-sm text-slate-500">No specific client information available for this type.</p>';
                    return;
                }

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

                try { lucide.createIcons(); } catch(e) { console.error(e); }
            }

            function updateOtherElementOptions() {
                const selectedConfigType = configTypeSelect.value;
                const selectedIpType = ipTypeSelect.value;
                const searchTerm = searchBar.value.toLowerCase();
                resetSelect(otherElementSelect, 'Select Subscription');
                resultArea.classList.add('hidden');
                if (selectedIpType && structuredData[selectedConfigType]?.[selectedIpType]) {
                    const allElements = structuredData[selectedConfigType][selectedIpType];
                    const filteredElements = Object.keys(allElements)
                        .filter(key => formatDisplayName(key).toLowerCase().includes(searchTerm))
                        .reduce((obj, key) => { (obj[key] = allElements[key]); return obj; }, {});
                    populateSelect(otherElementSelect, filteredElements, Object.keys(filteredElements).length > 0 ? 'Select Subscription' : 'No matches found');
                    otherElementSelect.disabled = false;
                    const options = Object.keys(filteredElements);
                    if (options.length === 1) {
                        otherElementSelect.value = options[0];
                        otherElementSelect.dispatchEvent(new Event('change'));
                    }
                }
            }
            configTypeSelect.addEventListener('change', () => {
                resetSelect(ipTypeSelect, 'Select Client/Core');
                resetSelect(otherElementSelect, 'Select Subscription');
                resultArea.classList.add('hidden');
                searchBar.value = '';
                searchBar.disabled = true;
                if (configTypeSelect.value && structuredData[configTypeSelect.value]) {
                    populateSelect(ipTypeSelect, structuredData[configTypeSelect.value], 'Select Client/Core');
                    ipTypeSelect.disabled = false;
                }
            });
            ipTypeSelect.addEventListener('change', () => {
                searchBar.disabled = !ipTypeSelect.value;
                searchBar.value = '';
                updateOtherElementOptions();
            });
            searchBar.addEventListener('input', updateOtherElementOptions);
            otherElementSelect.addEventListener('change', () => {
                const url = structuredData[configTypeSelect.value]?.[ipTypeSelect.value]?.[otherElementSelect.value] || null;
                if (url) {
                    subscriptionUrlInput.value = url;
                    updateQRCode(url);
                    updateClientInfo(ipTypeSelect.value); // Pass the core type (e.g., 'clash')
                    resultArea.classList.remove('hidden');
                } else {
                    resultArea.classList.add('hidden');
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
            populateSelect(configTypeSelect, structuredData, 'Select Config Type');
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
if ($file_count === 0) { die("Error: No subscription files were found to generate the page. Exiting." . PHP_EOL); }
echo "Found and categorized {$file_count} subscription files." . PHP_EOL;
$structured_data = process_files_to_structure($all_files);
$client_info = get_client_info();
date_default_timezone_set('Asia/Tehran'); 
$timestamp = date('Y-m-d H:i:s T');
$final_html = generate_full_html($structured_data, $client_info, $timestamp);
file_put_contents(OUTPUT_HTML_FILE, $final_html);
echo "Successfully generated page at: " . realpath(OUTPUT_HTML_FILE) . PHP_EOL;
