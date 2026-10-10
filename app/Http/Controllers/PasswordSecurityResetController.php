<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordSecurityResetController extends Controller
{
    public function queryQuestion(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($validated['email'])])
            ->first();

        if (! $user || ! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Bu e-posta adresine ait aktif yetkili hesabı bulunamadı.',
            ], 422);
        }

        if (empty($user->security_question) || empty($user->security_answer)) {
            return response()->json([
                'success' => false,
                'message' => 'Bu hesap için henüz bir güvenlik sorusu tanımlanmamış. Lütfen sistem yöneticisi ile iletişime geçin.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'question' => $user->security_question,
            'email' => $user->email,
        ]);
    }

    public function verifyAnswer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'answer' => ['required', 'string'],
        ]);

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($validated['email'])])
            ->first();

        if (! $user || ! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Yetkili hesabı bulunamadı.',
            ], 422);
        }

        $expected = mb_strtolower(trim((string) $user->security_answer), 'UTF-8');
        $given = mb_strtolower(trim((string) $validated['answer']), 'UTF-8');

        if ($expected !== $given) {
            return response()->json([
                'success' => false,
                'message' => 'Güvenlik sorusu cevabı hatalı. Lütfen tekrar deneyiniz.',
            ], 422);
        }

        $token = Str::random(48);
        $request->session()->put('security_reset_email', $user->email);
        $request->session()->put('security_reset_token', $token);

        return response()->json([
            'success' => true,
            'token' => $token,
            'message' => 'Güvenlik cevabı doğrulandı. Yeni şifrenizi belirleyebilirsiniz.',
        ]);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Yeni şifre zorunludur.',
            'password.min' => 'Yeni şifre en az :min karakter olmalıdır.',
            'password.confirmed' => 'Şifre tekrarı ile yeni şifre eşleşmiyor.',
        ]);

        $sessionEmail = $request->session()->get('security_reset_email');
        $sessionToken = $request->session()->get('security_reset_token');

        if (! $sessionEmail || ! $sessionToken ||
            strtolower($sessionEmail) !== strtolower($validated['email']) ||
            $sessionToken !== $validated['token']) {
            return response()->json([
                'success' => false,
                'message' => 'Doğrulama oturumunun süresi dolmuş veya geçersiz. Lütfen baştan başlayınız.',
            ], 403);
        }

        $user = User::query()
            ->whereRaw('LOWER(email) = ?', [mb_strtolower($validated['email'])])
            ->first();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı bulunamadı.',
            ], 404);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
            'failed_attempts' => 0,
            'locked_until' => null,
        ])->save();

        $request->session()->forget(['security_reset_email', 'security_reset_token']);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'security_question_password_reset',
            'table_name' => 'users',
            'record_id' => $user->id,
            'ip_address' => $request->ip(),
            'description' => "{$user->name} kullanıcısının şifresi güvenlik sorusu üzerinden başarıyla yenilendi.",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Şifreniz başarıyla kaydedildi. Yeni şifrenizle giriş yapabilirsiniz.',
        ]);
    }
}
