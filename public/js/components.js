document.addEventListener("DOMContentLoaded", function () {
    // Tempat Semua Script Component Start

    // Toast Start
    const toastElList = document.querySelectorAll(".toast");

    toastElList.forEach(function (toastEl) {
        const toast = new bootstrap.Toast(toastEl, {
            delay: 1500,
            autohide: true,
        });

        toast.show();
    });
    // Toast End

    // Preview Images Start
    window.openImagePreview = function (src) {
        const overlay = document.getElementById("imagePreviewOverlay");
        const img = document.getElementById("previewImage");

        if (!overlay || !img) return;

        img.src = src;
        overlay.style.display = "flex";

        setTimeout(() => {
            overlay.classList.add("show");
        }, 10);

        document.body.style.overflow = "hidden";
    };

    window.closeImagePreview = function () {
        const overlay = document.getElementById("imagePreviewOverlay");
        if (!overlay) return;

        overlay.classList.remove("show");
        overlay.classList.add("closing");

        setTimeout(() => {
            overlay.classList.remove("closing");
            overlay.style.display = "none";
            document.body.style.overflow = "auto";
        }, 250);
    };

    const overlay = document.getElementById("imagePreviewOverlay");

    if (overlay) {
        overlay.addEventListener("click", function (e) {
            if (e.target === this) {
                window.closeImagePreview();
            }
        });
    }
    // Preview Images End

    // Tempat Semua Script Component End
});
