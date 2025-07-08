function generate_full_html(array $structured_data): string
{
    // Encode the PHP structured data into a JSON string for JavaScript
    $json_structured_data = json_encode($structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

    // Using a HEREDOC for the HTML template. The '$' in JS template literals must be escaped.
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Subscription Links</title>
    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Custom styles for icons and specific elements not covered by Tailwind */
        body {
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        /* Ensure icons have a fixed size to prevent distortion */
        .icon {
            width: 24px;
            height: 24px;
            color: #4f46e5; /* Accent color for icons */
            flex-shrink: 0; /* Prevent icons from shrinking */
        }
        .flag {
            font-size: 1.5rem;
            line-height: 1;
            flex-shrink: 0; /* Prevent flags from shrinking */
        }
        /* Specific Tailwind-like color classes for protocol tags */
        .bg-sky-100 { background-color: #e0f2fe; } .text-sky-800 { color: #075985; }
        .bg-emerald-100 { background-color: #d1fae5; } .text-emerald-800 { color: #065f46; }
        .bg-blue-100 { background-color: #dbeafe; } .text-blue-800 { color: #1e40af; }
        .bg-red-100 { background-color: #fee2e2; } .text-red-800 { color: #991b1b; }
        .bg-purple-100 { background-color: #f3e8ff; } .text-purple-800 { color: #6b21a8; }
        .bg-pink-100 { background-color: #fce7f3; } .text-pink-800 { color: #9d174d; }
        .bg-yellow-100 { background-color: #fef9c3; } .text-yellow-800 { color: #854d0e; }
        .bg-slate-200 { background-color: #e2e8f0; } .text-slate-800 { color: #1e293b; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 leading-relaxed">
    <div class="container max-w-6xl mx-auto px-4 py-8">
        <header class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight mb-0">Dynamic Subscription Links</h1>
            <p class="text-base sm:text-lg text-slate-500 mt-2">Select your preferences to get the corresponding subscription link and QR code.</p>
        </header>

        <main>
            <div class="bg-white rounded-xl p-6 sm:p-8 shadow-lg border border-slate-200 mb-8 sm:mb-10">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6">
                    <!-- Config Type Selection -->
                    <div>
                        <label for="configType" class="block text-sm font-medium text-slate-700 mb-2">Config Type:</label>
                        <select id="configType" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100">
                            <option value="">Select Config Type</option>
                        </select>
                    </div>

                    <!-- IP Type Selection -->
                    <div>
                        <label for="ipType" class="block text-sm font-medium text-slate-700 mb-2">IP Type:</label>
                        <select id="ipType" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100" disabled>
                            <option value="">Select IP Type</option>
                        </select>
                    </div>

                    <!-- Other Elements Selection -->
                    <div>
                        <label for="otherElement" class="block text-sm font-medium text-slate-700 mb-2">Other Elements:</label>
                        <select id="otherElement" class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2.5 bg-slate-100" disabled>
                            <option value="">Select Element</option>
                        </select>
                    </div>
                </div>

                <!-- Display Area for URL and QR Code -->
                <div id="resultArea" class="hidden bg-slate-50 rounded-lg p-4 sm:p-6 border border-slate-200">
                    <h3 class="text-lg sm:text-xl font-semibold text-slate-800 mb-4">Your Subscription Link:</h3>
                    <div class="flex items-center mb-4">
                        <input type="text" id="subscriptionUrl" readonly
                            class="flex-grow font-mono text-xs sm:text-sm py-2 px-2.5 sm:py-2.5 sm:px-3 bg-white border border-slate-300 rounded-l-lg outline-none whitespace-nowrap overflow-hidden text-ellipsis" />
                        <button id="copyButton" class="flex-shrink-0 flex items-center justify-center w-10 h-10 sm:w-11 sm:h-11 bg-indigo-50 text-indigo-700 border border-indigo-600 rounded-r-lg cursor-pointer transition-colors duration-200 hover:bg-indigo-100" title="Copy URL">
                            <svg class='copy-icon w-4 h-4 sm:w-5 sm:h-5' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'>
                                <path d='M7.5 3.375c0-1.036.84-1.875 1.875-1.875h.375a3.75 3.75 0 0 1 3.75 3.75v1.875C13.5 8.16 12.66 9 11.625 9h-.375a3.75 3.75 0 0 1-3.75-3.75V3.375ZM6.188 1.875a.75.75 0 0 0-1.5 0v1.875a.75.75 0 0 0 .75.75h.375a.75.75 0 0 0 .75-.75V5.25ZM9 3.375a2.25 2.25 0 0 1 2.25-2.25h.375a2.25 2.25 0 0 1 2.25 2.25v1.875a2.25 2.25 0 0 1-2.25 2.25h-.375A2.25 2.25 0 0 1 9 5.25V3.375Z' />
                                <path d='M12.983 9.917a.75.75 0 0 0-1.166-.825l-5.334 3.078a.75.75 0 0 0-.417.825V21a.75.75 0 0 0 .75.75h10.5a.75.75 0 0 0 .75-.75V13a.75.75 0 0 0-.417-.825l-5.333-3.078Z' />
                            </svg>
                            <svg class='check-icon w-4 h-4 sm:w-5 sm:h-5 hidden' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor'>
                                <path fill-rule='evenodd' d='M19.916 4.626a.75.75 0 0 1 .208 1.04l-9 13.5a.75.75 0 0 1-1.154.114l-6-6a.75.75 0 0 1 1.06-1.06l5.353 5.353 8.493-12.74a.75.75 0 0 1 1.04-.207Z' clip-rule='evenodd' />
                            </svg>
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
            // --- Data and Constants ---
            // Data is injected by PHP and available here
            const structuredData = {$json_structured_data};

            // --- DOM Elements (safe to access now) ---
            const configTypeSelect = document.getElementById('configType');
            const ipTypeSelect = document.getElementById('ipType');
            const otherElementSelect = document.getElementById('otherElement');
            const resultArea = document.getElementById('resultArea');
            const subscriptionUrlInput = document.getElementById('subscriptionUrl');
            const copyButton = document.getElementById('copyButton');
            const qrcodeDiv = document.getElementById('qrcode');
            let qrcodeInstance = null; // To store the QR code instance

            // --- Helper Functions ---
            
            // Function to show custom message box
            function showMessageBox(message) {
                const messageBox = document.getElementById('messageBox');
                const messageBoxText = document.getElementById('messageBoxText');
                const messageBoxClose = document.getElementById('messageBoxClose');

                messageBoxText.textContent = message;
                messageBox.classList.remove('hidden');

                messageBoxClose.onclick = () => messageBox.classList.add('hidden');
                messageBox.onclick = (e) => {
                    if (e.target === messageBox) messageBox.classList.add('hidden');
                };
            }

            // Populates a select element
            function populateSelect(selectElement, data, defaultOptionText, disable = false) {
                selectElement.innerHTML = `<option value="">\${defaultOptionText}</option>`;
                const keys = Array.isArray(data) ? data : Object.keys(data);
                keys.forEach(key => {
                    const option = document.createElement('option');
                    option.value = key;

                    let formatType = null;
                    if (selectElement.id === 'ipType') {
                        formatType = key;
                    } else if (selectElement.id === 'otherElement' && ipTypeSelect.value === 'location') {
                        formatType = 'location';
                    } else if (selectElement.id === 'otherElement' && (ipTypeSelect.value === 'xray' || ipTypeSelect.value === 'meta')) {
                         formatType = 'protocol';
                    }

                    option.textContent = formatDisplayName(key, formatType);
                    selectElement.appendChild(option);
                });
                selectElement.disabled = disable;
            }

            // Formats display names for options
            function formatDisplayName(name, type = null) {
                if (type === 'location') {
                    return name.toUpperCase() + ' ' + getFlagEmoji(name);
                } else if (type === 'protocol') {
                    // Capitalize only the first letter for protocols
                     return name.charAt(0).toUpperCase() + name.slice(1).replace(/_/g, ' ');
                }
                // Default: Capitalize words, replace underscores
                return name.split(/[-_]/).map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
            }
            
            // Generates a country flag emoji
            function getFlagEmoji(countryCode) {
                if (typeof countryCode !== 'string' || countryCode.length !== 2 || !/^[A-Z]{2}$/i.test(countryCode)) {
                    return '🏳️';
                }
                countryCode = countryCode.toUpperCase();
                const regionalOffset = 0x1F1E6 - 0x41;
                const char1 = String.fromCodePoint(countryCode.charCodeAt(0) + regionalOffset);
                const char2 = String.fromCodePoint(countryCode.charCodeAt(1) + regionalOffset);
                return char1 + char2;
            }

            // Updates the QR code
            function updateQRCode(url) {
                qrcodeDiv.innerHTML = '';
                if (url) {
                    if (!qrcodeInstance) {
                        qrcodeInstance = new QRCode(qrcodeDiv, {
                            text: url, width: 128, height: 128,
                            colorDark: "#000000", colorLight: "#ffffff",
                            correctLevel: QRCode.CorrectLevel.H
                        });
                    } else {
                        qrcodeInstance.makeCode(url);
                    }
                }
            }

            // --- Event Listeners ---
            configTypeSelect.addEventListener('change', () => {
                const selectedConfigType = configTypeSelect.value;
                ipTypeSelect.value = '';
                otherElementSelect.value = '';
                resultArea.classList.add('hidden');

                if (selectedConfigType && structuredData[selectedConfigType]) {
                    populateSelect(ipTypeSelect, structuredData[selectedConfigType], 'Select Type');
                    otherElementSelect.disabled = true;
                } else {
                    populateSelect(ipTypeSelect, [], 'Select Type', true);
                    populateSelect(otherElementSelect, [], 'Select Element', true);
                }
            });

            ipTypeSelect.addEventListener('change', () => {
                const selectedConfigType = configTypeSelect.value;
                const selectedIpType = ipTypeSelect.value;
                otherElementSelect.value = '';
                resultArea.classList.add('hidden');

                if (selectedConfigType && selectedIpType && structuredData[selectedConfigType][selectedIpType]) {
                    populateSelect(otherElementSelect, structuredData[selectedConfigType][selectedIpType], 'Select Element');
                } else {
                    populateSelect(otherElementSelect, [], 'Select Element', true);
                }
            });

            otherElementSelect.addEventListener('change', () => {
                const selectedConfigType = configTypeSelect.value;
                const selectedIpType = ipTypeSelect.value;
                const selectedOtherElement = otherElementSelect.value;

                if (selectedConfigType && selectedIpType && selectedOtherElement &&
                    structuredData[selectedConfigType]?.[selectedIpType]?.[selectedOtherElement]) {
                    const url = structuredData[selectedConfigType][selectedIpType][selectedOtherElement];
                    subscriptionUrlInput.value = url;
                    updateQRCode(url);
                    resultArea.classList.remove('hidden');
                } else {
                    resultArea.classList.add('hidden');
                    subscriptionUrlInput.value = '';
                    updateQRCode('');
                }
            });

            copyButton.addEventListener('click', () => {
                const url = subscriptionUrlInput.value;
                if (!url) {
                    showMessageBox('No URL to copy. Please select a subscription link first.');
                    return;
                }
                navigator.clipboard.writeText(url).then(() => {
                    const copyIcon = copyButton.querySelector('.copy-icon');
                    const checkIcon = copyButton.querySelector('.check-icon');
                    copyIcon.classList.add('hidden');
                    checkIcon.classList.remove('hidden');
                    setTimeout(() => {
                        copyIcon.classList.remove('hidden');
                        checkIcon.classList.add('hidden');
                    }, 2000);
                }).catch(err => {
                    console.error('Failed to copy URL: ', err);
                    showMessageBox('Failed to copy URL. Please copy manually.');
                });
            });

            // --- Initializer ---
            // Populate the first dropdown on page load
            populateSelect(configTypeSelect, structuredData, 'Select Config Type');
        });
    </script>
</body>
</html>
HTML;
}