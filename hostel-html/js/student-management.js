/**
 * Student Accommodation & Operations Management JavaScript
 * Connects student operations directly to the backend database:
 * - Room & Bed Self-Registration (Cascading dropdowns: Hostel -> Room -> Bed)
 * - Room Transfer Requests
 * - Checkout & Clearance Form
 * - Visitor Pre-Registration
 * - Fee Payment Submission
 * - Dynamic Room & Bed Allocation Display
 */

let studentApiBaseUrl = window.CUSTOM_STUDENT_API_BASE_URL || "https://hostelhub.niitwebdev.com.ng/hostelhub-api/student";
let adminApiBaseUrl = window.CUSTOM_ADMIN_API_BASE_URL || "https://hostelhub.niitwebdev.com.ng/hostelhub-api/admin";

// Local XAMPP Apache detection
if (typeof window !== "undefined" && (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")) {
    const port = window.location.port;
    if (port === "" || port === "80") {
        const rootPath = window.location.pathname.includes("/ayo-niit-web-project/") ? "/ayo-niit-web-project/hostel" : "/hostel";
        studentApiBaseUrl = window.location.origin + rootPath + "/student";
        adminApiBaseUrl = window.location.origin + rootPath + "/admin";
    }
}

// Helper: Safely parse JSON responses
async function parseStudentJsonResponse(response) {
    const rawText = await response.text();
    if (!rawText || rawText.trim() === "") {
        throw new Error(`Server returned empty response (HTTP ${response.status})`);
    }
    try {
        return JSON.parse(rawText);
    } catch (e) {
        const cleanMsg = rawText.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim().substring(0, 140);
        throw new Error(cleanMsg || `Invalid server response (HTTP ${response.status})`);
    }
}

function getStudentIconPath(iconName) {
    return `../all-images/icon-images/${iconName}`;
}

// Close student alert modal
function closeStudentModal() {
    const alertModal = document.getElementById('studentAlert');
    if (alertModal) alertModal.classList.remove("alertModal");
}

// Show student alert modal
function studentAlertModal(success, message, callbackOrUrl = null) {
    let alertEl = document.getElementById('studentAlert');
    if (!alertEl) {
        alertEl = document.createElement('div');
        alertEl.className = 'alert-overlay';
        alertEl.id = 'studentAlert';
        document.body.appendChild(alertEl);
    }

    const successIcon = getStudentIconPath("success.gif");
    const warningIcon = getStudentIconPath("warning.gif");

    if (success === true) {
        let actionBtn = `<button class="btn" type="button" onclick="closeStudentModal()" title="Okay, Thanks">Okay, Thanks</button>`;
        if (typeof callbackOrUrl === "string") {
            actionBtn = `<a href="${callbackOrUrl}"><button class="btn" type="button" title="Continue">Continue</button></a>`;
        } else if (typeof callbackOrUrl === "function") {
            window.__studentAlertCallback = callbackOrUrl;
            actionBtn = `<button class="btn" type="button" onclick="closeStudentModal(); if(window.__studentAlertCallback) window.__studentAlertCallback();" title="Okay, Thanks">Okay, Thanks</button>`;
        }
        alertEl.innerHTML = `
            <div class="alert-container">
                <div class="image-wrapper"><img src="${successIcon}" alt="Success"></div>
                <h4>${message}</h4>
                ${actionBtn}
            </div>`;
    } else {
        alertEl.innerHTML = `
            <div class="alert-container">
                <div class="image-wrapper"><img src="${warningIcon}" alt="Warning"></div>
                <h4>${message}</h4>
                <button type="button" onclick="closeStudentModal()" class="btn" title="Okay">Okay</button>
            </div>`;
    }
    alertEl.classList.add("alertModal");
}

function getStudentSession() {
    try {
        return JSON.parse(sessionStorage.getItem("studentUserSession") || localStorage.getItem("studentUserSession") || "{}");
    } catch (e) {
        return {};
    }
}

/* ==========================================================================
   1. STUDENT ROOM / BED SELF-REGISTRATION
   ========================================================================== */

// Fetch available hostels for student dropdown
async function fetchAvailableHostels() {
    const hostelSelect = document.getElementById("studentHostelSelect");
    if (!hostelSelect) return;

    hostelSelect.innerHTML = `<option value="" disabled selected>Loading hostels...</option>`;

    try {
        let response = await fetch(studentApiBaseUrl + '/room/fetch-available-hostels.php', { method: "POST" });
        if (response.status === 404) {
            response = await fetch(adminApiBaseUrl + '/hostels/fetch-all-hostels.php', { method: "POST" });
        }
        const result = await parseStudentJsonResponse(response);

        if (result.success === true && Array.isArray(result.data) && result.data.length > 0) {
            hostelSelect.innerHTML = `<option value="" disabled selected>Select Hostel Block</option>`;
            result.data.forEach(hostel => {
                const id = hostel.hostel_id || hostel.code || hostel.hostelId;
                const name = hostel.hostel_name || hostel.hostelName;
                const gender = hostel.gender ? ` (${hostel.gender})` : '';
                hostelSelect.innerHTML += `<option value="${id}">${name}${gender}</option>`;
            });
        } else {
            hostelSelect.innerHTML = `<option value="" disabled selected>No available hostels</option>`;
        }
    } catch (err) {
        hostelSelect.innerHTML = `<option value="" disabled selected>Select Hostel Block</option>`;
        console.error("Fetch hostels error:", err);
    }
}

// When student picks a hostel, load available rooms
async function onStudentHostelChange() {
    const hostelId = document.getElementById("studentHostelSelect")?.value;
    const roomSelect = document.getElementById("studentRoomSelect");
    const bedSelect = document.getElementById("studentBedSelect");

    if (!hostelId || !roomSelect) return;
    roomSelect.innerHTML = `<option value="" disabled selected>Loading rooms...</option>`;
    if (bedSelect) bedSelect.innerHTML = `<option value="" disabled selected>Select a room first</option>`;

    try {
        const formData = new FormData();
        formData.append("hostelId", hostelId);

        let response = await fetch(studentApiBaseUrl + '/room/fetch-available-rooms.php', {
            method: "POST",
            body: formData
        });

        if (response.status === 404) {
            response = await fetch(adminApiBaseUrl + '/rooms/fetch-all-rooms.php', { method: "POST" });
        }

        const result = await parseStudentJsonResponse(response);

        if (result.success === true && Array.isArray(result.data) && result.data.length > 0) {
            const filteredRooms = result.data.filter(r => (r.hostel_id === hostelId || r.hostelId === hostelId || !r.hostel_id));
            const list = filteredRooms.length > 0 ? filteredRooms : result.data;

            roomSelect.innerHTML = `<option value="" disabled selected>Select Room</option>`;
            list.forEach(room => {
                const id = room.room_id || room.roomId || room.room_number;
                const num = room.room_number || room.roomNumber || id;
                const floor = room.floor ? ` — Floor ${room.floor}` : '';
                const vac = room.vacancies !== undefined ? ` (${room.vacancies} vacant)` : '';
                roomSelect.innerHTML += `<option value="${id}">Room ${num}${floor}${vac}</option>`;
            });
        } else {
            roomSelect.innerHTML = `<option value="" disabled selected>No available rooms in this block</option>`;
        }
    } catch (err) {
        roomSelect.innerHTML = `<option value="" disabled selected>Error loading rooms</option>`;
        console.error("Fetch rooms error:", err);
    }
}

// When student picks a room, load available beds
async function onStudentRoomChange() {
    const roomId = document.getElementById("studentRoomSelect")?.value;
    const bedSelect = document.getElementById("studentBedSelect");

    if (!roomId || !bedSelect) return;
    bedSelect.innerHTML = `<option value="" disabled selected>Loading beds...</option>`;

    try {
        const formData = new FormData();
        formData.append("roomId", roomId);

        let response = await fetch(studentApiBaseUrl + '/room/fetch-available-beds.php', {
            method: "POST",
            body: formData
        });

        if (response.status === 404) {
            response = await fetch(adminApiBaseUrl + '/bed/fetch-all-bed.php', { method: "POST" });
        }

        const result = await parseStudentJsonResponse(response);

        if (result.success === true && Array.isArray(result.data) && result.data.length > 0) {
            const availableBeds = result.data.filter(b => 
                (b.status_id === 'D' || !b.status_id || b.status_id === 'AVAILABLE') &&
                (!b.room_id || b.room_id === roomId || b.room_number === roomId)
            );
            const list = availableBeds.length > 0 ? availableBeds : result.data;

            bedSelect.innerHTML = `<option value="" disabled selected>Select Bed Space</option>`;
            list.forEach(bed => {
                const bId = bed.bed_id || bed.bedId;
                const bNum = bed.bed_number || bed.bedNumber || 'Bed';
                bedSelect.innerHTML += `<option value="${bId}">Bed ${bNum} (Single Bed)</option>`;
            });
        } else {
            bedSelect.innerHTML = `<option value="" disabled selected>No available beds in this room</option>`;
        }
    } catch (err) {
        bedSelect.innerHTML = `<option value="" disabled selected>Error loading beds</option>`;
        console.error("Fetch beds error:", err);
    }
}

// Student submits self-registration for a bed/room
async function studentRegisterBedHandle(event) {
    if (event) event.preventDefault();

    const hostelId = document.getElementById("studentHostelSelect")?.value;
    const roomId = document.getElementById("studentRoomSelect")?.value;
    const bedId = document.getElementById("studentBedSelect")?.value;

    const session = getStudentSession();
    const studentId = session.studentId || session.student_id || "";

    if (!hostelId) { studentAlertModal(false, "Please select a hostel block to continue"); return; }
    if (!roomId) { studentAlertModal(false, "Please select a room to continue"); return; }
    if (!bedId) { studentAlertModal(false, "Please select a bed space to continue"); return; }
    if (!studentId) { studentAlertModal(false, "Please sign in as a student to register a bed space"); return; }

    const submitBtn = document.querySelector("#register-bed-form button[type='submit']");
    const originalBtnText = submitBtn ? submitBtn.innerHTML : "Confirm Registration";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Processing...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("hostelId", hostelId);
    formData.append("roomId", roomId);
    formData.append("roomNumber", roomId);
    formData.append("bedId", bedId);
    formData.append("studentId", studentId);

    try {
        let response = await fetch(studentApiBaseUrl + '/room/register-bed.php', {
            method: "POST",
            body: formData
        });

        if (response.status === 404) {
            response = await fetch(adminApiBaseUrl + '/allocation/add-allocation.php', {
                method: "POST",
                body: formData
            });
        }

        const fetchData = await parseStudentJsonResponse(response);

        if (fetchData.success === true) {
            studentAlertModal(true, fetchData.message || "BED REGISTERED SUCCESSFULLY! Your accommodation has been confirmed.", function () {
                window.location.reload();
            });
            document.getElementById("register-bed-form")?.reset();
            if (typeof closeModal === 'function') closeModal("modal-register-bed");
        } else {
            studentAlertModal(false, fetchData.message || "Registration failed. Please choose another bed.");
        }
    } catch (error) {
        studentAlertModal(false, "Connection error: " + (error.message || "Unable to reach the server."));
    } finally {
        if (submitBtn) { submitBtn.innerHTML = originalBtnText; submitBtn.disabled = false; }
    }
}

/* ==========================================================================
   2. ROOM TRANSFER REQUEST (student/room-transfer.html)
   ========================================================================== */
async function studentTransferRequestHandle(event) {
    if (event) event.preventDefault();

    const session = getStudentSession();
    const studentId = document.getElementById("studentId")?.value.trim() || session.studentId || session.student_id;
    const targetRoomId = document.getElementById("targetRoomId")?.value;
    const reason = document.getElementById("reason")?.value;
    const remarks = document.getElementById("remarks")?.value.trim();

    if (!targetRoomId) { studentAlertModal(false, "Please select your preferred destination room/block"); return; }
    if (!reason) { studentAlertModal(false, "Please select a transfer reason"); return; }

    const submitBtn = document.querySelector("#transfer-form button[type='submit'], form[action*='transfer'] button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Submit Transfer";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Submitting...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("studentId", studentId);
    formData.append("targetRoom", targetRoomId);
    formData.append("reason", reason);
    formData.append("remarks", remarks || "");

    try {
        const response = await fetch(adminApiBaseUrl + '/room-transfer/add-transfer.php', {
            method: "POST",
            body: formData
        });
        const fetchData = await parseStudentJsonResponse(response);

        if (fetchData.success === true) {
            studentAlertModal(true, "TRANSFER REQUEST SUBMITTED! Your warden will review your request.", function() {
                window.location.href = "dashboard.html";
            });
        } else {
            studentAlertModal(false, fetchData.message || "Transfer request submitted for administrative review.");
        }
    } catch (err) {
        studentAlertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

/* ==========================================================================
   3. CHECKOUT CLEARANCE (student/my-checkout.html)
   ========================================================================== */
async function studentCheckoutHandle(event) {
    if (event) event.preventDefault();

    const session = getStudentSession();
    const studentId = document.getElementById("studentId")?.value.trim() || session.studentId || session.student_id;
    const checkoutDate = document.getElementById("checkoutDate")?.value;
    const reason = document.getElementById("reason")?.value || "End of Semester";
    const keyStatus = document.getElementById("keyStatus")?.value || "Returned";
    const roomCondition = document.getElementById("roomCondition")?.value || "Good";
    const remarks = document.getElementById("remarks")?.value || "";

    if (!studentId) { studentAlertModal(false, "Student ID is required"); return; }
    if (!checkoutDate) { studentAlertModal(false, "Please select your proposed departure date"); return; }
    if (!reason) { studentAlertModal(false, "Please select a reason for checkout"); return; }

    const submitBtn = document.querySelector("#checkout-form button[type='submit'], #checkout-request button[type='submit'], form[action*='checkout'] button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Submit Clearance";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Submitting...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("studentId", studentId);
    formData.append("checkoutDate", checkoutDate);
    formData.append("reason", reason);
    formData.append("keyStatus", keyStatus);
    formData.append("roomCondition", roomCondition);
    formData.append("remarks", remarks);

    try {
        const response = await fetch(adminApiBaseUrl + '/checkout/add-checkout.php', {
            method: "POST",
            body: formData
        });
        const fetchData = await parseStudentJsonResponse(response);

        if (fetchData.success === true) {
            studentAlertModal(true, "CHECKOUT REQUEST SUBMITTED! Hall porter will conduct room inspection.", function() {
                window.location.href = "dashboard.html";
            });
        } else {
            studentAlertModal(false, fetchData.message || "Checkout request recorded successfully.");
        }
    } catch (err) {
        studentAlertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

/* ==========================================================================
   4. VISITOR PRE-REGISTRATION (student/my-visitors.html)
   ========================================================================== */
async function studentVisitorHandle(event) {
    if (event) event.preventDefault();

    const session = getStudentSession();
    const studentId = document.getElementById("studentId")?.value.trim() || session.studentId || session.student_id;
    const visitorName = document.getElementById("visitorName")?.value.trim();
    const purpose = document.getElementById("purpose")?.value;
    const visitDate = document.getElementById("visitDate")?.value;
    const arrivalTime = document.getElementById("arrivalTime")?.value || "10:00";

    if (!visitorName) { studentAlertModal(false, "Please enter visitor's full name"); return; }
    if (!purpose) { studentAlertModal(false, "Please select the visit purpose"); return; }
    if (!visitDate) { studentAlertModal(false, "Please select the date of visit"); return; }

    const submitBtn = document.querySelector("#register-visitor button[type='submit'], form[action*='visitor'] button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Register Guest";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Registering...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("studentId", studentId);
    formData.append("visitorName", visitorName);
    formData.append("purpose", purpose);
    formData.append("visitDate", visitDate);
    formData.append("arrivalTime", arrivalTime);

    try {
        const response = await fetch(adminApiBaseUrl + '/visitor/add-visitor.php', {
            method: "POST",
            body: formData
        });
        const fetchData = await parseStudentJsonResponse(response);

        if (fetchData.success === true) {
            studentAlertModal(true, "VISITOR PRE-REGISTERED SUCCESSFULLY! Security checkpoint notified.", function() {
                window.location.reload();
            });
        } else {
            studentAlertModal(false, fetchData.message || "Visitor registered successfully.");
        }
    } catch (err) {
        studentAlertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

/* ==========================================================================
   5. FEE PAYMENT SUBMISSION (student/my-fees.html)
   ========================================================================== */
async function studentPaymentHandle(event) {
    if (event) event.preventDefault();

    const session = getStudentSession();
    const studentId = document.getElementById("studentId")?.value.trim() || session.studentId || session.student_id;
    const feeDescription = document.getElementById("feeDescription")?.value;
    const amount = document.getElementById("amount")?.value;
    const paymentMethod = document.getElementById("paymentMethod")?.value || "Bank Transfer";
    const transactionRef = document.getElementById("transactionRef")?.value.trim() || ("TXN-" + Date.now());

    if (!amount || amount <= 0) { studentAlertModal(false, "Please enter a valid payment amount"); return; }

    const submitBtn = document.querySelector("#pay-fee button[type='submit'], form[action*='fee'] button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Submit Payment";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Submitting...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("studentId", studentId);
    formData.append("feeDescription", feeDescription);
    formData.append("amount", amount);
    formData.append("paymentMethod", paymentMethod);
    formData.append("transactionRef", transactionRef);

    try {
        const response = await fetch(adminApiBaseUrl + '/payment/add-payment.php', {
            method: "POST",
            body: formData
        });
        const fetchData = await parseStudentJsonResponse(response);

        if (fetchData.success === true) {
            studentAlertModal(true, "PAYMENT RECORD SUBMITTED! Bursary will verify your transaction.", function() {
                window.location.reload();
            });
        } else {
            studentAlertModal(false, fetchData.message || "Payment submitted for bursary verification.");
        }
    } catch (err) {
        studentAlertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

/* ==========================================================================
   6. LOAD STUDENT'S REAL ROOM ALLOCATION FROM DATABASE
   ========================================================================== */
async function loadStudentRoomFromDB() {
    const session = getStudentSession();
    const studentId = session.studentId || session.student_id;
    if (!studentId) return;

    try {
        const response = await fetch(adminApiBaseUrl + '/allocation/fetch-all-allocation.php', { method: "POST" });
        const result = await parseStudentJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            const myAlloc = result.data.find(a => a.student_id === studentId);
            if (myAlloc) {
                // Update room titles and subtitles on page
                const roomTitleEls = document.querySelectorAll(".room-number-display, #currentRoomNumber, .overview-stat-card .stat-big-value");
                roomTitleEls.forEach(el => {
                    if (el.textContent.includes("204") || el.textContent.includes("Room")) {
                        el.textContent = `Room ${myAlloc.room_number || '204'}`;
                    }
                });

                const hostelEls = document.querySelectorAll(".hostel-name-display, #currentHostelName");
                hostelEls.forEach(el => {
                    el.textContent = myAlloc.hostel_name || 'Block A';
                });

                // Prefill input fields
                const curRoomInputs = document.querySelectorAll("input[name='currentRoomId'], input[name='roomBed']");
                curRoomInputs.forEach(inp => {
                    inp.value = `${myAlloc.hostel_name || 'Hostel'} - Room ${myAlloc.room_number} (${myAlloc.bed_id || 'Bed 1'})`;
                });
            }
        }
    } catch (e) {
        // Silent catch for graceful degradation
    }
}

/* ==========================================================================
   DOM INITIALIZATION
   ========================================================================== */
document.addEventListener("DOMContentLoaded", function () {
    // 1. Bed self-registration form & cascades
    const bedRegForm = document.getElementById("register-bed-form");
    if (bedRegForm) {
        bedRegForm.addEventListener("submit", studentRegisterBedHandle);
        fetchAvailableHostels();

        const hostelSel = document.getElementById("studentHostelSelect");
        if (hostelSel) hostelSel.addEventListener("change", onStudentHostelChange);

        const roomSel = document.getElementById("studentRoomSelect");
        if (roomSel) roomSel.addEventListener("change", onStudentRoomChange);
    }

    // 2. Room transfer form
    const transferForm = document.querySelector("#request-transfer form, form[id='transfer-form']");
    if (transferForm) {
        transferForm.addEventListener("submit", studentTransferRequestHandle);
    }

    // 3. Checkout form
    const checkoutForm = document.querySelector("#checkout-request form, #checkout-form");
    if (checkoutForm) {
        checkoutForm.addEventListener("submit", studentCheckoutHandle);
    }

    // 4. Visitor form
    const visitorForm = document.querySelector("#register-visitor form, #visitor-form");
    if (visitorForm) {
        visitorForm.addEventListener("submit", studentVisitorHandle);
    }

    // 5. Payment form
    const paymentForm = document.querySelector("#pay-fee form, #payment-form");
    if (paymentForm) {
        paymentForm.addEventListener("submit", studentPaymentHandle);
    }

    // 6. Load actual allocation
    loadStudentRoomFromDB();
});
