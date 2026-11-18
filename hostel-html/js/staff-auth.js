let apiBaseUrl = window.CUSTOM_STAFF_AUTH_API_BASE_URL || "https://hostelhub.niitwebdev.com.ng/hostelhub-api/admin/auth";

// Local XAMPP Apache detection (works on port 80, empty, and Live Server port 5500)
if (typeof window !== "undefined" && (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")) {
    const port = window.location.port;
    const protocol = window.location.protocol;
    const hostname = window.location.hostname;
    const localHost = (port === "" || port === "80") ? window.location.origin : `${protocol}//${hostname}`;
    const rootPath = window.location.pathname.includes("/ayo-niit-web-project/") ? "/ayo-niit-web-project/hostel" : "/ayo-niit-web-project/hostel";
    apiBaseUrl = localHost + rootPath + "/admin/auth";
}
let adminApiBaseUrl = apiBaseUrl;
let staffApiBaseUrl = apiBaseUrl;

async function parseJsonResponse(response) {
    const rawText = await response.text();
    if (!rawText || !rawText.trim()) {
        if (!response.ok) {
            throw new Error(`Server returned HTTP ${response.status} (${response.statusText || 'Error'})`);
        }
        throw new Error("Server returned an empty response");
    }
    const clean = rawText.trim().replace(/^[\s;]+|[\s;]+$/g, '');
    try {
        return JSON.parse(clean);
    } catch (e) {
        const match = clean.match(/\{[\s\S]*\}/);
        if (match) {
            try {
                return JSON.parse(match[0]);
            } catch (err) {}
        }
        if (response.status === 404) {
            throw new Error("API endpoint not found (HTTP 404). Please verify server API configuration.");
        }
        if (response.status === 500) {
            throw new Error("Server database error (HTTP 500). Please check database connection.");
        }
        const stripped = clean.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim().substring(0, 120);
        throw new Error(stripped || `Invalid response (HTTP ${response.status})`);
    }
}

function getIconPath(name) {
    if (window.location.pathname.includes('/admin-auth/') || window.location.pathname.includes('/staff-auth/')) {
        return '../../all-images/icon-images/' + name;
    }
    if (window.location.pathname.includes('/student/') || window.location.pathname.includes('/admin/') || window.location.pathname.includes('/staff/')) {
        return '../all-images/icon-images/' + name;
    }
    return 'all-images/icon-images/' + name;
}

function closeModal() {
    const alertEl = document.getElementById('alert');
    if (alertEl) {
        alertEl.classList.remove("alertModal");
    }
}

function alertModal(success, message, redirectUrl = "staff-sign-in.html") {
    let alertEl = document.getElementById('alert');
    if (!alertEl) {
        alertEl = document.createElement('div');
        alertEl.className = 'alert-overlay';
        alertEl.id = 'alert';
        document.body.appendChild(alertEl);
    }
    const successIcon = getIconPath("success.gif");
    const warningIcon = getIconPath("warning.gif");

    if (success === true || success === "true" || success === 1) {
        alertEl.innerHTML =
            `<div class="alert-container">
                <div class="image-wrapper">
                    <img src="${successIcon}" alt="Success">
                </div>
                <h4>${message}</h4>
              <a href="${redirectUrl}"> <button class="btn" title="Okay, Thanks" type="button">Okay, Thanks</button> </a>
            </div>`;
    } else {
        alertEl.innerHTML =
            ` <div class="alert-container">
            <div class="image-wrapper">
                <img src="${warningIcon}" alt="Warning">
            </div>
            <h4>${message}</h4>
            <button onclick="closeModal()" class="btn" title="Okay" type="button">Okay</button>
            </div>
        `;
    }

    alertEl.classList.add("alertModal");
}

async function staffSignInHandle() {
    const emailInput = document.getElementById("emailAddress");
    const passwordInput = document.getElementById("password");

    const emailAddress = emailInput ? emailInput.value.trim() : "";
    const password = passwordInput ? passwordInput.value : "";

    if (!emailAddress) {
        alertModal(false, "EMAIL ADDRESS REQUIRED");
        return;
    }

    if (!emailAddress.includes("@") || !emailAddress.includes(".")) {
        alertModal(false, "ENTER A VALID EMAIL");
        return;
    }

    if (!password) {
        alertModal(false, "PASSWORD REQUIRED");
        return;
    }

    const signInBtn = document.getElementById('signInBtnId') || document.querySelector('.portal-login-btn');
    let originalBtnText = 'Sign In';
    if (signInBtn) {
        originalBtnText = signInBtn.innerHTML;
        signInBtn.innerHTML = 'Authenticating...';
        signInBtn.disabled = true;
    }

    const formData = new FormData();
    formData.append("emailAddress", emailAddress);
    formData.append("password", password);

    try {
        const response = await fetch(apiBaseUrl + '/sign-in.php', {
            method: "POST",
            body: formData
        });

        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true || fetchData.success === "true") {
            if (fetchData.data) {
                sessionStorage.setItem("staffUserSession", JSON.stringify(fetchData.data));
                localStorage.setItem("staffUserSession", JSON.stringify(fetchData.data));
            }
            const welcomeName = (fetchData.data && (fetchData.data.firstName || fetchData.data.emailAddress))
                ? ' Welcome, ' + (fetchData.data.firstName || fetchData.data.emailAddress)
                : '';
            alertModal(true, (fetchData.message || "STAFF LOGIN SUCCESSFUL") + welcomeName, "../dashboard.html");
        } else {
            alertModal(false, fetchData.message || "INVALID LOGIN DETAILS! Check your email and password and try again");
        }
    } catch (error) {
        alertModal(false, error.message);
    } finally {
        if (signInBtn) {
            signInBtn.innerHTML = originalBtnText;
            signInBtn.disabled = false;
        }
    }
}

