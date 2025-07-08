<?php

declare(strict_types=1);

/**
 * Dynamic Subscription Page Generator (V2 - Corrected)
 *
 * This script scans all individual configuration files, extracts their metadata,
 * and embeds this data into a self-contained HTML page with robust JavaScript logic
 * for dynamically building custom subscription links.
 */

// --- Configuration ---
define('PROJECT_ROOT', __DIR__);
define('CONFIG_DIR', PROJECT_ROOT . '/subscriptions/location/normal');
define('OUTPUT_HTML_FILE', PROJECT_ROOT . '/index.html');

// --- Helper Functions ---

function detect_type(string $link): ?string {
    $link = trim($link);
    if (strpos($link, 'vmess://') === 0) return 'vmess';
    if (strpos($link, 'vless://') === 0) return 'vless';
    if (strpos($link, 'trojan://') === 0) return 'trojan';
    if (strpos($link, 'ss://') === 0) return 'ss';
    if (strpos($link, 'hy2://') === 0) return 'hy2';
    if (strpos($link, 'tuic://') === 0) return 'tuic';
    return null;
}

function get_ip_type(string $hostname): string {
    if (filter_var($hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) return 'ipv4';
    if (filter_var($hostname, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) return 'ipv6';
    return 'domain';
}

function scan_and_collect_configs(string $dir): array {
    if (!is_dir($dir)) return [];
    $allConfigs = [];
    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $filePath = $dir . '/' . $file;
        $location = strtoupper(pathinfo($file, PATHINFO_FILENAME));
        if ($location === 'MIX' || empty($location)) continue;

        $content = file_get_contents($filePath);
        if (empty($content)) continue;

        $lines = explode(PHP_EOL, trim($content));
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $protocol = detect_type($line);
            if ($protocol === null) continue;

            $host = 'unknown';
            if (in_array($protocol, ['vless', 'trojan', 'hy2', 'tuic'])) {
                if (preg_match('/^[^:]+:\/\/([^@]+@)?([^:\/?#]+)/', $line, $matches)) {
                    $host = $matches[2];
                }
            } elseif ($protocol === 'vmess') {
                $host = 'domain'; // Assume domain for base64-encoded vmess
            }

            $allConfigs[] = [
                'config'   => $line,
                'protocol' => $protocol,
                'location' => $location,
                'ipType'   => ($protocol === 'vmess') ? 'domain' : get_ip_type($host),
            ];
        }
    }
    return $allConfigs;
}

function generate_checkbox_group(string $title, string $idPrefix, array $items): string {
    $html = "<div class='mb-6'><h3 class='text-lg font-semibold border-b border-slate-300 pb-2 mb-3'>{$title}</h3><div class='flex items-center gap-x-4 mb-2'>
                <button class='text-sm font-semibold text-indigo-600 hover:text-indigo-800' data-controls='{$idPrefix}' data-action='select'>Select All</button>
                <button class='text-sm font-semibold text-slate-500 hover:text-slate-700' data-controls='{$idPrefix}' data-action='deselect'>Deselect All</button>
              </div><div class='space-y-1'>";
    foreach ($items as $item) {
        $itemId = $idPrefix . '-' . htmlspecialchars($item);
        $html .= "<label for='{$itemId}' class='flex items-center space-x-3 p-2 rounded-md hover:bg-slate-100 cursor-pointer transition-colors duration-150'>
                    <input type='checkbox' id='{$itemId}' name='{$idPrefix}' value='{$item}' class='h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1'>
                    <span class='font-medium text-slate-700'>" . htmlspecialchars(ucfirst($item)) . "</span>
                  </label>";
    }
    $html .= "</div></div>";
    return $html;
}

