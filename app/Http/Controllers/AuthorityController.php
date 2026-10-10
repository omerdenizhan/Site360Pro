<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\BuildingBlock;
use App\Models\OwnerReportLink;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthorityController extends Controller
{
    public function index(Request $request): View
    {
        $sites = Site::query()->orderBy('name')->get();
        $site = Site::query()->find($request->query('site_id')) ?? $sites->first();
        $blocks = $site
            ? BuildingBlock::query()->where('site_id', $site->id)->orderBy('name')->get()
            : collect();
        $blockId = $blocks->firstWhere('id', $request->integer('block_id'))?->id;

        $users = User::query()
            ->with('site')
            ->when($site, fn ($query) => $query->where(fn ($query) => $query->whereNull('site_id')->orWhere('site_id', $site->id)))
            ->orderBy('name')
            ->paginate(10, ['*'], 'users_page')
            ->withQueryString();

        return view('authorities.index', compact('sites', 'site', 'blocks', 'blockId', 'users'));
    }

    public function create(): View
    {
        $sites = Site::query()->orderBy('name')->get();

        return view('authorities.create', [
            'sites' => $sites,
            'user' => new User(['role' => 'site_manager', 'site_id' => $sites->first()?->id, 'is_active' => true]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedUser($request);
        $user = User::create($validated);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_user',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'description' => "{$user->name} panel yetkilisi oluşturuldu.",
        ]);

        return redirect()->route('authorities.index')->with('status', 'Yetkili hesabı oluşturuldu.');
    }

    public function edit(User $authority): View
    {
        return view('authorities.edit', [
            'sites' => Site::query()->orderBy('name')->get(),
            'user' => $authority,
        ]);
    }

    public function update(Request $request, User $authority): RedirectResponse
    {
        $validated = $this->validatedUser($request, $authority);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        if ($authority->is($request->user())) {
            $validated['is_active'] = true;
        }

        $authority->update($validated);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_user',
            'table_name' => 'users',
            'record_id' => $authority->id,
            'ip_address' => $request->ip(),
            'description' => "{$authority->name} panel yetkilisi güncellendi.",
        ]);

        return redirect()->route('authorities.index')->with('status', 'Yetkili hesabı güncellendi.');
    }

    public function destroy(Request $request, User $authority): RedirectResponse
    {
        if ($authority->is($request->user())) {
            return back()->withErrors(['delete' => 'Kendi hesabınızı silemezsiniz.']);
        }

        $name = $authority->name;
        $authority->delete();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_user',
            'table_name' => 'users',
            'ip_address' => $request->ip(),
            'description' => "{$name} panel yetkilisi silindi.",
        ]);

        return redirect()->route('authorities.index')->with('status', 'Yetkili hesabı silindi.');
    }

    public function storeOwnerLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'apartment_id' => ['required', 'exists:apartments,id'],
            'expires_days' => ['required', 'integer', 'between:1,365'],
        ]);

        $link = OwnerReportLink::create([
            'apartment_id' => $validated['apartment_id'],
            'token' => Str::random(56),
            'expires_at' => now()->addDays((int) $validated['expires_days']),
            'created_by' => $request->user()->id,
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_owner_report_link',
            'table_name' => 'owner_report_links',
            'record_id' => $link->id,
            'ip_address' => $request->ip(),
            'description' => 'Daire sahibi rapor bağlantısı oluşturuldu.',
        ]);

        return redirect()
            ->route('authorities.index', ['site_id' => $link->apartment->buildingBlock->site_id])
            ->with('status', 'Geçici rapor bağlantısı oluşturuldu: '.route('owner.reports.show', $link->token));
    }

    public function destroyOwnerLink(Request $request, OwnerReportLink $ownerReportLink): RedirectResponse
    {
        $ownerReportLink->delete();

        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_owner_report_link',
            'table_name' => 'owner_report_links',
            'ip_address' => $request->ip(),
            'description' => 'Daire sahibi rapor bağlantısı silindi.',
        ]);

        return redirect()->route('authorities.index')->with('status', 'Geçici rapor bağlantısı silindi.');
    }

    private function validatedUser(Request $request, ?User $user = null): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user?->id)],
            'role' => ['required', 'in:admin,site_manager'],
            'site_id' => ['nullable', 'required_if:role,site_manager', 'exists:sites,id'],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
            'security_question' => ['nullable', 'string', 'max:255', Rule::in(array_filter(array_merge(User::SECURITY_QUESTIONS, [$user?->security_question])))],
            'security_answer' => ['nullable', 'string', 'max:255', 'required_with:security_question'],
        ];

        $validated = $request->validate($rules, [
            'password.required' => 'Şifre zorunludur.',
            'password.min' => 'Şifre en az :min karakter olmalıdır.',
            'password.confirmed' => 'Şifre tekrarı eşleşmiyor.',
            'site_id.required_if' => 'Site yöneticisi için site seçimi zorunludur.',
            'security_question.in' => 'Lütfen listeden geçerli bir güvenlik sorusu seçiniz.',
            'security_answer.required_with' => 'Güvenlik sorusu seçildiyse cevabı da girilmelidir.',
        ]);
        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['role'] === 'admin') {
            $validated['site_id'] = null;
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        return $validated;
    }
}