async function staffForgotPasswordHandle() {
    const emailInput = document.getElementById("emailAddress");
    const emailAddress = emailInput ? emailInput.value.trim() : "";

    if (!emailAddress) {
        alertModal(false, "EMAIL ADDRESS REQUIRED");
        return;
    }

    if (!emailAddress.includes("@") || !emailAddress.includes(".")) {
        alertModal(false, "ENTER A VALID EMAIL");
        return;
    }

    const forgotPasswordBtn = document.getElementById('forgotPasswordBtnId');
    let originalBtnText = 'Proceed to reset password';
    if (forgotPasswordBtn) {
        originalBtnText = forgotPasswordBtn.innerHTML;
        forgotPasswordBtn.innerHTML = 'Processing...';
        forgotPasswordBtn.disabled = true;
    }

    const formData = new FormData();
    formData.append("emailAddress", emailAddress);
    formData.append("resetOtp", "");

    try {
        const response = await fetch(apiBaseUrl + '/forgot-password.php', {
            method: "POST",
            body: formData
        });

        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true || fetchData.success === "true") {
            sessionStorage.setItem("userForgotPasswordSession", JSON.stringify(fetchData));
            sessionStorage.setItem("staffForgotPasswordSession", JSON.stringify(fetchData));
            const email = (fetchData.data && fetchData.data.emailAddress) ? ' Hi, ' + fetchData.data.emailAddress : (' Hi, ' + emailAddress);
            alertModal(true, (fetchData.message || "OTP GENERATED SUCCESSFULLY") + email, "staff-reset-password.html");
        } else {
            alertModal(false, fetchData.message || "EMAIL NOT FOUND! No account is registered with this email address");
        }
    } catch (error) {
        alertModal(false, error.message);
    } finally {
        if (forgotPasswordBtn) {
            forgotPasswordBtn.innerHTML = originalBtnText;
            forgotPasswordBtn.disabled = false;
        }
    }
}

