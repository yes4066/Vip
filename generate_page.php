<?php

declare(strict_types=1);

/**
 * Proxy Subscription Generator (PSG) Page Builder
 *
 * Scans subscription directories and generates a modern, fully functional index.html.
 * This script uses a NOWDOC to prevent PHP/JS conflicts and ensures all JS logic
 * runs after the DOM is loaded.
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
 * @param string $dir The directory to scan.
 * @return array A list of relative file paths.
 */
function scan_directory(string $dir): array
{
    if (!is_dir($dir)) return [];
    
    $files = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    $ignoreExtensions = ['php', 'md', 'json', 'yml', 'yaml', 'ini'];

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
 * @param array $files_by_category Files grouped by category ('Standard', 'Lite').
 * @return array The structured data.
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
 * @param array $structured_data The hierarchical data for the dropdowns.
 * @return string The complete HTML content.
 */
function generate_full_html(array $structured_data): string
{
    $json_structured_data = json_encode($structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    $html_template = <<<'HTML'
<!DOCTYPE html>
<html lang="en">
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
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        .icon { width: 24px; height: 24px; color: #4f46e5; flex-shrink: 0; }
        .flag { font-size: 1.5rem; line-height: 1; flex-shrink: 0; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 leading-relaxed">
    <div class="container max-w-6xl mx-auto px-4 py-8">
        <header class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight mb-0">Proxy Subscription Generator (PSG)</h1>
            <p class="text-base sm:text-lg text-slate-500 mt-2">Select your preferences to get the corresponding subscription link and QR code.</p>
        </header>

        <main>
            <div class="bg-white rounded-xl p-6 sm:p-8 shadow-lg border border-slate-200 mb-8 sm:mb-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6">
                    <!-- Config Type Selection -->
                    <div>
                        <label for="configType" class="block text-sm font-medium text-slate-700 mb-2">Config Type:</label>
                        <select id="configType" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100"></select>
                    </div>

                    <!-- Type Selection -->
                    <div>
                        <label for="ipType" class="block text-sm font-medium text-slate-700 mb-2">Type:</label>
                        <select id="ipType" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100" disabled></select>
                    </div>

                    <!-- Element Selection -->
                    <div>
                        <label for="otherElement" class="block text-sm font-medium text-slate-700 mb-2">Element:</label>
                        <select id="otherElement" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100" disabled></select>
                    </div>
                </div>

                <!-- Display Area for URL and QR Code -->
                <div id="resultArea" class="hidden bg-slate-50 rounded-lg p-4 sm:p-6 border border-slate-200">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-800 mb-4">Your Subscription Link:</h3>
                    <div class="flex items-center mb-4">
                        <input type="text" id="subscriptionUrl" readonly
                            class="flex-grow font-mono text-xs sm:text-sm py-2 px-2.5 sm:py-2.5 sm:px-3 bg-white border border-slate-300 rounded-l-lg outline-none whitespace-nowrap overflow-hidden text-ellipsis" />
                        <button id="copyButton" class="flex-shrink-0 flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 bg-indigo-50 text-indigo-700 border border-indigo-600 rounded-r-lg cursor-pointer transition-colors duration-200 hover:bg-indigo-100" title="Copy URL">
                            <svg class='copy-icon w-4 h-4 sm:w-5 sm:h-5' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'><path d='M7.5 3.375c0-1.036.84-1.875 1.875-1.875h.375a3.75 3.75 0 0 1 3.75 3.75v1.875C13.5 8.16 12.66 9 11.625 9h-.375a3.75 3.75 0 0 1-3.75-3.75V3.375ZM6.188 1.875a.75.75 0 0 0-1.5 0v1.875a.75.75 0 0 0 .75.75h.375a.75.75 0 0 0 .75-.75V5.25ZM9 3.375a2.25 2.25 0 0 1 2.25-2.25h.375a2.25 2.25 0 0 1 2.25 2.25v1.875a2.25 2.25 0 0 1-2.25 2.25h-.375A2.25 2.25 0 0 1 9 5.25V3.375Z' /><path d='M12.983 9.917a.75.75 0 0 0-1.166-.825l-5.334 3.078a.75.75 0 0 0-.417.825V21a.75.75 0 0 0 .75.75h10.5a.75.75 0 0 0 .75-.75V13a.75.75 0 0 0-.417-.825l-5.333-3.078Z' /></svg>
                            <svg class='check-icon w-4 h-4 sm:w-5 sm:h-5 hidden' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'><path fill-rule='evenodd' d='M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z' clip-rule='evenodd' /></svg>
                        </button>
                    </div>
                    <div class="flex flex-col items-center justify-center">
                        <p class="text-sm text-slate-600 mb-2">Scan the QR code:</p>
                        <div id="qrcode" class="p-2 bg-white border border-slate-300 rounded-lg shadow-inner"></div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="text-center mt-12 sm:mt-16 py-6 sm:py-8 border-t border-slate-200 text-slate-500 text-sm sm:text-base">
            <p>Generated by a dynamic subscription link tool. Use at your own risk.</p>
        </footer>
    </div>

    <!-- Message Box HTML -->
    <div id="messageBox" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-lg p-6 shadow-xl max-w-sm w-full text-center">
            <p id="messageBoxText" class="text-lg font-semibold text-slate-800 mb-4"></p>
            <button id="messageBoxClose" class="bg-indigo-600 text-white px-5 py-2 rounded-md hover:bg-indigo-700 transition-colors duration-200">OK</button>
        </div>
    </div>

    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/davidshimjs-qrcodejs@0.0.2/qrcode.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const structuredData = __JSON_DATA_PLACEHOLDER__;

            const configTypeSelect = document.getElementById('configType');
            const ipTypeSelect = document.getElementById('ipType');
            const otherElementSelect = document.getElementById('otherElement');
            const resultArea = document.getElementById('resultArea');
            const subscriptionUrlInput = document.getElementById('subscriptionUrl');
            const copyButton = document.getElementById('copyButton');
            const qrcodeDiv = document.getElementById('qrcode');

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
                if (type === 'location') return name.toUpperCase() + ' ' + getFlagEmoji(name);
                return name.split(/[-_]/).map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            }
            
            function getFlagEmoji(countryCode) {
                if (typeof countryCode !== 'string' || !/^[a-zA-Z]{2}$/.test(countryCode)) return '🏳️';
                const code = countryCode.toUpperCase();
                return String.fromCodePoint(code.charCodeAt(0) + 127397) + String.fromCodePoint(code.charCodeAt(1) + 127397);
            }

            function updateQRCode(url) {
                qrcodeDiv.innerHTML = ''; // Always clear previous QR code
                if (url) {
                    // Create a new QRCode instance every time to avoid state issues
                    new QRCode(qrcodeDiv, {
                        text: url,
                        width: 128,
                        height: 128,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }
            }
            
            // --- Event Listeners ---
            configTypeSelect.addEventListener('change', () => {
                const selectedConfigType = configTypeSelect.value;
                resetSelect(ipTypeSelect, 'Select Type');
                resetSelect(otherElementSelect, 'Select Element');
                resultArea.classList.add('hidden');

                if (selectedConfigType && structuredData[selectedConfigType]) {
                    populateSelect(ipTypeSelect, structuredData[selectedConfigType], 'Select Type');
                    ipTypeSelect.disabled = false;
                }
            });

            ipTypeSelect.addEventListener('change', () => {
                const selectedConfigType = configTypeSelect.value;
                const selectedIpType = ipTypeSelect.value;
                resetSelect(otherElementSelect, 'Select Element');
                resultArea.classList.add('hidden');

                if (selectedIpType && structuredData[selectedConfigType]?.[selectedIpType]) {
                    populateSelect(otherElementSelect, structuredData[selectedConfigType][selectedIpType], 'Select Element');
                    otherElementSelect.disabled = false;
                }
            });

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
                    const copyIcon = copyButton.querySelector('.copy-icon');
                    const checkIcon = copyButton.querySelector('.check-icon');
                    copyIcon.classList.add('hidden');
                    checkIcon.classList.remove('hidden');
                    setTimeout(() => {
                        copyIcon.classList.remove('hidden');
                        checkIcon.classList.add('hidden');
                    }, 2000);
                }).catch(() => showMessageBox('Failed to copy URL.'));
            });

            // --- Initializer ---
            populateSelect(configTypeSelect, structuredData, 'Select Config Type');
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
if ($file_count === 0) {
    die("Error: No subscription files were found to generate the page. Please check your 'subscriptions' directories. Exiting." . PHP_EOL);
}

echo "Found and categorized {$file_count} subscription files." . PHP_EOL;
$structured_data = process_files_to_structure($all_files);

$final_html = generate_full_html($structured_data);
file_put_contents(OUTPUT_HTML_FILE, $final_html);

echo "Successfully generated page at: " . realpath(OUTPUT_HTML_FILE) . PHP_EOL;