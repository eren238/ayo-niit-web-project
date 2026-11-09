let apiBaseUrl = "https://drakionoil.com/api/niit-api-testing";

function closeModal() {
    const alertModal = document.getElementById('alert');
    alertModal.classList.remove("alertModal");
}

function alertModal(success, message, redirectUrl = "sign-in.html") {
    const alertModal = document.getElementById('alert');
    if (success == true) {
        alertModal.innerHTML =
            `<div class="alert-container">
                <div class="image-wrapper">
                    <img src="all-images/icon-images/success.gif" alt="Success">
                </div>
                <h4>${message}</h4>
              <a href="${redirectUrl}"> <button class="btn" title="Okay, Thanks">Okay, Thanks</button> </a>
            </div>`;
    } else {
        alertModal.innerHTML =
            ` <div class="alert-container">
            <div class="image-wrapper">
                <img src="all-images/icon-images/warning.gif" alt="Warning">
            </div>
            <h4>${message}</h4>
            <button onclick = "closeModal()" class="btn" title="Okay">Okay</button>
            </div>
        `;
    }

    alertModal.classList.add("alertModal");
}


async function signUpHandle() {

    const fullName = document.getElementById("fullName").value;
    const emailAddress = document.getElementById("emailAddress").value.trim();
    const phoneNumber = document.getElementById("phoneNumber").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

    if (!fullName) {
        alertModal(false, "FULL NAME REQUIRED")
        return;
    }

    if (!emailAddress) {
        alertModal(false, "EMAIL ADDRESS REQUIRED")
        return;
    }

    if (!emailAddress.includes("@") || !emailAddress.includes(".")) {
        alertModal(false, "ENTER A VALID EMAIL")
        return;
    }

    if (!phoneNumber) {
        alertModal(false, "PHONE NUMBER REQUIRED")
        return;
    }

    const phoneDigitsOnly = phoneNumber.startsWith("+") ? phoneNumber.slice(1) : phoneNumber;

    if (isNaN(phoneDigitsOnly) || phoneDigitsOnly.trim() === "") {
        alertModal(false, "PHONE NUMBER MUST CONTAIN ONLY NUMBERS")
        return;
    }

    if (phoneNumber.length < 11 || phoneNumber.length > 14) {
        alertModal(false, "INVALID PHONE NUMBER")
        return;
    }


    if (!password) {
        alertModal(false, "PASSWORD REQUIRED")
        return;
    }

    if (password.length < 8) {
        alertModal(false, "PASSWORD MUST NOT BE LESS THAN 8 CHARACTERS")
        return;
    }

    if (password !== confirmPassword) {
        alertModal(false, "PASSWORDS DO NOT MATCH")
        return;
    }



    const submitBtn = document.getElementById('submitBtnId');
    submitBtn.innerHTML = 'Processing...';
    submitBtn.disabled = true;

    const formData = new FormData();
    formData.append("fullName", fullName);
    formData.append("emailAddress", emailAddress);
    formData.append("phoneNumber", phoneNumber);
    formData.append("password", password);
    formData.append("confirmPassword", confirmPassword);


    try {

        const response = await fetch(apiBaseUrl + '/auth/sign-up', {
            method: "POST",
            body: formData
        });

        const fetchData = await response.json();

        if (fetchData.success == true) {
            sessionStorage.setItem("userSignUpSession", JSON.stringify(fetchData));
            alertModal(fetchData.success, fetchData.message);
        } else {
            alertModal(fetchData.success, fetchData.message);
        }

        submitBtn.innerHTML = 'Sign Up';
        submitBtn.disabled = false;

    } catch (error) {
        console.error(error);
        alertModal(false, error.message);

        submitBtn.innerHTML = 'Sign Up';
        submitBtn.disabled = false;
    }
}




