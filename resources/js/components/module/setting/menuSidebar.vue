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
                            <th>Status Menu</th>
                            <th>Aksi</th>
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
                                >
                                    <option value="">
                                        -- Tidak ada parent (Menu Utama) --
                                    </option>
                                    <option
                                        v-for="menu in dataMenu"
                                        :key="menu.kd_menu"
                                        :value="menu.kd_menu"
                                    >
                                        {{ menu.nama_menu }}
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Menu</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="inputData.nama_menu"
                                    placeholder="Masukkan nama module"
                                    @input="
                                        inputData.nama_menu =
                                            inputData.nama_menu.toUpperCase()
                                    "
                                />
                            </div>

                            <div class="mb-3">
                                <label class="form-label">URL Menu</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    v-model="inputData.url_module"
                                    placeholder="Masukkan URL menu"
                                    @input="
                                        inputData.url_module =
                                            inputData.url_module
                                    "
                                />
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
import LoadingData from "../../loading/loadingData.vue";

export default {
    data() {
        return {
            dataModule: [],
            dataMenu: [],
            dataTableInstance: null,
            inputData: {
                kd_module: "",
                nama_menu: "",
                url_module: "",
                parent_menu: "",
            },
            loading: true,
        };
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

            $("#select_module").on("change", (e) => {
                this.inputData.kd_module = $(e.target).val();
            });
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

                console.log("dlma", data);
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
                user_input: window.encryptedUserId,
            };
        },
    },
};
</script>
