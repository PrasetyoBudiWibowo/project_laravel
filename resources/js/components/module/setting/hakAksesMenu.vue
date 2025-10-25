<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>Daftar Menu</div>
            </div>
            <div class="card-body">
                <table
                    id="tabelHakAksesMenu"
                    class="display nowrap"
                    style="width: 100%"
                >
                    <thead>
                        <tr>
                            <th style="width: 5%">No</th>
                            <th>User</th>
                            <th>Hak Akses</th>
                            <th style="width: 20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="(user, index) in dataUser"
                            :key="user.id_user"
                        >
                            <td>{{ index + 1 }}</td>
                            <td>{{ user.nama_user }}</td>
                            <td>
                                <template
                                    v-if="
                                        user.akses_menu &&
                                        user.akses_menu.length > 0
                                    "
                                >
                                    <ul class="mb-0 ps-3">
                                        <li
                                            v-for="(
                                                akses, i
                                            ) in user.akses_menu"
                                            :key="i"
                                        >
                                            {{
                                                akses?.menu?.module.nama_module
                                            }}
                                            - {{ akses?.menu?.nama_menu }}
                                        </li>
                                    </ul>
                                </template>
                                <template v-else>
                                    <span class="badge bg-secondary"
                                        >Belum ada akses</span
                                    >
                                </template>
                            </td>
                            <td>
                                <button
                                    class="btn btn-sm btn-warning me-2"
                                    @click="editUser(user)"
                                >
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div
                class="modal fade"
                id="modalEditHakAksesMenu"
                tabindex="-1"
                aria-labelledby="modalEditHakAksesMenuLabel"
                aria-hidden="true"
                data-bs-backdrop="static"
                data-bs-keyboard="false"
            >
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5
                                class="modal-title"
                                id="modalEditHakAksesMenuLabel"
                            >
                                Hak Akses Menu
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
                                <label
                                    for="selected_user_name"
                                    class="form-label"
                                    >User</label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    id="selected_user_name"
                                    v-model="selectedUser.nama_user"
                                    disabled
                                />
                            </div>

                            <div class="mb-3">
                                <label for="select_module" class="form-label"
                                    >Module</label
                                >
                                <div class="d-flex">
                                    <select
                                        class="form-control me-2"
                                        name="select_module"
                                        id="select_module"
                                        v-model="selectedModule"
                                        @change="filterMenu(selectedModule)"
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
                                <label for="selected_menu" class="form-label"
                                    >Menu</label
                                >
                                <select
                                    class="form-control"
                                    id="selected_menu"
                                    v-model="selectedMenu"
                                    :disabled="!selectedModule"
                                >
                                    <option value="">-- PILIH MENU --</option>
                                    <option
                                        v-for="menu in dataMenu"
                                        :key="menu.kd_menu"
                                        :value="menu.kd_menu"
                                    >
                                        {{ menu.nama_menu }}
                                    </option>
                                </select>
                            </div>

                            <button
                                type="button"
                                class="btn btn-primary"
                                @click="tambahAksesMenu"
                            >
                                Tambah
                            </button>

                            <div class="mt-3">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <td>No</td>
                                            <td>Nama Menu</td>
                                            <td>Bisa Akses Menu</td>
                                            <td>Bisa Tambah Data</td>
                                            <td>Bisa Ubah Data</td>
                                            <td>Bisa Export Data</td>
                                            <td>Aksi</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="(
                                                menu, index
                                            ) in inputData.menus"
                                            :key="menu.kd_menu"
                                        >
                                            <td>{{ index + 1 }}</td>
                                            <td>{{ menu.nama_menu }}</td>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    v-model="menu.status_akses"
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    v-model="menu.can_insert"
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    v-model="menu.can_edit"
                                                />
                                            </td>
                                            <td>
                                                <input
                                                    type="checkbox"
                                                    v-model="menu.can_export"
                                                />
                                            </td>
                                            <td>
                                                <button
                                                    class="btn btn-sm btn-danger"
                                                    @click="
                                                        hapusAksesMenu(index)
                                                    "
                                                >
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="inputData.menus.length === 0">
                                            <td colspan="7" class="text-center">
                                                Belum ada menu dipilih
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
                                @click="btnSimpanAksesMenu"
                            >
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <LoadingData :visible="loading" message="Loading" />
    </div>
</template>

<script>
import LoadingData from "../../loading/loadingData.vue";
import { Modal } from "bootstrap";

