import "@tabler/core/dist/js/tabler.min.js";

function normalizePhone(phone) {
    const digits = String(phone).replace(/\D/g, "");

    if (digits.startsWith("90")) {
        return digits;
    }

    if (digits.startsWith("0")) {
        return `9${digits}`;
    }

    if (digits.length === 10) {
        return `90${digits}`;
    }

    return digits;
}

function updateWhatsAppTools() {
    const messageInput = document.querySelector("#whatsappMessage");
    const phonesInput = document.querySelector("#whatsappPhones");
    const openLink = document.querySelector("#whatsappOpenLink");
    const recipients = [
        ...document.querySelectorAll("[data-whatsapp-recipient]:checked"),
    ];

    if (!messageInput || !phonesInput || !openLink) {
        return;
    }

    const message = messageInput.value.trim();
    const phones = recipients
        .map((input) => normalizePhone(input.value))
        .filter(Boolean);
    const uniquePhones = [...new Set(phones)];
    const encodedMessage = encodeURIComponent(message);
    const link =
        uniquePhones.length === 1
            ? `https://wa.me/${uniquePhones[0]}?text=${encodedMessage}`
            : `https://wa.me/?text=${encodedMessage}`;

    phonesInput.value = uniquePhones.join("\n");
    openLink.href = link;
}

function getStoredTheme() {
    return (
        localStorage.getItem("Site360Pro_theme") ||
        document.documentElement.getAttribute("data-bs-theme") ||
        "light"
    );
}

(function applyInitialTheme() {
    const theme = getStoredTheme();
    document.documentElement.setAttribute("data-bs-theme", theme);
    document.documentElement.classList.add(
        theme === "dark" ? "theme-dark" : "theme-light",
    );
})();

function applyTheme(theme) {
    const isDark = theme === "dark";
    document.documentElement.setAttribute("data-bs-theme", theme);
    document.body?.setAttribute("data-bs-theme", theme);

    if (isDark) {
        document.documentElement.classList.add("theme-dark");
        document.documentElement.classList.remove("theme-light");
        document.body?.classList.add("theme-dark");
        document.body?.classList.remove("theme-light");
    } else {
        document.documentElement.classList.add("theme-light");
        document.documentElement.classList.remove("theme-dark");
        document.body?.classList.add("theme-light");
        document.body?.classList.remove("theme-dark");
    }

    localStorage.setItem("Site360Pro_theme", theme);

    document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
        btn.setAttribute(
            "aria-label",
            isDark ? "Aydınlık moda geç" : "Karanlık moda geç",
        );
        btn.setAttribute(
            "title",
            isDark ? "Aydınlık moda geç" : "Karanlık moda geç",
        );
    });

    try {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");
        fetch("/toggle-theme", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                ...(token ? { "X-CSRF-TOKEN": token } : {}),
            },
            body: JSON.stringify({ theme }),
        }).catch(() => {});
    } catch (e) {}
}

function initThemeToggle() {
    const currentTheme = getStoredTheme();
    applyTheme(currentTheme);

    document.querySelectorAll("[data-theme-toggle]").forEach((button) => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            const activeTheme =
                document.documentElement.getAttribute("data-bs-theme") ===
                "dark"
                    ? "light"
                    : "dark";
            applyTheme(activeTheme);
        });
    });
}

const TOAST_ICONS = {
    success:
        '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
    danger: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>',
    warning:
        '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>',
    info: '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="8"/><line x1="12" x2="12" y1="12" y2="16"/></svg>',
};

window.showToast = function (message, type = "success", duration = 5000) {
    const container = document.getElementById("appToastContainer");

    if (!message || !container) {
        return;
    }

    const toast = document.createElement("div");
    toast.className = `app-toast app-toast-${type}`;
    toast.setAttribute("role", "alert");
    toast.setAttribute("aria-atomic", "true");
    toast.innerHTML =
        `<span class="app-toast-icon">${TOAST_ICONS[type] || TOAST_ICONS.info}</span>` +
        `<span class="app-toast-body">${message}</span>` +
        '<button type="button" class="app-toast-close" aria-label="Kapat">&#x2715;</button>';

    toast
        .querySelector(".app-toast-close")
        .addEventListener("click", () => toast.remove());
    container.prepend(toast);

    requestAnimationFrame(() =>
        requestAnimationFrame(() => toast.classList.add("show")),
    );

    if (duration > 0) {
        setTimeout(() => {
            toast.classList.add("hide");
            toast.classList.remove("show");
            setTimeout(() => toast.remove(), 320);
        }, duration);
    }
};