//  signin
async function signInHandle() {

    const emailAddress = document.getElementById("emailAddress").value.trim();
    const password = document.getElementById("password").value;

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

    const signInBtn = document.getElementById('signInBtnId');
    signInBtn.innerHTML = 'Authenticating...';
    signInBtn.disabled = true;

    try {

        const response = await fetch(apiBaseUrl + '/auth/login', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                emailAddress,
                password,
                confirmPassword: password
            })
        });

        const fetchData = await response.json();

        if (fetchData.success == true) {
            const email = (fetchData.data && fetchData.data.emailAddress) ? ' Hi, ' + fetchData.data.emailAddress : '';
            alertModal(fetchData.success, fetchData.message + email, "index.html");
        } else {
            alertModal(fetchData.success, fetchData.message);
        }

        signInBtn.innerHTML = 'Sign In';
        signInBtn.disabled = false;

    } catch (error) {
        console.error(error);
        alertModal(false, error.message);

        signInBtn.innerHTML = 'Sign In';
        signInBtn.disabled = false;
    }
}


//forgot-password


async function forgotPasswordHandle() {

    const emailAddress = document.getElementById("emailAddress").value.trim();

    if (!emailAddress) {
        alertModal(false, "EMAIL ADDRESS REQUIRED");
        return;
    }

    if (!emailAddress.includes("@") || !emailAddress.includes(".")) {
        alertModal(false, "ENTER A VALID EMAIL");
        return;
    }


    const forgotPasswordBtn = document.getElementById('forgotPasswordBtnId');
    forgotPasswordBtn.innerHTML = 'Processing...';
    forgotPasswordBtn.disabled = true;

    try {

        const response = await fetch(apiBaseUrl + '/auth/forgot-password', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                emailAddress,
            })
        });

        const fetchData = await response.json();

        if (fetchData.success == true) {
            sessionStorage.setItem("userForgotPasswordSession", JSON.stringify(fetchData));
            const email = (fetchData.data && fetchData.data.emailAddress) ? ' Hi, ' + fetchData.data.emailAddress : '';
            alertModal(fetchData.success, fetchData.message + email, "reset-password.html");
        } else {
            alertModal(fetchData.success, fetchData.message);
        }

        forgotPasswordBtn.innerHTML = 'Proceed to reset password';
        forgotPasswordBtn.disabled = false;

    } catch (error) {
        console.error(error);
        alertModal(false, error.message);

        forgotPasswordBtn.innerHTML = 'Proceed to reset password';
        forgotPasswordBtn.disabled = false;
    }
}



//reset-password
async function resetPasswordHandle() {
    
    const otpCode = document.getElementById("otpCode").value.trim();
    const newPassword = document.getElementById("newPassword").value;
    const confirmPassword = document.getElementById("confirmPassword").value;

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
        alertModal(false, "NEW PASSWORD MUST NOT BE LESS THAN 8 CHARACTERS")
        return;
    }

    if (newPassword !== confirmPassword) {
        alertModal(false, "NEW PASSWORDS DO NOT MATCH")
        return;
    }


    const resetPasswordBtn = document.getElementById('resetPasswordBtnId');
    resetPasswordBtn.innerHTML = 'Processing...';
    resetPasswordBtn.disabled = true;

    try {

        const response = await fetch(apiBaseUrl + '/auth/reset-password', {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                otpCode,
                password: newPassword,
                confirmedPassword: confirmPassword
            })
        });

        const fetchData = await response.json();
        if (fetchData.success == true) {
            const email = (fetchData.data && fetchData.data.emailAddress) ? ' Hi, ' + fetchData.data.emailAddress : '';
            alertModal(fetchData.success, fetchData.message + email, "sign-in.html");
        } else {
            alertModal(fetchData.success, fetchData.message);
        }

        resetPasswordBtn.innerHTML = 'Reset Password';
        resetPasswordBtn.disabled = false;

    } catch (error) {
        console.error(error);
        alertModal(false, error.message);

        resetPasswordBtn.innerHTML = 'Reset Password';
        resetPasswordBtn.disabled = false;
    }
}