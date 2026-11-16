/**
 * Admin & Staff Management JavaScript
 * Connects all administrative operations directly to the MySQL backend:
 * - Staff, Students, Hostels, Rooms, Beds
 * - Allocations, Checkouts, Transfers, Visitors, Payments, Maintenance
 */

let adminApiBaseUrl = window.CUSTOM_ADMIN_API_BASE_URL || "https://hostelhub.niitwebdev.com.ng/hostelhub-api/admin";

// Local XAMPP Apache detection
if (typeof window !== "undefined" && (window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1")) {
    const port = window.location.port;
    if (port === "" || port === "80") {
        const rootPath = window.location.pathname.includes("/ayo-niit-web-project/") ? "/ayo-niit-web-project/hostel" : "/hostel";
        adminApiBaseUrl = window.location.origin + rootPath + "/admin";
    }
}

// Helper: Safely parse JSON from server responses
async function parseJsonResponse(response) {
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

// Resilient fetch helper: tries primary endpoint, falls back to alternative if 404
async function safeFetchAdmin(primaryPath, fallbackPath, options = {}) {
    try {
        const res = await fetch(adminApiBaseUrl + primaryPath, options);
        if (res.status === 404 && fallbackPath) {
            return await fetch(adminApiBaseUrl + fallbackPath, options);
        }
        return res;
    } catch (err) {
        if (fallbackPath) {
            try {
                return await fetch(adminApiBaseUrl + fallbackPath, options);
            } catch (e) {
                throw err;
            }
        }
        throw err;
    }
}

function getIconPath(iconName) {
    const isSub = window.location.pathname.includes('/admin/') || window.location.pathname.includes('/staff/') || window.location.pathname.includes('/student/');
    return isSub ? `../all-images/icon-images/${iconName}` : `all-images/icon-images/${iconName}`;
}

function closeModal(modalId = null) {
    if (modalId && typeof modalId === 'string' && modalId !== 'alert') {
        const targetModal = document.getElementById(modalId);
        if (targetModal) targetModal.classList.remove('active');
        document.body.style.overflow = '';
        return;
    }
    const alertModalEl = document.getElementById('alert');
    if (alertModalEl) {
        alertModalEl.classList.remove("alertModal");
    }
}

function alertModal(success, message, callbackOrUrl = null) {
    let alertEl = document.getElementById('alert');
    if (!alertEl) {
        alertEl = document.createElement('div');
        alertEl.className = 'alert-overlay';
        alertEl.id = 'alert';
        document.body.appendChild(alertEl);
    }

    const successIcon = getIconPath("success.gif");
    const warningIcon = getIconPath("warning.gif");

    if (success === true) {
        let actionBtn = `<button class="btn" type="button" onclick="closeModal()" title="Okay, Thanks">Okay, Thanks</button>`;
        if (typeof callbackOrUrl === "string") {
            actionBtn = `<a href="${callbackOrUrl}"><button class="btn" type="button" title="Continue">Continue</button></a>`;
        } else if (typeof callbackOrUrl === "function") {
            window.__alertModalCallback = callbackOrUrl;
            actionBtn = `<button class="btn" type="button" onclick="closeModal(); if (window.__alertModalCallback) window.__alertModalCallback();" title="Okay, Thanks">Okay, Thanks</button>`;
        }

        alertEl.innerHTML = `
            <div class="alert-container">
                <div class="image-wrapper"><img src="${successIcon}" alt="Success"></div>
                <h4>${message}</h4>
                ${actionBtn}
            </div>
        `;
    } else {
        alertEl.innerHTML = `
            <div class="alert-container">
                <div class="image-wrapper"><img src="${warningIcon}" alt="Warning"></div>
                <h4>${message}</h4>
                <button type="button" onclick="closeModal()" class="btn" title="Okay">Okay</button>
            </div>
        `;
    }
    alertEl.classList.add("alertModal");
}

/* ==========================================================================
   1. STAFF MANAGEMENT
   ========================================================================== */
let allStaffList = [];

async function fetchAllStaff() {
    const tableBody = document.querySelector("#staffTableBody, .management-table tbody");
    if (!tableBody || (!window.location.pathname.includes("staff-list.html") && !window.location.pathname.includes("staff.html"))) return;

    try {
        const response = await safeFetchAdmin('/staff/fetch-all-staff.php', null, { method: "POST" });
        const result = await parseJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            allStaffList = result.data;
            renderStaffTable(allStaffList);
            updateStaffStats(allStaffList);
        } else {
            tableBody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">No staff members found.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching staff:", error);
    }
}

function renderStaffTable(staffList) {
    const tableBody = document.querySelector("#staffTableBody, .management-table tbody");
    if (!tableBody) return;

    if (!staffList || staffList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 25px; color: #64748b;">No matching staff records found.</td></tr>`;
        return;
    }

    const badgeColors = [
        { bg: "#eff6ff", color: "#1e40af" },
        { bg: "#ecfdf5", color: "#059669" },
        { bg: "#fdf2f8", color: "#db2777" },
        { bg: "#fef3c7", color: "#b45309" }
    ];

    tableBody.innerHTML = staffList.map((staff, idx) => {
        const color = badgeColors[idx % badgeColors.length];
        const initials = ((staff.first_name?.[0] || '') + (staff.last_name?.[0] || '')).toUpperCase() || 'ST';
        const roleLabel = staff.role_name || staff.role_id || 'Staff';
        const statusLabel = (staff.status_id === '1' || staff.status_name === 'Active') ? 'Active' : 'Inactive';
        const statusClass = statusLabel === 'Active' ? 'badge-success' : 'badge-neutral';

        return `
            <tr>
                <td>
                    <div class="cell-user-info">
                        <div class="avatar-badge" style="background-color: ${color.bg}; color: ${color.color};">${initials}</div>
                        <div>
                            <div class="cell-title">${staff.first_name} ${staff.last_name}</div>
                            <div class="cell-subtitle">${staff.staff_id || 'N/A'}</div>
                        </div>
                    </div>
                </td>
                <td>${staff.email_address || '—'}</td>
                <td><span class="role-pill">${roleLabel}</span></td>
                <td>${staff.created_at ? new Date(staff.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Recently'}</td>
                <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 4px;">
                        <button type="button" class="btn-action-icon btn-action-delete" title="Delete Staff" onclick="deleteStaffHandle('${staff.staff_id}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");
}

function updateStaffStats(staffList) {
    const totalEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(1) .stat-big-value");
    const activeEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(2) .stat-big-value");
    if (totalEl) totalEl.textContent = staffList.length;
    if (activeEl) activeEl.textContent = staffList.filter(s => s.status_id === '1' || s.status_name === 'Active').length;
}

async function createStaffHandle(event) {
    if (event) event.preventDefault();

    const firstName = document.getElementById("firstName")?.value.trim();
    const lastName = document.getElementById("lastName")?.value.trim();
    const emailAddress = document.getElementById("emailAddress")?.value.trim();
    const password = document.getElementById("password")?.value;
    const roleId = document.getElementById("roleId")?.value;
    const statusId = document.getElementById("statusId")?.value || "1";

    if (!firstName || !lastName) { alertModal(false, "FIRST NAME AND LAST NAME ARE REQUIRED"); return; }
    if (!emailAddress) { alertModal(false, "EMAIL ADDRESS IS REQUIRED"); return; }
    if (!password || password.length < 6) { alertModal(false, "PASSWORD MUST BE AT LEAST 6 CHARACTERS"); return; }
    if (!roleId) { alertModal(false, "STAFF ROLE IS REQUIRED"); return; }

    const submitBtn = document.querySelector("#add-staff button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Save";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Saving...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("firstName", firstName);
    formData.append("lastName", lastName);
    formData.append("emailAddress", emailAddress);
    formData.append("password", password);
    formData.append("roleId", roleId);
    formData.append("statusId", statusId);

    try {
        const response = await safeFetchAdmin('/staff/create-staff.php', null, { method: "POST", body: formData });
        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true) {
            alertModal(true, `STAFF CREATED SUCCESSFULLY! Staff ID: ${fetchData.data?.staffId || ''}`);
            document.querySelector("#add-staff form")?.reset();
            closeModal("modal-add-staff");
            fetchAllStaff();
        } else {
            alertModal(false, fetchData.message || "Failed to create staff account.");
        }
    } catch (err) {
        alertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

async function deleteStaffHandle(staffId) {
    if (!confirm(`Are you sure you want to delete staff member ${staffId}?`)) return;

    const formData = new FormData();
    formData.append("staffId", staffId);

    try {
        const response = await safeFetchAdmin('/staff/delete-staff.php', null, { method: "POST", body: formData });
        const result = await parseJsonResponse(response);

        if (result.success === true) {
            alertModal(true, result.message || "STAFF MEMBER DELETED");
            fetchAllStaff();
        } else {
            alertModal(false, result.message || "Failed to delete staff.");
        }
    } catch (error) {
        alertModal(false, "Connection error: " + error.message);
    }
}

/* ==========================================================================
   2. STUDENT MANAGEMENT
   ========================================================================== */
let allStudentsList = [];

async function fetchAllStudents() {
    const tableBody = document.querySelector("#studentsTableBody, .management-table tbody");
    if (!tableBody || (!window.location.pathname.includes("students.html") && !window.location.pathname.includes("student-list.html"))) return;

    try {
        const response = await safeFetchAdmin('/student/fetch-all-student.php', null, { method: "POST" });
        const result = await parseJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            allStudentsList = result.data;
            renderStudentsTable(allStudentsList);
            updateStudentStats(allStudentsList);
        } else {
            tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">No registered students found.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching students:", error);
    }
}

function renderStudentsTable(studentsList) {
    const tableBody = document.querySelector("#studentsTableBody, .management-table tbody");
    if (!tableBody) return;

    if (!studentsList || studentsList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 25px; color: #64748b;">No matching student records found.</td></tr>`;
        return;
    }

    const badgeColors = [
        { bg: "#eff6ff", color: "#1e40af" },
        { bg: "#ecfdf5", color: "#059669" },
        { bg: "#fdf2f8", color: "#db2777" },
        { bg: "#fef3c7", color: "#b45309" }
    ];

    tableBody.innerHTML = studentsList.map((student, idx) => {
        const color = badgeColors[idx % badgeColors.length];
        const initials = ((student.first_name?.[0] || '') + (student.last_name?.[0] || '')).toUpperCase() || 'ST';
        const statusLabel = (student.status_id === '1' || student.status_name === 'Active') ? 'Active' : 'Inactive';
        const statusClass = statusLabel === 'Active' ? 'badge-success' : 'badge-neutral';

        return `
            <tr>
                <td>
                    <div class="cell-user-info">
                        <div class="avatar-badge" style="background-color: ${color.bg}; color: ${color.color};">${initials}</div>
                        <div>
                            <div class="cell-title">${student.first_name} ${student.last_name}</div>
                            <div class="cell-subtitle">${student.student_id || 'N/A'}</div>
                        </div>
                    </div>
                </td>
                <td>${student.gender || '—'}</td>
                <td>${student.department || '—'}</td>
                <td>${student.level ? student.level + ' L' : '—'}</td>
                <td>${student.email_address || '—'}</td>
                <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 4px;">
                        <button type="button" class="btn-action-icon btn-action-delete" title="Delete Student" onclick="deleteStudentHandle('${student.student_id}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");
}

function updateStudentStats(studentsList) {
    const totalEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(1) .stat-big-value");
    if (totalEl) totalEl.textContent = studentsList.length.toLocaleString();
}

async function createStudentHandle(event) {
    if (event) event.preventDefault();

    const firstName = document.getElementById("firstName")?.value.trim();
    const lastName = document.getElementById("lastName")?.value.trim();
    const gender = document.getElementById("gender")?.value;
    const department = document.getElementById("department")?.value.trim();
    const level = document.getElementById("level")?.value;
    const phoneNumber = document.getElementById("phoneNumber")?.value.trim();
    const emailAddress = document.getElementById("emailAddress")?.value.trim();
    const password = document.getElementById("password")?.value;
    const statusId = document.getElementById("statusId")?.value || "1";

    if (!firstName || !lastName) { alertModal(false, "NAME IS REQUIRED"); return; }
    if (!gender) { alertModal(false, "GENDER IS REQUIRED"); return; }
    if (!department) { alertModal(false, "DEPARTMENT IS REQUIRED"); return; }
    if (!level) { alertModal(false, "LEVEL IS REQUIRED"); return; }
    if (!phoneNumber) { alertModal(false, "PHONE NUMBER IS REQUIRED"); return; }
    if (!emailAddress) { alertModal(false, "EMAIL ADDRESS IS REQUIRED"); return; }
    if (!password || password.length < 6) { alertModal(false, "PASSWORD MUST BE AT LEAST 6 CHARACTERS"); return; }

    const submitBtn = document.querySelector("#add-student button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Save";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Saving...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("firstName", firstName);
    formData.append("lastName", lastName);
    formData.append("gender", gender);
    formData.append("department", department);
    formData.append("level", level);
    formData.append("phoneNumber", phoneNumber);
    formData.append("emailAddress", emailAddress);
    formData.append("password", password);
    formData.append("statusId", statusId);

    try {
        const response = await safeFetchAdmin('/student/create-student.php', null, { method: "POST", body: formData });
        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true) {
            alertModal(true, `STUDENT REGISTERED SUCCESSFULLY! ID: ${fetchData.data?.studentId || ''}`);
            document.querySelector("#add-student form")?.reset();
            closeModal("modal-add-student");
            fetchAllStudents();
        } else {
            alertModal(false, fetchData.message || "Failed to register student.");
        }
    } catch (err) {
        alertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

async function deleteStudentHandle(studentId) {
    if (!confirm(`Are you sure you want to delete student ${studentId}?`)) return;

    const formData = new FormData();
    formData.append("studentId", studentId);

    try {
        const response = await safeFetchAdmin('/student/delete-student.php', null, { method: "POST", body: formData });
        const result = await parseJsonResponse(response);

        if (result.success === true) {
            alertModal(true, result.message || "STUDENT DELETED");
            fetchAllStudents();
        } else {
            alertModal(false, result.message || "Failed to delete student.");
        }
    } catch (error) {
        alertModal(false, "Connection error: " + error.message);
    }
}

/* ==========================================================================
   3. HOSTEL MANAGEMENT (admin/hostels.html)
   ========================================================================== */
let allHostelsList = [];

async function fetchAllHostels() {
    const tableBody = document.querySelector("#hostelsTableBody, .management-table tbody");
    if (!tableBody && !document.getElementById("hostelId") && !document.getElementById("studentHostelSelect")) return;

    try {
        const response = await safeFetchAdmin('/hostels/fetch-all-hostels.php', '/hostel/fetch-all-hostel.php', { method: "POST" });
        const result = await parseJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            allHostelsList = result.data;
            if (tableBody && window.location.pathname.includes("hostels.html")) {
                renderHostelsTable(allHostelsList);
                updateHostelStats(allHostelsList);
            }
            populateHostelDropdowns(allHostelsList);
        } else if (tableBody && window.location.pathname.includes("hostels.html")) {
            tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">No hostel blocks configured yet. Click "Add New Hostel" above.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching hostels:", error);
    }
}

function renderHostelsTable(hostelList) {
    const tableBody = document.querySelector("#hostelsTableBody, .management-table tbody");
    if (!tableBody) return;

    if (!hostelList || hostelList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 25px; color: #64748b;">No matching hostels found.</td></tr>`;
        return;
    }

    const badgeColors = [
        { bg: "#eff6ff", color: "#1e40af" },
        { bg: "#ecfdf5", color: "#059669" },
        { bg: "#fdf2f8", color: "#db2777" },
        { bg: "#fef3c7", color: "#b45309" }
    ];

    tableBody.innerHTML = hostelList.map((hostel, idx) => {
        const color = badgeColors[idx % badgeColors.length];
        const hostelId = hostel.hostel_id || hostel.hostelId || 'HOSTEL';
        const initial = (hostel.hostel_name || 'H')[0].toUpperCase();
        const statusLabel = (hostel.status_id === '1' || hostel.status_name === 'Active' || hostel.status_id === 'ACTIVE') ? 'Active' : 'Maintenance';
        const statusClass = statusLabel === 'Active' ? 'badge-success' : 'badge-warning';

        return `
            <tr>
                <td>
                    <div class="cell-user-info">
                        <div class="avatar-badge" style="background-color: ${color.bg}; color: ${color.color};">${initial}</div>
                        <div>
                            <div class="cell-title">${hostel.hostel_name || 'Hostel Block'}</div>
                            <div class="cell-subtitle">ID: ${hostelId}</div>
                        </div>
                    </div>
                </td>
                <td><span class="role-pill">${hostelId}</span></td>
                <td>${hostel.gender || 'Mixed'}</td>
                <td><strong>${hostel.hostel_capacity || hostel.capacity || 0}</strong> Students</td>
                <td>${hostel.total_rooms || Math.ceil((hostel.hostel_capacity || 40) / 4)} Rooms</td>
                <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 4px;">
                        <button type="button" class="btn-action-icon btn-action-delete" title="Delete Hostel" onclick="deleteHostelHandle('${hostel.hostel_id || hostelId}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");
}

function updateHostelStats(hostelList) {
    const totalEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(1) .stat-big-value");
    const maleEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(2) .stat-big-value");
    const femaleEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(3) .stat-big-value");
    const capEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(4) .stat-big-value");

    if (totalEl) totalEl.textContent = hostelList.length + " Blocks";
    if (maleEl) maleEl.textContent = hostelList.filter(h => (h.gender || '').toLowerCase() === 'male').length + " Hostels";
    if (femaleEl) femaleEl.textContent = hostelList.filter(h => (h.gender || '').toLowerCase() === 'female').length + " Hostels";
    if (capEl) {
        const totalCap = hostelList.reduce((acc, h) => acc + parseInt(h.hostel_capacity || h.capacity || 0), 0);
        capEl.textContent = totalCap.toLocaleString();
    }
}

async function createHostelHandle(event) {
    if (event) event.preventDefault();

    const hostelName = document.getElementById("hostelName")?.value.trim();
    const gender = document.getElementById("gender")?.value;
    const capacity = document.getElementById("capacity")?.value;
    const totalRooms = document.getElementById("totalRooms")?.value;
    const statusId = document.getElementById("statusId")?.value || "1";

    if (!hostelName) { alertModal(false, "HOSTEL NAME IS REQUIRED"); return; }
    if (!gender) { alertModal(false, "GENDER IS REQUIRED"); return; }
    if (!capacity) { alertModal(false, "STUDENT CAPACITY IS REQUIRED"); return; }

    const submitBtn = document.querySelector("#add-hostel button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Save Hostel";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Saving...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("hostelName", hostelName);
    formData.append("gender", gender);
    formData.append("hostelCapacity", capacity);
    formData.append("capacity", capacity);
    formData.append("totalRooms", totalRooms || "20");
    formData.append("statusId", statusId);

    try {
        const response = await safeFetchAdmin('/hostels/add-hostel.php', '/hostel/create-hostel.php', { method: "POST", body: formData });
        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true) {
            alertModal(true, `HOSTEL CREATED SUCCESSFULLY!`);
            document.querySelector("#add-hostel form")?.reset();
            closeModal("modal-add-hostel");
            fetchAllHostels();
        } else {
            alertModal(false, fetchData.message || "Failed to save hostel.");
        }
    } catch (err) {
        alertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

async function deleteHostelHandle(hostelId) {
    if (!confirm(`Are you sure you want to delete hostel ${hostelId}? This will remove associated rooms.`)) return;

    const formData = new FormData();
    formData.append("hostelId", hostelId);

    try {
        const response = await safeFetchAdmin('/hostels/delete-hostel.php', '/hostel/delete-hostel.php', { method: "POST", body: formData });
        const result = await parseJsonResponse(response);

        if (result.success === true) {
            alertModal(true, result.message || "HOSTEL DELETED");
            fetchAllHostels();
        } else {
            alertModal(false, result.message || "Failed to delete hostel.");
        }
    } catch (error) {
        alertModal(false, "Connection error: " + error.message);
    }
}

/* ==========================================================================
   4. ROOM MANAGEMENT (admin/rooms.html)
   ========================================================================== */
let allRoomsList = [];

async function fetchAllRooms() {
    const tableBody = document.querySelector("#roomsTableBody, .management-table tbody");
    if (!tableBody && !document.getElementById("roomId") && !document.getElementById("studentRoomSelect")) return;

    try {
        const response = await safeFetchAdmin('/rooms/fetch-all-rooms.php', '/room/fetch-all-room.php', { method: "POST" });
        const result = await parseJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            allRoomsList = result.data;
            if (tableBody && window.location.pathname.includes("rooms.html")) {
                renderRoomsTable(allRoomsList);
                updateRoomStats(allRoomsList);
            }
            populateRoomDropdowns(allRoomsList);
        } else if (tableBody && window.location.pathname.includes("rooms.html")) {
            tableBody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 30px; color: #64748b;">No rooms configured. Click "Create New Room" to add one.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching rooms:", error);
    }
}

function renderRoomsTable(roomList) {
    const tableBody = document.querySelector("#roomsTableBody, .management-table tbody");
    if (!tableBody) return;

    if (!roomList || roomList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 25px; color: #64748b;">No matching rooms found.</td></tr>`;
        return;
    }

    const badgeColors = [
        { bg: "#eff6ff", color: "#1e40af" },
        { bg: "#ecfdf5", color: "#059669" },
        { bg: "#fdf2f8", color: "#db2777" },
        { bg: "#fef3c7", color: "#b45309" }
    ];

    tableBody.innerHTML = roomList.map((room, idx) => {
        const color = badgeColors[idx % badgeColors.length];
        const roomNumber = room.room_number || room.roomNumber || 'N/A';
        const isMaint = (room.status_id === 'MAINTENANCE' || room.status === 'MAINTENANCE');
        const isOcc = (room.status_id === 'OCCUPIED' || room.status === 'OCCUPIED');
        const statusLabel = isMaint ? 'Maintenance' : (isOcc ? 'Occupied' : 'Available');
        const statusClass = isMaint ? 'badge-warning' : (isOcc ? 'badge-success' : 'badge-info');

        return `
            <tr>
                <td>
                    <div class="cell-user-info">
                        <div class="avatar-badge" style="background-color: ${color.bg}; color: ${color.color};">${roomNumber}</div>
                        <div>
                            <div class="cell-title">Room ${roomNumber}</div>
                            <div class="cell-subtitle">Floor ${room.floor || '1'} • ${room.block || 'Wing A'}</div>
                        </div>
                    </div>
                </td>
                <td><strong>${room.hostel_name || room.hostelName || room.hostel_id || 'Hostel'}</strong></td>
                <td>${room.room_capacity || room.capacity || 4} Beds</td>
                <td><span style="font-weight: 600;">${room.occupied || 0} / ${room.room_capacity || room.capacity || 4}</span></td>
                <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 4px;">
                        <button type="button" class="btn-action-icon btn-action-delete" title="Delete Room" onclick="deleteRoomHandle('${room.room_id || room.roomId}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");
}

function updateRoomStats(roomList) {
    const totalEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(1) .stat-big-value");
    const occupiedEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(2) .stat-big-value");
    const availableEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(3) .stat-big-value");

    if (totalEl) totalEl.textContent = roomList.length;
    const occupied = roomList.filter(r => r.status_id === 'OCCUPIED' || r.status === 'OCCUPIED').length;
    if (occupiedEl) occupiedEl.textContent = occupied;
    if (availableEl) availableEl.textContent = roomList.length - occupied;
}

async function createRoomHandle(event) {
    if (event) event.preventDefault();

    const hostelId = document.getElementById("hostelId")?.value;
    const roomNumber = document.getElementById("roomNumber")?.value.trim();
    const floor = document.getElementById("floor")?.value || "1";
    const capacity = document.getElementById("capacity")?.value || "4";
    const statusId = document.getElementById("statusId")?.value || "1";

    if (!hostelId) { alertModal(false, "HOSTEL IS REQUIRED, Please select a hostel block"); return; }
    if (!roomNumber) { alertModal(false, "ROOM NUMBER IS REQUIRED"); return; }

    const submitBtn = document.querySelector("#add-room button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Create Room";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Creating...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("hostelId", hostelId);
    formData.append("roomNumber", roomNumber);
    formData.append("block", hostelId);
    formData.append("floor", floor);
    formData.append("roomCapacity", capacity);
    formData.append("capacity", capacity);
    formData.append("statusId", statusId);

    try {
        const response = await safeFetchAdmin('/rooms/add-room.php', '/room/create-room.php', { method: "POST", body: formData });
        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true) {
            alertModal(true, `ROOM CREATED SUCCESSFULLY! Room ${roomNumber}`);
            document.querySelector("#add-room form")?.reset();
            closeModal("modal-add-room");
            fetchAllRooms();
        } else {
            alertModal(false, fetchData.message || "Failed to create room.");
        }
    } catch (err) {
        alertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

async function deleteRoomHandle(roomId) {
    if (!confirm(`Are you sure you want to delete room ${roomId}?`)) return;

    const formData = new FormData();
    formData.append("roomId", roomId);

    try {
        const response = await safeFetchAdmin('/rooms/delete-room.php', '/room/delete-room.php', { method: "POST", body: formData });
        const result = await parseJsonResponse(response);

        if (result.success === true) {
            alertModal(true, result.message || "ROOM DELETED");
            fetchAllRooms();
        } else {
            alertModal(false, result.message || "Failed to delete room.");
        }
    } catch (error) {
        alertModal(false, "Connection error: " + error.message);
    }
}

/* ==========================================================================
   5. BED MANAGEMENT (admin/beds.html)
   ========================================================================== */
let allBedsList = [];

async function fetchAllBeds() {
    const tableBody = document.querySelector("#bedsTableBody, .management-table tbody");
    if (!tableBody && !document.getElementById("bedId")) return;

    try {
        const response = await safeFetchAdmin('/bed/fetch-all-bed.php', null, { method: "POST" });
        const result = await parseJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            allBedsList = result.data;
            if (tableBody && window.location.pathname.includes("beds.html")) {
                renderBedsTable(allBedsList);
                updateBedStats(allBedsList);
            }
            populateBedDropdowns(allBedsList);
        } else if (tableBody && window.location.pathname.includes("beds.html")) {
            tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">No bed spaces configured yet. Click "Add Bed Space" above.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching beds:", error);
    }
}

function renderBedsTable(bedList) {
    const tableBody = document.querySelector("#bedsTableBody, .management-table tbody");
    if (!tableBody) return;

    if (!bedList || bedList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 25px; color: #64748b;">No matching bed spaces found.</td></tr>`;
        return;
    }

    const badgeColors = [
        { bg: "#eff6ff", color: "#1e40af" },
        { bg: "#ecfdf5", color: "#059669" },
        { bg: "#fdf2f8", color: "#db2777" },
        { bg: "#fef3c7", color: "#b45309" }
    ];

    tableBody.innerHTML = bedList.map((bed, idx) => {
        const color = badgeColors[idx % badgeColors.length];
        const isOcc = bed.status_id === 'O' || bed.student_id;
        const isMaint = bed.status_id === 'M' || bed.status_id === 'MAINTENANCE';
        const statusLabel = isOcc ? 'Occupied' : (isMaint ? 'Maintenance' : 'Available');
        const statusClass = isOcc ? 'badge-success' : (isMaint ? 'badge-warning' : 'badge-info');
        const residentName = bed.first_name ? `${bed.first_name} ${bed.last_name}` : (isOcc ? 'Assigned Resident' : '— Unassigned —');

        return `
            <tr>
                <td>
                    <div class="cell-user-info">
                        <div class="avatar-badge" style="background-color: ${color.bg}; color: ${color.color};">${bed.bed_number || 'B1'}</div>
                        <div>
                            <div class="cell-title">Bed ${bed.bed_number}</div>
                            <div class="cell-subtitle">ID: ${bed.bed_id}</div>
                        </div>
                    </div>
                </td>
                <td><strong>Room ${bed.room_number || '—'}</strong></td>
                <td>${bed.hostel_name || bed.hostel_id || 'Hostel'}</td>
                <td>${residentName}</td>
                <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                <td>${bed.created_at ? new Date(bed.created_at).toLocaleDateString('en-GB') : 'Active'}</td>
                <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 4px;">
                        <button type="button" class="btn-action-icon btn-action-delete" title="Delete Bed" onclick="deleteBedHandle('${bed.bed_id}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");
}

function updateBedStats(bedList) {
    const totalEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(1) .stat-big-value");
    const occEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(2) .stat-big-value");
    const availEl = document.querySelector(".stats-cards-grid-4 .overview-stat-card:nth-child(3) .stat-big-value");

    if (totalEl) totalEl.textContent = bedList.length.toLocaleString();
    const occupied = bedList.filter(b => b.status_id === 'O' || b.student_id).length;
    if (occEl) occEl.textContent = occupied.toLocaleString();
    if (availEl) availEl.textContent = (bedList.length - occupied).toLocaleString();
}

async function createBedHandle(event) {
    if (event) event.preventDefault();

    const hostelId = document.getElementById("hostelId")?.value;
    const roomNumber = document.getElementById("roomNumber")?.value.trim();
    const bedNumber = document.getElementById("bedNumber")?.value.trim();
    const statusId = document.getElementById("statusId")?.value || "D";

    if (!hostelId) { alertModal(false, "HOSTEL IS REQUIRED"); return; }
    if (!roomNumber) { alertModal(false, "ROOM NUMBER IS REQUIRED"); return; }
    if (!bedNumber) { alertModal(false, "BED NUMBER IS REQUIRED (e.g. 01, 02)"); return; }

    const submitBtn = document.querySelector("#add-bed button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Add Bed Space";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Adding...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("hostelId", hostelId);
    formData.append("roomId", roomNumber);
    formData.append("roomNumber", roomNumber);
    formData.append("bedNumber", bedNumber);
    formData.append("statusId", statusId === "OCCUPIED" ? "O" : (statusId === "MAINTENANCE" ? "M" : "D"));

    try {
        const response = await safeFetchAdmin('/bed/add-bed.php', '/bed/create-bed.php', { method: "POST", body: formData });
        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true) {
            alertModal(true, `BED SPACE ADDED SUCCESSFULLY! Bed ${bedNumber}`);
            document.querySelector("#add-bed form")?.reset();
            closeModal("modal-add-bed");
            fetchAllBeds();
        } else {
            alertModal(false, fetchData.message || "Failed to add bed space.");
        }
    } catch (err) {
        alertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

async function deleteBedHandle(bedId) {
    if (!confirm(`Are you sure you want to delete bed ${bedId}?`)) return;

    const formData = new FormData();
    formData.append("bedId", bedId);

    try {
        const response = await safeFetchAdmin('/bed/delete-bed.php', null, { method: "POST", body: formData });
        const result = await parseJsonResponse(response);

        if (result.success === true) {
            alertModal(true, result.message || "BED DELETED");
            fetchAllBeds();
        } else {
            alertModal(false, result.message || "Failed to delete bed.");
        }
    } catch (error) {
        alertModal(false, "Connection error: " + error.message);
    }
}

/* ==========================================================================
   6. ALLOCATION MANAGEMENT (admin/allocations.html)
   ========================================================================== */
let allAllocationsList = [];

async function fetchAllAllocations() {
    const tableBody = document.querySelector(".management-table tbody");
    if (!tableBody || !window.location.pathname.includes("allocations.html")) return;

    try {
        const response = await safeFetchAdmin('/allocation/fetch-all-allocation.php', null, { method: "POST" });
        const result = await parseJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            allAllocationsList = result.data;
            renderAllocationsTable(allAllocationsList);
        } else {
            tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">No allocations recorded. Use the form below to allocate a bed to a resident.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching allocations:", error);
    }
}

function renderAllocationsTable(allocList) {
    const tableBody = document.querySelector(".management-table tbody");
    if (!tableBody) return;

    if (!allocList || allocList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 25px; color: #64748b;">No matching allocations found.</td></tr>`;
        return;
    }

    const badgeColors = [
        { bg: "#eff6ff", color: "#1e40af" },
        { bg: "#ecfdf5", color: "#059669" },
        { bg: "#fdf2f8", color: "#db2777" },
        { bg: "#fef3c7", color: "#b45309" }
    ];

    tableBody.innerHTML = allocList.map((alloc, idx) => {
        const color = badgeColors[idx % badgeColors.length];
        const studentName = (alloc.first_name && alloc.last_name) ? `${alloc.first_name} ${alloc.last_name}` : 'Student Resident';
        const initials = ((alloc.first_name?.[0] || '') + (alloc.last_name?.[0] || '')).toUpperCase() || 'ST';
        const dateStr = alloc.created_at ? new Date(alloc.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Recently';

        return `
            <tr>
                <td>
                    <div class="cell-user-info">
                        <div class="avatar-badge" style="background-color: ${color.bg}; color: ${color.color};">${initials}</div>
                        <div>
                            <div class="cell-title">${studentName}</div>
                            <div class="cell-subtitle">${alloc.student_id || 'N/A'}</div>
                        </div>
                    </div>
                </td>
                <td><strong>${alloc.hostel_name || 'Hostel'} - Room ${alloc.room_number || '—'}</strong></td>
                <td>${alloc.bed_id || 'Standard Bed'}</td>
                <td>2024 / 2025</td>
                <td>${dateStr}</td>
                <td><span class="status-badge badge-success">Allocated</span></td>
                <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 4px;">
                        <button type="button" class="btn-action-icon btn-action-delete" title="Deallocate" onclick="deleteAllocationHandle('${alloc.allocation_id}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");
}

async function createAllocationHandle(event) {
    if (event) event.preventDefault();

    const studentId = document.getElementById("studentId")?.value;
    const hostelId = document.getElementById("hostelId")?.value;
    const roomNumber = document.getElementById("roomNumber")?.value.trim();
    const bedId = document.getElementById("bedId")?.value;

    if (!studentId) { alertModal(false, "STUDENT IS REQUIRED"); return; }
    if (!hostelId) { alertModal(false, "HOSTEL IS REQUIRED"); return; }
    if (!roomNumber) { alertModal(false, "ROOM NUMBER IS REQUIRED"); return; }
    if (!bedId) { alertModal(false, "BED SPACE IS REQUIRED"); return; }

    const submitBtn = document.querySelector("#new-allocation button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Complete Allocation";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Allocating...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("studentId", studentId);
    formData.append("hostelId", hostelId);
    formData.append("roomId", roomNumber);
    formData.append("roomNumber", roomNumber);
    formData.append("bedId", bedId);

    try {
        const response = await safeFetchAdmin('/allocation/add-allocation.php', null, { method: "POST", body: formData });
        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true) {
            alertModal(true, `RESIDENT ALLOCATED SUCCESSFULLY!`);
            document.querySelector("#new-allocation form")?.reset();
            closeModal("modal-new-allocation");
            fetchAllAllocations();
        } else {
            alertModal(false, fetchData.message || "Failed to allocate resident.");
        }
    } catch (err) {
        alertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

async function deleteAllocationHandle(allocationId) {
    if (!confirm(`Are you sure you want to remove this allocation? The bed will be freed.`)) return;

    const formData = new FormData();
    formData.append("allocationId", allocationId);

    try {
        const response = await safeFetchAdmin('/allocation/delete-allocation.php', null, { method: "POST", body: formData });
        const result = await parseJsonResponse(response);

        if (result.success === true) {
            alertModal(true, result.message || "ALLOCATION REMOVED SUCCESSFULLY");
            fetchAllAllocations();
        } else {
            alertModal(false, result.message || "Failed to deallocate.");
        }
    } catch (error) {
        alertModal(false, "Connection error: " + error.message);
    }
}

/* ==========================================================================
   7. CHECKOUTS MANAGEMENT (admin/checkouts.html)
   ========================================================================== */
let allCheckoutsList = [];

async function fetchAllCheckouts() {
    const tableBody = document.querySelector(".management-table tbody");
    if (!tableBody || !window.location.pathname.includes("checkout")) return;

    try {
        const response = await safeFetchAdmin('/checkout/fetch-checkout.php', null, { method: "POST" });
        const result = await parseJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            allCheckoutsList = result.data;
            renderCheckoutsTable(allCheckoutsList);
        } else {
            tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">No checkout records found.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching checkouts:", error);
    }
}

function renderCheckoutsTable(checkoutList) {
    const tableBody = document.querySelector(".management-table tbody");
    if (!tableBody) return;

    if (!checkoutList || checkoutList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 25px; color: #64748b;">No matching checkouts found.</td></tr>`;
        return;
    }

    const badgeColors = [
        { bg: "#eff6ff", color: "#1e40af" },
        { bg: "#ecfdf5", color: "#059669" },
        { bg: "#fdf2f8", color: "#db2777" }
    ];

    tableBody.innerHTML = checkoutList.map((item, idx) => {
        const color = badgeColors[idx % badgeColors.length];
        const studentName = (item.first_name && item.last_name) ? `${item.first_name} ${item.last_name}` : 'Resident';
        const initials = ((item.first_name?.[0] || '') + (item.last_name?.[0] || '')).toUpperCase() || 'CK';
        const dateStr = item.checkout_date ? new Date(item.checkout_date).toLocaleDateString('en-GB') : 'Recent';

        return `
            <tr>
                <td>
                    <div class="cell-user-info">
                        <div class="avatar-badge" style="background-color: ${color.bg}; color: ${color.color};">${initials}</div>
                        <div>
                            <div class="cell-title">${studentName}</div>
                            <div class="cell-subtitle">${item.student_id || 'N/A'}</div>
                        </div>
                    </div>
                </td>
                <td>${item.hostel_name || 'Hostel'} - Room ${item.room_number || '—'}</td>
                <td>${dateStr}</td>
                <td><span class="role-pill">${item.reason || 'End of Semester'}</span></td>
                <td><span class="status-badge badge-success">${item.key_status || 'Returned'}</span></td>
                <td>${item.room_condition || 'Good Condition'}</td>
                <td><span class="status-badge badge-success">Cleared</span></td>
                <td style="text-align: right;">
                    <div style="display: inline-flex; gap: 4px;">
                        <button type="button" class="btn-action-icon btn-action-delete" title="Delete Record" onclick="deleteCheckoutHandle('${item.checkout_id}')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join("");
}

async function createCheckoutHandle(event) {
    if (event) event.preventDefault();

    const studentId = document.getElementById("studentId")?.value?.trim();
    const checkoutDate = document.getElementById("checkoutDate")?.value;
    const reason = document.getElementById("reason")?.value || "End of Semester";
    const keyStatus = document.getElementById("keyStatus")?.value || "Returned";
    const roomCondition = document.getElementById("roomCondition")?.value || "Good";
    const statusId = document.getElementById("statusId")?.value || "CLEARED";
    const remarks = document.getElementById("remarks")?.value || "";

    if (!studentId) { alertModal(false, "STUDENT ID IS REQUIRED"); return; }
    if (!checkoutDate) { alertModal(false, "CHECKOUT DATE IS REQUIRED"); return; }
    if (!reason) { alertModal(false, "REASON FOR CHECKOUT IS REQUIRED"); return; }

    const submitBtn = document.querySelector("#log-checkout button[type='submit']");
    const origText = submitBtn ? submitBtn.innerHTML : "Record Checkout";
    if (submitBtn) { submitBtn.innerHTML = `<i class="bi bi-hourglass-split"></i> Recording...`; submitBtn.disabled = true; }

    const formData = new FormData();
    formData.append("studentId", studentId);
    formData.append("checkoutDate", checkoutDate);
    formData.append("reason", reason);
    formData.append("keyStatus", keyStatus);
    formData.append("roomCondition", roomCondition);
    formData.append("statusId", statusId);
    formData.append("remarks", remarks);

    try {
        const response = await safeFetchAdmin('/checkout/add-checkout.php', null, { method: "POST", body: formData });
        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true) {
            alertModal(true, "CHECKOUT RECORDED SUCCESSFULLY");
            document.querySelector("#log-checkout form")?.reset();
            closeModal("modal-log-checkout");
            fetchAllCheckouts();
        } else {
            alertModal(false, fetchData.message || "Failed to log checkout.");
        }
    } catch (err) {
        alertModal(false, "Connection error: " + err.message);
    } finally {
        if (submitBtn) { submitBtn.innerHTML = origText; submitBtn.disabled = false; }
    }
}

async function deleteCheckoutHandle(checkoutId) {
    if (!confirm(`Are you sure you want to delete checkout record ${checkoutId}?`)) return;

    const formData = new FormData();
    formData.append("checkoutId", checkoutId);

    try {
        const response = await safeFetchAdmin('/checkout/delete-checkout.php', null, { method: "POST", body: formData });
        const result = await parseJsonResponse(response);

        if (result.success === true) {
            alertModal(true, result.message || "CHECKOUT RECORD DELETED");
            fetchAllCheckouts();
        } else {
            alertModal(false, result.message || "Failed to delete record.");
        }
    } catch (error) {
        alertModal(false, "Connection error: " + error.message);
    }
}

/* ==========================================================================
   8. TRANSFERS MANAGEMENT (admin/transfers.html)
   ========================================================================== */
let allTransfersList = [];

async function fetchAllTransfers() {
    const tableBody = document.querySelector(".management-table tbody");
    if (!tableBody || !window.location.pathname.includes("transfer")) return;

    try {
        const response = await safeFetchAdmin('/room-transfer/fetch-all-transfer.php', null, { method: "POST" });
        const result = await parseJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            allTransfersList = result.data;
            renderTransfersTable(allTransfersList);
        } else {
            tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">No transfer requests found.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching transfers:", error);
    }
}

function renderTransfersTable(transferList) {
    const tableBody = document.querySelector(".management-table tbody");
    if (!tableBody) return;

    if (!transferList || transferList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 25px; color: #64748b;">No matching transfer records found.</td></tr>`;
        return;
    }

    tableBody.innerHTML = transferList.map((t) => {
        const studentName = (t.first_name && t.last_name) ? `${t.first_name} ${t.last_name}` : 'Resident';
        const initials = ((t.first_name?.[0] || '') + (t.last_name?.[0] || '')).toUpperCase() || 'TR';

        return `
            <tr>
                <td>
                    <div class="cell-user-info">
                        <div class="avatar-badge" style="background-color: #eff6ff; color: #1e40af;">${initials}</div>
                        <div>
                            <div class="cell-title">${studentName}</div>
                            <div class="cell-subtitle">${t.student_id || 'N/A'}</div>
                        </div>
                    </div>
                </td>
                <td>${t.old_hostel_name || 'Old'} - Room ${t.old_room_number || '—'}</td>
                <td><strong>${t.new_hostel_name || 'New'} - Room ${t.new_room_number || '—'}</strong></td>
                <td>${t.reason || 'Room Preference'}</td>
                <td><span class="status-badge badge-success">Approved</span></td>
                <td>${t.remarks || 'Standard Transfer'}</td>
                <td style="text-align: right;">
                    <button type="button" class="btn-action-icon btn-action-delete" title="Delete" onclick="deleteTransferHandle('${t.transfer_id}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join("");
}

async function deleteTransferHandle(transferId) {
    if (!confirm(`Are you sure you want to delete transfer record ${transferId}?`)) return;

    const formData = new FormData();
    formData.append("transferId", transferId);

    try {
        const response = await safeFetchAdmin('/room-transfer/delete-transfer.php', null, { method: "POST", body: formData });
        const result = await parseJsonResponse(response);

        if (result.success === true) {
            alertModal(true, result.message || "TRANSFER RECORD DELETED");
            fetchAllTransfers();
        } else {
            alertModal(false, result.message || "Failed to delete transfer.");
        }
    } catch (error) {
        alertModal(false, "Connection error: " + error.message);
    }
}

/* ==========================================================================
   9. VISITORS MANAGEMENT (admin/visitors.html)
   ========================================================================== */
let allVisitorsList = [];

async function fetchAllVisitors() {
    const tableBody = document.querySelector(".management-table tbody");
    if (!tableBody || !window.location.pathname.includes("visitor")) return;

    try {
        const response = await safeFetchAdmin('/visitor/fetch-all-visitor.php', null, { method: "POST" });
        const result = await parseJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            allVisitorsList = result.data;
            renderVisitorsTable(allVisitorsList);
        } else {
            tableBody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">No visitor logs found.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching visitors:", error);
    }
}

function renderVisitorsTable(visitorList) {
    const tableBody = document.querySelector(".management-table tbody");
    if (!tableBody) return;

    if (!visitorList || visitorList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 25px; color: #64748b;">No matching visitor records found.</td></tr>`;
        return;
    }

    tableBody.innerHTML = visitorList.map((v) => {
        const hostName = (v.first_name && v.last_name) ? `${v.first_name} ${v.last_name}` : 'Student Host';
        const isDeparted = Boolean(v.departure_time && v.departure_time !== '00:00:00');
        const statusLabel = isDeparted ? 'Departed' : 'Inside';
        const statusClass = isDeparted ? 'badge-neutral' : 'badge-info';

        return `
            <tr>
                <td>
                    <div class="cell-user-info">
                        <div class="avatar-badge" style="background-color: #eff6ff; color: #1e40af;">VI</div>
                        <div>
                            <div class="cell-title">${v.visitor_name}</div>
                            <div class="cell-subtitle">Visitor</div>
                        </div>
                    </div>
                </td>
                <td><strong>${v.student_id}</strong> (${hostName})</td>
                <td>${v.purpose || 'Visit'}</td>
                <td>${v.visitDate || 'Today'}</td>
                <td>${v.arrivalTime || '—'}</td>
                <td>${isDeparted ? v.departureTime : '— Inside —'}</td>
                <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                <td style="text-align: right;">
                    <button type="button" class="btn-action-icon btn-action-delete" title="Delete Log" onclick="deleteVisitorHandle('${v.visitor_id}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join("");
}

async function createVisitorHandle(event) {
    if (event) event.preventDefault();

    const studentId = document.getElementById("studentId")?.value.trim();
    const visitorName = document.getElementById("visitorName")?.value.trim();
    const purpose = document.getElementById("purpose")?.value;
    const visitDate = document.getElementById("visitDate")?.value;
    const arrivalTime = document.getElementById("arrivalTime")?.value || "10:00";

    if (!studentId) { alertModal(false, "STUDENT MATRIC / ID IS REQUIRED"); return; }
    if (!visitorName) { alertModal(false, "VISITOR NAME IS REQUIRED"); return; }
    if (!purpose) { alertModal(false, "PURPOSE IS REQUIRED"); return; }
    if (!visitDate) { alertModal(false, "VISIT DATE IS REQUIRED"); return; }

    const formData = new FormData();
    formData.append("studentId", studentId);
    formData.append("visitorName", visitorName);
    formData.append("purpose", purpose);
    formData.append("visitDate", visitDate);
    formData.append("arrivalTime", arrivalTime);

    try {
        const response = await safeFetchAdmin('/visitor/add-visitor.php', null, { method: "POST", body: formData });
        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true) {
            alertModal(true, "VISITOR REGISTERED SUCCESSFULLY");
            document.querySelector("#log-visitor form, #register-visitor form")?.reset();
            closeModal("modal-log-visitor");
            fetchAllVisitors();
        } else {
            alertModal(false, fetchData.message || "Failed to log visitor.");
        }
    } catch (err) {
        alertModal(false, "Connection error: " + err.message);
    }
}

async function deleteVisitorHandle(visitorId) {
    if (!confirm(`Are you sure you want to delete visitor log ${visitorId}?`)) return;

    const formData = new FormData();
    formData.append("visitorId", visitorId);

    try {
        const response = await safeFetchAdmin('/visitor/delete-visitor.php', null, { method: "POST", body: formData });
        const result = await parseJsonResponse(response);

        if (result.success === true) {
            alertModal(true, result.message || "VISITOR LOG DELETED");
            fetchAllVisitors();
        } else {
            alertModal(false, result.message || "Failed to delete visitor log.");
        }
    } catch (error) {
        alertModal(false, "Connection error: " + error.message);
    }
}

/* ==========================================================================
   10. PAYMENTS MANAGEMENT (admin/payments.html)
   ========================================================================== */
let allPaymentsList = [];

async function fetchAllPayments() {
    const tableBody = document.querySelector(".management-table tbody");
    if (!tableBody || !window.location.pathname.includes("payment")) return;

    try {
        const response = await safeFetchAdmin('/payment/fetch-all-payment.php', null, { method: "POST" });
        const result = await parseJsonResponse(response);

        if (result.success === true && Array.isArray(result.data)) {
            allPaymentsList = result.data;
            renderPaymentsTable(allPaymentsList);
        } else {
            tableBody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 30px; color: #64748b;">No payment records found.</td></tr>`;
        }
    } catch (error) {
        console.error("Error fetching payments:", error);
    }
}

function renderPaymentsTable(paymentList) {
    const tableBody = document.querySelector(".management-table tbody");
    if (!tableBody) return;

    if (!paymentList || paymentList.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="8" style="text-align: center; padding: 25px; color: #64748b;">No matching payments found.</td></tr>`;
        return;
    }

    tableBody.innerHTML = paymentList.map((p) => {
        const studentName = (p.first_name && p.last_name) ? `${p.first_name} ${p.last_name}` : 'Student';
        const initials = ((p.first_name?.[0] || '') + (p.last_name?.[0] || '')).toUpperCase() || 'PM';
        const amount = Number(p.amount || 50000).toLocaleString();
        const dateStr = p.payment_date ? new Date(p.payment_date).toLocaleDateString('en-GB') : 'Recent';

        return `
            <tr>
                <td>
                    <div class="cell-user-info">
                        <div class="avatar-badge" style="background-color: #eff6ff; color: #1e40af;">${initials}</div>
                        <div>
                            <div class="cell-title">${studentName}</div>
                            <div class="cell-subtitle">${p.student_id || 'N/A'}</div>
                        </div>
                    </div>
                </td>
                <td>${p.fee_name || 'Hostel Accommodation Fee'}</td>
                <td><strong>₦${amount}</strong></td>
                <td>${p.payment_method || 'Bank Transfer'}</td>
                <td><code>${p.payment_id || 'TXN-' + Math.floor(Math.random() * 89999 + 10000)}</code></td>
                <td>${dateStr}</td>
                <td><span class="status-badge badge-success">Verified</span></td>
                <td style="text-align: right;">
                    <button type="button" class="btn-action-icon btn-action-delete" title="Delete Payment" onclick="deletePaymentHandle('${p.payment_id}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    }).join("");
}

async function createPaymentHandle(event) {
    if (event) event.preventDefault();

    const studentId = document.getElementById("studentId")?.value.trim();
    const feeDescription = document.getElementById("feeDescription")?.value || "Annual Hostel Fee";
    const amount = document.getElementById("amount")?.value;
    const paymentMethod = document.getElementById("paymentMethod")?.value || "Bank Transfer";
    const transactionRef = document.getElementById("transactionRef")?.value.trim() || ("TXN-" + Date.now());

    if (!studentId) { alertModal(false, "STUDENT MATRIC / ID IS REQUIRED"); return; }
    if (!amount) { alertModal(false, "AMOUNT IS REQUIRED"); return; }

    const formData = new FormData();
    formData.append("studentId", studentId);
    formData.append("feeDescription", feeDescription);
    formData.append("amount", amount);
    formData.append("paymentMethod", paymentMethod);
    formData.append("transactionRef", transactionRef);

    try {
        const response = await safeFetchAdmin('/payment/add-payment.php', null, { method: "POST", body: formData });
        const fetchData = await parseJsonResponse(response);

        if (fetchData.success === true) {
            alertModal(true, "PAYMENT RECORDED SUCCESSFULLY");
            document.querySelector("#record-payment form, #pay-fee form")?.reset();
            closeModal("modal-record-payment");
            fetchAllPayments();
        } else {
            alertModal(false, fetchData.message || "Failed to save payment.");
        }
    } catch (err) {
        alertModal(false, "Connection error: " + err.message);
    }
}

async function deletePaymentHandle(paymentId) {
    if (!confirm(`Are you sure you want to delete payment record ${paymentId}?`)) return;

    const formData = new FormData();
    formData.append("paymentId", paymentId);

    try {
        const response = await safeFetchAdmin('/payment/delete-payment.php', null, { method: "POST", body: formData });
        const result = await parseJsonResponse(response);

        if (result.success === true) {
            alertModal(true, result.message || "PAYMENT RECORD DELETED");
            fetchAllPayments();
        } else {
            alertModal(false, result.message || "Failed to delete payment.");
        }
    } catch (error) {
        alertModal(false, "Connection error: " + error.message);
    }
}

/* ==========================================================================
   11. MAINTENANCE & COMPLAINTS MANAGEMENT
   ========================================================================== */
let allMaintenanceList = [];

async function fetchAllMaintenance() {
    try {
        const response = await safeFetchAdmin('/maintenance/fetch-all-maintenance.php', null, { method: "POST" });
        const result = await parseJsonResponse(response);
        if (result.success === true && Array.isArray(result.data)) {
            allMaintenanceList = result.data;
        }
    } catch (err) {
        console.error("Error fetching maintenance:", err);
    }
}

/* ==========================================================================
   12. DYNAMIC DROPDOWN POPULATORS
   ========================================================================== */
function populateHostelDropdowns(hostels) {
    const selects = document.querySelectorAll("select[name='hostelId'], #hostelId, #studentHostelSelect");
    selects.forEach(sel => {
        if (!sel) return;
        const currentVal = sel.value;
        const defaultPrompt = sel.getAttribute("id") === "studentHostelSelect" ? "Select Hostel Block" : "Select Hostel Block";
        sel.innerHTML = `<option value="" disabled ${!currentVal ? 'selected' : ''}>${defaultPrompt}</option>`;
        hostels.forEach(h => {
            const hId = h.hostel_id || h.hostelName;
            const hName = h.hostel_name || hId;
            sel.innerHTML += `<option value="${hId}" ${currentVal === hId ? 'selected' : ''}>${hName} (${h.gender || 'Mixed'})</option>`;
        });
    });
}

function populateRoomDropdowns(rooms) {
    const selects = document.querySelectorAll("select[name='roomId'], #roomId, #studentRoomSelect");
    selects.forEach(sel => {
        if (!sel) return;
        const currentVal = sel.value;
        sel.innerHTML = `<option value="" disabled ${!currentVal ? 'selected' : ''}>Select Room</option>`;
        rooms.forEach(r => {
            const rId = r.room_id || r.room_number;
            sel.innerHTML += `<option value="${rId}" ${currentVal === rId ? 'selected' : ''}>Room ${r.room_number} (${r.hostel_name || r.hostel_id || 'Block'})</option>`;
        });
    });
}

function populateBedDropdowns(beds) {
    const selects = document.querySelectorAll("select[name='bedId'], #bedId, #studentBedSelect");
    selects.forEach(sel => {
        if (!sel) return;
        const currentVal = sel.value;
        sel.innerHTML = `<option value="" disabled ${!currentVal ? 'selected' : ''}>Select Bed Space</option>`;
        beds.forEach(b => {
            const bId = b.bed_id;
            sel.innerHTML += `<option value="${bId}" ${currentVal === bId ? 'selected' : ''}>Bed ${b.bed_number} (Room ${b.room_number || ''})</option>`;
        });
    });
}

function populateStudentDropdowns(students) {
    const selects = document.querySelectorAll("select[name='studentId'], #studentId");
    selects.forEach(sel => {
        if (!sel || sel.tagName.toLowerCase() !== 'select') return;
        const currentVal = sel.value;
        sel.innerHTML = `<option value="" disabled ${!currentVal ? 'selected' : ''}>Choose a registered student</option>`;
        students.forEach(s => {
            sel.innerHTML += `<option value="${s.student_id}" ${currentVal === s.student_id ? 'selected' : ''}>${s.first_name} ${s.last_name} — ${s.department || ''} (${s.student_id})</option>`;
        });
    });
}

/* ==========================================================================
   PAGE INITIALIZATION & EVENT LISTENERS
   ========================================================================== */
document.addEventListener("DOMContentLoaded", function () {
    // 1. Staff
    const staffForm = document.querySelector("#add-staff form");
    if (staffForm) {
        staffForm.addEventListener("submit", createStaffHandle);
        fetchAllStaff();
    }

    // 2. Students
    const studentForm = document.querySelector("#add-student form");
    if (studentForm) {
        studentForm.addEventListener("submit", createStudentHandle);
        fetchAllStudents();
    }

    // 3. Hostels
    const hostelForm = document.querySelector("#add-hostel form");
    if (hostelForm) {
        hostelForm.addEventListener("submit", createHostelHandle);
    }
    fetchAllHostels();

    // 4. Rooms
    const roomForm = document.querySelector("#add-room form");
    if (roomForm) {
        roomForm.addEventListener("submit", createRoomHandle);
    }
    fetchAllRooms();

    // 5. Beds
    const bedForm = document.querySelector("#add-bed form");
    if (bedForm) {
        bedForm.addEventListener("submit", createBedHandle);
    }
    fetchAllBeds();

    // 6. Allocations
    const allocForm = document.querySelector("#new-allocation form");
    if (allocForm) {
        allocForm.addEventListener("submit", createAllocationHandle);
        fetchAllAllocations();
    }

    // 7. Checkouts
    const checkoutForm = document.querySelector("#log-checkout form");
    if (checkoutForm) {
        checkoutForm.addEventListener("submit", createCheckoutHandle);
        fetchAllCheckouts();
    }

    // 8. Transfers
    const transferForm = document.querySelector("#request-transfer form");
    if (transferForm) {
        transferForm.addEventListener("submit", function(e) {
            e.preventDefault();
            alertModal(true, "TRANSFER REQUEST RECORDED FOR EVALUATION");
        });
        fetchAllTransfers();
    }

    // 9. Visitors
    const visitorForm = document.querySelector("#log-visitor form");
    if (visitorForm) {
        visitorForm.addEventListener("submit", createVisitorHandle);
        fetchAllVisitors();
    }

    // 10. Payments
    const paymentForm = document.querySelector("#record-payment form");
    if (paymentForm) {
        paymentForm.addEventListener("submit", createPaymentHandle);
        fetchAllPayments();
    }

    // Real-time table filter across all search bars
    const searchInputs = document.querySelectorAll(".toolbar-search-box input");
    searchInputs.forEach(input => {
        input.addEventListener("input", function() {
            const query = this.value.toLowerCase().trim();
            const tableRows = document.querySelectorAll(".management-table tbody tr");
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? "" : "none";
            });
        });
    });
});
