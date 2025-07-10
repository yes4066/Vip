<?php

declare(strict_types=1);

/**
 * Proxy Subscription Generator (PSG) Page Builder
 *
 * Scans subscription directories and generates a modern, fully functional index.html.
 * This script includes a Search/Filter bar, Last Generated timestamp, and a fully responsive,
 * universal "Subscription DNA" feature with advanced charts for all subscription types.
 */

// --- Configuration ---
define('PROJECT_ROOT', __DIR__);
define('GITHUB_REPO_URL', 'https://raw.githubusercontent.com/itsyebekhe/PSG/main');
define('OUTPUT_HTML_FILE', PROJECT_ROOT . '/index.html');
define('SCAN_DIRECTORIES', [
    'Standard' => PROJECT_ROOT . '/subscriptions',
    'Lite' => PROJECT_ROOT . '/lite/subscriptions',
]);

function get_client_info(): array { /* ... unchanged ... */ return [ 'clash' => [ 'windows' => [['name' => 'Clash Verge (Rev) - x64 Installer', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_x64-setup.exe'],['name' => 'Clash Verge (Rev) - ARM64 Installer', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_arm64-setup.msi']], 'macos' => [['name' => 'Clash Verge (Rev) - Apple Silicon', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_aarch64.dmg'],['name' => 'ClashX - Universal', 'url' => 'https://github.com/yichengchen/clashX/releases/latest/download/ClashX.dmg']], 'android' => [['name' => 'Clash for Android (CFA) - arm64-v8a', 'url' => 'https://github.com/Kr328/ClashForAndroid/releases/latest/download/cfa-2.5.12-premium-arm64-v8a-release.apk']], 'ios' => [['name' => 'Stash (Recommended for Clash)', 'url' => 'https://apps.apple.com/us/app/stash/id1596063349']], 'linux' => [['name' => 'Clash Verge (Rev) - amd64 (.deb)', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/clash-verge_1.6.8_amd64.deb']] ], 'meta' => [ 'windows' => [['name' => 'Clash Verge (Rev) - x64 Installer', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_x64-setup.exe']], 'macos' => [['name' => 'Clash Verge (Rev) - Apple Silicon', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/Clash.Verge_1.6.8_aarch64.dmg']], 'android' => [['name' => 'Clash for Android (CFA) - arm64-v8a', 'url' => 'https://github.com/Kr328/ClashForAndroid/releases/latest/download/cfa-2.5.12-premium-arm64-v8a-release.apk']], 'ios' => [['name' => 'Stash (Recommended for Clash Meta)', 'url' => 'https://apps.apple.com/us/app/stash/id1596063349']], 'linux' => [['name' => 'Clash Verge (Rev) - amd64 (.deb)', 'url' => 'https://github.com/clash-verge-rev/clash-verge-rev/releases/latest/download/clash-verge_1.6.8_amd64.deb']] ], 'location' => [ 'windows' => [['name' => 'v2rayN (with Xray core)', 'url' => 'https://github.com/2dust/v2rayN/releases/latest/download/v2rayN-With-Core.zip']], 'android' => [['name' => 'v2rayNG - arm64-v8a', 'url' => 'https://github.com/2dust/v2rayNG/releases/latest/download/v2rayNG_1.8.19_arm64-v8a.apk']], 'ios' => [['name' => 'Shadowrocket (Classic Choice)', 'url' => 'https://apps.apple.com/us/app/shadowrocket/id932747118']] ], 'singbox' => [ 'windows' => [['name' => 'Hiddify-Next - x64 Installer', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Windows-x64-Setup.exe']], 'macos' => [['name' => 'Hiddify-Next - Universal', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-MacOS.dmg']], 'android' => [['name' => 'Hiddify-Next - Universal', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Android-universal.apk']], 'ios' => [['name' => 'Streisand (Recommended for Sing-Box)', 'url' => 'https://apps.apple.com/us/app/streisand/id6450534064']], 'linux' => [['name' => 'Hiddify-Next - x64 (.AppImage)', 'url' => 'https://github.com/hiddify/hiddify-next/releases/latest/download/Hiddify-Linux-x64.AppImage']] ], 'surfboard' => [ 'android' => [['name' => 'Surfboard (Google Play)', 'url' => 'https://play.google.com/store/apps/details?id=com.getsurfboard']] ], 'xray' => [ 'windows' => [['name' => 'v2rayN (with Xray core)', 'url' => 'https://github.com/2dust/v2rayN/releases/latest/download/v2rayN-With-Core.zip']], 'android' => [['name' => 'v2rayNG - arm64-v8a', 'url' => 'https://github.com/2dust/v2rayNG/releases/latest/download/v2rayNG_1.8.19_arm64-v8a.apk']], 'ios' => [['name' => 'Shadowrocket (Classic Choice)', 'url' => 'https://apps.apple.com/us/app/shadowrocket/id932747118']] ]]; }
function scan_directory(string $dir): array { /* ... unchanged ... */ if (!is_dir($dir)) { return []; } $files = []; $iterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST ); $ignoreExtensions = ['php', 'md', 'ini', 'txt', 'log', 'conf']; foreach ($iterator as $file) { if ($file->isFile() && !in_array(strtolower($file->getExtension()), $ignoreExtensions)) { $relativePath = str_replace(PROJECT_ROOT . DIRECTORY_SEPARATOR, '', $file->getRealPath()); $files[] = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath); } } return $files; }
function process_files_to_structure(array $files_by_category): array { /* ... unchanged ... */ $structure = []; foreach (SCAN_DIRECTORIES as $category_key => $category_dir_path) { $base_dir_relative = ltrim(str_replace(PROJECT_ROOT, '', $category_dir_path), DIRECTORY_SEPARATOR); $base_dir_relative = str_replace(DIRECTORY_SEPARATOR, '/', $base_dir_relative); if (!isset($files_by_category[$category_key])) { continue; } foreach ($files_by_category[$category_key] as $path) { $relative_path_from_base = str_replace($base_dir_relative . '/', '', $path); $path_for_parsing = $relative_path_from_base; if (strpos($path_for_parsing, 'xray/') === 0) { if (strpos($path_for_parsing, 'xray/base64/') !== 0) continue; $path_for_parsing = str_replace('xray/base64/', 'xray/', $path_for_parsing); } elseif (strpos($path_for_parsing, 'location/') === 0) { if (strpos($path_for_parsing, 'location/base64/') !== 0) continue; $path_for_parsing = str_replace('location/base64/', 'location/', $path_for_parsing); } $parts = explode('/', $path_for_parsing); if (count($parts) < 2) continue; $type = array_shift($parts); $name = pathinfo(implode('/', $parts), PATHINFO_FILENAME); $url = GITHUB_REPO_URL . '/' . $path; $structure[$category_key][$type][$name] = $url; } } foreach ($structure as &$categories) { ksort($categories); foreach ($categories as &$elements) { ksort($elements); } } ksort($structure); return $structure; }

/**
 * Generates the complete HTML content for the PSG page.
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
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-8 items-start">
                        <!-- Left Column: Subscription Details -->
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
                        <!-- Right Column: Client Info -->
                        <div id="client-info-container">
                           <h3 class="text-lg sm:text-xl font-semibold text-slate-800 mb-2">Compatible Clients:</h3>
                           <div id="client-info-list" class="space-y-5"></div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- Footer -->
        <footer class="text-center mt-12 sm:mt-16 py-6 sm:py-8 border-t border-slate-200">
            <div class="flex flex-col sm:flex-row justify-center items-center gap-y-4 gap-x-6 text-slate-500 text-sm">
                <p>Created with ❤️ by YEBEKHE</p>
                <div class="flex items-center gap-x-3">
                    <a href="https://t.me/yebekhe" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 transition-colors" title="Telegram"><i data-lucide="send" class="h-5 w-5"></i></a>
                    <a href="https://x.com/yebekhe" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 transition-colors" title="X (Twitter)"><i data-lucide="twitter" class="h-5 w-5"></i></a>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-4">Last Generated: __TIMESTAMP_PLACEHOLDER__</p>
        </footer>
    </div>
    
    <!-- Modals -->
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
            
            <div id="dnaLoadingState" class="text-center py-10">
                 <p class="flex items-center justify-center gap-2 text-slate-600">
                    <i data-lucide="loader-2" class="animate-spin w-5 h-5"></i> 
                    Analyzing... Please wait.
                </p>
            </div>

            <div id="dnaResultsContainer" class="hidden grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-8">
                    <div>
                        <h3 class="font-semibold mb-3 text-center text-slate-700">Protocol Distribution</h3>
                        <div class="max-w-[200px] sm:max-w-[250px] mx-auto relative">
                            <canvas id="protocolChart"></canvas>
                            <div id="protocolTotal" class="absolute inset-0 flex items-center justify-center text-center leading-none">
                                <div><span class="text-3xl font-bold text-slate-800"></span><span class="text-sm text-slate-500 block">Nodes</span></div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-3 text-center text-slate-700">Provider Keywords</h3>
                        <div id="providerTagCloud" class="p-4 bg-slate-100 rounded-lg min-h-[150px] flex flex-wrap gap-2 items-center justify-center"></div>
                    </div>
                </div>
                <div class="space-y-8">
                     <div>
                        <h3 class="font-semibold mb-3 text-center text-slate-700">Geographic Distribution</h3>
                        <div id="countryBarChartContainer"><canvas id="countryBarChart"></canvas></div>
                    </div>
                     <div>
                        <h3 class="font-semibold mb-3 text-center text-slate-700">Transport & Security</h3>
                        <div id="transportChartContainer"><canvas id="transportChart"></canvas></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs@0.0.2/qrcode.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const structuredData = __JSON_DATA_PLACEHOLDER__;
            const clientInfoData = __CLIENT_INFO_PLACEHOLDER__;
            // Get all DOM elements
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
            const dnaModal = document.getElementById('dnaModal');
            const dnaModalCloseButton = document.getElementById('dnaModalCloseButton');

            let charts = {};
            const countryCodeMap = { US: 'United States', SG: 'Singapore', JP: 'Japan', KR: 'S. Korea', DE: 'Germany', NL: 'Netherlands', GB: 'UK', FR: 'France', CA: 'Canada', AU: 'Australia', HK: 'Hong Kong', TW: 'Taiwan', RU: 'Russia', IN: 'India', TR: 'Turkey', IR: 'Iran', AE: 'UAE' };

            function getCountryName(code) { return countryCodeMap[code] || code; }
            function getFlagEmoji(countryCode) {
                if (!/^[A-Z]{2}$/.test(countryCode)) return '';
                return String.fromCodePoint(...countryCode.toUpperCase().split('').map(char => 127397 + char.charCodeAt()));
            }

            // --- UNIVERSAL DNA PARSER (UPGRADED) ---
            function getUniversalDna(content, coreType) {
                const dna = { protocols: {}, countries: {}, providers: {}, transports: {}, security: {tls: {}, reality: {}}, total: 0 };
                const providerKeywords = ['aws', 'cdn', 'google', 'azure', 'oracle', 'linode', 'vultr', 'digitalocean', 'hetzner', 'ovh', 'alibaba', 'vip', 'premium'];
                
                try {
                    switch (coreType.toLowerCase()) {
                        case 'clash':
                        case 'meta':
                            const parsedYaml = jsyaml.load(content);
                            if (parsedYaml && Array.isArray(parsedYaml.proxies)) {
                                parsedYaml.proxies.forEach(p => {
                                    if(!p || !p.name || !p.type) return;
                                    dna.total++;
                                    const lowerName = p.name.toLowerCase();
                                    const transport = p.network || 'tcp';
                                    dna.protocols[p.type] = (dna.protocols[p.type] || 0) + 1;
                                    dna.transports[transport] = (dna.transports[transport] || 0) + 1;
                                    if (p.tls) dna.security.tls[transport] = (dna.security.tls[transport] || 0) + 1;
                                    if (p['reality-opts']) dna.security.reality[transport] = (dna.security.reality[transport] || 0) + 1;
                                    
                                    const countryMatch = lowerName.match(/\[([a-z]{2})\]|\b([a-z]{2})\b|([a-z]{2})[-_]/);
                                    if (countryMatch) dna.countries[(countryMatch[1] || countryMatch[2] || countryMatch[3]).toUpperCase()] = (dna.countries[(countryMatch[1] || countryMatch[2] || countryMatch[3]).toUpperCase()] || 0) + 1;
                                    providerKeywords.forEach(k => { if(lowerName.includes(k)) dna.providers[k.toUpperCase()] = (dna.providers[k.toUpperCase()] || 0) + 1; });
                                });
                            }
                            break;

                        case 'singbox':
                            // FIX: Use robust jsonc_parser (note the underscore)
                            const parsedJson = jsonc_parser.parse(content);
                            const utilityTypes = ['selector', 'urltest', 'direct', 'block', 'dns'];
                            if (parsedJson && Array.isArray(parsedJson.outbounds)) {
                                parsedJson.outbounds
                                    .filter(o => o.type && !utilityTypes.includes(o.type))
                                    .forEach(o => {
                                        if(!o || !o.tag || !o.type) return;
                                        dna.total++;
                                        const lowerName = o.tag.toLowerCase();
                                        const transport = o.transport?.type || o.network || 'tcp';
                                        dna.protocols[o.type] = (dna.protocols[o.type] || 0) + 1;
                                        dna.transports[transport] = (dna.transports[transport] || 0) + 1;
                                        if (o.tls?.enabled) {
                                            if(o.tls.reality?.enabled) dna.security.reality[transport] = (dna.security.reality[transport] || 0) + 1;
                                            else dna.security.tls[transport] = (dna.security.tls[transport] || 0) + 1;
                                        }
                                        const countryMatch = lowerName.match(/\[([a-z]{2})\]|\b([a-z]{2})\b|([a-z]{2})[-_]/);
                                        if (countryMatch) dna.countries[(countryMatch[1] || countryMatch[2] || countryMatch[3]).toUpperCase()] = (dna.countries[(countryMatch[1] || countryMatch[2] || countryMatch[3]).toUpperCase()] || 0) + 1;
                                        providerKeywords.forEach(k => { if(lowerName.includes(k)) dna.providers[k.toUpperCase()] = (dna.providers[k.toUpperCase()] || 0) + 1; });
                                    });
                            }
                            break;

                        case 'xray':
                        case 'location':
                        default: // Fallback for base64 with expanded regex
                            const decoded = atob(content);
                            // FIX: Added dedicated regex for VMess, separate from others.
                            const vmessRegex = /^vmess:\/\/(.+)$/;
                            const standardRegex = /^(vless|trojan|ss|hy2|tuic):\/\/([^@]+@)?([^:?#]+):(\d+)\??([^#]+)?#(.+)$/;
                            const hysteriaRegex = /^(hysteria|hysteria2):\/\/([^:?#]+):(\d+)\??([^#]+)?#(.+)$/;

                            decoded.split('\n').forEach(line => {
                                line = line.trim();
                                if (!line) return;
                                
                                let match;
                                let protocol = '', lowerName = '', transport = 'tcp', security = 'none';

                                if (match = line.match(vmessRegex)) {
                                    try {
                                        const vmessConfig = JSON.parse(atob(match[1]));
                                        protocol = 'vmess';
                                        lowerName = (vmessConfig.ps || '').toLowerCase();
                                        transport = vmessConfig.net || 'tcp';
                                        security = vmessConfig.tls || 'none';
                                    } catch (e) { /* Malformed vmess, skip */ return; }
                                } else if (match = line.match(standardRegex) || line.match(hysteriaRegex)) {
                                    protocol = match[1];
                                    const params = new URLSearchParams(match[match.length - 2] || '');
                                    lowerName = decodeURIComponent(match[match.length - 1] || '').trim().toLowerCase();
                                    transport = params.get('type') || 'tcp';
                                    security = params.get('security') || 'none';
                                }

                                if (protocol) {
                                    dna.total++;
                                    dna.protocols[protocol] = (dna.protocols[protocol] || 0) + 1;
                                    dna.transports[transport] = (dna.transports[transport] || 0) + 1;
                                    if (security === 'tls') dna.security.tls[transport] = (dna.security.tls[transport] || 0) + 1;
                                    else if (security === 'reality') dna.security.reality[transport] = (dna.security.reality[transport] || 0) + 1;

                                    const countryMatch = lowerName.match(/\[([a-z]{2})\]|\b([a-z]{2})\b|([a-z]{2})[-_]/);
                                    if (countryMatch) dna.countries[(countryMatch[1] || countryMatch[2] || countryMatch[3]).toUpperCase()] = (dna.countries[(countryMatch[1] || countryMatch[2] || countryMatch[3]).toUpperCase()] || 0) + 1;
                                    providerKeywords.forEach(k => { if(lowerName.includes(k)) dna.providers[k.toUpperCase()] = (dna.providers[k.toUpperCase()] || 0) + 1; });
                                }
                            });
                            break;
                    }
                } catch (e) {
                    console.error(`Parsing failed for type ${coreType}:`, e);
                    throw new Error(`Could not parse the subscription content. It may be invalid or malformed for the '${coreType}' type.`);
                }
                return dna;
            }

            // --- UI & Event Functions ---
            function closeDnaModal(event) { if (event && event.target.id !== 'dnaModal') return; const modalContent = document.getElementById('dnaModalContent'); modalContent.classList.add('scale-95', 'opacity-0'); setTimeout(() => dnaModal.classList.add('hidden'), 200); Object.values(charts).forEach(chart => { if (chart) chart.destroy(); }); }
            function showMessageBox(message) { const box = document.getElementById('messageBox'); document.getElementById('messageBoxText').textContent = message; box.classList.remove('hidden'); document.getElementById('messageBoxClose').onclick = () => box.classList.add('hidden'); }
            function populateSelect(selectElement, sortedKeys, defaultOptionText) { selectElement.innerHTML = `<option value="">${defaultOptionText}</option>`; sortedKeys.forEach(key => { const option = document.createElement('option'); option.value = key; option.textContent = formatDisplayName(key); selectElement.appendChild(option); }); }
            function resetSelect(selectElement, defaultText) { selectElement.innerHTML = `<option value="">${defaultText}</option>`; selectElement.disabled = true; }
            function formatDisplayName(name) { const specialReplacements = { 'ss': 'SHADOWSOCKS' }; const uppercaseTypes = ['mix', 'vless', 'vmess', 'trojan', 'ssr', 'ws', 'grpc', 'reality', 'hy2', 'hysteria2', 'tuic', 'xhttp']; const parts = name.split(/[-_]/).filter(p => p !== ''); let flag = ''; const countryCodeMatch = name.match(/^([A-Z]{2})[-_]/); if (countryCodeMatch) { flag = getFlagEmoji(countryCodeMatch[1]); } const displayNameParts = parts.map((part) => { const lowerPart = part.toLowerCase(); if (specialReplacements[lowerPart]) return specialReplacements[lowerPart]; if (uppercaseTypes.includes(lowerPart)) return part.toUpperCase(); return part.charAt(0).toUpperCase() + part.slice(1).toLowerCase(); }); const textName = displayNameParts.join(' '); return flag ? `${flag} ${textName.trim()}` : textName.trim(); }
            function updateQRCode(url) { qrcodeDiv.innerHTML = ''; if (url) { try { new QRCode(qrcodeDiv, { text: url, width: 128, height: 128, colorDark: "#000000", colorLight: "#FFFFFF", correctLevel: QRCode.CorrectLevel.H }); } catch (error) { console.error('QR code init failed:', error); } } }
            function updateClientInfo(coreType) { const clientInfoContainer = document.getElementById('client-info-container'); clientInfoList.innerHTML = ''; const platforms = clientInfoData[coreType]; if (!platforms || Object.keys(platforms).length === 0) { clientInfoContainer.classList.add('hidden'); return; } clientInfoContainer.classList.remove('hidden'); Object.entries(platforms).forEach(([platformName, clients]) => { if (clients.length > 0) { const platformContainer = document.createElement('div'); const titleDiv = document.createElement('div'); titleDiv.className = 'flex items-center gap-2 text-sm font-semibold text-slate-600 mb-2'; const iconName = { windows: 'monitor', macos: 'apple', android: 'smartphone', ios: 'tablet', linux: 'terminal' }[platformName.toLowerCase()] || 'box'; const icon = document.createElement('i'); icon.setAttribute('data-lucide', iconName); icon.className = 'w-4 h-4 text-slate-500'; titleDiv.appendChild(icon); const titleText = document.createElement('span'); titleText.textContent = platformName.charAt(0).toUpperCase() + platformName.slice(1); titleDiv.appendChild(titleText); platformContainer.appendChild(titleDiv); const linksContainer = document.createElement('div'); linksContainer.className = 'flex flex-col gap-2'; clients.forEach(client => { const link = document.createElement('a'); link.href = client.url; link.target = '_blank'; link.rel = 'noopener noreferrer'; link.className = 'flex items-center justify-between p-2.5 bg-slate-100 rounded-lg hover:bg-slate-200 transition-colors duration-200 text-slate-700 hover:text-indigo-600'; const nameSpan = document.createElement('span'); nameSpan.className = 'font-medium text-sm'; nameSpan.textContent = client.name; const downloadIcon = document.createElement('i'); downloadIcon.setAttribute('data-lucide', 'download'); downloadIcon.className = 'w-4 h-4 text-slate-500'; link.appendChild(nameSpan); link.appendChild(downloadIcon); linksContainer.appendChild(link); }); platformContainer.appendChild(linksContainer); clientInfoList.appendChild(platformContainer); } }); lucide.createIcons(); }
            function updateOtherElementOptions() { const selectedConfigType = configTypeSelect.value; const selectedIpType = ipTypeSelect.value; const searchTerm = searchBar.value.toLowerCase(); resetSelect(otherElementSelect, 'Select Subscription'); subscriptionDetailsContainer.classList.add('hidden'); if (selectedIpType && structuredData[selectedConfigType]?.[selectedIpType]) { const allElements = structuredData[selectedConfigType][selectedIpType]; const filteredAndSortedKeys = Object.keys(allElements).filter(key => formatDisplayName(key).toLowerCase().includes(searchTerm)).sort((a, b) => a.localeCompare(b)); populateSelect(otherElementSelect, filteredAndSortedKeys, filteredAndSortedKeys.length > 0 ? 'Select Subscription' : 'No matches found'); otherElementSelect.disabled = false; } }

            // --- Event Listeners ---
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

                    const transportLabels = Object.keys(dna.transports);
                    charts.transport = new Chart(document.getElementById('transportChart'), {
                        type: 'bar',
                        data: {
                            labels: transportLabels,
                            datasets: [
                                { label: 'TLS', data: transportLabels.map(l => dna.security.tls[l] || 0), backgroundColor: '#34d399' },
                                { label: 'REALITY', data: transportLabels.map(l => dna.security.reality[l] || 0), backgroundColor: '#a78bfa' },
                                { label: 'Insecure', data: transportLabels.map(l => dna.transports[l] - (dna.security.tls[l] || 0) - (dna.security.reality[l] || 0)), backgroundColor: '#fbbf24' }
                            ]
                        },
                        options: { responsive: true, scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true } }, plugins: { legend: { position: 'bottom' }} }
                    });

                    const sortedCountries = Object.entries(dna.countries).sort((a, b) => b[1] - a[1]).slice(0, 7);
                    charts.country = new Chart(document.getElementById('countryBarChart'), {
                        type: 'bar', data: { labels: sortedCountries.map(c => `${getFlagEmoji(c[0])} ${getCountryName(c[0])}`), datasets: [{ label: '# Nodes', data: sortedCountries.map(c => c[1]), backgroundColor: '#60a5fa', borderRadius: 4 }] },
                        options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
                    });

                    const tagCloud = document.getElementById('providerTagCloud');
                    tagCloud.innerHTML = '';
                    const sortedProviders = Object.entries(dna.providers).sort((a, b) => b[1] - a[1]);
                    if (sortedProviders.length === 0) {
                        tagCloud.innerHTML = '<p class="text-slate-500 text-sm">No common keywords found.</p>';
                    } else {
                        sortedProviders.forEach(([provider, count]) => {
                           const tag = document.createElement('span');
                           tag.className = 'bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-1 rounded-full';
                           tag.textContent = `${provider} (${count})`;
                           tagCloud.appendChild(tag);
                        });
                    }
                    document.getElementById('dnaLoadingState').classList.add('hidden');
                    document.getElementById('dnaResultsContainer').classList.remove('hidden');
                } catch (error) {
                    closeDnaModal();
                    showMessageBox(`Analysis Failed: ${error.message}`);
                }
            });

            dnaModalCloseButton.addEventListener('click', () => closeDnaModal());
            dnaModal.addEventListener('click', (event) => closeDnaModal(event));
            configTypeSelect.addEventListener('change', () => { resetSelect(ipTypeSelect, 'Select Client/Core'); resetSelect(otherElementSelect, 'Select Subscription'); searchBar.value = ''; searchBar.disabled = true; resultArea.classList.add('hidden'); if (configTypeSelect.value && structuredData[configTypeSelect.value]) { populateSelect(ipTypeSelect, Object.keys(structuredData[configTypeSelect.value]), 'Select Client/Core'); ipTypeSelect.disabled = false; } });
            ipTypeSelect.addEventListener('change', () => { searchBar.value = ''; if (ipTypeSelect.value) { updateClientInfo(ipTypeSelect.value); resultArea.classList.remove('hidden'); subscriptionDetailsContainer.classList.add('hidden'); searchBar.disabled = false; updateOtherElementOptions(); analyzeButton.style.display = 'flex'; } else { resultArea.classList.add('hidden'); searchBar.disabled = true; resetSelect(otherElementSelect, 'Select Subscription'); } });
            searchBar.addEventListener('input', updateOtherElementOptions);
            otherElementSelect.addEventListener('change', () => { const url = structuredData[configTypeSelect.value]?.[ipTypeSelect.value]?.[otherElementSelect.value]; if (url) { subscriptionUrlInput.value = url; updateQRCode(url); subscriptionDetailsContainer.classList.remove('hidden'); } else { subscriptionDetailsContainer.classList.add('hidden'); } });
            copyButton.addEventListener('click', () => { if (!subscriptionUrlInput.value) { showMessageBox('No URL to copy.'); return; } navigator.clipboard.writeText(subscriptionUrlInput.value) .then(() => { const copyIcon = copyButton.querySelector('.copy-icon'); const checkIcon = copyButton.querySelector('.check-icon'); copyIcon.classList.add('hidden'); checkIcon.classList.remove('hidden'); setTimeout(() => { copyIcon.classList.remove('hidden'); checkIcon.classList.add('hidden'); }, 2000); }).catch(err => { showMessageBox('Failed to copy URL.'); }); });

            // Page Initialization
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
