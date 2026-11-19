/**
 * School Hostel Management System - Session & User Profile Manager
 * Dynamically synchronizes logged-in user names, avatars, roles and IDs across
 * Student, Admin, and Staff portals so real user details are always displayed.
 *
 * Strict Session Policy:
 * - Sessions live ONLY in sessionStorage (auto-clears on tab/browser close).
 * - Visiting any portal page without an active session redirects to the login screen.
 */

(function () {
    "use strict";

    // 1. Wipe any old persistent localStorage sessions to prevent back-door link access
    try {
        localStorage.removeItem("adminUserSession");
        localStorage.removeItem("staffUserSession");
        localStorage.removeItem("studentUserSession");
        localStorage.removeItem("userSignUpSession");
    } catch (e) {}

    function parseSession(raw) {
        if (!raw) return null;
        try {
            const data = JSON.parse(raw);
            return (data && typeof data === "object" && data.data && typeof data.data === "object") ? data.data : data;
        } catch (e) {
            return null;
        }
    }

    // 2. Strict Route Guard: Ensure user cannot enter any part of the portal via link if not logged in
    function enforceRouteGuard() {
        const rawPath = window.location.pathname.toLowerCase().replace(/\\/g, "/");
        const filename = rawPath.substring(rawPath.lastIndexOf("/") + 1);

        // Public/Auth pages that do not require an active session
        const isAuthFile = 
            rawPath.includes("/admin-auth/") ||
            rawPath.includes("/staff-auth/") ||
            filename === "admin-sign-in.html" ||
            filename === "admin-forgot-password.html" ||
            filename === "admin-reset-password.html" ||
            filename === "staff-sign-in.html" ||
            filename === "staff-forgot-password.html" ||
            filename === "staff-reset-password.html" ||
            filename === "student-login.html" ||
            filename === "student-sign-up.html" ||
            filename === "student-forgot-password.html" ||
            filename === "student-reset-password.html" ||
            filename === "index.html" ||
            filename === "";

        if (isAuthFile) {
            return true;
        }

        // Admin Portal: requires adminUserSession in sessionStorage
        if (rawPath.includes("/admin/")) {
            const adminSession = parseSession(sessionStorage.getItem("adminUserSession"));
            if (!adminSession) {
                window.location.replace("admin-auth/admin-sign-in.html");
                return false;
            }
        }
        // Staff Portal: requires staffUserSession in sessionStorage
        else if (rawPath.includes("/staff/")) {
            const staffSession = parseSession(sessionStorage.getItem("staffUserSession"));
            if (!staffSession) {
                window.location.replace("staff-auth/staff-sign-in.html");
                return false;
            }
        }
        // Student Portal: requires studentUserSession in sessionStorage
        else if (rawPath.includes("/student/")) {
            const studentSession = parseSession(sessionStorage.getItem("studentUserSession"));
            if (!studentSession) {
                window.location.replace("student-login.html");
                return false;
            }
        }

        return true;
    }

    // Run guard immediately on script execution
    if (!enforceRouteGuard()) {
        return;
    }

    function getInitials(first, last, fullFallback) {
        if (first && last) {
            return (first.trim()[0] + last.trim()[0]).toUpperCase();
        }
        if (fullFallback && typeof fullFallback === "string") {
            const parts = fullFallback.trim().split(/\s+/).filter(Boolean);
            if (parts.length >= 2) {
                return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
            }
            if (parts.length === 1 && parts[0].length > 0) {
                return parts[0].substring(0, 2).toUpperCase();
            }
        }
        return "U";
    }

    function getSessionData() {
        const path = window.location.pathname.toLowerCase().replace(/\\/g, "/");
        let portal = "student";
        let session = null;

        if (path.includes("/admin/")) {
            portal = "admin";
            session = parseSession(sessionStorage.getItem("adminUserSession"));
        } else if (path.includes("/staff/")) {
            portal = "staff";
            session = parseSession(sessionStorage.getItem("staffUserSession"));
        } else {
            portal = "student";
            session = parseSession(sessionStorage.getItem("studentUserSession"));
        }

        return { portal, user: session };
    }

    function applyUserProfile() {
        const { portal, user } = getSessionData();
        if (!user) return;

        let firstName = user.firstName || user.first_name || user.firstname || "";
        let lastName = user.lastName || user.last_name || user.lastname || "";
        let fullName = (firstName && lastName)
            ? `${firstName} ${lastName}`
            : (user.fullName || user.fullname || user.name || firstName || lastName || "");

        let email = user.emailAddress || user.email_address || user.email || "";
        let userId = user.studentId || user.student_id || user.matricNumber || user.matric_number || user.matricNo ||
                     user.staffId || user.staff_id || user.adminId || "";
        let role = user.roleName || user.role_name || user.role || user.roleTitle || "";
        let department = user.department || user.dept || "";
        let level = user.level || user.academicLevel || "";
        let phoneNumber = user.phoneNumber || user.phone_number || user.phone || "";
        let gender = user.gender || "";

        if (!fullName && email) {
            const username = email.split("@")[0];
            fullName = username.charAt(0).toUpperCase() + username.slice(1);
        }

        const initials = getInitials(firstName, lastName, fullName || email || (portal === "admin" ? "Admin User" : portal === "staff" ? "Staff" : "Student"));

        // 1. Update all top-bar profile badges across any page
        document.querySelectorAll(".user-profile-badge").forEach(badge => {
            const avatarEl = badge.querySelector(".user-avatar-circle");
            const nameEl = badge.querySelector(".user-display-name");
            const roleEl = badge.querySelector(".user-display-role");

            if (avatarEl && initials) {
                avatarEl.textContent = initials;
            }
            if (nameEl && fullName) {
                nameEl.textContent = fullName;
            }
            if (roleEl) {
                if (portal === "student") {
                    if (department && level) {
                        roleEl.textContent = `${department} (${level})`;
                    } else if (userId) {
                        roleEl.textContent = userId;
                    } else if (role) {
                        roleEl.textContent = role;
                    }
                } else if (portal === "admin") {
                    roleEl.textContent = role || "Super Admin";
                } else if (portal === "staff") {
                    roleEl.textContent = role || "Hall Warden";
                }
            }
        });

        // 2. Student Portal Page-Specific Enhancements
        if (portal === "student" && user) {
            const topWelcomeP = document.querySelector(".dashboard-topbar .page-title-group p");
            if (topWelcomeP && topWelcomeP.textContent.includes("Welcome")) {
                topWelcomeP.textContent = `Welcome back, ${fullName}${userId ? ' (Matric: ' + userId + ')' : ''}`;
            }

            document.querySelectorAll(".roommate-card.current-user").forEach(card => {
                const nameEl = card.querySelector(".roommate-name");
                if (nameEl) nameEl.textContent = `${fullName} (You)`;

                const avatarEl = card.querySelector(".avatar-badge");
                if (avatarEl) avatarEl.textContent = initials;

                const bedEl = card.querySelector(".roommate-bed");
                if (bedEl && userId) {
                    const bedText = bedEl.textContent;
                    if (bedText.includes("•")) {
                        const parts = bedText.split("•");
                        if (parts.length >= 2) {
                            bedEl.textContent = `${parts[0].trim()} • ${parts[1].trim()} • ${userId}`;
                        }
                    }
                }

                const phoneEl = card.querySelector(".roommate-phone");
                if (phoneEl && (department || phoneNumber)) {
                    let desc = department ? `${department}${level ? ' (' + level + ')' : ''}` : "";
                    if (phoneNumber) {
                        desc = desc ? `${desc} • ${phoneNumber}` : phoneNumber;
                    }
                    if (desc) phoneEl.textContent = desc;
                }
            });

            const profileAvatar = document.querySelector(".section-card .user-avatar-circle");
            if (profileAvatar) profileAvatar.textContent = initials;

            const profileHeaderH3 = document.querySelector(".section-card h3");
            if (profileHeaderH3 && (profileHeaderH3.textContent.includes("Posikay") || profileHeaderH3.textContent.includes("Ayo") || profileHeaderH3.textContent.includes("Student"))) {
                profileHeaderH3.textContent = fullName;
            }

            const profileHeaderP = document.querySelector(".section-card div > p");
            if (profileHeaderP && profileHeaderP.innerHTML.includes("Matric ID:")) {
                const deptInfo = department ? ` • ${department}${level ? ' (' + level + ')' : ''}` : "";
                profileHeaderP.innerHTML = `Matric ID: <strong>${userId || 'Pending'}</strong>${deptInfo}`;
            }

            const setVal = (name, val) => {
                if (!val) return;
                const el = document.querySelector(`input[name="${name}"]`);
                if (el) el.value = val;
            };

            setVal("firstName", firstName);
            setVal("lastName", lastName);
            setVal("gender", gender);
            setVal("department", department);
            setVal("level", level);
            setVal("phoneNumber", phoneNumber);
            setVal("emailAddress", email);
        }

        // 3. Admin & Staff specific elements
        if (portal === "admin" && user) {
            const adminSubtitle = document.querySelector(".sidebar-brand .brand-subtitle");
            if (adminSubtitle && role) adminSubtitle.textContent = role + " Console";
        }
    }

    // Attach sign-out cleaner to all Sign Out links
    function attachSignOutHandler() {
        document.querySelectorAll("a").forEach(link => {
            const text = (link.textContent || "").toLowerCase().trim();
            const href = (link.getAttribute("href") || "").toLowerCase();
            if (text.includes("sign out") || text.includes("logout") || href.includes("sign-in") || href.includes("login")) {
                link.addEventListener("click", function () {
                    const path = window.location.pathname.toLowerCase().replace(/\\/g, "/");
                    if (path.includes("/admin")) {
                        sessionStorage.removeItem("adminUserSession");
                    } else if (path.includes("/staff")) {
                        sessionStorage.removeItem("staffUserSession");
                    } else {
                        sessionStorage.removeItem("studentUserSession");
                        sessionStorage.removeItem("userSignUpSession");
                    }
                });
            }
        });
    }

    // Expose helpers globally
    window.HostelSession = {
        getUser: getSessionData,
        apply: applyUserProfile,
        setStudentSession: function (data) {
            sessionStorage.setItem("studentUserSession", JSON.stringify(data));
            applyUserProfile();
        },
        setAdminSession: function (data) {
            sessionStorage.setItem("adminUserSession", JSON.stringify(data));
            applyUserProfile();
        },
        setStaffSession: function (data) {
            sessionStorage.setItem("staffUserSession", JSON.stringify(data));
            applyUserProfile();
        }
    };

    // Execute immediately if DOM is ready, or on DOMContentLoaded
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function () {
            applyUserProfile();
            attachSignOutHandler();
        });
    } else {
        applyUserProfile();
        attachSignOutHandler();
    }

    // Also run on pageshow (handles browser back/forward cache)
    window.addEventListener("pageshow", function () {
        if (!enforceRouteGuard()) return;
        applyUserProfile();
    });
})();