async function staffResetPasswordHandle() {
    const otpInput = document.getElementById("otpCode") || document.getElementById("resetOtp") || document.getElementById("otp");
    const newPasswordInput = document.getElementById("newPassword");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const emailInput = document.getElementById("emailAddress");

    const otpCode = otpInput ? otpInput.value.trim() : "";
    const newPassword = newPasswordInput ? newPasswordInput.value : "";
    const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value : "";

    let emailAddress = emailInput ? emailInput.value.trim() : "";
    if (!emailAddress) {
        const stored = sessionStorage.getItem("userForgotPasswordSession") || sessionStorage.getItem("staffForgotPasswordSession");
        if (stored) {
            try {
                const parsed = JSON.parse(stored);
                emailAddress = (parsed.data && parsed.data.emailAddress) ? parsed.data.emailAddress : (parsed.emailAddress || "");
            } catch (e) {}
        }
    }

    if (!emailAddress) {
        alertModal(false, "EMAIL ADDRESS REQUIRED");
        return;
    }

    if (!otpCode) {
        alertModal(false, "OTP REQUIRED");
        return;
    }

    if (otpCode.length < 6) {
        alertModal(false, "OTP MUST NOT BE LESS THAN 6");
        return;
    }

    if (otpCode.length > 6) {
        alertModal(false, "OTP MUST NOT BE MORE THAN 6");
        return;
    }

    if (!newPassword) {
        alertModal(false, "NEW PASSWORD REQUIRED");
        return;
    }

    if (newPassword.length < 8) {
        alertModal(false, "NEW PASSWORD MUST NOT BE LESS THAN 8 CHARACTERS");
        return;
    }

    if (newPassword !== confirmPassword) {
        alertModal(false, "NEW PASSWORDS DO NOT MATCH");
        return;
    }

    const resetPasswordBtn = document.getElementById('resetPasswordBtnId');
    let originalBtnText = 'Reset Password';
    if (resetPasswordBtn) {
        originalBtnText = resetPasswordBtn.innerHTML;
        resetPasswordBtn.innerHTML = 'Processing...';
        resetPasswordBtn.disabled = true;
    }

    const formData = new FormData();
    formData.append("emailAddress", emailAddress);
    formData.append("resetOtp", otpCode);
    formData.append("newPassword", newPassword);
    formData.append("confirmPassword", confirmPassword);

    try {
        const response = await fetch(apiBaseUrl + '/reset-password.php', {
            method: "POST",
            body: formData
        });

        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true || fetchData.success === "true") {
            sessionStorage.removeItem("userForgotPasswordSession");
            sessionStorage.removeItem("staffForgotPasswordSession");
            const email = (fetchData.data && fetchData.data.emailAddress) ? ' Hi, ' + fetchData.data.emailAddress : '';
            alertModal(true, (fetchData.message || "PASSWORD RESET SUCCESSFUL") + email, "staff-sign-in.html");
        } else {
            alertModal(false, fetchData.message || "INVALID RESET OTP! Kindly check your OTP and try again");
        }
    } catch (error) {
        alertModal(false, error.message);
    } finally {
        if (resetPasswordBtn) {
            resetPasswordBtn.innerHTML = originalBtnText;
            resetPasswordBtn.disabled = false;
        }
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const toggleButtons = document.querySelectorAll(".password-toggle-trigger");
    toggleButtons.forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const wrapper = btn.closest(".password-input-wrapper") || btn.parentElement;
            const input = wrapper ? wrapper.querySelector("input") : null;
            const icon = btn.querySelector("i");
            if (input) {
                const isPassword = input.getAttribute("type") === "password";
                input.setAttribute("type", isPassword ? "text" : "password");
                if (icon) {
                    icon.classList.toggle("bi-eye-slash", !isPassword);
                    icon.classList.toggle("bi-eye", isPassword);
                }
            }
        });
    });

    const stored = sessionStorage.getItem("userForgotPasswordSession") || sessionStorage.getItem("staffForgotPasswordSession");
    if (stored) {
        try {
            const parsed = JSON.parse(stored);
            const data = parsed.data || parsed;
            const email = data.emailAddress || "";
            const name = data.firstName || data.fullName || "Staff";

            const userNameEl = document.getElementById("userName");
            if (userNameEl) userNameEl.textContent = name;

            const userEmailEl = document.getElementById("userEmail") || document.getElementById("displayEmail");
            if (userEmailEl && email) userEmailEl.textContent = email;

            const emailInput = document.getElementById("emailAddress");
            if (emailInput && !emailInput.value && email) emailInput.value = email;
        } catch (e) {}
    }
});