function generate_full_html(array $configs, array $protocols, array $locations, array $ipTypes): string {
    $jsonConfigs = json_encode($configs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    $protocolCheckboxes = generate_checkbox_group('Protocols', 'proto', $protocols);
    $locationCheckboxes = generate_checkbox_group('Locations', 'loc', $locations);
    $ipTypeCheckboxes = generate_checkbox_group('IP Types', 'ip', $ipTypes);

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Subscription Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        .controls-panel { height: 100vh; overflow-y: auto; }
        #qrcode img { margin: auto; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">
    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Controls Panel -->
        <aside id="controls" class="w-full md:w-80 bg-white border-r border-slate-200 p-6 controls-panel">
            <h2 class="text-2xl font-bold mb-6">Filters</h2>
            {$protocolCheckboxes}
            {$locationCheckboxes}
            {$ipTypeCheckboxes}
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-10">
            <header class="mb-8">
                <h1 class="text-4xl font-bold tracking-tight">Subscription Generator</h1>
                <p class="text-lg text-slate-500 mt-2">Select filters to generate a custom subscription link.</p>
            </header>

            <div class="bg-white p-6 rounded-xl shadow-md border border-slate-200">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-lg">Your Custom Subscription</h3>
                    <span id="configCounter" class="text-sm font-medium bg-indigo-100 text-indigo-800 px-2.5 py-1 rounded-full"></span>
                </div>
                <p class="text-slate-500 mb-4 text-sm">Copy the link or scan the QR code with your client app.</p>

                <div class="flex items-center mb-4">
                    <input type="text" id="subscriptionLink" readonly
                        class="flex-grow font-mono text-sm p-3 bg-slate-100 border border-slate-300 rounded-l-lg outline-none"
                        placeholder="Select filters to generate link...">
                    <button id="copyBtn" class="flex-shrink-0 p-3 bg-indigo-600 text-white rounded-r-lg hover:bg-indigo-700 disabled:bg-slate-400 transition-colors duration-200">
                        <svg id="copyIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        <svg id="checkIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    </button>
                </div>
                
                <div id="qrcodeWrapper" class="p-4 bg-slate-50 rounded-lg text-center hidden">
                   <div id="qrcode"></div>
                </div>
                <div id="placeholderWrapper" class="p-4 bg-slate-50 rounded-lg text-center">
                    <p class="text-slate-500">QR code will appear here</p>
                </div>
            </div>
        </main>
    </div>

    <script>
        // --- Data embedded from PHP ---
        const allConfigs = JSON.parse('{$jsonConfigs}');

        // --- DOM Elements ---
        const controlsPanel = document.getElementById('controls');
        const subLinkInput = document.getElementById('subscriptionLink');
        const copyBtn = document.getElementById('copyBtn');
        const copyIcon = document.getElementById('copyIcon');
        const checkIcon = document.getElementById('checkIcon');
        const qrcodeContainer = document.getElementById('qrcode');
        const qrcodeWrapper = document.getElementById('qrcodeWrapper');
        const placeholderWrapper = document.getElementById('placeholderWrapper');
        const configCounter = document.getElementById('configCounter');
        
        let qrCodeInstance = null;

        // --- State ---
        const selections = {
            proto: new Set(),
            loc: new Set(),
            ip: new Set()
        };

        // --- Functions ---
        function generateSubscriptionLink() {
            const filtered = allConfigs.filter(cfg => {
                const protoMatch = selections.proto.size === 0 || selections.proto.has(cfg.protocol);
                const locMatch = selections.loc.size === 0 || selections.loc.has(cfg.location);
                const ipMatch = selections.ip.size === 0 || selections.ip.has(cfg.ipType);
                return protoMatch && locMatch && ipMatch;
            });

            configCounter.textContent = `${'${filtered.length}'} configs found`;

            if (filtered.length === 0) {
                subLinkInput.value = '';
                subLinkInput.placeholder = 'No configs match your selection.';
                qrcodeWrapper.classList.add('hidden');
                placeholderWrapper.classList.remove('hidden');
                if (qrCodeInstance) qrCodeInstance.clear();
                copyBtn.disabled = true;
                return;
            }

            // *** FIX: Use '\n' for newlines, not '\\n' ***
            const configStrings = filtered.map(c => c.config).join('\\n');
            const base64Content = btoa(configStrings);
            const dataUri = `data:text/plain;base64,${'${base64Content}'}`;

            subLinkInput.value = dataUri;
            copyBtn.disabled = false;
            
            // Generate QR Code
            placeholderWrapper.classList.add('hidden');
            qrcodeWrapper.classList.remove('hidden');
            if (qrCodeInstance) {
                qrCodeInstance.clear();
                qrCodeInstance.makeCode(dataUri);
            } else {
                qrCodeInstance = new QRCode(qrcodeContainer, {
                    text: dataUri,
                    width: 220,
                    height: 220,
                    correctLevel: QRCode.CorrectLevel.M
                });
            }
        }

        // --- Event Listeners ---
        controlsPanel.addEventListener('change', (e) => {
            if (e.target.type === 'checkbox') {
                const { name, value, checked } = e.target;
                if (checked) {
                    selections[name].add(value);
                } else {
                    selections[name].delete(value);
                }
                generateSubscriptionLink();
            }
        });

        controlsPanel.addEventListener('click', (e) => {
            const button = e.target.closest('button[data-controls]');
            if (button) {
                const prefix = button.dataset.controls;
                const action = button.dataset.action;
                // *** FIX: Correct CSS selector with quotes ***
                const checkboxes = document.querySelectorAll(`input[name="${'${prefix}'}"]`);
                
                checkboxes.forEach(cb => {
                    cb.checked = (action === 'select');
                    if (cb.checked) {
                        selections[prefix].add(cb.value);
                    } else {
                        selections[prefix].delete(cb.value);
                    }
                });
                generateSubscriptionLink();
            }
        });

        copyBtn.addEventListener('click', () => {
            if (!subLinkInput.value) return;
            navigator.clipboard.writeText(subLinkInput.value).then(() => {
                copyIcon.classList.add('hidden');
                checkIcon.classList.remove('hidden');
                copyBtn.classList.add('bg-emerald-600');
                copyBtn.classList.remove('bg-indigo-600');
                setTimeout(() => {
                    copyIcon.classList.remove('hidden');
                    checkIcon.classList.add('hidden');
                    copyBtn.classList.remove('bg-emerald-600');
                    copyBtn.classList.add('bg-indigo-600');
                }, 2000);
            }).catch(err => {
                alert('Failed to copy!');
            });
        });

        // --- Initial Call ---
        // *** FIX: Generate link on page load ***
        generateSubscriptionLink();

    </script>
</body>
</html>
HTML;
}

// --- Main Execution ---
echo "Starting dynamic subscription page generator (V2)..." . PHP_EOL;

if (!is_dir(CONFIG_DIR)) {
    die("Error: Config directory not found at " . CONFIG_DIR . "\n");
}

$all_configs = scan_and_collect_configs(CONFIG_DIR);
if (empty($all_configs)) {
    die("No valid config files found to generate the page. Exiting.\n");
}
echo "Found and parsed " . count($all_configs) . " individual configs." . PHP_EOL;

$protocols = array_values(array_unique(array_column($all_configs, 'protocol')));
$locations = array_values(array_unique(array_column($all_configs, 'location')));
$ip_types  = array_values(array_unique(array_column($all_configs, 'ipType')));
sort($protocols);
sort($locations);
sort($ip_types);

$final_html = generate_full_html($all_configs, $protocols, $locations, $ip_types);
file_put_contents(OUTPUT_HTML_FILE, $final_html);

echo "Successfully generated dynamic page at: " . OUTPUT_HTML_FILE . "\n";
?>
