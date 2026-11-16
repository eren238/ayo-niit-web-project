function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add("active");
        document.body.style.overflow = "hidden";
        const firstInput = modal.querySelector("input:not([type='hidden']), select, textarea");
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 80);
        }
    }
}

function closeModal(modalId) {
    if (modalId && typeof modalId === 'string' && modalId !== 'alert' && modalId !== 'studentAlert') {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove("active");
        }
    } else {
        document.querySelectorAll(".modal-overlay.active").forEach(m => m.classList.remove("active"));
    }
    const alertEl = document.getElementById("alert");
    if (alertEl) alertEl.classList.remove("alertModal");
    const studentAlertEl = document.getElementById("studentAlert");
    if (studentAlertEl) studentAlertEl.classList.remove("alertModal");
    document.body.style.overflow = "";
}

document.addEventListener("DOMContentLoaded", function () {
    const formSectionIds = [
        "add-hostel", "add-student", "add-staff", "add-room", "add-bed",
        "new-allocation", "log-checkout", "record-payment", "log-visitor", "new-transfer",
        "add-fee", "add-complaint", "register-bed", "register-visitor"
    ];

    formSectionIds.forEach(id => {
        const section = document.getElementById(id);
        if (!section) return;

        if (section.parentElement && section.parentElement.classList.contains("modal-dialog-custom")) {
            return;
        }

        const overlay = document.createElement("div");
        overlay.className = "modal-overlay";
        overlay.id = "modal-" + id;

        const dialog = document.createElement("div");
        dialog.className = "modal-dialog-custom";

        const headerEl = section.querySelector("div:first-child");
        if (headerEl) {
            headerEl.classList.add("modal-header-custom");
            if (!headerEl.querySelector(".modal-close-btn")) {
                const closeBtn = document.createElement("button");
                closeBtn.type = "button";
                closeBtn.className = "modal-close-btn";
                closeBtn.title = "Close";
                closeBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
                closeBtn.addEventListener("click", function () {
                    closeModal("modal-" + id);
                });
                headerEl.appendChild(closeBtn);
            }
        }

        section.parentNode.insertBefore(overlay, section);
        dialog.appendChild(section);
        overlay.appendChild(dialog);

        overlay.addEventListener("click", function (e) {
            if (e.target === overlay) {
                closeModal("modal-" + id);
            }
        });

        const resetBtn = section.querySelector("button[type='reset']");
        if (resetBtn) {
            resetBtn.addEventListener("click", function () {
                setTimeout(() => closeModal("modal-" + id), 150);
            });
        }
    });

    document.addEventListener("click", function (e) {
        const link = e.target.closest('a[href^="#"]');
        if (!link) return;

        const href = link.getAttribute("href");
        if (!href || href === "#") return;

        const targetId = href.substring(1);
        const modalOverlay = document.getElementById("modal-" + targetId);
        if (modalOverlay) {
            e.preventDefault();
            openModal("modal-" + targetId);
        }
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeModal();
        }
    });

    if (window.location.hash) {
        const targetId = window.location.hash.substring(1);
        const modalOverlay = document.getElementById("modal-" + targetId);
        if (modalOverlay) {
            setTimeout(() => openModal("modal-" + targetId), 100);
        }
    }

    if (window.HostelSession && typeof window.HostelSession.apply === "function") {
        window.HostelSession.apply();
    }
});