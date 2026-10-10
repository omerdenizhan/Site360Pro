<div class="modal modal-blur fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordTitle" aria-hidden="true" data-bs-backdrop="static" data-question-url="{{ route('password.security.question') }}" data-verify-url="{{ route('password.security.verify') }}" data-reset-url="{{ route('password.security.reset') }}">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-body-tertiary">
                <h5 class="modal-title d-flex align-items-center gap-2" id="forgotPasswordTitle">
                    <span class="avatar avatar-sm bg-primary-lt"><x-icon name="key" /></span>
                    <span id="modalHeaderStepText">Şifremi Unuttum - Adım 1/3</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat" id="btnCloseForgotModal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="forgotAlert" class="alert d-none mb-3" role="alert"></div>
                <div id="stepEmailSection">
                    <p class="text-secondary small mb-3">Hesabınıza ait e-posta adresinizi giriniz. Sistemde kayıtlı güvenlik sorunuz getirilecektir.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold required">E-Posta Adresi</label>
                        <input type="email" class="form-control" id="forgotEmailInput" placeholder="ornek@alanadi.com" autocomplete="email" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-ghost-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" class="btn btn-primary" id="btnSubmitEmail">
                            <span>Devam Et</span> <x-icon name="arrow-right" class="ms-1" />
                        </button>
                    </div>
                </div>
                <div id="stepQuestionSection" class="d-none">
                    <div class="mb-3">
                        <label class="form-label text-secondary small mb-1">Kayıtlı Güvenlik Sorusu:</label>
                        <div class="p-3 bg-body-tertiary border rounded fw-semibold text-primary" id="forgotQuestionDisplay">
                    </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold required">Güvenlik Sorusu Cevabı</label>
                        <input type="text" class="form-control" id="forgotAnswerInput" placeholder="Cevabınızı giriniz" autocomplete="off" required>
                        <div class="form-hint">Cevap büyük/küçük harf duyarsız kontrol edilir.</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <button type="button" class="btn btn-ghost-secondary btn-sm" id="btnBackToEmail">
                            <x-icon name="arrow-left" class="me-1" /> Geri
                        </button>
                        <button type="button" class="btn btn-primary" id="btnSubmitAnswer">
                            <span>Doğrula</span> <x-icon name="shield-check" class="ms-1" />
                        </button>
                    </div>
                </div>
                <div id="stepPasswordSection" class="d-none">
                    <p class="text-secondary small mb-3">Güvenlik doğrulamanız başarıyla tamamlandı. Lütfen yeni şifrenizi belirleyiniz.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold required">Yeni Şifre</label>
                        <input type="password" class="form-control" id="forgotNewPassword" placeholder="En az 8 karakter" autocomplete="new-password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold required">Yeni Şifre (Tekrar)</label>
                        <input type="password" class="form-control" id="forgotNewPasswordConfirm" placeholder="Şifrenizi tekrar giriniz" autocomplete="new-password" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-success w-100" id="btnSubmitNewPassword">
                            <x-icon name="device-floppy" class="me-1" /> Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

