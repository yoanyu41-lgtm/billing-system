<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ទូទាត់តាម KHQR - {{ $reference ?? 'វិក្កយបត្រ' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Battambang:wght@400;700&display=swap');
        body {
            font-family: 'Battambang', 'Noto Sans Khmer', sans-serif;
        }
        .bank-button {
            transition: all 0.3s ease;
        }
        .bank-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full mb-4">
                    <i class="fas fa-qrcode text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">ទូទាត់តាម KHQR</h1>
                @if($reference)
                    <p class="text-gray-600">លេខយោង: <span class="font-semibold">{{ $reference }}</span></p>
                @endif
            </div>
            
            <!-- Amount Display -->
            <div class="mt-6 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-6 text-white text-center">
                <p class="text-sm opacity-90 mb-1">ចំនួនទឹកប្រាក់</p>
                <p class="text-4xl font-bold">
                    @if($currency === 'USD')
                        ${{ number_format($amount, 2) }}
                    @else
                        {{ number_format($amount, 0) }} ៛
                    @endif
                </p>
            </div>
        </div>

        <!-- Method Selection -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-mobile-alt mr-2 text-blue-500"></i>
                ជ្រើសរើសវិធីទូទាត់
            </h2>
            
            @if($isPersonalQr)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>ចំណាំ:</strong> QR Code នេះជាប្រភេទផ្ទាល់ខ្លួន។ សូមប្រើប៊ូតុងធនាគារខាងក្រោមដើម្បីទូទាត់ដោយចំនួនទឹកប្រាក់ស្វ័យប្រវត្តិ។
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Deep Link Buttons -->
            <div class="space-y-3 mb-6">
                <p class="text-sm text-gray-600 mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    ចុចលើធនាគាររបស់អ្នកដើម្បីបើកកម្មវិធីជាមួយចំនួនទឹកប្រាក់ដែលបានបំពេញរួច
                </p>
                
                @foreach($deepLinks as $bankCode => $link)
                    <a href="{{ $link['url'] }}" 
                       class="bank-button flex items-center justify-between w-full p-4 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-blue-50 hover:to-indigo-50 rounded-xl border-2 border-gray-200 hover:border-blue-300">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                @if($bankCode === 'aba')
                                    <span class="text-red-600 font-bold text-lg">ABA</span>
                                @elseif($bankCode === 'wing')
                                    <span class="text-blue-600 font-bold text-lg">WING</span>
                                @elseif($bankCode === 'acleda')
                                    <span class="text-green-600 font-bold text-sm">ACLEDA</span>
                                @elseif($bankCode === 'sathapana')
                                    <span class="text-orange-600 font-bold text-xs">Sathapana</span>
                                @elseif($bankCode === 'phillip')
                                    <span class="text-purple-600 font-bold text-sm">Phillip</span>
                                @else
                                    <i class="fas fa-university text-gray-600"></i>
                                @endif
                            </div>
                            <div class="text-left">
                                <p class="font-semibold text-gray-800">{{ $link['name'] }}</p>
                                <p class="text-xs text-gray-500">បើកកម្មវិធីដោយស្វ័យប្រវត្តិ</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                @endforeach
            </div>

            <!-- OR Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500 font-medium">ឬ</span>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="text-center">
                <p class="text-gray-700 mb-4 flex items-center justify-center">
                    <i class="fas fa-qrcode mr-2 text-gray-500"></i>
                    ស្កែន QR Code ដោយកម្មវិធីធនាគាររបស់អ្នក
                </p>
                
                @if($qrImageUrl)
                    <div class="inline-block p-4 bg-white rounded-xl shadow-lg">
                        <img src="{{ $qrImageUrl }}" 
                             alt="KHQR Code" 
                             class="w-64 h-64 mx-auto">
                    </div>
                    
                    @if($isPersonalQr)
                        <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg inline-block">
                            <p class="text-sm text-orange-800">
                                <i class="fas fa-hand-point-up mr-1"></i>
                                សូមបញ្ចូលចំនួនទឹកប្រាក់ដោយដៃ: 
                                <strong>
                                    @if($currency === 'USD')
                                        ${{ number_format($amount, 2) }}
                                    @else
                                        {{ number_format($amount, 0) }} ៛
                                    @endif
                                </strong>
                            </p>
                        </div>
                    @endif
                @else
                    <div class="p-8 bg-red-50 rounded-xl">
                        <i class="fas fa-exclamation-circle text-red-500 text-3xl mb-2"></i>
                        <p class="text-red-600">មិនអាចបង្កើត QR Code បានទេ</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-question-circle mr-2 text-blue-500"></i>
                របៀបប្រើប្រាស់
            </h3>
            <ol class="space-y-3 text-gray-700">
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">1</span>
                    <span><strong>វិធីលឿន:</strong> ចុចលើប៊ូតុងធនាគាររបស់អ្នក ដើម្បីបើកកម្មវិធីជាមួយចំនួនទឹកប្រាក់ដែលបានបំពេញរួច</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">2</span>
                    <span><strong>វិធីធម្មតា:</strong> ស្កែន QR Code ខាងលើដោយកម្មវិធីធនាគាររបស់អ្នក</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">3</span>
                    <span>បញ្ជាក់ចំនួនទឹកប្រាក់ និងបញ្ចូលលេខកូដសុវត្ថិភាព (PIN)</span>
                </li>
                <li class="flex items-start">
                    <span class="flex-shrink-0 w-6 h-6 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-bold mr-3 mt-0.5">4</span>
                    <span>រង់ចាំការបញ្ជាក់ពីប្រព័ន្ធ</span>
                </li>
            </ol>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600 text-sm">
            <p>
                <i class="fas fa-shield-alt mr-1"></i>
                ការទូទាត់របស់អ្នកត្រូវបានការពារដោយសុវត្ថិភាព KHQR
            </p>
            <p class="mt-2 text-xs text-gray-500">
                Powered by KHQR - National Bank of Cambodia
            </p>
        </div>
    </div>

    <script>
        // Copy payload to clipboard function
        function copyPayload() {
            const payload = '{{ $payload }}';
            navigator.clipboard.writeText(payload).then(() => {
                alert('បានចម្លង QR Code payload រួចហើយ!');
            });
        }

        // Detect mobile device and auto-suggest deep link
        function detectAndSuggestBank() {
            const userAgent = navigator.userAgent.toLowerCase();
            
            // Try to detect which banking app is installed
            // This is a basic implementation - real detection requires app-specific logic
            
            if (window.matchMedia('(display-mode: standalone)').matches) {
                console.log('Running as installed app');
            }
        }

        window.addEventListener('load', detectAndSuggestBank);
    </script>
</body>
</html>
