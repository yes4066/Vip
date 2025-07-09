<?php

declare(strict_types=1);

/**
 * Proxy Subscription Generator (PSG) Page Builder
 *
 * Scans subscription directories and generates a modern, fully functional index.html.
 * This script includes a Search/Filter bar, Dark Mode, Last Generated timestamp,
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

/**
 * Scans a directory recursively for subscription files.
 */
function scan_directory(string $dir): array
{
    if (!is_dir($dir)) return [];
    
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $ignoreExtensions = ['php', 'md', 'yml', 'yaml', 'ini'];

    foreach ($iterator as $file) {
        if ($file->isFile() && !in_array(strtolower($file->getExtension()), $ignoreExtensions)) {
            $relativePath = str_replace(PROJECT_ROOT . DIRECTORY_SEPARATOR, '', $file->getRealPath());
            $files[] = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
        }
    }
    return $files;
}

/**
 * Processes a list of file paths into a structured, hierarchical array for the frontend.
 */
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

            $type = array_shift($parts);
            $name = pathinfo(implode('/', $parts), PATHINFO_FILENAME);

            $url = GITHUB_REPO_URL . '/' . $path;
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
 * Generates the complete HTML page with embedded data and correct JavaScript.
 */
function generate_full_html(array $structured_data, string $generation_timestamp): string
{
    $json_structured_data = json_encode($structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    $html_template = <<<'HTML'
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proxy Subscription Generator (PSG)</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Theme Initializer -->
    <script>
        // Apply theme right away to prevent FOUC (Flash of Unstyled Content)
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .lucide { width: 20px; height: 20px; stroke-width: 2; } /* Default Lucide icon size */
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-300 leading-relaxed transition-colors duration-300">
    <div class="container max-w-6xl mx-auto px-4 py-8">
        <header class="flex justify-between items-center mb-10">
            <div class="text-left">
                <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 dark:text-white mb-0">Proxy Subscription Generator</h1>
                <p class="text-base sm:text-lg text-slate-500 dark:text-slate-400 mt-2">Select your preferences to get a subscription link.</p>
            </div>
            <button id="theme-toggle" type="button" class="p-2 rounded-lg text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400">
                <i data-lucide="sun" class="sun-icon"></i>
                <i data-lucide="moon" class="moon-icon"></i>
                <span class="sr-only">Toggle dark mode</span>
            </button>
        </header>

        <main>
            <div class="bg-white dark:bg-slate-800 rounded-xl p-6 sm:p-8 shadow-lg border border-slate-200 dark:border-slate-700 mb-8 sm:mb-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6">
                    <div>
                        <label for="configType" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Config Type:</label>
                        <select id="configType" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 dark:bg-slate-700 dark:text-slate-200"></select>
                    </div>
                    <div>
                        <label for="ipType" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Type:</label>
                        <select id="ipType" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 dark:bg-slate-700 dark:text-slate-200" disabled></select>
                    </div>
                    <div>
                        <label for="otherElement" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Element:</label>
                        <input type="search" id="searchBar" placeholder="Filter elements..." class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 mb-2 bg-slate-100 dark:bg-slate-700 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500" disabled>
                        <select id="otherElement" class="block w-full rounded-md border-slate-300 dark:border-slate-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100 dark:bg-slate-700 dark:text-slate-200" disabled></select>
                    </div>
                </div>

                <div id="resultArea" class="hidden bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 sm:p-6 border border-slate-200 dark:border-slate-700">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-800 dark:text-slate-200 mb-4">Your Subscription Link:</h3>
                    <div class="flex items-center mb-4">
                        <input type="text" id="subscriptionUrl" readonly
                            class="flex-grow font-mono text-xs sm:text-sm py-2 px-2.5 sm:py-2.5 sm:px-3 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-l-lg outline-none whitespace-nowrap overflow-hidden text-ellipsis" />
                        <button id="copyButton" class="flex-shrink-0 flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 border border-l-0 border-indigo-600 dark:border-indigo-500 rounded-r-lg cursor-pointer transition-colors duration-200 hover:bg-indigo-100 dark:hover:bg-indigo-800" title="Copy URL">
                            <i data-lucide="copy" class="copy-icon w-5 h-5"></i>
                            <i data-lucide="check" class="check-icon w-5 h-5 hidden"></i>
                        </button>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">Scan the QR code:</p>
                        <div id="qrcode" class="p-2 bg-white border border-slate-300 dark:border-slate-600 rounded-lg shadow-inner"></div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="text-center mt-12 sm:mt-16 py-6 sm:py-8 border-t border-slate-200 dark:border-slate-700">
            <div class="flex justify-center items-center gap-x-6 text-slate-500 dark:text-slate-400 text-sm">
                <p>Created with ❤️ by YEBEKHE</p>
                <div class="flex items-center gap-x-3">
                    <a href="https://t.me/yebekhe" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="Telegram">
                        <i data-lucide="send"></i>
                    </a>
                    <a href="https://x.com/yebekhe" target="_blank" rel="noopener noreferrer" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors" title="X (Twitter)">
                        <i data-lucide="twitter"></i>
                    </a>
                </div>
            </div>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-4">Last Generated: __TIMESTAMP_PLACEHOLDER__</p>
        </footer>
    </div>

    <!-- Message Box HTML -->
    <div id="messageBox" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white dark:bg-slate-800 rounded-lg p-6 shadow-xl max-w-sm w-full text-center">
            <p id="messageBoxText" class="text-lg font-semibold text-slate-800 dark:text-slate-200 mb-4"></p>
            <button id="messageBoxClose" class="bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors duration-200">OK</button>
        </div>
    </div>

    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs@0.0.2/qrcode.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const structuredData = __JSON_DATA_PLACEHOLDER__;

            // Element References
            const configTypeSelect = document.getElementById('configType');
            const ipTypeSelect = document.getElementById('ipType');
            const otherElementSelect = document.getElementById('otherElement');
            const searchBar = document.getElementById('searchBar');
            const resultArea = document.getElementById('resultArea');
            const subscriptionUrlInput = document.getElementById('subscriptionUrl');
            const copyButton = document.getElementById('copyButton');
            const qrcodeDiv = document.getElementById('qrcode');
            const themeToggleButton = document.getElementById('theme-toggle');

            // --- Helper Functions ---
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
                    const formatType = (selectElement.id === 'otherElement' && ipTypeSelect.value === 'location') ? 'location' : 'default';
                    option.textContent = formatDisplayName(key, formatType);
                    selectElement.appendChild(option);
                });
            }
            
            function resetSelect(selectElement, defaultText) {
                selectElement.innerHTML = `<option value="">${defaultText}</option>`;
                selectElement.disabled = true;
            }

            function formatDisplayName(name, type = 'default') {
                if (type === 'location') {
                    return name.toUpperCase() + ' ' + getFlagEmoji(name);
                }
                let baseName = name, suffix = '';
                if (name.endsWith('_ipv4')) { baseName = name.slice(0, -5); suffix = ' (IPv4)'; } 
                else if (name.endsWith('_ipv6')) { baseName = name.slice(0, -5); suffix = ' (IPv6)'; } 
                else if (name.endsWith('_domain')) { baseName = name.slice(0, -7); suffix = ' (Domain)'; }
                
                const formattedBase = baseName.split(/[-_]/).map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
                return formattedBase + suffix;
            }
            
            function getFlagEmoji(countryCode) {
                if (typeof countryCode !== 'string' || !/^[a-zA-Z]{2}$/.test(countryCode)) return '🏳️';
                const code = countryCode.toUpperCase();
                return String.fromCodePoint(code.charCodeAt(0) + 127397) + String.fromCodePoint(code.charCodeAt(1) + 127397);
            }

            function updateQRCode(url) {
                qrcodeDiv.innerHTML = '';
                if (url) {
                    new QRCode(qrcodeDiv, {
                        text: url, width: 128, height: 128,
                        colorDark: "#000000", colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }
            }

            // --- Main Application Logic ---
            function updateOtherElementOptions() {
                const selectedConfigType = configTypeSelect.value;
                const selectedIpType = ipTypeSelect.value;
                const searchTerm = searchBar.value.toLowerCase();

                resetSelect(otherElementSelect, 'Select Element');
                resultArea.classList.add('hidden');

                if (selectedIpType && structuredData[selectedConfigType]?.[selectedIpType]) {
                    const allElements = structuredData[selectedConfigType][selectedIpType];
                    
                    const filteredElements = Object.keys(allElements)
                        .filter(key => formatDisplayName(key, (selectedIpType === 'location' ? 'location' : 'default')).toLowerCase().includes(searchTerm))
                        .reduce((obj, key) => {
                            obj[key] = allElements[key];
                            return obj;
                        }, {});

                    populateSelect(otherElementSelect, filteredElements, Object.keys(filteredElements).length > 0 ? 'Select Element' : 'No matches found');
                    otherElementSelect.disabled = false;
                    
                    const options = Object.keys(filteredElements);
                    if (options.length === 1) {
                        otherElementSelect.value = options[0];
                        otherElementSelect.dispatchEvent(new Event('change'));
                    }
                }
            }
            
            configTypeSelect.addEventListener('change', () => {
                const selectedConfigType = configTypeSelect.value;
                resetSelect(ipTypeSelect, 'Select Type');
                resetSelect(otherElementSelect, 'Select Element');
                resultArea.classList.add('hidden');
                searchBar.value = '';
                searchBar.disabled = true;

                if (selectedConfigType && structuredData[selectedConfigType]) {
                    populateSelect(ipTypeSelect, structuredData[selectedConfigType], 'Select Type');
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
                    resultArea.classList.remove('hidden');
                } else {
                    resultArea.classList.add('hidden');
                }
            });

            copyButton.addEventListener('click', () => {
                if (!subscriptionUrlInput.value) {
                    showMessageBox('No URL to copy.');
                    return;
                }
                navigator.clipboard.writeText(subscriptionUrlInput.value).then(() => {
                    // Re-run createIcons to ensure the checkmark is rendered
                    const copyIcon = copyButton.querySelector('.copy-icon');
                    const checkIcon = copyButton.querySelector('.check-icon');
                    copyIcon.classList.add('hidden');
                    checkIcon.classList.remove('hidden');
                    lucide.createIcons({
                        nodes: [checkIcon],
                        attrs: {'class': 'check-icon w-5 h-5'}
                    });

                    setTimeout(() => {
                        copyIcon.classList.remove('hidden');
                        checkIcon.classList.add('hidden');
                    }, 2000);
                }).catch(() => showMessageBox('Failed to copy URL.'));
            });

            // --- Dark Mode Handler ---
            const updateThemeIcons = () => {
                const isDark = document.documentElement.classList.contains('dark');
                const sunIcon = document.querySelector('.sun-icon');
                const moonIcon = document.querySelector('.moon-icon');
                
                // Ensure icons exist before trying to modify them
                if (sunIcon) sunIcon.style.display = isDark ? 'none' : 'inline-block';
                if (moonIcon) moonIcon.style.display = isDark ? 'inline-block' : 'none';
            };

            themeToggleButton.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');
                localStorage.theme = isDark ? 'dark' : 'light';
                updateThemeIcons();
            });


            // --- Initial Page Setup ---
            // Render all Lucide icons first
            lucide.createIcons();

            // Then, set the initial visibility of the theme icons
            updateThemeIcons();

            populateSelect(configTypeSelect, structuredData, 'Select Config Type');
            configTypeSelect.disabled = false;
        });
    </script>
</body>
</html>
HTML;

    // Replace placeholders with dynamic data
    $final_html = str_replace('__JSON_DATA_PLACEHOLDER__', $json_structured_data, $html_template);
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
if ($file_count === 0) {
    die("Error: No subscription files were found to generate the page. Exiting." . PHP_EOL);
}

echo "Found and categorized {$file_count} subscription files." . PHP_EOL;
$structured_data = process_files_to_structure($all_files);

// Set timezone for the timestamp for consistency
date_default_timezone_set('UTC'); 
$timestamp = date('Y-m-d H:i:s T');

$final_html = generate_full_html($structured_data, $timestamp);
file_put_contents(OUTPUT_HTML_FILE, $final_html);

echo "Successfully generated page at: " . realpath(OUTPUT_HTML_FILE) . PHP_EOL;