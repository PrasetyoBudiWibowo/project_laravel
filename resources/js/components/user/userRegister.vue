<template>
    <div class="p-4">
        <div class="card">
            <div class="card-header">
                <div>User Terdaftar</div>
            </div>
            <div class="card-body">
                <a-table :columns="columns" :data-source="users" bordered />
            </div>
        </div>
    </div>

    <div
        class="modal fade"
        id="userModal"
        tabindex="-1"
        aria-labelledby="userModalLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Edit User</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="inputNama" class="form-label"
                            ><strong>Nama:</strong></label
                        >
                        <input
                            type="text"
                            id="inputNama"
                            class="form-control"
                            v-model="selectedUser.nama_user"
                            placeholder="Masukkan nama user"
                        />
                    </div>

                    <div class="mb-3">
                        <label for="inputPassword" class="form-label"
                            ><strong>Password:</strong></label
                        >
                        <input
                            type="text"
                            id="inputPassword"
                            class="form-control"
                            v-model="selectedUser.password_tampil"
                            placeholder="Masukkan password"
                        />
                    </div>

                    <div class="mb-3">
                        <label for="level_user" class="form-label"
                            >Level User</label
                        >
                        <select
                            id="id_usr_level"
                            v-model="selectedUser.id_usr_level"
                            class="form-control"
                            name="id_usr_level"
                        >
                            <option disabled value="">-- Pilih Level --</option>
                            <option
                                v-for="level in levels"
                                :key="level.id"
                                :value="level.id"
                            >
                                {{ level.level_user }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="statusUser" class="form-label"
                            >Status User</label
                        >
                        <select
                            id="statusUser"
                            v-model="selectedUser.status_user"
                            class="form-control"
                        >
                            <option value="ACTIVE">ACTIVE</option>
                            <option value="NON ACTIVE">NON ACTIVE</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="blokir" class="form-label">Blokir</label>
                        <select
                            id="blokir"
                            v-model="selectedUser.blokir"
                            class="form-control"
                        >
                            <option value="YA">YA</option>
                            <option value="TIDAK">TIDAK</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Close
                    </button>
                    <button type="button" class="btn btn-primary">
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { h } from "vue";
import { Modal } from "bootstrap";

export default {
    data() {
        return {
            rawColumns: [
                {
                    title: "No",
                    dataIndex: "_no",
                    key: "_no",
                    width: 60,
                    customRender: ({ text }) => h("span", text),
                },
                {
                    title: "Nama",
                    key: "nama_user",
                    customRender: ({ record }) => {
                        const imgName =
                            record.img_user && record.img_user.trim() !== ""
                                ? `/assets/img/user/${record.img_user}.${record.format_img_user}`
                                : `/assets/img/default/Default-Profile.png`;

                        return h(
                            "div",
                            {
                                style: {
                                    display: "flex",
                                    alignItems: "center",
                                },
                            },
                            [
                                h("img", {
                                    src: imgName,
                                    alt: record.nama_user,
                                    style: {
                                        width: "50px",
                                        height: "50px",
                                        borderRadius: "50%",
                                        marginRight: "8px",
                                        objectFit: "cover",
                                    },
                                }),
                                h("span", record.nama_user),
                            ]
                        );
                    },
                },
                {
                    title: "Password",
                    dataIndex: "password_tampil",
                    key: "password_tampil",
                },
                {
                    title: "Status User",
                    dataIndex: "status_user",
                    key: "status_user",
                    customRender: ({ text }) => {
                        return h(
                            "span",
                            {
                                class: [
                                    "badge",
                                    text === "ACTIVE"
                                        ? "bg-success"
                                        : "bg-danger",
                                ],
                            },
                            text
                        );
                    },
                },
                {
                    title: "Blokir",
                    dataIndex: "blokir",
                    key: "blokir",
                    customRender: ({ text }) => {
                        return h(
                            "span",
                            {
                                class: [
                                    "badge",
                                    text === "TIDAK"
                                        ? "bg-success"
                                        : "bg-danger",
                                ],
                            },
                            text
                        );
                    },
                },
                {
                    title: "Level",
                    dataIndex: ["level", "level_user"],
                    key: "level_user",
                },
                {
                    title: "Aksi",
                    key: "aksi",
                    width: 100,
                    customRender: ({ record }) =>
                        h(
                            "button",
                            {
                                class: "btn btn-sm btn-primary",
                                onClick: () => this.showModal(record),
                            },
                            "Edit"
                        ),
                },
            ],
            users: [],
            columns: [],
            levels: [],
            selectedUser: {
                nama_user: "",
                password_tampil: "",
                id_usr_level: "",
                status_user: "",
                blokir: "",
            },
            modalInstance: null,
        };
    },
    mounted() {
        this.columns = this.generateColumns(this.rawColumns);
        this.dataUser();
        this.dataLevels();

        this.$nextTick(() => {
            const modalEl = $("#userModal")[0];
            this.modalInstance = new Modal(modalEl);

            defaultSelect2("#id_usr_level", "-- Pilih Level --", "#userModal");
            defaultSelect2("#statusUser", null, "#userModal");
            defaultSelect2("#blokir", null, "#userModal");

            $("#id_usr_level").on("change", (e) => {
                this.selectedUser.id_usr_level = e.target.value;
            });
            $("#statusUser").on("change", (e) => {
                this.selectedUser.status_user = e.target.value;
            });
            $("#blokir").on("change", (e) => {
                this.selectedUser.blokir = e.target.value;
            });
        });
    },
    methods: {
        async dataUser() {
            try {
                const data = await getDataUserRegister();
                this.users = (data || []).map((item, index) => ({
                    ...item,
                    _no: index + 1,
                }));
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan dataUser: ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                    buttonsStyling: false,
                });
            }
        },
        async dataLevels() {
            try {
                const data = await getLevelUser();
                this.levels = data || [];

                this.$nextTick(() => {
                    defaultSelect2(
                        "#id_usr_level",
                        "-- Pilih Level --",
                        "#userModal"
                    );

                    $("#id_usr_level")
                        .val(this.selectedUser.id_usr_level)
                        .trigger("change");
                });
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan dataLevels: ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                    buttonsStyling: false,
                });
            }
        },
        generateColumns(rawColumns) {
            return rawColumns.map((col) => ({
                ...col,
                sorter: (a, b) => {
                    if (col.dataIndex !== undefined) {
                        const aVal = this.getValueByPath(a, col.dataIndex);
                        const bVal = this.getValueByPath(b, col.dataIndex);

                        if (
                            typeof aVal === "string" &&
                            typeof bVal === "string"
                        ) {
                            return aVal.localeCompare(bVal);
                        }
                        if (
                            typeof aVal === "number" &&
                            typeof bVal === "number"
                        ) {
                            return aVal - bVal;
                        }
                        return 0;
                    }

                    if (
                        col.key &&
                        a[col.key] !== undefined &&
                        b[col.key] !== undefined
                    ) {
                        const aVal = a[col.key];
                        const bVal = b[col.key];

                        if (
                            typeof aVal === "string" &&
                            typeof bVal === "string"
                        ) {
                            return aVal.localeCompare(bVal);
                        }
                        if (
                            typeof aVal === "number" &&
                            typeof bVal === "number"
                        ) {
                            return aVal - bVal;
                        }
                    }

                    return 0;
                },
            }));
        },
        getValueByPath(obj, path) {
            if (Array.isArray(path)) {
                return path.reduce(
                    (acc, key) =>
                        acc && acc[key] !== undefined ? acc[key] : "",
                    obj
                );
            }
            return obj[path];
        },
        showModal(userData) {
            this.selectedUser = { ...userData };
            this.$nextTick(() => {
                $("#id_usr_level")
                    .val(this.selectedUser.id_usr_level)
                    .trigger("change");
                $("#statusUser")
                    .val(this.selectedUser.status_user)
                    .trigger("change");
                $("#blokir").val(this.selectedUser.blokir).trigger("change");
            });
            this.modalInstance.show();
        },
    },
};
</script>
