<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Services\Installer\DatabaseInstaller;
use App\Services\Installer\InstallationFinalizer;
use App\Services\Installer\InstallationState;
use App\Services\Installer\RequirementsChecker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
class InstallController extends Controller
{
    private const WEAK_PASSWORDS = ['admin123', 'password', 'password1', '12345678', '123456789', 'qwerty123', 'admin1234', 'sifre123'];
    public function __construct(
        private readonly InstallationState $state,
        private readonly RequirementsChecker $requirements,
        private readonly DatabaseInstaller $databases,
        private readonly InstallationFinalizer $finalizer,
    ) {}
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('install.step', ['step' => $this->firstPending($request)]);
    }
    public function show(Request $request, string $step): View|RedirectResponse
    {
        if (! $this->accessible($request, $step)) {
            return redirect()->route('install.step', ['step' => $this->firstPending($request)]);
        }

        return view('installer.steps.'.$step, $this->viewData($request, $step));
    }
    public function store(Request $request, string $step): RedirectResponse
    {
        if (! $this->accessible($request, $step)) {
            return redirect()->route('install.step', ['step' => $this->firstPending($request)]);
        }
        $result = match ($step) {
            'requirements' => $this->saveRequirements(),
            'application' => $this->saveApplication($request),
            'database' => $this->saveDatabase($request),
            'admin' => $this->saveAdmin($request),
            'site' => $this->saveSite($request),
            'communication' => $this->saveCommunication($request),
            'visual' => $this->saveVisual($request),
            default => abort(404),
        };
        if ($result instanceof RedirectResponse) {
            return $result;
        }
        $request->session()->put('installer.data.'.$step, $result);
        $request->session()->put('installer.done.'.$step, true);
        return redirect()->route('install.step', ['step' => $this->nextStep($step)]);
    }
    public function testDatabase(Request $request): JsonResponse
    {
        $data = $this->validateDatabase($request);
        if (! ($this->requirements->availableDrivers()[$data['driver']] ?? false)) {
            return response()->json(['ok' => false, 'message' => 'Seçilen sürücü için PHP eklentisi yüklü değil.'], 422);
        }
        try {
            $info = $this->databases->inspect($data, $data['create_database']);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'Bağlantı kurulamadı: '.Str::limit($e->getMessage(), 300)], 422);
        }
        $message = 'Bağlantı başarılı. Sunucu sürümü: '.$info['version'].'.';
        if ($info['has_users']) {
            return response()->json(['ok' => false, 'message' => $message.' Ancak bu veritabanında zaten kullanıcı kayıtları var; sihirbaz boş veritabanı gerektirir.'], 422);
        }
        if ($info['tables'] > 0) {
            $message .= ' Veritabanında '.$info['tables'].' tablo var; mevcut tablolar korunur, yalnızca eksik tablolar oluşturulur.';
        }
        return response()->json(['ok' => true, 'message' => $message, 'tables' => $info['tables']]);
    }
    public function testMail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mail_host' => ['required', 'string', 'max:190'],
            'mail_port' => ['required', 'integer', 'between:1,65535'],
            'mail_scheme' => ['nullable', Rule::in(['', 'none', 'smtps'])],
            'mail_username' => ['nullable', 'string', 'max:190'],
            'mail_password' => ['nullable', 'string', 'max:190'],
            'mail_from_address' => ['required', 'email', 'max:190'],
            'mail_from_name' => ['required', 'string', 'max:120'],
            'test_email' => ['required', 'email', 'max:190'],
        ]);
        $password = (string) ($validated['mail_password'] ?? '');
        if ($password === '') {
            $saved = $request->session()->get('installer.data.communication.mail.password');
            $password = $saved ? Crypt::decryptString($saved) : '';
        }
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp' => [
                'transport' => 'smtp',
                'scheme' => ($validated['mail_scheme'] ?? '') === 'smtps' ? 'smtps' : null,
                'host' => $validated['mail_host'],
                'port' => (int) $validated['mail_port'],
                'username' => $validated['mail_username'] ?: null,
                'password' => $password !== '' ? $password : null,
                'timeout' => 10,
            ],
            'mail.from' => ['address' => $validated['mail_from_address'], 'name' => $validated['mail_from_name']],
        ]);
        Mail::purge('smtp');
        try {
            Mail::mailer('smtp')->raw(
                'Bu ileti Site360Pro kurulum sihirbazından gönderilen SMTP bağlantı testidir.',
                fn ($message) => $message->to($validated['test_email'])->subject('Site360Pro SMTP bağlantı testi'),
            );
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => 'E-posta gönderilemedi: '.Str::limit($e->getMessage(), 300)], 422);
        }
        return response()->json(['ok' => true, 'message' => $validated['test_email'].' adresine test e-postası gönderildi.']);
    }
    public function finish(Request $request): View|RedirectResponse
    {
        if ($this->firstPending($request) !== 'summary') {
            return redirect()->route('install.step', ['step' => $this->firstPending($request)]);
        }
        $request->validate(
            ['confirm' => ['accepted']],
            ['confirm.accepted' => 'Devam etmek için bilgileri kontrol ettiğinizi onaylayın.'],
        );
        $data = $request->session()->get('installer.data', []);
        $database = $data['database'] ?? null;
        if ($database && config('database.default') !== $database['driver']) {
            return back()->withErrors(['finish' => 'Veritabanı yapılandırması henüz yüklenmedi. Sunucuyu yeniden başlatıp (php artisan serve kullanıyorsanız) sayfayı yenileyin; yapılandırma önbelleği kullanıyorsanız "php artisan config:clear" çalıştırın.']);
        }

        if (! $this->state->isMigrated()) {
            return back()->withErrors(['finish' => 'Veritabanı tabloları bulunamadı. Lütfen Veritabanı adımını yeniden tamamlayın.']);
        }
        try {
            $result = $this->finalizer->finalize($data, $request->ip());
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['finish' => 'Kurulum tamamlanamadı: '.Str::limit($e->getMessage(), 300)]);
        }
        $request->session()->forget('installer');
        return view('installer.complete', [
            'result' => $result,
            'appUrl' => rtrim($data['application']['app_url'], '/'),
            'basePath' => base_path(),
        ]);
    }
    private function steps(): array
    {
        return array_keys(config('installer.steps'));
    }
    private function isDone(Request $request, string $step): bool
    {
        return match ($step) {
            'database' => (bool) $request->session()->get('installer.done.database') || $this->state->isMigrated(),
            'summary' => false,
            default => (bool) $request->session()->get('installer.done.'.$step),
        };
    }
    private function accessible(Request $request, string $step): bool
    {
        foreach ($this->steps() as $candidate) {
            if ($candidate === $step) {
                return true;
            }
            if (! $this->isDone($request, $candidate)) {
                return false;
            }
        }
        return false;
    }
    private function firstPending(Request $request): string
    {
        foreach ($this->steps() as $step) {
            if (! $this->isDone($request, $step)) {
                return $step;
            }
        }
        return 'summary';
    }
    private function nextStep(string $step): string
    {
        $steps = $this->steps();
        $index = array_search($step, $steps, true);
        return $steps[min($index + 1, count($steps) - 1)];
    }
    private function viewData(Request $request, string $step): array
    {
        $steps = $this->steps();
        $index = array_search($step, $steps, true);
        $list = [];
        foreach (config('installer.steps') as $key => $meta) {
            $list[] = $meta + [
                'key' => $key,
                'done' => $key !== 'summary' && $this->isDone($request, $key),
                'current' => $key === $step,
            ];
        }
        $data = [
            'steps' => $list,
            'current' => $step,
            'number' => $index + 1,
            'total' => count($steps),
            'previous' => $index > 0 ? $steps[$index - 1] : null,
            'values' => $this->values($request, $step),
        ];
        return $data + match ($step) {
            'requirements' => ['report' => $this->requirements->report()],
            'database' => ['drivers' => $this->driverOptions()],
            'admin' => ['questions' => User::SECURITY_QUESTIONS, 'hasPassword' => (bool) $request->session()->get('installer.data.admin.password')],
            'communication' => [
                'hasMailPassword' => (bool) $request->session()->get('installer.data.communication.mail.password'),
                'hasWhatsappToken' => (bool) $request->session()->get('installer.data.communication.whatsapp.token'),
            ],
            'visual' => ['accentColors' => config('installer.accent_colors')],
            'summary' => $this->summaryData($request),
            default => [],
        };
    }
    private function driverOptions(): array
    {
        $available = $this->requirements->availableDrivers();
        $options = [];
        foreach (config('installer.drivers') as $key => $driver) {
            $options[$key] = ['label' => $driver['label'], 'available' => $available[$key]];
        }
        return $options;
    }
    private function summaryData(Request $request): array
    {
        $data = $request->session()->get('installer.data', []);
        $blocks = $data['site']['blocks'] ?? [];
        return [
            'data' => $data,
            'blockCount' => count($blocks),
            'apartmentCount' => array_sum(array_column($blocks, 'count')),
            'databaseDriver' => $data['database']['driver'] ?? config('database.default'),
            'ready' => $this->firstPending($request) === 'summary',
        ];
    }
    private function values(Request $request, string $step): array
    {
        $stored = $request->session()->get('installer.data.'.$step, []);
        return match ($step) {
            'application' => $stored + [
                'app_url' => rtrim($request->getSchemeAndHttpHost().$request->getBasePath(), '/'),
                'app_env' => 'production',
                'app_debug' => false,
                'timezone' => (string) config('app.timezone', 'Europe/Istanbul'),
            ],
            'database' => $stored + [
                'driver' => in_array(config('database.default'), array_keys(config('installer.drivers')), true) ? config('database.default') : 'sqlite',
                'host' => '127.0.0.1',
                'port' => 3306,
                'database' => 'site360pro',
                'username' => 'root',
            ],
            'admin' => $stored + ['name' => '', 'email' => '', 'security_question' => '', 'security_answer' => ''],
            'site' => $stored + [
                'name' => '',
                'address' => '',
                'opening_balance' => 0,
                'opening_balance_date' => now()->startOfMonth()->toDateString(),
                'blocks' => [['name' => 'A Blok', 'count' => 10]],
                'per_floor' => 0,
            ],
            'communication' => $this->communicationValues($request, $stored),
            'visual' => $stored + [
                'accent_color' => '#206bc4',
                'glass_effect' => false,
                'animations_effect' => true,
                'card_hover_effect' => true,
                'enabled' => false,
                'count' => 20,
                'speed' => 'normal',
                'opacity' => 30,
                'color_mode' => 'accent',
                'size' => 'mixed',
            ],
            default => [],
        };
    }
    private function communicationValues(Request $request, array $stored): array
    {
        $mail = $stored['mail'] ?? [];
        $whatsapp = $stored['whatsapp'] ?? [];
        return [
            'public_url' => $stored['public_url'] ?? $request->session()->get('installer.data.application.app_url', ''),
            'support_email' => $stored['support_email'] ?? $request->session()->get('installer.data.admin.email', ''),
            'mail_mailer' => $mail['mailer'] ?? 'log',
            'mail_host' => $mail['host'] ?? '',
            'mail_port' => $mail['port'] ?? 587,
            'mail_scheme' => $mail['scheme'] ?? 'none',
            'mail_username' => $mail['username'] ?? '',
            'mail_from_address' => $mail['from_address'] ?? $request->session()->get('installer.data.admin.email', 'hello@example.com'),
            'mail_from_name' => $mail['from_name'] ?? 'Site360Pro',
            'whatsapp_enabled' => $whatsapp['enabled'] ?? false,
            'whatsapp_api_url' => $whatsapp['api_url'] ?? '',
            'whatsapp_session_id' => $whatsapp['session_id'] ?? '',
            'whatsapp_group_id' => $whatsapp['group_id'] ?? '',
        ];
    }
    private function saveRequirements(): array|RedirectResponse
    {
        if (! $this->requirements->passes()) {
            return back()->withErrors(['requirements' => 'Zorunlu gereksinimler karşılanmadan devam edilemez. Eksikleri giderip sayfayı yenileyin.']);
        }
        return [];
    }
    private function saveApplication(Request $request): array
    {
        $validated = $request->validate([
            'app_url' => ['required', 'url', 'max:255'],
            'app_env' => ['required', Rule::in(['production', 'local'])],
            'timezone' => ['required', Rule::in(\DateTimeZone::listIdentifiers())],
        ]);
        return [
            'app_url' => rtrim($validated['app_url'], '/'),
            'app_env' => $validated['app_env'],
            'app_debug' => $validated['app_env'] === 'local' && $request->boolean('app_debug'),
            'timezone' => $validated['timezone'],
        ];
    }
    private function validateDatabase(Request $request): array
    {
        $external = fn () => Rule::requiredIf(fn () => $request->input('driver') !== 'sqlite');
        $validated = $request->validate([
            'driver' => ['required', Rule::in(array_keys(config('installer.drivers')))],
            'host' => [$external(), 'nullable', 'string', 'max:190', 'regex:/^[A-Za-z0-9._\-:\[\]]+$/'],
            'port' => [$external(), 'nullable', 'integer', 'between:1,65535'],
            'database' => [$external(), 'nullable', 'string', 'max:64', 'regex:/^[A-Za-z0-9_$\-]+$/'],
            'username' => [$external(), 'nullable', 'string', 'max:190'],
            'password' => ['nullable', 'string', 'max:190'],
        ]);
        return $validated + [
            'password' => '',
            'host' => null,
            'port' => null,
            'database' => null,
            'username' => null,
            'create_database' => $request->boolean('create_database') && $validated['driver'] !== 'sqlite',
            'fresh' => $request->boolean('fresh'),
        ];
    }
    private function saveDatabase(Request $request): array|RedirectResponse
    {
        $data = $this->validateDatabase($request);
        if (! ($this->requirements->availableDrivers()[$data['driver']] ?? false)) {
            return back()->withInput($request->except('password'))->withErrors(['driver' => 'Seçilen sürücü için PHP eklentisi yüklü değil.']);
        }
        if ($data['fresh'] && $request->input('fresh_confirmation') !== 'TEMIZ_KURULUM') {
            return back()->withInput($request->except('password'))->withErrors(['fresh_confirmation' => 'Temiz kurulum için onay alanına TEMIZ_KURULUM yazın.']);
        }
        try {
            $info = $this->databases->inspect($data, $data['create_database']);
            if ($info['has_users']) {
                return back()->withInput($request->except('password'))->withErrors(['database' => 'Bu veritabanında zaten kullanıcı kayıtları var. Sihirbaz boş bir veritabanı gerektirir; mevcut bir kurulumu kullanmak için .env dosyasını doğrudan düzenleyin.']);
            }
            $this->databases->migrate($data, $data['fresh']);
            $this->databases->persist($data);
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput($request->except('password'))->withErrors(['database' => 'Veritabanı işlemi başarısız: '.Str::limit($e->getMessage(), 300)]);
        }
        return [
            'driver' => $data['driver'],
            'host' => $data['host'],
            'port' => $data['port'],
            'database' => $data['database'],
            'username' => $data['username'],
        ];
    }
    private function saveAdmin(Request $request): array
    {
        $existing = $request->session()->get('installer.data.admin', []);
        $email = strtolower((string) $request->input('email'));
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'password' => [
                Rule::requiredIf(empty($existing['password'])),
                'nullable', 'string', 'min:8', 'max:72', 'confirmed',
                function (string $attribute, mixed $value, \Closure $fail) use ($email) {
                    if ($value === null || $value === '') {
                        return;
                    }
                    if (! preg_match('/[A-Za-z]/', $value) || ! preg_match('/\d/', $value)) {
                        $fail('Şifre en az bir harf ve bir rakam içermelidir.');
                    } elseif (in_array(strtolower($value), self::WEAK_PASSWORDS, true)) {
                        $fail('Bu şifre çok yaygın ve tahmin edilmesi kolay. Lütfen daha güçlü bir şifre seçin.');
                    } elseif ($email !== '' && strtolower($value) === $email) {
                        $fail('Şifre e-posta adresiyle aynı olamaz.');
                    }
                },
            ],
            'security_question' => ['required', Rule::in(User::SECURITY_QUESTIONS)],
            'security_answer' => ['required', 'string', 'max:255'],
        ]);
        return [
            'name' => trim($validated['name']),
            'email' => $email,
            'password' => ! empty($validated['password']) ? Crypt::encryptString($validated['password']) : $existing['password'],
            'security_question' => $validated['security_question'],
            'security_answer' => trim($validated['security_answer']),
        ];
    }
    private function saveSite(Request $request): array
    {
        $blocks = collect($request->input('blocks', []))
            ->filter(fn ($block) => is_array($block) && trim((string) ($block['name'] ?? '')) !== '')
            ->map(fn ($block) => ['name' => trim($block['name']), 'count' => ($block['count'] ?? '') === '' ? 0 : $block['count']])
            ->values()
            ->all();
        $request->merge(['blocks' => $blocks]);
        $limits = config('installer.limits');
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:255'],
            'opening_balance' => ['required', 'numeric', 'between:-999999999,999999999'],
            'opening_balance_date' => ['required', 'date'],
            'blocks' => ['required', 'array', 'min:1', 'max:'.$limits['blocks']],
            'blocks.*.name' => ['required', 'string', 'max:80', 'distinct:ignore_case'],
            'blocks.*.count' => ['required', 'integer', 'min:0', 'max:'.$limits['apartments_per_block']],
            'per_floor' => ['nullable', 'integer', 'min:0', 'max:50'],
        ]);
        if (array_sum(array_column($validated['blocks'], 'count')) > $limits['apartments_total']) {
            throw ValidationException::withMessages([
                'blocks' => 'Toplam daire sayısı en fazla '.$limits['apartments_total'].' olabilir. Fazlasını kurulumdan sonra ekleyebilirsiniz.',
            ]);
        }
        return [
            'name' => trim($validated['name']),
            'address' => trim((string) ($validated['address'] ?? '')),
            'opening_balance' => $validated['opening_balance'],
            'opening_balance_date' => $validated['opening_balance_date'],
            'blocks' => array_map(fn ($block) => ['name' => $block['name'], 'count' => (int) $block['count']], $validated['blocks']),
            'per_floor' => (int) ($validated['per_floor'] ?? 0),
        ];
    }
    private function saveCommunication(Request $request): array
    {
        $existing = $request->session()->get('installer.data.communication', []);
        $smtp = fn () => Rule::requiredIf(fn () => $request->input('mail_mailer') === 'smtp');
        $whatsapp = fn () => Rule::requiredIf(fn () => $request->boolean('whatsapp_enabled'));
        $validated = $request->validate([
            'public_url' => ['nullable', 'url', 'max:255'],
            'support_email' => ['nullable', 'email', 'max:160'],
            'mail_mailer' => ['required', Rule::in(['log', 'smtp'])],
            'mail_host' => [$smtp(), 'nullable', 'string', 'max:190'],
            'mail_port' => [$smtp(), 'nullable', 'integer', 'between:1,65535'],
            'mail_scheme' => ['nullable', Rule::in(['none', 'smtps'])],
            'mail_username' => ['nullable', 'string', 'max:190'],
            'mail_password' => ['nullable', 'string', 'max:190'],
            'mail_from_address' => ['required', 'email', 'max:190'],
            'mail_from_name' => ['required', 'string', 'max:120'],
            'whatsapp_api_url' => [$whatsapp(), 'nullable', 'string', 'max:255'],
            'whatsapp_session_id' => [$whatsapp(), 'nullable', 'string', 'max:100'],
            'whatsapp_token' => [
                Rule::requiredIf(fn () => $request->boolean('whatsapp_enabled') && empty($existing['whatsapp']['token'])),
                'nullable', 'string', 'max:255',
            ],
            'whatsapp_group_id' => [$whatsapp(), 'nullable', 'string', 'max:120'],
        ], [
            'whatsapp_api_url.required' => 'WhatsApp etkinleştirildiğinde API URL zorunludur.',
            'whatsapp_session_id.required' => 'WhatsApp etkinleştirildiğinde oturum / cihaz kimliği zorunludur.',
            'whatsapp_token.required' => 'WhatsApp etkinleştirildiğinde API token zorunludur.',
            'whatsapp_group_id.required' => 'WhatsApp etkinleştirildiğinde Group ID zorunludur.',
        ]);
        $mailPassword = $validated['mail_password'] ?? '';
        $token = $validated['whatsapp_token'] ?? '';
        return [
            'public_url' => rtrim((string) ($validated['public_url'] ?? ''), '/'),
            'support_email' => (string) ($validated['support_email'] ?? ''),
            'mail' => [
                'mailer' => $validated['mail_mailer'],
                'host' => (string) ($validated['mail_host'] ?? ''),
                'port' => $validated['mail_port'] ?? null,
                'scheme' => $validated['mail_scheme'] ?? 'none',
                'username' => (string) ($validated['mail_username'] ?? ''),
                'password' => $mailPassword !== '' ? Crypt::encryptString($mailPassword) : ($existing['mail']['password'] ?? ''),
                'from_address' => $validated['mail_from_address'],
                'from_name' => $validated['mail_from_name'],
            ],
            'whatsapp' => [
                'enabled' => $request->boolean('whatsapp_enabled'),
                'api_url' => (string) ($validated['whatsapp_api_url'] ?? ''),
                'session_id' => (string) ($validated['whatsapp_session_id'] ?? ''),
                'token' => $token !== '' ? Crypt::encryptString($token) : ($existing['whatsapp']['token'] ?? ''),
                'group_id' => (string) ($validated['whatsapp_group_id'] ?? ''),
            ],
        ];
    }
    private function saveVisual(Request $request): array
    {
        $validated = $request->validate([
            'accent_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'count' => ['nullable', 'integer', 'min:5', 'max:100'],
            'speed' => ['nullable', 'string', 'in:slow,normal,fast'],
            'opacity' => ['nullable', 'integer', 'min:5', 'max:90'],
            'color_mode' => ['nullable', 'string', 'in:accent,blue,pastel,white'],
            'size' => ['nullable', 'string', 'in:small,medium,large,mixed'],
        ]);
        return [
            'accent_color' => $validated['accent_color'],
            'glass_effect' => $request->boolean('glass_effect'),
            'animations_effect' => $request->boolean('animations_effect'),
            'card_hover_effect' => $request->boolean('card_hover_effect'),
            'enabled' => $request->boolean('enabled'),
            'count' => (int) ($validated['count'] ?? 20),
            'speed' => $validated['speed'] ?? 'normal',
            'opacity' => (int) ($validated['opacity'] ?? 30),
            'color_mode' => $validated['color_mode'] ?? 'accent',
            'size' => $validated['size'] ?? 'mixed',
        ];
    }
}
