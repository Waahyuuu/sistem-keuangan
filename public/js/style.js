document.addEventListener("DOMContentLoaded", function () {
    /* ===========================
       CSRF
    ============================ */
    const meta = document.querySelector('meta[name="csrf-token"]');
    const token = meta ? meta.getAttribute("content") : null;

    /* ===========================
       TOGGLE REKENING
    ============================ */
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
                    checkbox.checked = !checkbox.checked;
                    alert("Terjadi kesalahan!");
                });
        });
    });

    /* ===========================
   TAMBAH PROGRAM
============================ */
    const parentSelect = document.getElementById("departemenSelect");
    const subSelect = document.getElementById("subDepartemenSelect");
    const subWrapper = document.getElementById("subWrapper");
    const hiddenInput = document.getElementById("hiddenDepartemenId");

    if (parentSelect && subSelect && hiddenInput) {
        parentSelect.addEventListener("change", function () {
            const parentId = this.value;
            subSelect.innerHTML = "";
            hiddenInput.value = parentId; // default kirim parent

            if (!parentId) {
                subWrapper.style.display = "none";
                return;
            }

            const parent = window.departemensData?.find(
                (d) => d.id == parentId,
            );

            if (parent && parent.children.length > 0) {
                subWrapper.style.display = "block";

                subSelect.innerHTML =
                    '<option value="">-- Pilih Sub Departemen --</option>';

                parent.children.forEach((sub) => {
                    subSelect.innerHTML += `<option value="${sub.id}">${sub.name_dep}</option>`;
                });
            } else {
                subWrapper.style.display = "none";
            }
        });

        subSelect.addEventListener("change", function () {
            hiddenInput.value = this.value; // override kalau pilih sub
        });
    }

    /* ===========================
   EDIT PROGRAM
============================ */
    document.querySelectorAll(".edit-departemen").forEach((select) => {
        const subTargetId = select.dataset.target;
        const hiddenId = select.dataset.hidden;
        const selectedId = select.dataset.selected;

        const subSelect = document.getElementById(subTargetId);
        const hiddenInput = document.getElementById(hiddenId);
        const wrapper = document.getElementById(
            "subWrapperEdit" + subTargetId.replace("subEdit", ""),
        );

        function loadSub(parentId) {
            hiddenInput.value = parentId; // default kirim parent
            subSelect.innerHTML = "";

            const parent = window.departemensData.find((d) => d.id == parentId);

            if (parent && parent.children.length > 0) {
                wrapper.style.display = "block";

                subSelect.innerHTML =
                    '<option value="">-- Pilih Sub Departemen --</option>';

                parent.children.forEach((sub) => {
                    const isSelected = sub.id == selectedId ? "selected" : "";

                    subSelect.innerHTML += `
                    <option value="${sub.id}" ${isSelected}>
                        ${sub.name_dep}
                    </option>
                `;
                });
            } else {
                wrapper.style.display = "none";
            }
        }

        // Saat parent berubah
        select.addEventListener("change", function () {
            loadSub(this.value);
        });

        // Saat sub berubah
        subSelect.addEventListener("change", function () {
            hiddenInput.value = this.value;
        });

        // Trigger awal supaya saat modal dibuka langsung benar
        loadSub(select.value);
    });

    /* ===========================
       RESET MODAL
    ============================ */
    document.querySelectorAll(".modal").forEach((modal) => {
        modal.addEventListener("hidden.bs.modal", function () {
            const form = modal.querySelector("form");
            if (form) form.reset();

            const select = modal.querySelector(".edit-departemen");
            if (select) select.dispatchEvent(new Event("change"));
        });
    });

    /* ===========================
       AUTO LOGOUT (2 JAM)
    ============================ */
    setTimeout(function () {
        alert("Sesi anda telah habis (2 jam). Silakan login kembali.");
        document.getElementById("logout-form")?.submit();
    }, 7200000);

    /*
    |--------------------------------------------------------------------------
    | DETAIL MODAL FETCH
    |--------------------------------------------------------------------------
    */

    const modal = document.getElementById("modalDetailTransaksi");

    if (modal) {
        modal.addEventListener("show.bs.modal", function (event) {
            const card = event.relatedTarget;
            if (!card) return;

            const id = card.getAttribute("data-id");
            if (!id) return;

            const modalBody = modal.querySelector(".modal-body");
            modalBody.innerHTML = "Loading...";

            fetch(`/transaksi/${id}`)
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Gagal mengambil data");
                    }
                    return response.text();
                })
                .then((data) => {
                    modalBody.innerHTML = data;
                })
                .catch((error) => {
                    modalBody.innerHTML = `
                        <div class="alert alert-danger">
                            Terjadi kesalahan saat memuat data.
                        </div>
                    `;
                    console.error(error);
                });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | TOM SELECT INIT
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll(".kategoriSelect").forEach(function (el) {
        new TomSelect(el, {
            plugins: ["remove_button"],
            create: false,
            maxItems: null,
            hideSelected: true,
            closeAfterSelect: true,
            persist: false,
            placeholder: "Cari kategori...",
        });
    });

    /* ===========================
       FILTER REKENING ASAL-TUJUAN
    ============================ */
    const asal = document.getElementById("rekening_asal");
    const tujuan = document.getElementById("rekening_tujuan");

    if (asal && tujuan) {
        asal.addEventListener("change", function () {
            // reset semua option
            Array.from(tujuan.options).forEach((option) => {
                option.disabled = false;
            });

            // reset pilihan tujuan
            tujuan.value = "";

            // disable yang sama dengan asal
            if (this.value !== "") {
                const option = tujuan.querySelector(
                    `option[value="${this.value}"]`,
                );
                if (option) option.disabled = true;
            }
        });
    }
});
