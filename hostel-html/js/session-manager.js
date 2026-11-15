/**
 * School Hostel Management System - Session & User Profile Manager
 * Dynamically synchronizes logged-in user names, avatars, roles and IDs across
 * Student, Admin, and Staff portals so real user details are always displayed.
 */

(function () {
    "use strict";

    function parseSession(raw) {
        if (!raw) return null;
        try {
            const data = JSON.parse(raw);
            return (data && typeof data === "object" && data.data && typeof data.data === "object") ? data.data : data;
        } catch (e) {
            return null;
        }
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
        const path = window.location.pathname.toLowerCase();
        let portal = "student";
        let session = null;

        if (path.includes("/admin/") || path.includes("/admin-auth/")) {
            portal = "admin";
            session = parseSession(sessionStorage.getItem("adminUserSession")) ||
                      parseSession(localStorage.getItem("adminUserSession")) ||
                      parseSession(sessionStorage.getItem("staffUserSession")) ||
                      parseSession(localStorage.getItem("staffUserSession"));
        } else if (path.includes("/staff/") || path.includes("/staff-auth/")) {
            portal = "staff";
            session = parseSession(sessionStorage.getItem("staffUserSession")) ||
                      parseSession(localStorage.getItem("staffUserSession")) ||
                      parseSession(sessionStorage.getItem("adminUserSession")) ||
                      parseSession(localStorage.getItem("adminUserSession"));
        } else {
            portal = "student";
            session = parseSession(sessionStorage.getItem("studentUserSession")) ||
                      parseSession(localStorage.getItem("studentUserSession")) ||
                      parseSession(sessionStorage.getItem("userSignUpSession")) ||
                      parseSession(localStorage.getItem("userSignUpSession"));
        }

        return { portal, user: session };
    }

    function applyUserProfile() {
        const { portal, user } = getSessionData();

        let firstName = "";
        let lastName = "";
        let fullName = "";
        let email = "";
        let userId = "";
        let role = "";
        let department = "";
        let level = "";
        let phoneNumber = "";
        let gender = "";
        let initials = "";

        if (user) {
            firstName = user.firstName || user.first_name || user.firstname || "";
            lastName = user.lastName || user.last_name || user.lastname || "";
            fullName = (firstName && lastName)
                ? `${firstName} ${lastName}`
                : (user.fullName || user.fullname || user.name || firstName || lastName || "");

            email = user.emailAddress || user.email_address || user.email || "";
            userId = user.studentId || user.student_id || user.matricNumber || user.matric_number || user.matricNo ||
                     user.staffId || user.staff_id || user.adminId || "";
            role = user.roleName || user.role_name || user.role || user.roleTitle || "";
            department = user.department || user.dept || "";
            level = user.level || user.academicLevel || "";
            phoneNumber = user.phoneNumber || user.phone_number || user.phone || "";
            gender = user.gender || "";

            if (!fullName && email) {
                const username = email.split("@")[0];
                fullName = username.charAt(0).toUpperCase() + username.slice(1);
            }

            initials = getInitials(firstName, lastName, fullName || email || (portal === "admin" ? "Admin User" : portal === "staff" ? "Staff" : "Student"));
        } else {
            // Default fallbacks when no session is stored yet
            if (portal === "admin") {
                firstName = "Admin";
                lastName = "User";
                fullName = "Admin User";
                role = "Super Admin";
                initials = "AU";
            } else if (portal === "staff") {
                firstName = "Staff";
                lastName = "Member";
                fullName = "Staff Member";
                role = "Hall Warden";
                initials = "SM";
            } else {
                firstName = "Student";
                lastName = "Resident";
                fullName = "Student Resident";
                userId = "STUDENT PORTAL";
                initials = "SR";
            }
        }

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
            // Dashboard header welcome text
            const topWelcomeP = document.querySelector(".dashboard-topbar .page-title-group p");
            if (topWelcomeP && topWelcomeP.textContent.includes("Welcome")) {
                topWelcomeP.textContent = `Welcome back, ${fullName}${userId ? ' (Matric: ' + userId + ')' : ''}`;
            }

            // Roommate cards: update the card for current user "(You)"
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

            // Student profile page (profile.html)
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

            // Form inputs in profile.html
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
                    const path = window.location.pathname.toLowerCase();
                    if (path.includes("/admin")) {
                        sessionStorage.removeItem("adminUserSession");
                        localStorage.removeItem("adminUserSession");
                    } else if (path.includes("/staff")) {
                        sessionStorage.removeItem("staffUserSession");
                        localStorage.removeItem("staffUserSession");
                    } else {
                        sessionStorage.removeItem("studentUserSession");
                        localStorage.removeItem("studentUserSession");
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
            localStorage.setItem("studentUserSession", JSON.stringify(data));
            applyUserProfile();
        },
        setAdminSession: function (data) {
            sessionStorage.setItem("adminUserSession", JSON.stringify(data));
            localStorage.setItem("adminUserSession", JSON.stringify(data));
            applyUserProfile();
        },
        setStaffSession: function (data) {
            sessionStorage.setItem("staffUserSession", JSON.stringify(data));
            localStorage.setItem("staffUserSession", JSON.stringify(data));
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
        applyUserProfile();
    });
})();