function initFlashToasts() {
    const container = document.getElementById("appToastContainer");

    if (!container?.dataset.flash) {
        return;
    }

    try {
        JSON.parse(container.dataset.flash).forEach((flash) =>
            window.showToast(flash.message, flash.type, 5000),
        );
    } catch (e) {}
}

function initBubbleEffect() {
    const canvas = document.getElementById("bubbleEffectCanvas");

    if (!canvas || !canvas.getContext) {
        return;
    }

    let cfg = {};
    try {
        cfg = JSON.parse(canvas.dataset.bubble || "{}");
    } catch (e) {}

    const accent = canvas.dataset.accent || "#206bc4";
    const ctx = canvas.getContext("2d");
    const speedMap = { slow: 0.25, normal: 0.55, fast: 1.1 };
    const sizeMap = {
        small: [4, 12],
        medium: [12, 26],
        large: [24, 44],
        mixed: [4, 40],
    };
    const pastel = [
        "#a5d8ff",
        "#ffc9de",
        "#d0bfff",
        "#b2f2bb",
        "#ffec99",
        "#99e9f2",
    ];
    const blues = ["#38bdf8", "#0ea5e9", "#7dd3fc", "#60a5fa", "#bae6fd"];
    const alpha = Math.min(0.9, Math.max(0.05, (cfg.opacity || 30) / 100));
    let w = 0;
    let h = 0;
    let bubbles = [];

    const hexToRgb = (hex) => {
        const n = parseInt(hex.replace("#", ""), 16);
        return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
    };
    const randomOf = (list) => list[Math.floor(Math.random() * list.length)];

    function pickColor() {
        if (cfg.color === "white") return [255, 255, 255];
        if (cfg.color === "pastel") return hexToRgb(randomOf(pastel));
        if (cfg.color === "blue") return hexToRgb(randomOf(blues));
        return hexToRgb(accent);
    }

    function resize() {
        const dpr = window.devicePixelRatio || 1;
        w = window.innerWidth;
        h = window.innerHeight;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    }

    function make(initial) {
        const range = sizeMap[cfg.size] || sizeMap.mixed;
        const r = range[0] + Math.random() * (range[1] - range[0]);

        return {
            x: Math.random() * w,
            y: initial ? Math.random() * h : h + r + Math.random() * 60,
            r,
            vy:
                (speedMap[cfg.speed] || speedMap.normal) *
                (0.6 + Math.random() * 0.8),
            phase: Math.random() * Math.PI * 2,
            sway: 0.2 + Math.random() * 0.6,
            color: pickColor(),
        };
    }

    function frame() {
        ctx.clearRect(0, 0, w, h);

        for (let i = 0; i < bubbles.length; i++) {
            const b = bubbles[i];
            b.y -= b.vy;
            b.phase += 0.01;
            b.x += Math.sin(b.phase) * b.sway;

            if (b.y < -b.r * 2) {
                bubbles[i] = make(false);
                continue;
            }

            const c = b.color;
            const rim = cfg.color === "white" ? [120, 150, 200] : c;
            const g = ctx.createRadialGradient(
                b.x - b.r * 0.35,
                b.y - b.r * 0.35,
                b.r * 0.05,
                b.x,
                b.y,
                b.r,
            );
            g.addColorStop(0, `rgba(255,255,255,${Math.min(1, alpha * 1.6)})`);
            g.addColorStop(
                0.55,
                `rgba(${c[0]},${c[1]},${c[2]},${alpha * 0.55})`,
            );
            g.addColorStop(
                1,
                `rgba(${c[0]},${c[1]},${c[2]},${Math.min(1, alpha * 1.1)})`,
            );
            ctx.beginPath();
            ctx.arc(b.x, b.y, b.r, 0, Math.PI * 2);
            ctx.fillStyle = g;
            ctx.fill();
            ctx.lineWidth = 1.5;
            ctx.strokeStyle = `rgba(${rim[0]},${rim[1]},${rim[2]},${Math.min(1, alpha * 1.8)})`;
            ctx.stroke();
        }

        requestAnimationFrame(frame);
    }

    window.addEventListener("resize", resize);
    resize();
    const count = Math.max(5, Math.min(100, cfg.count || 20));
    bubbles = Array.from({ length: count }, () => make(true));
    requestAnimationFrame(frame);
}

