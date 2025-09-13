<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>Daftar Menu</div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-start-end mb-3">
                    <button
                        class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTambahMenu"
                    >
                        <i class="fas fa-plus me-1"></i> Tambah Menu
                    </button>
                </div>

                <table
                    id="tabelDaftarMenu"
                    class="display nowrap"
                    style="width: 100%"
                >
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Module</th>
                            <th>Nama Menu</th>
                            <th>Parent Menu</th>
                            <th>Status Menu</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div
                class="modal fade"
                id="modalTambahMenu"
                tabindex="-1"
                aria-labelledby="modalTambahMenuLabel"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
            >
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTambahMenuLabel">
                                Tambah Menu
                            </h5>
                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                            ></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="select_module" class="form-label"
                                    >Module</label
                                >
                                <div class="d-flex">
                                    <select
                                        class="form-control me-2"
                                        name="select_module"
                                        id="select_module"
                                        v-model="inputData.kd_module"
                                    >
                                        <option value="">
                                            -- PILIH MODULE --
                                        </option>
                                        <option
                                            v-for="item in dataModule"
                                            :key="item.kd_module"
                                            :value="item.kd_module"
                                        >
                                            {{ item.nama_module }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="parent_menu" class="form-label"
                                    >Parent Menu</label
                                >
                                <select
                                    class="form-control"
                                    id="parent_menu"
                                    v-model="inputData.parent_menu"
                                    :disabled="!inputData.kd_module"
                                >
                                    <option
                                        v-for="menu in dataMenu"
                                        :key="menu.kd_menu"
                                        :value="menu.kd_menu"
                                    >
                                        {{ menu.nama_menu }}
                                    </option>
                                </select>
                            </div>

                            <div
                                class="form-group mt-2 mb-3"
                                v-if="
                                    inputData.parent_menu &&
                                    inputData.parent_menu !== 'default'
                                "
                            >
                                <label>Tipe Menu</label>
                                <div>
                                    <label>
                                        <input
                                            type="radio"
                                            value="child"
                                            v-model="inputData.tipe_menu"
                                        />
                                        Tetap sebagai Child
                                    </label>
                                    <label class="ml-3">
                                        <input
                                            type="radio"
                                            value="parent"
                                            v-model="inputData.tipe_menu"
                                        />
                                        Jadikan Parent baru
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Menu</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="inputData.nama_menu"
                                    placeholder="Masukkan nama module"
                                    @input="urlOtomatis"
                                />
                            </div>

                            <div class="mb-3 row">
                                <label class="form-label">Icon Menu</label>

                                <div class="col-8">
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="inputData.icon_menu"
                                        placeholder="Contoh: fa-brands fa-slack"
                                    />
                                </div>

                                <div class="col-4" v-if="inputData.icon_menu">
                                    <i
                                        :class="inputData.icon_menu"
                                        style="font-size: 38px"
                                    ></i>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                class="btn btn-primary"
                                @click="btnSimpanMenu"
                            >
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import * as yup from "yup";
import LoadingData from "../../loading/loadingData.vue";

export default {
    data() {
        return {
            dataTableMenu: [],
            filterDataTableMenu: [],
            dataTableInstance: null,
            dataModule: [],
            allMenu: [],
            dataMenu: [],
            dataTableInstance: null,
            inputData: {
                kd_module: "",
                nama_menu: "",
                url_menu: "",
                parent_menu: "",
                icon_menu: "",
                tipe_menu: "",
            },
            loading: true,
        };
    },
    watch: {
        "inputData.kd_module"(val) {
            if (val) {
                this.filterMenu(val);
                this.inputData.parent_menu = "";
            } else {
                this.dataMenu = [];
            }
        },
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        await this.module();
        await this.daftarMenu();

        this.$nextTick(() => {
            defaultSelect2("#select_module", "-- PILIH --", "#modalTambahMenu");
            defaultSelect2("#parent_menu", "-- PILIH --", "#modalTambahMenu");

            $("#select_module").on("change", (e) => {
                this.inputData.kd_module = $(e.target).val();
            });

            $("#modalTambahMenu").on("hide.bs.modal", () => {
                this.resetFormTambah();
            });

            $("#parent_menu").on("change", (e) => {
                this.inputData.parent_menu = $(e.target).val();
            });

            this.refreshTable();
        });
    },
    methods: {
        async module() {
            try {
                const data = await getAllModule();
                this.dataModule = data || [];
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan module: ${err.statusText || err}`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        async daftarMenu() {
            try {
                const data = await getAllMenu();

                this.allMenu = data || [];
                this.filterMenu(this.inputData.kd_module);

                this.dataTableMenu = data || [];
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan daftarMenu: ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        filterMenu(kdModule) {
            this.dataMenu = this.allMenu.filter(
                (it) => it.urutan === null && it.kd_module === kdModule
            );

            this.dataMenu.push({
                kd_module: "",
                kd_menu: "default",
                nama_menu: "-- Tidak ada parent (Menu Utama) --",
            });
        },
        resetFormTambah() {
            this.inputData.nama_menu = "";
            this.inputData.url_menu = "";
            this.inputData.kd_module = "";
            this.inputData.parent_menu = "";
            this.inputData.icon_menu = "";
            this.inputData.tipe_menu = "child";
            $("#select_module").val("").trigger("change");
            $("#parent_menu").val(null).trigger("change");
        },
        urlOtomatis() {
            this.inputData.nama_menu = this.inputData.nama_menu.toUpperCase();
            this.inputData.url_menu = generateUrl(this.inputData.nama_menu);
        },
        refreshTable() {
            if (this.dataTableInstance) {
                this.dataTableInstance.clear().destroy();
                this.dataTableInstance = null;
            }

            this.dataTableInstance = $("#tabelDaftarMenu").DataTable({
                data: this.dataTableMenu,
                scrollCollapse: true,
                scrollY: 300,
                fixedHeader: true,
                columns: [
                    {
                        data: null,
                        width: "5%",
                        render: function (data, type, row, meta) {
                            return meta.row + 1;
                        },
                    },
                    { data: "module.nama_module" },
                    { data: "nama_menu" },
                    { data: "parent.nama_menu" },
                    { data: "status_akses" },
                ],
                initComplete: function () {
                    $("#tabelDaftarMenu tbody").on(
                        "mouseenter",
                        "tr",
                        function () {
                            $(this).css("background-color", "Yellow");
                        }
                    );
                    $("#tabelDaftarMenu tbody").on(
                        "mouseleave",
                        "tr",
                        function () {
                            $(this).css("background-color", "");
                        }
                    );
                },
            });
        },
        btnSimpanMenu() {
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda Yakin Ingin Menyimpan Data ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-success",
                    cancelButton: "btn btn-danger",
                },
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.simpanMenu();
                }
            });
        },
        async simpanMenu() {
            let dataToSave = {
                ...this.inputData,
                tipe_menu:
                    this.inputData.parent_menu === "default"
                        ? "parent"
                        : this.inputData.tipe_menu,
                user_input: window.encryptedUserId,
            };

            let requireValue = [];

            requireValue.push({
                value: dataToSave.nama_menu,
                message: "Nama Menu Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.parent_menu,
                message: "Parent Menu Tidak Boleh Kosong",
            });

            if (dataToSave.parent_menu !== "default") {
                requireValue.push({
                    value: dataToSave.tipe_menu,
                    message: "Tipe Menu Tidak Boleh Kosong",
                });
            }

            const schema = yup.object({
                nama_menu: yup
                    .string()
                    .required("Nama Menu wajib diisi")
                    .matches(
                        /^[A-Za-z\s]+$/,
                        "Nama Menu hanya boleh huruf dan spasi"
                    ),
                icon_menu: yup
                    .string()
                    .matches(
                        /^[a-z\s\-]+$/,
                        "Icon Menu hanya boleh huruf kecil, spasi, dan karakter '-'"
                    ),
            });

            try {
                await schema.validate(dataToSave, { abortEarly: false });

                Swal.fire({
                    title: "Sedang Proses Simpan Data",
                    text: "Mohon tunggu.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                const response = await axios.post("/simpan-menu", dataToSave);
                const result = response.data;

                Swal.close();

                if (result.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: result.message || "Data berhasil Diubah!",
                        customClass: {
                            confirmButton: "btn btn-success",
                        },
                    }).then(() => {
                        $("#modalTambahMenu").modal("hide");
                        $("#modalTambahMenu").on("hidden.bs.modal", () => {
                            window.location.reload();
                        });
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: result.message,
                        confirmButtonText: "Tutup",
                        customClass: {
                            confirmButton: "btn btn-danger",
                        },
                    });
                }
            } catch (error) {
                Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan: ${
                        error.response?.data?.message || error.message
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                    buttonsStyling: false,
                });
            }
        },
    },
};
</script>
