document.addEventListener("DOMContentLoaded", function () {
    // CSRF (Penting)
    const meta = document.querySelector('meta[name="csrf-token"]');
    const token = meta ? meta.getAttribute("content") : null;

    // toogle
    document.querySelectorAll(".toggle-rekening").forEach((toggle) => {
        toggle.addEventListener("change", function () {
            let id = this.dataset.id;
            let checkbox = this;

            fetch(`/rekening/${id}/toggle`, {
                method: "PATCH",
                headers: {
                    "X-CSRF-TOKEN": token,
                    Accept: "application/json",
                },
            })
                .then((res) => {
                    if (!res.ok) throw new Error("Gagal update");
                    return res.json();
                })
                .then((data) => {
                    let statusText = checkbox
                        .closest(".d-flex")
                        .querySelector(".status-text");

                    if (data.is_active) {
                        statusText.textContent = "Aktif";
                        statusText.classList.remove("text-muted");
                        statusText.classList.add("text-success");
                    } else {
                        statusText.textContent = "Nonaktif";
                        statusText.classList.remove("text-success");
                        statusText.classList.add("text-muted");
                    }
                })
                .catch(() => {
                    // kalau gagal, balikin toggle
                    checkbox.checked = !checkbox.checked;
                    alert("Terjadi kesalahan!");
                });
        });
    });

    // toast
    const toastElList = document.querySelectorAll(".show-auto");

    toastElList.forEach(function (toastEl) {
        const toast = new bootstrap.Toast(toastEl, {
            delay: 2000,
        });
        toast.show();
    });

    // departemen (khusus)
    const parentSelect = document.getElementById("departemenSelect");
    const subSelect = document.getElementById("subDepartemenSelect");
    const subWrapper = document.getElementById("subWrapper");

    if (parentSelect && subSelect && subWrapper) {
        parentSelect.addEventListener("change", function () {
            const parentId = this.value;

            subSelect.innerHTML =
                '<option value="">-- Pilih Sub Departemen --</option>';

            if (!parentId) {
                subWrapper.style.display = "none";
                subSelect.removeAttribute("required");
                return;
            }

            const parent = window.departemensData.find((d) => d.id == parentId);

            if (parent && parent.children && parent.children.length > 0) {
                subWrapper.style.display = "block";

                parent.children.forEach((sub) => {
                    subSelect.innerHTML += `
                    <option value="${sub.id}">
                        ${sub.name_dep}
                    </option>
                `;
                });
            } else {
                subWrapper.style.display = "none";

                subSelect.innerHTML = `<option value="${parentId}" selected></option>`;
            }
        });

        parentSelect.dispatchEvent(new Event("change"));
    }

    // edit departemen (khusus)
    document.querySelectorAll(".edit-departemen").forEach((select) => {
        const subTargetId = select.dataset.target;
        const selectedDepartemenId = select.dataset.selected; // ambil dari data attribute
        const subSelect = document.getElementById(subTargetId);
        const wrapper = document.getElementById(
            "subWrapperEdit" + subTargetId.replace("subEdit", ""),
        );

        select.addEventListener("change", function () {
            const parentId = this.value;

            subSelect.innerHTML = "";

            const parent = window.departemensData.find((d) => d.id == parentId);

            if (parent && parent.children && parent.children.length > 0) {
                wrapper.style.display = "block";

                parent.children.forEach((sub) => {
                    const selected =
                        sub.id == selectedDepartemenId ? "selected" : "";

                    subSelect.innerHTML += `
                    <option value="${sub.id}" ${selected}>
                        ${sub.name_dep}
                    </option>
                `;
                });
            } else {
                wrapper.style.display = "none";

                subSelect.innerHTML = `
                <option value="${parentId}" selected></option>
            `;
            }
        });

        // trigger saat modal load
        select.dispatchEvent(new Event("change"));
    });

    // reset modal jika dicancel
    document.querySelectorAll(".modal").forEach((modal) => {
        modal.addEventListener("hidden.bs.modal", function () {
            const forms = modal.querySelectorAll("form");
            forms.forEach((form) => form.reset());

            // Kalau ada dropdown departemen edit, trigger ulang
            const parentSelect = modal.querySelector(".edit-departemen");
            if (parentSelect) {
                parentSelect.dispatchEvent(new Event("change"));
            }
        });
    });

    // auto logout
    const autoLogoutTime = 7200000;

    setTimeout(function () {
        alert("Sesi anda telah habis (2 jam). Silakan login kembali.");
        document.getElementById("logout-form").submit();
    }, autoLogoutTime);

    // detail transaksi
    document.querySelectorAll(".detail-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            const modalBody = document.querySelector(
                "#modalDetailTransaksi .modal-body",
            );

            modalBody.innerHTML = "Loading...";

            fetch(`/transaksi/${id}`)
                .then((response) => {
                    if (response.status === 403) {
                        throw new Error("403");
                    }

                    if (!response.ok) {
                        throw new Error("HTTP " + response.status);
                    }

                    return response.text();
                })
                .then((html) => {
                    modalBody.innerHTML = html;
                })
                .catch((error) => {
                    if (error.message === "403") {
                        modalBody.innerHTML =
                            '<div class="text-danger">Akses ditolak</div>';
                    } else {
                        modalBody.innerHTML =
                            '<div class="text-danger">Gagal memuat detail</div>';
                    }
                });
        });
    });

    // rekening
    const asal = document.getElementById("rekeningAsal");
    const tujuan = document.getElementById("rekeningTujuan");

    asal.addEventListener("change", function () {
        Array.from(tujuan.options).forEach((option) => {
            option.disabled = false;
        });

        if (this.value !== "") {
            tujuan.querySelector(`option[value="${this.value}"]`).disabled =
                true;
        }
    });
});
