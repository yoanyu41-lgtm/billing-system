<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Setting;
use App\Models\Payment;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TelegramController extends Controller
{
    public function __construct(private readonly TelegramService $telegramService)
    {
    }

    public function webhook(Request $request)
    {
        $update = $request->all();
        Log::info('Telegram webhook received', ['update' => $update]);

        // 1. Handle incoming message
        if (isset($update['message'])) {
            $chatId = data_get($update, 'message.chat.id');
            
            // Handle contact sharing
            if (isset($update['message']['contact'])) {
                $contact = $update['message']['contact'];
                $phoneNumber = $contact['phone_number'];
                $contactUserId = $contact['user_id'] ?? null;
                $fromId = data_get($update, 'message.from.id');

                // Security check: only link if the shared contact matches the sender's user ID
                if ($contactUserId && (int)$contactUserId !== (int)$fromId) {
                    $this->replyToChat($chatId, "⚠️ លោកអ្នកអាចចែករំលែកតែលេខទូរស័ព្ទផ្ទាល់ខ្លួនរបស់លោកអ្នកប៉ុណ្ណោះ។");
                    return response('OK');
                }

                $cleanPhone = $this->cleanPhoneNumber($phoneNumber);
                $customers = Customer::all();
                $matchedCustomer = null;

                foreach ($customers as $customer) {
                    if ($this->cleanPhoneNumber($customer->phone) === $cleanPhone) {
                        $matchedCustomer = $customer;
                        break;
                    }
                }

                if ($matchedCustomer) {
                    $matchedCustomer->update(['telegram_id' => $chatId]);

                    $msg = "✅ <b>ភ្ជាប់គណនីបានជោគជ័យ!</b>\n\n";
                    $msg .= "សូមស្វាគមន៍ <b>" . htmlspecialchars($matchedCustomer->name) . "</b> មកកាន់ប្រព័ន្ធបង់ប្រាក់របស់ពួកយើង។\n\n";
                    $msg .= "លោកអ្នកអាចប្រើប្រាស់ប៊ូតុងម៉ឺនុយខាងក្រោមដើម្បីពិនិត្យព័ត៌មានគណនី ឬការបង់រំលស់របស់លោកអ្នក។";

                    $this->replyToChat($chatId, $msg, $this->getMainMenuKeyboard());
                } else {
                    $msg = "❌ <b>រកមិនឃើញលេខទូរស័ព្ទ!</b>\n\n";
                    $msg .= "លេខទូរស័ព្ទ <code>" . htmlspecialchars($phoneNumber) . "</code> មិនទាន់ត្រូវបានចុះឈ្មោះក្នុងប្រព័ន្ធបង់រំលស់របស់យើងនៅឡើយទេ។\n\n";
                    $msg .= "សូមទាក់ទងមកខាងហាងដើម្បីចុះឈ្មោះលេខទូរស័ព្ទនេះជាមុនសិន។";

                    $this->replyToChat($chatId, $msg, $this->getRegisterKeyboard());
                }

                return response('OK');
            }

            // Handle photo uploads (payment receipts)
            if (isset($update['message']['photo'])) {
                $customer = Customer::where('telegram_id', $chatId)->first();

                if (! $customer) {
                    $msg = "⚠️ <b>គណនីមិនទាន់បានភ្ជាប់!</b>\n\n";
                    $msg .= "សូមភ្ជាប់គណនីរបស់លោកអ្នកជាមុនសិន ដោយចុចប៊ូតុងខាងក្រោម៖";
                    $this->replyToChat($chatId, $msg, $this->getRegisterKeyboard());
                    return response('OK');
                }

                $installment = $customer->installments()->whereIn('status', ['active', 'overdue'])->first();

                if (! $installment) {
                    $this->replyToChat($chatId, "❌ រកមិនឃើញគម្រោងបង់រំលស់ដែលកំពុងដំណើរការ សម្រាប់គណនីរបស់លោកអ្នកឡើយ។", $this->getMainMenuKeyboard());
                    return response('OK');
                }

                $photoArray = $update['message']['photo'];
                $highestResPhoto = end($photoArray);
                $fileId = $highestResPhoto['file_id'];
                
                $token = config('services.telegram.bot_token') ?: Setting::where('key', 'telegram_token')->value('value');

                try {
                    $fileResponse = Http::timeout(15)->get("https://api.telegram.org/bot{$token}/getFile", [
                        'file_id' => $fileId,
                    ]);

                    if ($fileResponse->successful() && data_get($fileResponse->json(), 'ok') === true) {
                        $filePath = data_get($fileResponse->json(), 'result.file_path');
                        $fileUrl = "https://api.telegram.org/file/bot{$token}/{$filePath}";

                        // Download the photo
                        $imageContent = Http::timeout(30)->get($fileUrl)->body();

                        $filename = 'telegram_' . time() . '_' . uniqid() . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
                        
                        if (!file_exists(storage_path('app/public/qr_images'))) {
                            mkdir(storage_path('app/public/qr_images'), 0755, true);
                        }

                        file_put_contents(storage_path('app/public/qr_images/' . $filename), $imageContent);

                        $qrImagePath = 'qr_images/' . $filename;

                        // Find payment method (default to "QR Code", "ABA" or first method)
                        $paymentMethodId = \App\Models\PaymentMethod::where('name', 'like', '%QR%')
                            ->orWhere('name', 'like', '%ABA%')
                            ->value('id') 
                            ?: (\App\Models\PaymentMethod::first()->id ?? null);

                        Payment::create([
                            'installment_id' => $installment->id,
                            'amount' => $installment->monthly_payment,
                            'payment_date' => now()->toDateString(),
                            'payment_method_id' => $paymentMethodId,
                            'status' => 'pending',
                            'qr_image' => $qrImagePath,
                        ]);

                        $msg = "⏳ <b>ទទួលបានវិក្កយបត្ររួចរាល់!</b>\n\n";
                        $msg .= "ការទូទាត់ប្រាក់ចំនួន <b>" . $this->formatPrice($installment->monthly_payment) . "</b> ត្រូវបានបញ្ចូលទៅក្នុងប្រព័ន្ធរង់ចាំការពិនិត្យ។\n\n";
                        $msg .= "ក្រុមការងារកំពុងផ្ទៀងផ្ទាត់ការទូទាត់ប្រាក់របស់លោកអ្នក។ យើងខ្ញុំនឹងផ្ញើសារជូនដំណឹងនៅពេលទទួលបានការអនុម័តជោគជ័យ។";
                        
                        $this->replyToChat($chatId, $msg, $this->getMainMenuKeyboard());
                    } else {
                        $this->replyToChat($chatId, "❌ ការទាញយករូបភាពវិក្កយបត្រមានបញ្ហា។ សូមសាកល្បងផ្ញើម្តងទៀត។", $this->getMainMenuKeyboard());
                    }
                } catch (\Throwable $e) {
                    Log::error('Telegram photo download error: ' . $e->getMessage());
                    $this->replyToChat($chatId, "❌ កើតមានកំហុសក្នុងការរក្សាទុករូបភាពវិក្កយបត្រ។ សូមសាកល្បងម្តងទៀត។", $this->getMainMenuKeyboard());
                }

                return response('OK');
            }

            // Handle normal text message
            $text = trim((string) data_get($update, 'message.text', ''));

            if ($text === '') {
                return response('OK');
            }

            // Command /start
            if (str_starts_with($text, '/start')) {
                $parts = explode(' ', $text);
                $parameter = isset($parts[1]) ? trim($parts[1]) : null;

                if ($parameter) {
                    $customer = null;
                    if (is_numeric($parameter)) {
                        if (strlen($parameter) < 6) {
                            $customer = Customer::find($parameter);
                        } else {
                            $cleanPhone = $this->cleanPhoneNumber($parameter);
                            $customers = Customer::all();
                            foreach ($customers as $c) {
                                if ($this->cleanPhoneNumber($c->phone) === $cleanPhone) {
                                    $customer = $c;
                                    break;
                                }
                            }
                        }
                    } else {
                        if (str_starts_with(strtolower($parameter), 'cust_')) {
                            $id = substr($parameter, 5);
                            $customer = Customer::find($id);
                        }
                    }

                    if ($customer) {
                        $customer->update(['telegram_id' => $chatId]);

                        $msg = "✅ <b>ភ្ជាប់គណនីបានជោគជ័យ!</b>\n\n";
                        $msg .= "សូមស្វាគមន៍ <b>" . htmlspecialchars($customer->name) . "</b> មកកាន់ប្រព័ន្ធបង់ប្រាក់របស់ពួកយើង។\n\n";
                        $msg .= "លោកអ្នកអាចប្រើប្រាស់ប៊ូតុងម៉ឺនុយខាងក្រោមដើម្បីឆែកព័ត៌មានគណនី និងការបង់រំលស់។";

                        $this->replyToChat($chatId, $msg, $this->getMainMenuKeyboard());
                        return response('OK');
                    } else {
                        $this->replyToChat($chatId, "❌ រកមិនឃើញគណនីដែលត្រូវនឹងកូដភ្ជាប់នេះទេ។", $this->getRegisterKeyboard());
                        return response('OK');
                    }
                }

                $customer = Customer::where('telegram_id', $chatId)->first();
                if ($customer) {
                    $msg = "👋 សួស្តី <b>" . htmlspecialchars($customer->name) . "</b>!\n\n";
                    $msg .= "គណនីរបស់អ្នកត្រូវបានភ្ជាប់រួចរាល់ហើយ។ សូមជ្រើសរើសជម្រើសពីម៉ឺនុយខាងក្រោម៖";
                    $this->replyToChat($chatId, $msg, $this->getMainMenuKeyboard());
                } else {
                    $msg = "👋 <b>សូមស្វាគមន៍មកកាន់ប្រព័ន្ធបង់រំលស់របស់យើង!</b>\n\n";
                    $msg .= "ដើម្បីអាចឆែកព័ត៌មានការបង់រំលស់ និងប្រវត្តិនៃការបង់ប្រាក់បាន សូមចុចប៊ូតុង <b>«📱 ចែករំលែកលេខទូរស័ព្ទដើម្បីភ្ជាប់គណនី»</b> ខាងក្រោម ដើម្បីភ្ជាប់គណនី Telegram នេះជាមួយប្រព័ន្ធរបស់យើង។";
                    $this->replyToChat($chatId, $msg, $this->getRegisterKeyboard());
                }
                return response('OK');
            }

            // Command /unlink
            if ($text === '/unlink') {
                $customer = Customer::where('telegram_id', $chatId)->first();
                if ($customer) {
                    $customer->update(['telegram_id' => null]);
                    $msg = "⚠️ <b>ផ្តាច់គណនីរួចរាល់!</b>\n\n";
                    $msg .= "គណនីរបស់លោកអ្នកត្រូវបានផ្តាច់ពីប្រព័ន្ធ Telegram Bot នេះហើយ។ បើលោកអ្នកចង់ភ្ជាប់ឡើងវិញ សូមប្រើប្រាស់ប៊ូតុងខាងក្រោម。";
                    $this->replyToChat($chatId, $msg, $this->getRegisterKeyboard());
                } else {
                    $this->replyToChat($chatId, "❌ គណនី Telegram នេះមិនទាន់បានភ្ជាប់ជាមួយប្រព័ន្ធណាមួយឡើយ។", $this->getRegisterKeyboard());
                }
                return response('OK');
            }

            // Command /id
            if ($text === '/id') {
                $this->replyToChat($chatId, 'Your Telegram chat ID is: ' . $chatId);
                return response('OK');
            }

            // Check if user is linked for other menu options
            $customer = Customer::where('telegram_id', $chatId)->first();

            if (! $customer) {
                // If they are not linked, check if they are clicking general info buttons
                if ($text === '📞 ទំនាក់ទំនងហាង') {
                    $this->sendContactShopInfo($chatId);
                } elseif ($text === '❓ ជំនួយ') {
                    $this->sendHelpInfo($chatId, false);
                } else {
                    $msg = "⚠️ <b>គណនីមិនទាន់បានភ្ជាប់!</b>\n\n";
                    $msg .= "សូមភ្ជាប់គណនីរបស់លោកអ្នកជាមុនសិន ដោយចុចប៊ូតុងខាងក្រោម៖";
                    $this->replyToChat($chatId, $msg, $this->getRegisterKeyboard());
                }
                return response('OK');
            }

            // Linked Customer actions
            switch ($text) {
                case '📋 គណនីរបស់ខ្ញុំ':
                    $activeCount = $customer->installments()->whereIn('status', ['active', 'overdue'])->count();
                    $completedCount = $customer->installments()->where('status', 'completed')->count();

                    $msg = "👤 <b>ព័ត៌មានគណនីរបស់លោកអ្នក</b>\n\n";
                    $msg .= "• <b>ឈ្មោះ៖</b> " . htmlspecialchars($customer->name) . "\n";
                    $msg .= "• <b>ភេទ៖</b> " . ($customer->gender === 'female' ? 'ស្រី' : 'ប្រុស') . "\n";
                    $msg .= "• <b>លេខទូរស័ព្ទ៖</b> " . htmlspecialchars($customer->phone) . "\n";
                    $msg .= "• <b>អត្តសញ្ញាណប័ណ្ណ៖</b> " . htmlspecialchars($customer->id_card ?? '-') . "\n";
                    $msg .= "• <b>អាសយដ្ឋាន៖</b> " . htmlspecialchars($customer->address ?? '-') . "\n\n";
                    $msg .= "📊 <b>ស្ថានភាពគម្រោង៖</b>\n";
                    $msg .= "• កំពុងដំណើរការ៖ <b>{$activeCount}</b> គម្រោង\n";
                    $msg .= "• បានបង់រួចរាល់៖ <b>{$completedCount}</b> គម្រោង\n";

                    $this->replyToChat($chatId, $msg, $this->getMainMenuKeyboard());
                    break;

                case '💳 ការបង់រំលស់':
                    $installments = $customer->installments()
                        ->whereIn('status', ['active', 'overdue'])
                        ->with('product')
                        ->get();

                    if ($installments->isEmpty()) {
                        $this->replyToChat($chatId, "❌ លោកអ្នកមិនមានគម្រោងបង់រំលស់ដែលកំពុងដំណើរការនោះទេ។", $this->getMainMenuKeyboard());
                        break;
                    }

                    $msg = "💳 <b>បញ្ជីគម្រោងបង់រំលស់កំពុងដំណើរការ (" . $installments->count() . ")</b>\n\n";

                    foreach ($installments as $index => $inst) {
                        $productName = $inst->product ? $inst->product->name : 'ផលិតផលមិនស្គាល់';
                        $statusText = $inst->status === 'overdue' ? '🔴 ហួសកាលកំណត់ (Overdue)' : '🟢 កំពុងដំណើរការ (Active)';

                        $msg .= "<b>" . ($index + 1) . ". " . htmlspecialchars($productName) . "</b>\n";
                        if ($inst->product) {
                            $specs = [];
                            if ($inst->product->cpu) $specs[] = 'CPU: ' . $inst->product->cpu;
                            if ($inst->product->ram) $specs[] = 'RAM: ' . $inst->product->ram;
                            if ($inst->product->storage) $specs[] = 'Storage: ' . $inst->product->storage;
                            if (!empty($specs)) {
                                $msg .= "   <i>(" . implode(', ', $specs) . ")</i>\n";
                            }
                        }
                        $msg .= "   • តម្លៃសរុប៖ " . $this->formatPrice($inst->total_price) . "\n";
                        $msg .= "   • ប្រាក់កក់មុន៖ " . $this->formatPrice($inst->down_payment) . "\n";
                        $msg .= "   • ត្រូវបង់ប្រចាំខែ៖ <b>" . $this->formatPrice($inst->monthly_payment) . " / ខែ</b>\n";
                        $msg .= "   • ប្រាក់នៅសល់៖ <b>" . $this->formatPrice($inst->remaining_balance) . "</b>\n";

                        if ($inst->next_due_date) {
                            $dueDate = Carbon::parse($inst->next_due_date)->format('d-M-Y');
                            $msg .= "   • ថ្ងៃត្រូវបង់បន្ទាប់៖ <b>{$dueDate}</b>\n";
                        }
                        $msg .= "   • ស្ថានភាព៖ {$statusText}\n\n";
                    }

                    $this->replyToChat($chatId, $msg, $this->getMainMenuKeyboard());
                    break;

                case '📜 ប្រវត្តិបង់ប្រាក់':
                    $installmentIds = $customer->installments()->pluck('id');

                    $payments = Payment::whereIn('installment_id', $installmentIds)
                        ->with(['installment.product', 'paymentMethod'])
                        ->latest('payment_date')
                        ->latest('created_at')
                        ->limit(5)
                        ->get();

                    if ($payments->isEmpty()) {
                        $this->replyToChat($chatId, "❌ មិនទាន់មានប្រវត្តិបង់ប្រាក់នៅក្នុងប្រព័ន្ធនៅឡើយទេ។", $this->getMainMenuKeyboard());
                        break;
                    }

                    $msg = "📜 <b>ប្រវត្តិនៃការបង់ប្រាក់ (៥ លើកចុងក្រោយ)</b>\n\n";

                    foreach ($payments as $payment) {
                        $date = Carbon::parse($payment->payment_date)->format('d-M-Y');
                        $productName = $payment->installment && $payment->installment->product
                            ? $payment->installment->product->name
                            : 'គម្រោងបង់រំលស់';

                        $method = $payment->paymentMethod ? $payment->paymentMethod->name : 'សាច់ប្រាក់';

                        $statusEmoji = '⏳';
                        $statusText = 'រង់ចាំការអនុម័ត';
                        if ($payment->status === 'approved') {
                            $statusEmoji = '✅';
                            $statusText = 'បានអនុម័ត';
                        } elseif ($payment->status === 'rejected') {
                            $statusEmoji = '❌';
                            $statusText = 'បានបដិសេធ';
                        }

                        $msg .= "{$statusEmoji} <b>" . $this->formatPrice($payment->amount) . "</b> - {$statusText}\n";
                        $msg .= "   • ថ្ងៃបង់៖ {$date}\n";
                        $msg .= "   • បង់តាម៖ {$method}\n";
                        $msg .= "   • សម្រាប់៖ " . htmlspecialchars($productName) . "\n\n";
                    }

                    $this->replyToChat($chatId, $msg, $this->getMainMenuKeyboard());
                    break;

                case '🏦 ព័ត៌មានបង់ប្រាក់ & QR':
                    $shopName = Setting::where('key', 'company_name_km')->value('value')
                        ?: Setting::where('key', 'company_name')->value('value')
                        ?: 'CityTech Computer Shop';

                    $bankQr = Setting::where('key', 'company_bank_qr')->value('value');

                    $msg = "🏦 <b>ព័ត៌មានបង់ប្រាក់របស់ហាង " . htmlspecialchars($shopName) . "</b>\n\n";
                    $msg .= "លោកអ្នកអាចធ្វើការទូទាត់ប្រាក់ប្រចាំខែតាមរយៈគណនីធនាគាររបស់ហាងដូចខាងក្រោម៖\n\n";
                    $msg .= "🏦 <b>ធនាគារ ៖ ABA Bank</b>\n";
                    $msg .= "👤 <b>ឈ្មោះគណនី ៖ CITYTECH COMPUTER</b>\n";
                    $msg .= "🔢 <b>លេខគណនី ៖ 000 111 222</b>\n\n";
                    $msg .= "⚠️ <i>បញ្ជាក់៖ បន្ទាប់ពីផ្ទេរប្រាក់រួច សូមផ្ញើវិក្កយបត្រផ្ទេរប្រាក់មកកាន់ក្រុមការងារដើម្បីផ្ទៀងផ្ទាត់ និងអនុម័តការបង់ប្រាក់។</i>";

                    if ($bankQr) {
                        $this->replyPhotoToChat($chatId, $bankQr, $msg, $this->getMainMenuKeyboard());
                    } else {
                        $this->replyToChat($chatId, $msg, $this->getMainMenuKeyboard());
                    }
                    break;

                case '📞 ទំនាក់ទំនងហាង':
                    $this->sendContactShopInfo($chatId);
                    break;

                case '❓ ជំនួយ':
                    $this->sendHelpInfo($chatId, true);
                    break;

                default:
                    $msg = "❓ ខ្ញុំមិនយល់ពីសារនេះទេ។ សូមជ្រើសរើសជម្រើសពីម៉ឺនុយខាងក្រោម ឬវាយពាក្យ <b>❓ ជំនួយ</b> ដើម្បីដឹងពីរបៀបប្រើប្រាស់។";
                    $this->replyToChat($chatId, $msg, $this->getMainMenuKeyboard());
                    break;
            }
        }

        return response('OK');
    }

    public function sendMessage($customerId, $message)
    {
        return $this->telegramService->sendToCustomer($customerId, $message);
    }

    private function cleanPhoneNumber(?string $phone): string
    {
        if (blank($phone)) {
            return '';
        }

        // Remove all non-numeric characters
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        // If it starts with 855, replace it with 0
        if (str_starts_with($cleaned, '855')) {
            $cleaned = '0' . substr($cleaned, 3);
        }

        // If it doesn't start with 0, prepend 0
        if (!str_starts_with($cleaned, '0') && strlen($cleaned) > 0) {
            $cleaned = '0' . $cleaned;
        }

        return $cleaned;
    }

    private function formatPrice($amount): string
    {
        $exchangeRate = Setting::where('key', 'exchange_rate')->value('value') ?: 4100;
        $usd = '$' . number_format($amount, 2);
        $riel = number_format(round($amount * $exchangeRate)) . ' ៛';
        return "{$usd} ({$riel})";
    }

    private function sendContactShopInfo($chatId): void
    {
        $shopName = Setting::where('key', 'company_name_km')->value('value')
            ?: Setting::where('key', 'company_name')->value('value')
            ?: 'CityTech Computer Shop';

        $phone = Setting::where('key', 'company_phone')->value('value') ?: '012-345-678';
        $email = Setting::where('key', 'company_email')->value('value') ?: 'info@citytech.com';
        $address = Setting::where('key', 'company_address_km')->value('value')
            ?: Setting::where('key', 'company_address')->value('value')
            ?: 'ភ្នំពេញ ប្រទេសកម្ពុជា';

        $msg = "📞 <b>ព័ត៌មានទំនាក់ទំនងហាង</b>\n\n";
        $msg .= "🏢 <b>" . htmlspecialchars($shopName) . "</b>\n";
        $msg .= "📞 <b>ទូរស័ព្ទ៖</b> " . htmlspecialchars($phone) . "\n";
        $msg .= "✉️ <b>អ៊ីមែល៖</b> " . htmlspecialchars($email) . "\n";
        $msg .= "📍 <b>អាសយដ្ឋាន៖</b> " . htmlspecialchars($address) . "\n";
        $msg .= "🕒 <b>ម៉ោងធ្វើការ៖</b> ៨:០០ ព្រឹក - ៦:០០ ល្ងាច (ចន្ទ - អាទិត្យ)";

        $customer = Customer::where('telegram_id', $chatId)->first();
        $keyboard = $customer ? $this->getMainMenuKeyboard() : $this->getRegisterKeyboard();

        $this->replyToChat($chatId, $msg, $keyboard);
    }

    private function sendHelpInfo($chatId, bool $isLinked): void
    {
        $msg = "❓ <b>របៀបប្រើប្រាស់ Telegram Bot នេះ៖</b>\n\n";
        if ($isLinked) {
            $msg .= "• <b>📋 គណនីរបស់ខ្ញុំ៖</b> ពិនិត្យមើលព័ត៌មានផ្ទាល់ខ្លួន និងចំនួនគម្រោងបង់រំលស់\n";
            $msg .= "• <b>💳 ការបង់រំលស់៖</b> មើលព័ត៌មានលម្អិតនៃគម្រោងបង់រំលស់កំពុងដំណើរការ ចំនួនប្រាក់ត្រូវបង់ និងថ្ងៃត្រូវបង់បន្ទាប់\n";
            $msg .= "• <b>📜 ប្រវត្តិបង់ប្រាក់៖</b> មើលប្រវត្តិនៃការបង់ប្រាក់ ៥ លើកចុងក្រោយរបស់លោកអ្នក\n";
            $msg .= "• <b>🏦 ព័ត៌មានបង់ប្រាក់ &amp; QR៖</b> ទទួលបានព័ត៌មានគណនីធនាគារ និងរូបភាព QR Code សម្រាប់ទូទាត់ប្រាក់\n";
            $msg .= "• <b>📞 ទំនាក់ទំនងហាង៖</b> ទទួលបានលេខទូរស័ព្ទ អាសយដ្ឋាន និងម៉ោងធ្វើការរបស់ហាង\n\n";
            $msg .= "👉 ប្រសិនបើលោកអ្នកចង់ផ្តាច់គណនីនេះពីប្រព័ន្ធ សូមវាយបញ្ជា៖ /unlink\n\n";
        } else {
            $msg .= "• ចុចប៊ូតុង <b>«📱 ចែករំលែកលេខទូរស័ព្ទដើម្បីភ្ជាប់គណនី»</b> ដើម្បីភ្ជាប់គណនី Telegram នេះជាមួយប្រព័ន្ធបង់រំលស់\n";
            $msg .= "• ចុចប៊ូតុង <b>«📞 ទំនាក់ទំនងហាង»</b> ដើម្បីមើលព័ត៌មានទំនាក់ទំនងរបស់ហាង\n\n";
            $msg .= "👉 ប្រសិនបើលោកអ្នកមានកូដភ្ជាប់គណនីពីខាងហាង លោកអ្នកអាចភ្ជាប់ដោយវាយបញ្ជា៖ <code>/start [កូដភ្ជាប់]</code>\n\n";
        }

        $msg .= "👉 សម្រាប់ជំនួយបន្ថែម ឬសាកសួរផ្ទាល់ សូមទាក់ទងមកកាន់ Admin តាមរយៈ Telegram៖ <a href=\"https://t.me/+85569244286\">ចុចទីនេះដើម្បីទាក់ទង</a>";

        $keyboard = $isLinked ? $this->getMainMenuKeyboard() : $this->getRegisterKeyboard();
        $this->replyToChat($chatId, $msg, $keyboard);
    }

    private function getRegisterKeyboard(): array
    {
        return [
            'keyboard' => [
                [
                    [
                        'text' => '📱 ចែករំលែកលេខទូរស័ព្ទដើម្បីភ្ជាប់គណនី',
                        'request_contact' => true
                    ]
                ],
                [
                    ['text' => '📞 ទំនាក់ទំនងហាង'],
                    ['text' => '❓ ជំនួយ']
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];
    }

    private function getMainMenuKeyboard(): array
    {
        return [
            'keyboard' => [
                [
                    ['text' => '📋 គណនីរបស់ខ្ញុំ'],
                    ['text' => '💳 ការបង់រំលស់']
                ],
                [
                    ['text' => '📜 ប្រវត្តិបង់ប្រាក់'],
                    ['text' => '🏦 ព័ត៌មានបង់ប្រាក់ & QR']
                ],
                [
                    ['text' => '📞 ទំនាក់ទំនងហាង'],
                    ['text' => '❓ ជំនួយ']
                ]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];
    }

    private function replyToChat($chatId, string $message, ?array $replyMarkup = null): void
    {
        $token = config('services.telegram.bot_token') ?: Setting::where('key', 'telegram_token')->value('value');

        if (blank($token) || blank($chatId)) {
            return;
        }

        $params = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        Http::asForm()->timeout(15)->post("https://api.telegram.org/bot{$token}/sendMessage", $params);
    }

    private function replyPhotoToChat($chatId, string $photoPath, string $caption, ?array $replyMarkup = null): void
    {
        $token = config('services.telegram.bot_token') ?: Setting::where('key', 'telegram_token')->value('value');

        if (blank($token) || blank($chatId)) {
            return;
        }

        $fullPath = storage_path('app/public/' . $photoPath);
        if (!file_exists($fullPath)) {
            // Fallback to text message if photo file is missing in storage
            $this->replyToChat($chatId, $caption, $replyMarkup);
            return;
        }

        $params = [
            'chat_id' => $chatId,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        Http::attach(
            'photo', file_get_contents($fullPath), basename($photoPath)
        )->post("https://api.telegram.org/bot{$token}/sendPhoto", $params);
    }
}
