<template>
    <div
        class="modal fade"
        id="modalEditPersonalKaryawan"
        tabindex="-1"
        aria-labelledby="modalEditPersonalKaryawanLabel"
        aria-hidden="true"
        data-bs-backdrop="static"
        data-bs-keyboard="false"
    >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Data Pribadi Karyawan</h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <!-- Nama Karyawan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Karyawan</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="dataPersonalKaryawan.nama_karyawan"
                            />
                        </div>

                        <!-- Nama Panggilan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Panggilan</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="
                                    dataPersonalKaryawan.nama_panggilan_karyawan
                                "
                            />
                        </div>

                        <!-- Email Pribadi -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email Pribadi</label>
                            <input
                                type="email"
                                class="form-control"
                                v-model="dataPersonalKaryawan.email_pribadi"
                                placeholder="nama@email.com"
                            />
                        </div>

                        <!-- Jenis kelamin -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select
                                class="form-select"
                                v-model="dataPersonalKaryawan.gender"
                            >
                                <option value="">-- Pilih --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold"
                                >Tanggal Lahir</label
                            >

                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <input
                                    type="date"
                                    class="form-control"
                                    v-model="dataPersonalKaryawan.tgl_lahir"
                                    :max="today"
                                />
                            </div>

                            <small
                                class="text-muted"
                                v-if="dataPersonalKaryawan.tgl_lahir"
                            >
                                {{ hariLahir }} • {{ umur }} Tahun
                            </small>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="kd_agama" class="form-label">
                                Agama
                            </label>
                            <select
                                class="form-control"
                                name="kd_agama"
                                id="kd_agama"
                                v-model="dataPersonalKaryawan.kd_agama"
                            >
                                <option value="">-- AGAMA --</option>
                                <option
                                    v-for="religion in dataReligion"
                                    :key="religion.kd_agama"
                                    :value="`${religion.kd_agama}`"
                                >
                                    {{ religion.nama_agama }}
                                </option>
                            </select>
                        </div>

                        <!-- No KTP -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">No KTP</label>
                            <input
                                type="text"
                                class="form-control"
                                maxlength="16"
                                placeholder="16 digit NIK"
                                :value="dataPersonalKaryawan.no_ktp"
                                @input="onInputKtp"
                            />
                        </div>

                        <!-- NPWP -->
                        <div class="col-md-4 mb-3">
                            <label class="form-label">NPWP</label>
                            <input
                                type="text"
                                class="form-control"
                                :value="dataPersonalKaryawan.npwp"
                                @input="onInputNpwp"
                                placeholder="99.999.999.9-999.999"
                                maxlength="20"
                            />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">No. Telepon / HP 1</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="dataPersonalKaryawan.no_telp1"
                                placeholder="08xxxxxxxxxx"
                                @input="onInputNoTelp($event, 'no_telp1')"
                                maxlength="15"
                            />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">No. Telepon / HP 2</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="dataPersonalKaryawan.no_telp2"
                                placeholder="08xxxxxxxxxx"
                                @input="onInputNoTelp($event, 'no_telp2')"
                                maxlength="15"
                            />
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">No. Telepon / HP 3</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="dataPersonalKaryawan.no_telp3"
                                placeholder="08xxxxxxxxxx"
                                @input="onInputNoTelp($event, 'no_telp3')"
                                maxlength="15"
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tinggi Badan</label>
                            <input
                                type="number"
                                class="form-control"
                                v-model="dataPersonalKaryawan.tinggi_karyawan"
                            />
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Berat badan</label>
                            <input
                                type="number"
                                class="form-control"
                                v-model="dataPersonalKaryawan.berat_karyawan"
                            />
                        </div>

                        <!-- TEMPAT LAHIR -->
                        <div class="col-md-10 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <AutoComplete
                                v-model="selectedLokasiLahir"
                                :suggestions="filteredLokasi"
                                optionLabel="text"
                                dropdown
                                placeholder="Ketik provinsi / kota / kecamatan"
                                @complete="searchLokasi"
                                appendTo="self"
                                forceSelection
                                class="w-100"
                            />
                            <small class="text-muted">
                                Ketik provinsi, kota/kabupaten, atau kecamatan
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Alamat
                            </label>
                            <textarea
                                class="form-control"
                                rows="3"
                                placeholder="MASUKAN ALAMAT"
                                :value="dataPersonalKaryawan.alamat_lahir"
                                @input="
                                    dataPersonalKaryawan.alamat_lahir =
                                        $event.target.value.toUpperCase()
                                "
                            ></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold d-block">
                                Alamat tinggal sama dengan alamat lahir
                            </label>

                            <label class="switch-modern">
                                <input
                                    type="checkbox"
                                    v-model="isAlamatTinggalSama"
                                    @change="onToggleAlamatTinggal"
                                />
                                <span class="slider"></span>
                            </label>
                        </div>

                        <!-- TEMPAT TINGGAL -->
                        <div class="col-md-10 mb-3">
                            <label class="form-label">Tempat Tinggal</label>

                            <AutoComplete
                                v-model="selectedLokasiTinggal"
                                :suggestions="filteredLokasiTinggal"
                                optionLabel="text"
                                dropdown
                                placeholder="Ketik provinsi / kota / kecamatan"
                                @complete="searchLokasiTinggal"
                                appendTo="self"
                                forceSelection
                                class="w-100"
                                :disabled="isAlamatTinggalSama"
                            />

                            <small class="text-muted">
                                Ketik provinsi, kota/kabupaten, atau kecamatan
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Alamat Tinggal
                            </label>
                            <textarea
                                class="form-control"
                                rows="3"
                                placeholder="MASUKAN ALAMAT TINGGAL"
                                :disabled="isAlamatTinggalSama"
                                :value="dataPersonalKaryawan.alamat_tinggal"
                                @input="
                                    dataPersonalKaryawan.alamat_tinggal =
                                        $event.target.value.toUpperCase()
                                "
                            ></textarea>
                        </div>

                        <div class="col-md-12 mb-3 mt-2">
                            <label class="form-label fw-semibold">
                                Catatan (opsional)
                            </label>
                            <textarea
                                class="form-control"
                                rows="3"
                                placeholder="ALASAN DI UBAH..."
                                :value="dataPersonalKaryawan.keterangan_input"
                                @input="
                                    dataPersonalKaryawan.keterangan_input =
                                        $event.target.value.toUpperCase()
                                "
                            ></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        @click="closeModal"
                    >
                        Batal
                    </button>

                    <button
                        class="btn btn-primary"
                        @click="btnSimpanDataPersonalKaryawan"
                    >
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from "bootstrap";
import AutoComplete from "primevue/autocomplete";
import * as yup from "yup";
import dayjs from "dayjs";
import "dayjs/locale/id";
dayjs.locale("id");