export default {
    components: { LoadingData },
    data() {
        return {
            dataUser: [],
            dataModule: [],
            allMenu: [],
            dataMenu: [],
            selectedUser: {},
            dataTableInstance: null,
            editModal: null,
            selectedModule: null,
            selectedMenu: null,

            loading: true,

            inputData: {
                kd_user: "",
                menus: [],
            },
        };
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        await this.daftarUser();
        await this.module();
        await this.daftarMenu();

        this.$nextTick(() => {
            defaultSelect2(
                "#select_module",
                "-- PILIH --",
                "#modalEditHakAksesMenu"
            );

            defaultSelect2(
                "#selected_menu",
                "-- PILIH --",
                "#modalEditHakAksesMenu"
            );

            $("#select_module").on("change", (e) => {
                this.selectedModule = $(e.target).val();
                this.filterMenu(this.selectedModule);
            });

            $("#modalEditHakAksesMenu").on("hide.bs.modal", () => {
                this.resetFromEdit();
            });

            $("#selected_menu").on("change", (e) => {
                this.selectedMenu = $(e.target).val();
            });

            this.refreshTable();
        });

        this.loading = false;
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
                    text: `Terjadi kesalahan module: ${
                        error.statusText || error
                    }`,
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

                this.dataMenu = data || [];
                this.allMenu = data || [];
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan daftarMenu: ${
                        error.statusText || error
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        async daftarUser() {
            try {
                const data = await getDataUserRegister();

                this.dataUser =
                    data.filter(
                        (it) =>
                            it.id_usr_level !== "1" ||
                            it.level.level_user !== "SUPER ADMIN"
                    ) || [];

                console.log("datadasar", data);
            } catch (error) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan daftarUser: ${
                        error.statusText || error
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        filterMenu(kdModule) {
            if (kdModule) {
                this.dataMenu = this.allMenu.filter(
                    (it) => it.kd_module === kdModule && it.urutan !== null
                );
            } else {
                this.dataMenu = [];
            }
        },
        resetFromEdit() {
            this.selectedModule = null;
            this.selectedMenu = null;
            this.inputData = {
                kd_user: "",
                menus: [],
            };
            $("#select_module").val("").trigger("change");
            $("#selected_menu").val("").trigger("change");
        },
        editUser(user) {
            this.inputData.menus = [];
            this.selectedUser = JSON.parse(JSON.stringify(user));
            this.inputData.kd_user = user.kd_asli_user;

            if (Array.isArray(user.akses_menu) && user.akses_menu.length > 0) {
                user.akses_menu.forEach((aksesMenu) => {
                    this.inputData.menus.push({
                        kd_menu: aksesMenu.menu.kd_menu,
                        nama_menu: aksesMenu.menu.nama_menu,
                        can_insert:
                            aksesMenu.bisa_insert === "YA" ? true : false,
                        can_edit: aksesMenu.bisa_edit === "YA" ? true : false,
                        can_export:
                            aksesMenu.bisa_export === "YA" ? true : false,
                        status_akses:
                            aksesMenu.status_akses === "YA" ? true : false,
                    });
                });
            }

            if (!this.editModal) {
                this.editModal = new Modal($("#modalEditHakAksesMenu"));
            }
            this.editModal.show();
        },
        tambahAksesMenu() {
            if (!this.selectedMenu) {
                Swal.fire({
                    icon: "warning",
                    title: "Peringatan",
                    text: "Silakan pilih menu dulu",
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
                return;
            }

            const sudahAda = this.inputData.menus.some(
                (m) => m.kd_menu === this.selectedMenu
            );

            if (sudahAda) {
                Swal.fire({
                    icon: "info",
                    title: "Info",
                    text: "Menu sudah ditambahkan",
                    confirmButtonText: "OK",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
                return;
            }

            const menuObj = this.dataMenu.find(
                (m) => m.kd_menu === this.selectedMenu
            );

            if (menuObj) {
                this.inputData.menus.push({
                    kd_menu: menuObj.kd_menu,
                    nama_menu: menuObj.nama_menu,
                    can_insert: false,
                    can_edit: false,
                    can_export: false,
                    status_akses: false,
                });
                this.selectedMenu = null;
            }
        },

        hapusAksesMenu(index) {
            this.inputData.menus.splice(index, 1);
        },
        refreshTable() {
            if (this.dataTableInstance) {
                this.dataTableInstance.clear().destroy();
                this.dataTableInstance = null;
            }

            this.dataTableInstance = $("#tabelHakAksesMenu").DataTable({
                scrollCollapse: true,
                scrollY: 300,
                fixedHeader: true,
                initComplete: function () {
                    $("#tabelHakAksesMenu tbody").on(
                        "mouseenter",
                        "tr",
                        function () {
                            $(this).css("background-color", "Yellow");
                        }
                    );
                    $("#tabelHakAksesMenu tbody").on(
                        "mouseleave",
                        "tr",
                        function () {
                            $(this).css("background-color", "");
                        }
                    );
                },
            });
        },
        btnSimpanAksesMenu() {
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
                    this.simpanAksesMenu();
                }
            });
        },
        async simpanAksesMenu() {
            let dataToSave = {
                ...this.inputData,
                user_input: window.encryptedUserId,
            };

            try {
                Swal.fire({
                    title: "Sedang Proses Simpan Data",
                    text: "Mohon tunggu.",
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                const response = await axios.post(
                    "/hak-akses-menu",
                    dataToSave
                );
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
                        $("#modalEditHakAksesMenu").modal("hide");
                        $("#modalEditHakAksesMenu").on(
                            "hidden.bs.modal",
                            () => {
                                window.location.reload();
                            }
                        );
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
<style>
#tabelHakAksesMenu td ul {
    list-style: disc;
    margin: 0;
    padding-left: 1.2rem;
}

#tabelHakAksesMenu td li {
    line-height: 1.3rem;
}
</style>