function initAccentPicker() {
    const picker = document.getElementById("accentPicker");
    const btn = document.getElementById("accentPickerBtn");
    const list = document.getElementById("accentPickerList");
    const input = document.getElementById("accentColorInput");
    const swatch = document.getElementById("accentPickerSwatch");
    const text = document.getElementById("accentPickerText");

    if (!picker || !btn || !list || !input) {
        return;
    }

    const setOpen = (open) => {
        list.hidden = !open;
        btn.setAttribute("aria-expanded", open ? "true" : "false");
    };

    const choose = (item) => {
        input.value = item.dataset.code;
        swatch.style.backgroundColor = item.dataset.code;
        text.textContent = `${item.dataset.name} `;
        const code = document.createElement("span");
        code.className = "text-secondary";
        code.textContent = `(${item.dataset.code})`;
        text.appendChild(code);

        list.querySelectorAll(".accent-picker-item").forEach((li) => {
            const on = li === item;
            li.classList.toggle("is-selected", on);
            li.setAttribute("aria-selected", on ? "true" : "false");
        });
        setOpen(false);
    };

    btn.addEventListener("click", () => setOpen(list.hidden));
    list.querySelectorAll(".accent-picker-item").forEach((item) => {
        item.addEventListener("click", () => choose(item));
        item.addEventListener("keydown", (e) => {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                choose(item);
            }
        });
    });
    document.addEventListener("click", (e) => {
        if (!picker.contains(e.target)) {
            setOpen(false);
        }
    });
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            setOpen(false);
        }
    });
}

function initWhatsappSettingsForm() {
    const form = document.getElementById("whatsappSettingsForm");

    if (!form) {
        return;
    }

    const fields = [
        ["api_url", "API URL"],
        ["session_id", "Oturum / Cihaz Kimliği"],
        ["token", "API Token"],
        ["group_id", "Group ID"],
    ];

    form.addEventListener("submit", (e) => {
        form.querySelectorAll(".is-invalid").forEach((el) =>
            el.classList.remove("is-invalid"),
        );

        if (!form.querySelector('[name="enabled"]').checked) {
            return;
        }

        const missing = [];
        fields.forEach(([name, label]) => {
            const el = form.querySelector(`[name="${name}"]`);
            let empty = !el.value.trim();

            if (name === "token" && form.dataset.tokenSaved === "1") {
                empty = false;
            }

            if (empty) {
                missing.push(label);
                el.classList.add("is-invalid");
            }
        });

        if (missing.length) {
            e.preventDefault();
            window.showToast(
                `WhatsApp entegrasyonunu aktif etmek için şu alanları doldurun: ${missing.join(", ")}.`,
                "warning",
                6000,
            );
            form.querySelector(".is-invalid").focus();
        }
    });
}