export default {
    components: {
        AutoComplete,
    },
    data() {
        return {
            modalDataPersonal: null,
            isAlamatTinggalSama: false,

            selectedLokasiLahir: null,
            filteredLokasi: [],

            selectedLokasiTinggal: null,
            filteredLokasiTinggal: [],

            lokasiOptions: [],
            backupLokasiTinggal: null,

            dataPersonalKaryawan: {
                nama_karyawan: "",
                nama_panggilan_karyawan: "",
                gender: "",
                kd_agama: "",
                nama_agama: "",
                kd_negara: "",
                tgl_lahir: "",
                no_ktp: "",
                npwp: "",
                kd_provinsi_lahir: "",
                kd_kota_kab_lahir: "",
                kd_kecamatan_lahir: "",
                alamat_lahir: "",

                kd_provinsi_tinggal: "",
                kd_kota_kab_tinggal: "",
                kd_kecamatan_tinggal: "",
                alamat_tinggal: "",

                tinggi_karyawan: "",
                berat_karyawan: "",
                no_telp1: "",
                no_telp2: "",
                no_telp3: "",

                keterangan_input: "",
            },

            dataKecamatan: [],
            dataReligion: [],
        };
    },
    watch: {
        selectedLokasiLahir(val) {
            if (!val) {
                this.dataPersonalKaryawan.kd_provinsi_lahir = "";
                this.dataPersonalKaryawan.kd_kota_kab_lahir = "";
                this.dataPersonalKaryawan.kd_kecamatan_lahir = "";
                return;
            }

            this.dataPersonalKaryawan.kd_provinsi_lahir = val.kd_provinsi;
            this.dataPersonalKaryawan.kd_kota_kab_lahir = val.kd_kota_kab;
            this.dataPersonalKaryawan.kd_kecamatan_lahir = val.kd_kecamatan;

            if (this.isAlamatTinggalSama) {
                this.copyLahirToTinggal();
            }
        },
        selectedLokasiTinggal(val) {
            if (!val) return;

            this.dataPersonalKaryawan.kd_provinsi_tinggal = val.kd_provinsi;
            this.dataPersonalKaryawan.kd_kota_kab_tinggal = val.kd_kota_kab;
            this.dataPersonalKaryawan.kd_kecamatan_tinggal = val.kd_kecamatan;
        },
    },
    computed: {
        today() {
            return dayjs().format("YYYY-MM-DD");
        },
        umur() {
            if (!this.dataPersonalKaryawan.tgl_lahir) return "";
            return dayjs().diff(
                dayjs(this.dataPersonalKaryawan.tgl_lahir),
                "year"
            );
        },
        hariLahir() {
            if (!this.dataPersonalKaryawan.tgl_lahir) return "";
            return dayjs(this.dataPersonalKaryawan.tgl_lahir).format("dddd");
        },
    },
    async mounted() {
        await this.kecamatan();
        await this.religion();

        this.prepareLokasi();

        this.modalDataPersonal = new Modal(
            document.getElementById("modalEditPersonalKaryawan")
        );

        document
            .getElementById("modalEditPersonalKaryawan")
            .addEventListener("shown.bs.modal", () => {
                this.$nextTick(() => {
                    this.prepareLokasi();
                });
            });
    },
    methods: {
        openModalPersonalData(encrypted, data) {
            this.dataPersonalKaryawan = {
                ...data,
                nama_agama: data.Agama?.nama_agama ?? "",
            };

            this.$nextTick(() => {
                this.setLokasiIfExist();
                this.setLokasiTinggalIfExist();
                this.setAgamaIfExist();
            });

            this.modalDataPersonal.show();
        },
        closeModal() {
            this.modalDataPersonal.hide();
        },
        onInputNpwp(e) {
            this.dataPersonalKaryawan.npwp = formatNpwp(e.target.value);
        },
        onInputNoTelp(event, field) {
            let val = event.target.value;

            // hanya angka
            val = val.replace(/[^0-9]/g, "");

            if (val.startsWith("62")) {
                val = "0" + val.slice(2);
            }

            if (val.length > 13) {
                val = val.slice(0, 13);
            }

            this.dataPersonalKaryawan[field] = val;
        },
        prepareLokasi() {
            this.lokasiOptions = this.dataKecamatan.map((kec) => ({
                id: kec.kd_kecamatan,
                text: `${kec.kota_kabupaten.provinsi.nama_provinsi}-${kec.kota_kabupaten.nama_kota_kabupaten}-${kec.nama_kecamatan}`,
                kd_kecamatan: kec.kd_kecamatan,
                kd_kota_kab: kec.kd_kota_kabupaten,
                kd_provinsi: kec.kota_kabupaten.kd_provinsi,
            }));
        },
        searchLokasi(event) {
            const query = event.query.toLowerCase();

            this.filteredLokasi = this.lokasiOptions.filter((item) =>
                item.text.toLowerCase().includes(query)
            );
        },
        searchLokasiTinggal(event) {
            const query = event.query.toLowerCase();

            this.filteredLokasiTinggal = this.lokasiOptions.filter((item) =>
                item.text.toLowerCase().includes(query)
            );
        },
        setLokasiIfExist() {
            if (
                !this.dataPersonalKaryawan.kd_provinsi_lahir ||
                !this.dataPersonalKaryawan.kd_kota_kab_lahir ||
                !this.dataPersonalKaryawan.kd_kecamatan_lahir
            ) {
                this.selectedLokasiLahir = null;
                return;
            }

            const found = this.lokasiOptions.find(
                (x) =>
                    x.kd_kecamatan ===
                        this.dataPersonalKaryawan.kd_kecamatan_lahir &&
                    x.kd_kota_kab ===
                        this.dataPersonalKaryawan.kd_kota_kab_lahir &&
                    x.kd_provinsi ===
                        this.dataPersonalKaryawan.kd_provinsi_lahir
            );

            if (!found) {
                this.selectedLokasiLahir = null;
                return;
            }

            this.selectedLokasiLahir = found;
        },
        setLokasiTinggalIfExist() {
            if (
                !this.dataPersonalKaryawan.kd_provinsi_tinggal ||
                !this.dataPersonalKaryawan.kd_kota_kab_tinggal ||
                !this.dataPersonalKaryawan.kd_kecamatan_tinggal
            ) {
                this.selectedLokasiTinggal = null;
                return;
            }

            console.log("lodaoj", this.lokasiOptions);

            const found = this.lokasiOptions.find(
                (x) =>
                    x.kd_kecamatan ===
                        this.dataPersonalKaryawan.kd_kecamatan_tinggal &&
                    x.kd_kota_kab ===
                        this.dataPersonalKaryawan.kd_kota_kab_tinggal &&
                    x.kd_provinsi ===
                        this.dataPersonalKaryawan.kd_provinsi_tinggal
            );

            this.selectedLokasiTinggal = found ?? null;
        },
        setAgamaIfExist() {
            if (!this.dataPersonalKaryawan.nama_agama) return;

            const found = this.dataReligion.find(
                (r) =>
                    r.nama_agama.toLowerCase() ===
                    this.dataPersonalKaryawan.nama_agama.toLowerCase()
            );

            if (found) {
                this.dataPersonalKaryawan.kd_agama = found.kd_agama;
            }
        },
        onToggleAlamatTinggal() {
            if (this.isAlamatTinggalSama) {
                // backup dulu
                this.backupLokasiTinggal = this.selectedLokasiTinggal
                    ? { ...this.selectedLokasiTinggal }
                    : null;

                if (this.selectedLokasiLahir) {
                    const src = this.selectedLokasiLahir;

                    this.selectedLokasiTinggal = { ...src };

                    this.dataPersonalKaryawan.kd_provinsi_tinggal =
                        src.kd_provinsi;
                    this.dataPersonalKaryawan.kd_kota_kab_tinggal =
                        src.kd_kota_kab;
                    this.dataPersonalKaryawan.kd_kecamatan_tinggal =
                        src.kd_kecamatan;

                    this.dataPersonalKaryawan.alamat_tinggal =
                        this.dataPersonalKaryawan.alamat_lahir;
                } else {
                    this.selectedLokasiTinggal = null;
                }
            } else {
                this.selectedLokasiTinggal = this.backupLokasiTinggal;

                if (this.backupLokasiTinggal) {
                    const src = this.backupLokasiTinggal;

                    this.dataPersonalKaryawan.kd_provinsi_tinggal =
                        src.kd_provinsi;
                    this.dataPersonalKaryawan.kd_kota_kab_tinggal =
                        src.kd_kota_kab;
                    this.dataPersonalKaryawan.kd_kecamatan_tinggal =
                        src.kd_kecamatan;
                } else {
                    this.dataPersonalKaryawan.kd_provinsi_tinggal = "";
                    this.dataPersonalKaryawan.kd_kota_kab_tinggal = "";
                    this.dataPersonalKaryawan.kd_kecamatan_tinggal = "";
                    this.dataPersonalKaryawan.alamat_tinggal = "";
                }
            }
        },
        onInputKtp(e) {
            let value = e.target.value;

            value = value.replace(/\D/g, "");

            if (value.length > 16) {
                value = value.slice(0, 16);
            }

            this.dataPersonalKaryawan.no_ktp = value;
        },
        async kecamatan() {
            try {
                const data = await getAllDataKecamatan();
                this.dataKecamatan = data || [];
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan dataKecamatan: ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        async religion() {
            try {
                const data = await getAllReligion();
                this.dataReligion = data || [];
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan religion: ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        btnSimpanDataPersonalKaryawan() {
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
                    this.simpanDataPersonalKaryawan();
                }
            });
        },
        async simpanDataPersonalKaryawan() {
            let dataToSave = {
                type: "DATA PRIBADI",
                ...this.dataPersonalKaryawan,
                bln_lahir: dayjs(this.dataPersonalKaryawan.tgl_lahir).format(
                    "MM"
                ),
                thn_lahir: dayjs(this.dataPersonalKaryawan.tgl_lahir).format(
                    "YYYY"
                ),
                user_ubah: window.encryptedUserId,
            };

            console.log("datatosave", dataToSave);

            let requireValue = [];

            requireValue.push({
                value: dataToSave.nama_karyawan,
                message: "Nama karyawan Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.gender,
                message: "Jenis Kelamin Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.kd_agama,
                message: "Agama Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.no_ktp,
                message: "No KTP Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.tgl_lahir,
                message: "Tanggal Lahir Tidak Boleh Kosong",
            });

            requireValue.push({
                value: dataToSave.no_telp1,
                message: "No Telp / Hp 1 Tidak Boleh Kosong",
            });

            if (!validasiBanyakInputan(requireValue)) return;

            const schema = yup.object({
                nama_karyawan: yup
                    .string()
                    .required("Nama Karyawan wajib diisi")
                    .matches(
                        /^[A-Za-z\s&]+$/,
                        "Nama Karyawan Hanya Boleh huruf yang diperbolehkan"
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

                const response = await axios.post(
                    "/hrd/ubah-karyawan",
                    dataToSave
                );
                const result = response.data;

                Swal.close();

                if (result.status === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: result.message || "Data berhasil Ubah Data!",
                        customClass: {
                            confirmButton: "btn btn-success",
                        },
                    }).then(() => {
                        window.location.reload();
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
                    text: `Terjadi kesalahan simpanDataPersonalKaryawan : ${
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
.switch-modern {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
}

.switch-modern input {
    opacity: 0;
    width: 0;
    height: 0;
}

.switch-modern .slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #ccc;
    transition: 0.4s;
    border-radius: 34px;
}

.switch-modern .slider::before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.4s;
    border-radius: 50%;
}

.switch-modern input:checked + .slider {
    background-color: #0784cc;
}

.switch-modern input:checked + .slider::before {
    transform: translateX(24px);
}

.p-autocomplete-panel {
    z-index: 2000 !important;
}
</style>
