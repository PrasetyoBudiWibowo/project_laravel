<template>
    <div class="p-4">
        <div class="card mb-3">
            <div class="card-header">
                <div>MASTER DEPARTEMENT</div>
            </div>

            <div class="card-body">
                <div class="d-flex justify-content mb-3 gap-2">
                    <button
                        v-if="
                            dataUser?.level_user === 'SUPER ADMIN' ||
                            hakAkses?.bisa_insert === 'YA'
                        "
                        class="btn btn-primary"
                        @click="openModal"
                    >
                        <i class="fas fa-plus me-1"></i> Tambah
                    </button>
                </div>

                <div class="row mt-2">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table
                                id="tableDepartement"
                                class="display nowrap table-bordered"
                                style="width: 100%"
                            >
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Divisi</th>
                                        <th>Depatement</th>
                                        <th
                                            v-show="
                                                dataUser?.level_user ===
                                                    'SUPER ADMIN' ||
                                                hakAkses?.bisa_edit === 'YA'
                                            "
                                        >
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="(item, index) in dataDepartement"
                                        :key="item.kd_departement"
                                    >
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ item.divisi.nama_divisi }}</td>
                                        <td>{{ item.nama_departement }}</td>
                                        <td>
                                            <button
                                                class="btn btn-sm btn-warning me-2"
                                                @click="openEdit(item)"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                class="btn btn-sm btn-danger"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- <modalTambahDepartement
                    ref="modalTambahDepartement"
                    :dataDivisi="dataDivisi"
                /> -->

                <modalTambahDepartement ref="modalTambahDepartement" />
                <modalEditDepartement ref="modalEditDepartement" />
            </div>
        </div>
    </div>

    <loadingData :visible="loadingMenu" message="Loading" />
</template>

<script>
import modalTambahDepartement from "./modal/modalTambahDepartement.vue";
import modalEditDepartement from "./modal/modalEditDepartement.vue";
import loadingData from "../../../../loading/loadingData.vue";

export default {
    components: { modalTambahDepartement, loadingData, modalEditDepartement },
    data() {
        return {
            dataUser: window.userData,
            hakAkses: null,
            dataTableInstance: null,
            dataDepartement: [],

            loadingMenu: true,
        };
    },
    async mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;

        if (this.dataUser.level_user !== "SUPER ADMIN") {
            await this.cekStatusAkses();
        }

        await this.departement();
        this.$nextTick(() => {
            this.refreshTable();
        });

        this.loadingMenu = false;
    },
    methods: {
        openModal() {
            this.$refs.modalTambahDepartement.openModal();
        },
        openEdit(item) {
            this.$refs.modalEditDepartement.openModal(item);
        },
        async cekStatusAkses() {
            try {
                const data = {
                    user: window.encryptedUserId,
                    url: window.location.pathname,
                };

                const allowed = await validasiUserHalaman(data);

                if (!allowed) {
                    return;
                }
                console.log("dhnas", (this.hakAkses = allowed[0]));
            } catch (error) {
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
        async departement() {
            try {
                const data = await getAllDepartement();
                this.dataDepartement = data || [];
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan this.departement(): ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        refreshTable() {
            if (this.dataTableInstance) {
                this.dataTableInstance.clear().destroy();
                this.dataTableInstance = null;
            }

            this.dataTableInstance = $("#tableDepartement").DataTable({
                // scrollCollapse: true,
                // scrollY: 300,
                // fixedHeader: true,
                initComplete: function () {
                    $("#tableDepartement tbody").on(
                        "mouseenter",
                        "tr",
                        function () {
                            $(this).css("background-color", "Yellow");
                        }
                    );
                    $("#tableDepartement tbody").on(
                        "mouseleave",
                        "tr",
                        function () {
                            $(this).css("background-color", "");
                        }
                    );
                },
            });
        },
    },
};
</script>