function initForgotPasswordModal() {
    const modalEl = document.getElementById("forgotPasswordModal");

    if (!modalEl) {
        return;
    }

    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }

    let resetEmail = "";
    let resetToken = "";

    const alertBox = document.getElementById("forgotAlert");
    const headerText = document.getElementById("modalHeaderStepText");
    const step1 = document.getElementById("stepEmailSection");
    const step2 = document.getElementById("stepQuestionSection");
    const step3 = document.getElementById("stepPasswordSection");
    const emailInput = document.getElementById("forgotEmailInput");
    const questionDisplay = document.getElementById("forgotQuestionDisplay");
    const answerInput = document.getElementById("forgotAnswerInput");
    const passwordInput = document.getElementById("forgotNewPassword");
    const confirmInput = document.getElementById("forgotNewPasswordConfirm");

    const showAlert = (msg, type = "danger") => {
        alertBox.className = `alert alert-${type} py-2 px-3 mb-3`;
        alertBox.textContent = msg;
    };

    const hideAlert = () => {
        alertBox.classList.add("d-none");
        alertBox.textContent = "";
    };

    const resetModalState = (defaultEmail = "") => {
        hideAlert();
        resetEmail = defaultEmail;
        resetToken = "";
        emailInput.value = defaultEmail;
        answerInput.value = "";
        passwordInput.value = "";
        confirmInput.value = "";
        step1.classList.remove("d-none");
        step2.classList.add("d-none");
        step3.classList.add("d-none");
        headerText.textContent = "Şifremi Unuttum - Adım 1/3";
    };

    const openModal = () => {
        if (window.bootstrap?.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
            return;
        }

        const trigger = document.createElement("button");
        trigger.type = "button";
        trigger.hidden = true;
        trigger.setAttribute("data-bs-toggle", "modal");
        trigger.setAttribute("data-bs-target", "#forgotPasswordModal");
        document.body.appendChild(trigger);
        trigger.click();
        trigger.remove();
    };

    const closeModal = () => {
        if (window.bootstrap?.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalEl).hide();
            return;
        }

        document.getElementById("btnCloseForgotModal")?.click();
    };

    window.openForgotPasswordModal = (presetEmail = "") => {
        resetModalState(presetEmail);
        openModal();

        if (presetEmail) {
            setTimeout(
                () => document.getElementById("btnSubmitEmail")?.click(),
                300,
            );
        } else {
            setTimeout(() => emailInput.focus(), 300);
        }
    };

    async function postJson(url, payload, btn, busyText) {
        hideAlert();
        btn.disabled = true;
        const originalContent = btn.innerHTML;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-1" role="status"></span> ${busyText}`;

        try {
            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");
            const res = await fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify(payload),
            });
            const data = await res.json();

            return { ok: res.ok && data.success, data };
        } catch (err) {
            showAlert(
                "Sunucu ile bağlantı kurulamadı. Lütfen tekrar deneyiniz.",
            );

            return { ok: false, data: null };
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    }

    document
        .getElementById("btnOpenForgotPassword")
        ?.addEventListener("click", (e) => {
            e.preventDefault();
            window.openForgotPasswordModal();
        });

    document.querySelectorAll("[data-forgot-email]").forEach((button) => {
        button.addEventListener("click", () =>
            window.openForgotPasswordModal(button.dataset.forgotEmail),
        );
    });

    document.getElementById("btnBackToEmail")?.addEventListener("click", () => {
        hideAlert();
        step2.classList.add("d-none");
        step1.classList.remove("d-none");
        headerText.textContent = "Şifremi Unuttum - Adım 1/3";
    });

    document
        .getElementById("btnSubmitEmail")
        ?.addEventListener("click", async function () {
            const email = emailInput.value.trim();

            if (!email) {
                showAlert("Lütfen e-posta adresinizi giriniz.");
                return;
            }

            const { ok, data } = await postJson(
                modalEl.dataset.questionUrl,
                { email },
                this,
                "Kontrol ediliyor...",
            );

            if (ok) {
                resetEmail = data.email;
                questionDisplay.textContent = data.question;
                step1.classList.add("d-none");
                step2.classList.remove("d-none");
                headerText.textContent = "Güvenlik Sorusu - Adım 2/3";
                setTimeout(() => answerInput.focus(), 200);
            } else if (data) {
                showAlert(
                    data.message ||
                        "Kullanıcı bulunamadı veya güvenlik sorusu tanımlanmamış.",
                );
            }
        });

    document
        .getElementById("btnSubmitAnswer")
        ?.addEventListener("click", async function () {
            const answer = answerInput.value.trim();

            if (!answer) {
                showAlert("Lütfen güvenlik sorusunun cevabını giriniz.");
                return;
            }

            const { ok, data } = await postJson(
                modalEl.dataset.verifyUrl,
                { email: resetEmail, answer },
                this,
                "Doğrulanıyor...",
            );

            if (ok) {
                resetToken = data.token;
                step2.classList.add("d-none");
                step3.classList.remove("d-none");
                headerText.textContent = "Yeni Şifre Belirleme - Adım 3/3";
                setTimeout(() => passwordInput.focus(), 200);
            } else if (data) {
                showAlert(data.message || "Güvenlik sorusu cevabı hatalı.");
            }
        });

    document
        .getElementById("btnSubmitNewPassword")
        ?.addEventListener("click", async function () {
            const password = passwordInput.value;
            const confirm = confirmInput.value;

            if (!password || password.length < 8) {
                showAlert("Şifre en az 8 karakter olmalıdır.");
                return;
            }

            if (password !== confirm) {
                showAlert("Şifreler eşleşmiyor.");
                return;
            }

            const { ok, data } = await postJson(
                modalEl.dataset.resetUrl,
                {
                    email: resetEmail,
                    token: resetToken,
                    password,
                    password_confirmation: confirm,
                },
                this,
                "Kaydediliyor...",
            );

            if (ok) {
                closeModal();
                const loginAlert = document.getElementById(
                    "loginPageSuccessAlert",
                );

                if (loginAlert) {
                    (
                        loginAlert.querySelector("span") || loginAlert
                    ).textContent =
                        data.message ||
                        "Şifreniz güncellendi. Yeni şifrenizle giriş yapabilirsiniz.";
                    loginAlert.classList.remove("d-none");
                } else {
                    window.showToast(
                        data.message || "Şifreniz başarıyla güncellendi.",
                        "success",
                        5000,
                    );
                }
            } else if (data) {
                showAlert(
                    data.message || "Şifre sıfırlanırken bir hata oluştu.",
                );
            }
        });
}

