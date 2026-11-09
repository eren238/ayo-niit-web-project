let apiBaseUrl = window.CUSTOM_API_BASE_URL || "https://hostelhub.niitwebdev.com.ng/hotelhub-api/student";

if (typeof window !== "undefined" && (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")) {
    if (window.location.pathname.includes("/ayo-niit-web-project/")) {
        apiBaseUrl = window.location.origin + "/ayo-niit-web-project/hostel/student";
    } else {
        apiBaseUrl = window.location.origin + "/hostel/student";
    }
}



function getIconPath(iconName) {
    const isSubfolder = window.location.pathname.includes('/student/') ||
                        window.location.pathname.includes('/admin/') ||
                        window.location.pathname.includes('/staff/');
    return isSubfolder ? `../all-images/icon-images/${iconName}` : `all-images/icon-images/${iconName}`;
}

// Close Alert Modal
function closeModal() {
    const alertModal = document.getElementById('alert');
    if (alertModal) {
        alertModal.classList.remove("alertModal");
    }
}

// Show Alert Modal with Success/Warning dialog
function alertModal(success, message, redirectUrl = "student-login.html") {
    let alertModal = document.getElementById('alert');

    // Dynamically inject alert overlay if missing on the page
    if (!alertModal) {
        alertModal = document.createElement('div');
        alertModal.className = 'alert-overlay';
        alertModal.id = 'alert';
        document.body.appendChild(alertModal);
    }

    const successIcon = getIconPath("success.gif");
    const warningIcon = getIconPath("warning.gif");

    if (success === true) {
        alertModal.innerHTML = `
            <div class="alert-container">
                <div class="image-wrapper">
                    <img src="${successIcon}" alt="Success">
                </div>
                <h4>${message}</h4>
                <a href="${redirectUrl}">
                    <button class="btn" type="button" title="Okay, Thanks">Okay, Thanks</button>
                </a>
            </div>
        `;
    } else {
        alertModal.innerHTML = `
            <div class="alert-container">
                <div class="image-wrapper">
                    <img src="${warningIcon}" alt="Warning">
                </div>
                <h4>${message}</h4>
                <button type="button" onclick="closeModal()" class="btn" title="Okay">Okay</button>
            </div>
        `;
    }

    alertModal.classList.add("alertModal");
}

/*  1. STUDENT SIGN IN */
async function signInHandle() {
    const emailInput = document.getElementById("emailAddress");
    const passwordInput = document.getElementById("password");

    const emailAddress = emailInput ? emailInput.value.trim() : "";
    const password = passwordInput ? passwordInput.value : "";

    // Client-side validations
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
    const originalBtnText = signInBtn ? signInBtn.innerHTML : 'Login';
    if (signInBtn) {
        signInBtn.innerHTML = 'Authenticating...';
        signInBtn.disabled = true;
    }

    const formData = new FormData();
    formData.append("emailAddress", emailAddress);
    formData.append("password", password);

    try {
        const response = await fetch(apiBaseUrl + '/student-auth/sign-in.php', {
            method: "POST",
            body: formData
        });

        const fetchData = await response.json();

        if (fetchData.success === true) {
            // Save student user session
            if (fetchData.data) {
                sessionStorage.setItem("studentUserSession", JSON.stringify(fetchData.data));
            }

            const welcomeName = (fetchData.data && (fetchData.data.firstName || fetchData.data.emailAddress))
                ? ' Hi, ' + (fetchData.data.firstName || fetchData.data.emailAddress)
                : '';

            alertModal(true, (fetchData.message || "STUDENT LOGIN SUCCESSFUL") + welcomeName, "dashboard.html");
        } else {
            alertModal(false, fetchData.message || "INVALID LOGIN DETAILS! Check your email and password and try again");
        }
    } catch (error) {
        console.error("Sign in error:", error);
        alertModal(false, "Connection error: " + (error.message || "Unable to reach server. Please try again."));
    } finally {
        if (signInBtn) {
            signInBtn.innerHTML = originalBtnText;
            signInBtn.disabled = false;
        }
    }
}

/*  2. STUDENT SIGN UP */
async function signUpHandle() {
    const firstNameEl = document.getElementById("firstName");
    const lastNameEl = document.getElementById("lastName");
    const genderEl = document.getElementById("gender");
    const departmentEl = document.getElementById("department");
    const levelEl = document.getElementById("level");
    const phoneNumberEl = document.getElementById("phoneNumber");
    const emailAddressEl = document.getElementById("emailAddress");
    const passwordEl = document.getElementById("password");
    const confirmPasswordEl = document.getElementById("confirmPassword");

    const firstName = firstNameEl ? firstNameEl.value.trim() : "";
    const lastName = lastNameEl ? lastNameEl.value.trim() : "";
    const gender = genderEl ? genderEl.value.trim() : "";
    const department = departmentEl ? departmentEl.value.trim() : "";
    const level = levelEl ? levelEl.value.trim() : "";
    const phoneNumber = phoneNumberEl ? phoneNumberEl.value.trim() : "";
    const emailAddress = emailAddressEl ? emailAddressEl.value.trim() : "";
    const password = passwordEl ? passwordEl.value : "";
    const confirmPassword = confirmPasswordEl ? confirmPasswordEl.value : "";


    if (!firstName) {
        alertModal(false, "FIRST NAME IS REQUIRED, Kindly fill in the first name to continue");
        return;
    }

    if (!lastName) {
        alertModal(false, "LAST NAME IS REQUIRED, Kindly fill in the last name to continue");
        return;
    }

    if (!gender) {
        alertModal(false, "GENDER IS REQUIRED, Kindly fill in the gender to continue");
        return;
    }

    if (!department) {
        alertModal(false, "DEPARTMENT IS REQUIRED, Kindly fill in the department to continue");
        return;
    }

    if (!level) {
        alertModal(false, "LEVEL IS REQUIRED, Kindly fill in the level to continue");
        return;
    }

    if (!phoneNumber) {
        alertModal(false, "PHONE NUMBER IS REQUIRED, Kindly fill in the phone number to continue");
        return;
    }

    const phoneDigitsOnly = phoneNumber.startsWith("+") ? phoneNumber.slice(1) : phoneNumber;
    if (isNaN(phoneDigitsOnly) || phoneDigitsOnly.trim() === "") {
        alertModal(false, "PHONE NUMBER MUST CONTAIN ONLY NUMBERS");
        return;
    }

    if (phoneNumber.length < 11 || phoneNumber.length > 14) {
        alertModal(false, "INVALID PHONE NUMBER (must be 11-14 digits)");
        return;
    }

    if (!emailAddress) {
        alertModal(false, "EMAIL ADDRESS IS REQUIRED, Kindly fill in the email address to continue");
        return;
    }

    if (!emailAddress.includes("@") || !emailAddress.includes(".")) {
        alertModal(false, "INVALID EMAIL ADDRESS! Enter a valid email address and try again");
        return;
    }

    if (!password) {
        alertModal(false, "PASSWORD IS REQUIRED, Kindly fill in the password to continue");
        return;
    }

    if (password.length < 8) {
        alertModal(false, "PASSWORD MUST NOT BE LESS THAN 8 CHARACTERS");
        return;
    }

    if (confirmPasswordEl && password !== confirmPassword) {
        alertModal(false, "PASSWORDS DO NOT MATCH");
        return;
    }

    const submitBtn = document.getElementById('signUpBtn') || document.getElementById('submitBtnId');
    const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Sign Up';
    if (submitBtn) {
        submitBtn.innerHTML = 'Processing...';
        submitBtn.disabled = true;
    }

    // Prepare FormData for sign-up.php
    const formData = new FormData();
    formData.append("firstName", firstName);
    formData.append("lastName", lastName);
    formData.append("gender", gender);
    formData.append("department", department);
    formData.append("level", level);
    formData.append("phoneNumber", phoneNumber);
    formData.append("emailAddress", emailAddress);
    formData.append("password", password);
    formData.append("statusId", "1"); // Active status

    try {
        const response = await fetch(apiBaseUrl + '/student-auth/sign-up.php', {
            method: "POST",
            body: formData
        });

        const fetchData = await response.json();

        if (fetchData.success === true) {
            sessionStorage.setItem("studentSignUpSession", JSON.stringify(fetchData));
            alertModal(true, fetchData.message || "STUDENT CREATED SUCCESSFUL", "student-login.html");
        } else {
            alertModal(false, fetchData.message || "Unable to complete sign-up. Please try again.");
        }
    } catch (error) {
        console.error("Sign up error:", error);
        alertModal(false, "Connection error: " + (error.message || "Unable to reach server. Please try again."));
    } finally {
        if (submitBtn) {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        }
    }
}

/*  STUDENT FORGOT PASSWORD */
async function forgotPasswordHandle() {
    const emailInput = document.getElementById("emailAddress");
    const emailAddress = emailInput ? emailInput.value.trim() : "";

    if (!emailAddress) {
        alertModal(false, "EMAIL ADDRESS IS REQUIRED, Kindly fill in the email address to continue");
        return;
    }

    if (!emailAddress.includes("@") || !emailAddress.includes(".")) {
        alertModal(false, "INVALID EMAIL ADDRESS! Enter a valid email address and try again");
        return;
    }

    const forgotPasswordBtn = document.getElementById('forgotPasswordBtnId') || document.getElementById('resetBtn');
    const originalBtnText = forgotPasswordBtn ? forgotPasswordBtn.innerHTML : 'Proceed to Reset Password';
    if (forgotPasswordBtn) {
        forgotPasswordBtn.innerHTML = 'Processing...';
        forgotPasswordBtn.disabled = true;
    }

    const formData = new FormData();
    formData.append("emailAddress", emailAddress);
    formData.append("resetOtp", ""); // optional parameter handled by forgot-password.php

    try {
        const response = await fetch(apiBaseUrl + '/student-auth/forgot-password.php', {
            method: "POST",
            body: formData
        });

        const fetchData = await response.json();

        if (fetchData.success === true) {
            // Save email and OTP in session storage for student-reset-password.html
            const sessionPayload = {
                emailAddress: emailAddress,
                otpCode: (fetchData.data && fetchData.data.otpCode) ? fetchData.data.otpCode : "",
                studentId: (fetchData.data && fetchData.data.studentId) ? fetchData.data.studentId : ""
            };
            sessionStorage.setItem("studentForgotPasswordSession", JSON.stringify(sessionPayload));

            // Inform user of OTP (useful for local testing without mail server)
            const otpNotice = (fetchData.data && fetchData.data.otpCode)
                ? ` (OTP: ${fetchData.data.otpCode})`
                : '';

            alertModal(true, (fetchData.message || "OTP GENERATED SUCCESSFULLY") + otpNotice, "student-reset-password.html");
        } else {
            alertModal(false, fetchData.message || "EMAIL NOT FOUND! No account is registered with this email address");
        }
    } catch (error) {
        console.error("Forgot password error:", error);
        alertModal(false, "Connection error: " + (error.message || "Unable to reach server. Please try again."));
    } finally {
        if (forgotPasswordBtn) {
            forgotPasswordBtn.innerHTML = originalBtnText;
            forgotPasswordBtn.disabled = false;
        }
    }
}

/* ==========================================================================
   4. STUDENT RESET PASSWORD
   ========================================================================== */
async function resetPasswordHandle() {
    const otpInput = document.getElementById("resetOtp") || document.getElementById("otpCode");
    const newPasswordInput = document.getElementById("newPassword") || document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const emailInput = document.getElementById("emailAddress");

    // Email can be read from input or from previous forgotPassword session
    let emailAddress = emailInput ? emailInput.value.trim() : "";
    if (!emailAddress) {
        const storedSession = sessionStorage.getItem("studentForgotPasswordSession");
        if (storedSession) {
            try {
                const parsed = JSON.parse(storedSession);
                emailAddress = parsed.emailAddress || "";
            } catch (e) {
                // Ignore parse error
            }
        }
    }

    const resetOtp = otpInput ? otpInput.value.trim() : "";
    const newPassword = newPasswordInput ? newPasswordInput.value : "";
    const confirmPassword = confirmPasswordInput ? confirmPasswordInput.value : "";

    if (!emailAddress) {
        alertModal(false, "EMAIL ADDRESS IS REQUIRED, Kindly fill in the email address to continue");
        return;
    }

    if (!resetOtp) {
        alertModal(false, "RESET OTP IS REQUIRED, Kindly fill in the reset otp to continue");
        return;
    }

    if (resetOtp.length !== 6) {
        alertModal(false, "RESET OTP MUST BE EXACTLY 6 DIGITS");
        return;
    }

    if (!newPassword) {
        alertModal(false, "NEW PASSWORD IS REQUIRED, Kindly fill in the new password to continue");
        return;
    }

    if (newPassword.length < 8) {
        alertModal(false, "NEW PASSWORD MUST NOT BE LESS THAN 8 CHARACTERS");
        return;
    }

    if (newPassword !== confirmPassword) {
        alertModal(false, "PASSWORDS DO NOT MATCH, Kindly ensure both passwords are the same");
        return;
    }

    const resetPasswordBtn = document.getElementById('resetPasswordBtnId') || document.getElementById('loginBtn');
    const originalBtnText = resetPasswordBtn ? resetPasswordBtn.innerHTML : 'Reset Password';
    if (resetPasswordBtn) {
        resetPasswordBtn.innerHTML = 'Processing...';
        resetPasswordBtn.disabled = true;
    }

    const formData = new FormData();
    formData.append("emailAddress", emailAddress);
    formData.append("resetOtp", resetOtp);
    formData.append("newPassword", newPassword);
    formData.append("confirmPassword", confirmPassword);

    try {
        const response = await fetch(apiBaseUrl + '/student-auth/reset-password.php', {
            method: "POST",
            body: formData
        });

        const fetchData = await response.json();

        if (fetchData.success === true) {
            sessionStorage.removeItem("studentForgotPasswordSession");
            alertModal(true, fetchData.message || "PASSWORD RESET SUCCESSFUL", "student-login.html");
        } else {
            alertModal(false, fetchData.message || "INVALID RESET OTP! Kindly check your details and try again");
        }
    } catch (error) {
        console.error("Reset password error:", error);
        alertModal(false, "Connection error: " + (error.message || "Unable to reach server. Please try again."));
    } finally {
        if (resetPasswordBtn) {
            resetPasswordBtn.innerHTML = originalBtnText;
            resetPasswordBtn.disabled = false;
        }
    }
}

/* ==========================================================================
   DOM INITIALIZATION: Password Visibility Toggles & Prefills
   ========================================================================== */
document.addEventListener("DOMContentLoaded", function () {
    // 1. Password eye toggles
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

    // 2. Auto-populate email & OTP notice on student-reset-password.html
    const storedForgotSession = sessionStorage.getItem("studentForgotPasswordSession");
    if (storedForgotSession) {
        try {
            const data = JSON.parse(storedForgotSession);
            if (data && data.emailAddress) {
                const emailInput = document.getElementById("emailAddress");
                if (emailInput && !emailInput.value) {
                    emailInput.value = data.emailAddress;
                }

                const displayEmailEl = document.getElementById("displayEmail");
                if (displayEmailEl) {
                    displayEmailEl.textContent = data.emailAddress;
                }

                const otpNoticeEl = document.getElementById("otpNotice");
                if (otpNoticeEl) {
                    otpNoticeEl.style.display = "block";
                }
            }
            if (data && data.otpCode) {
                const otpInput = document.getElementById("resetOtp") || document.getElementById("otpCode");
                if (otpInput && !otpInput.value) {
                    otpInput.value = data.otpCode;
                }
            }
        } catch (err) {
            console.error("Error reading forgot-password session:", err);
        }
    }
});