document.addEventListener("DOMContentLoaded", () => {
    initThemeToggle();
    initFlashToasts();
    initBubbleEffect();
    initAccentPicker();
    initWhatsappSettingsForm();
    initForgotPasswordModal();

    document.querySelectorAll("select[data-auto-submit]").forEach((select) => {
        select.addEventListener("change", () => select.form.submit());
    });

    const sidebarToggle = document.querySelector("[data-sidebar-toggle]");
    const sidebarBackdrop = document.querySelector("[data-sidebar-backdrop]");

    function closeSidebar() {
        document.body.classList.remove("sidebar-open");
        sidebarToggle?.setAttribute("aria-expanded", "false");
    }

    function toggleSidebar() {
        const isOpen = document.body.classList.toggle("sidebar-open");
        sidebarToggle?.setAttribute("aria-expanded", String(isOpen));
    }

    sidebarToggle?.addEventListener("click", toggleSidebar);
    sidebarBackdrop?.addEventListener("click", closeSidebar);
    document.querySelectorAll(".app-sidebar .sidebar-link").forEach((link) => {
        link.addEventListener("click", closeSidebar);
    });

    const confirmModal = document.querySelector("[data-confirm-modal]");
    const confirmMessage = confirmModal?.querySelector(
        "[data-confirm-message]",
    );
    const confirmAccept = confirmModal?.querySelector("[data-confirm-accept]");
    const confirmCancel = confirmModal?.querySelector("[data-confirm-cancel]");
    let pendingConfirmForm = null;

    function closeConfirmModal() {
        confirmModal?.classList.remove("show");
        confirmModal?.setAttribute("aria-hidden", "true");
        pendingConfirmForm = null;
    }

    function openConfirmModal(form) {
        pendingConfirmForm = form;

        if (confirmMessage) {
            confirmMessage.textContent =
                form.dataset.confirm || "Bu işlem devam etsin mi?";
        }

        confirmModal?.classList.add("show");
        confirmModal?.setAttribute("aria-hidden", "false");
        confirmCancel?.focus();
    }

    document.querySelectorAll("form[data-confirm]").forEach((form) => {
        form.addEventListener("submit", (event) => {
            if (form.dataset.confirmed === "true") {
                return;
            }

            event.preventDefault();
            openConfirmModal(form);
        });
    });

    confirmAccept?.addEventListener("click", () => {
        if (!pendingConfirmForm) {
            return;
        }

        pendingConfirmForm.dataset.confirmed = "true";
        pendingConfirmForm.requestSubmit();
        closeConfirmModal();
    });

    confirmCancel?.addEventListener("click", closeConfirmModal);

    confirmModal?.addEventListener("click", (event) => {
        if (event.target === confirmModal) {
            closeConfirmModal();
        }
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            closeSidebar();
        }

        if (
            event.key === "Escape" &&
            confirmModal?.classList.contains("show")
        ) {
            closeConfirmModal();
        }
    });

    document.querySelectorAll("[data-copy-target]").forEach((button) => {
        button.addEventListener("click", async () => {
            const target = document.querySelector(button.dataset.copyTarget);

            if (!target) {
                return;
            }

            await navigator.clipboard.writeText(
                target.value || target.textContent || "",
            );
            button.classList.add("btn-success");
            setTimeout(() => button.classList.remove("btn-success"), 900);
        });
    });

    document.querySelectorAll("[data-toggle-checks]").forEach((button) => {
        button.addEventListener("click", () => {
            const inputs = [
                ...document.querySelectorAll(button.dataset.toggleChecks),
            ];
            const shouldCheck = inputs.some((input) => !input.checked);

            inputs.forEach((input) => {
                input.checked = shouldCheck;
                input.dispatchEvent(new Event("change", { bubbles: true }));
            });
        });
    });

    document
        .querySelectorAll("[data-manual-match-toggle]")
        .forEach((button) => {
            button.addEventListener("click", () => {
                const row = document.getElementById(
                    button.dataset.manualMatchToggle,
                );

                if (!row) {
                    return;
                }

                row.classList.toggle("d-none");
                button.classList.toggle("btn-outline-primary");
                button.classList.toggle("btn-primary");
            });
        });

    document.querySelectorAll("[data-add-block]").forEach((button) => {
        button.addEventListener("click", () => {
            const list = button
                .closest("form")
                ?.querySelector("[data-block-list]");

            if (!list) {
                return;
            }

            const row = list.querySelector(".input-group")?.cloneNode(true);

            if (!row) {
                return;
            }

            row.querySelector("input").value = "";
            list.appendChild(row);
            row.querySelector("input")?.focus();
        });
    });

    document
        .querySelectorAll("[data-announcement-ticker]")
        .forEach((ticker) => {
            const count = Number(ticker.dataset.announcementCount || 0);
            const track = ticker.querySelector(".topbar-announcement-track");
            const item = ticker.querySelector(".topbar-announcement-item");

            if (!track || !item || count <= 1) {
                return;
            }

            const itemHeight = item.getBoundingClientRect().height || 40;
            let index = 0;

            window.setInterval(() => {
                index += 1;
                track.style.transition =
                    "transform .45s cubic-bezier(.45, 0, .2, 1)";
                track.style.transform = `translateY(-${index * itemHeight}px)`;

                if (index === count) {
                    window.setTimeout(() => {
                        track.style.transition = "none";
                        track.style.transform = "translateY(0)";
                        index = 0;
                    }, 480);
                }
            }, 4000);
        });

    document
        .querySelector("#whatsappMessage")
        ?.addEventListener("input", updateWhatsAppTools);
    document.querySelectorAll("[data-whatsapp-recipient]").forEach((input) => {
        input.addEventListener("change", updateWhatsAppTools);
    });

    updateWhatsAppTools();
});

(function () {
    var KEY = "site360pro.sidebarWidth",
        MIN = 180,
        MAX = 250,
        root = document.documentElement;
    function clamp(w) {
        return Math.max(MIN, Math.min(MAX, w));
    }
    function apply(w) {
        root.style.setProperty("--appSidebar-sidebar-width", clamp(w) + "px");
    }
    try {
        var saved = parseInt(localStorage.getItem(KEY), 10);
        if (saved) apply(saved);
    } catch (e) {}
    var shell = document.querySelector(".app-shell");
    if (!shell) return;
    var bar = document.createElement("div");
    bar.className = "sidebar-resizer d-print-none";
    bar.title = "Genişliği ayarlamak için sürükleyin (çift tıklayın: sıfırla)";
    shell.appendChild(bar);
    var dragging = false;
    bar.addEventListener("pointerdown", function (e) {
        dragging = true;
        bar.setPointerCapture(e.pointerId);
        document.body.classList.add("sidebar-resizing");
        e.preventDefault();
    });
    bar.addEventListener("pointermove", function (e) {
        if (dragging) apply(e.clientX);
    });
    function stop(e) {
        if (!dragging) return;
        dragging = false;
        document.body.classList.remove("sidebar-resizing");
        try {
            localStorage.setItem(KEY, String(clamp(e.clientX)));
        } catch (err) {}
    }
    bar.addEventListener("pointerup", stop);
    bar.addEventListener("pointercancel", stop);
    bar.addEventListener("dblclick", function () {
        root.style.removeProperty("--appSidebar-sidebar-width");
        try {
            localStorage.removeItem(KEY);
        } catch (e) {}
    });
})();
