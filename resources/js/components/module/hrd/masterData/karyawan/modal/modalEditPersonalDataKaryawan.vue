<template>
    <div
        class="modal fade"
        id="modalEditPersonalKayawan"
        tabindex="-1"
        aria-labelledby="modalEditPersonalKayawanLabel"
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
                        <!-- Nama Panggilan -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Karyawan</label>
                            <input
                                type="text"
                                class="form-control"
                                v-model="dataPersonalKaryawan.nama_karyawan"
                            />
                        </div>

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
                            <label class="form-label">Tanggal Lahir</label>
                            <input
                                type="date"
                                class="form-control"
                                v-model="dataPersonalKaryawan.tgl_lahir"
                            />
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Lahir</label>
                            <select
                                id="select_lokasi_lahir"
                                class="form-select"
                                style="width: 100%"
                            ></select>

                            <small class="text-muted">
                                Ketik provinsi, kota/kabupaten, atau kecamatan
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Alamat Lahir (opsional)
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tempat Tinggal</label>
                            <select
                                id="select_lokasi_tinggal"
                                class="form-select"
                                :disabled="isAlamatTinggalSama"
                                style="width: 100%"
                            ></select>

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

                    <button class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from "bootstrap";

export default {
    data() {
        return {
            modalDataPersonal: null,
            isAlamatTinggalSama: false,
            errorKtp: "",

            dataPersonalKaryawan: {
                namakd_negara_karyawan: "",
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
            },

            dataKecamatan: [],
            dataReligion: [],
        };
    },
    async mounted() {
        await this.kecamatan();
        await this.religion();

        this.initSelectLokasi();
        this.initSelectLokasiTinggal();

        this.modalDataPersonal = new Modal(
            document.getElementById("modalEditPersonalKayawan")
        );
    },
    methods: {
        openModalPersonalData(encrypted, data) {
            this.dataPersonalKaryawan = {
                ...data,
                nama_agama: data.Agama?.nama_agama ?? "",
            };

            this.$nextTick(() => {
                this.setAgamaIfExist();
                this.setLokasiIfExist();
                this.setLokasiTinggalIfExist();
            });

            this.modalDataPersonal.show();
        },
        closeModal() {
            this.modalDataPersonal.hide();
        },
        onInputNpwp(e) {
            this.dataPersonalKaryawan.npwp = formatNpwp(e.target.value);
        },
        initSelectLokasi() {
            const options = this.dataKecamatan.map((kec) => ({
                id: kec.kd_kecamatan,
                text: `${kec.kota_kabupaten.provinsi.nama_provinsi} / ${kec.kota_kabupaten.nama_kota_kabupaten} / ${kec.nama_kecamatan}`,

                // FLATTEN DATA
                kd_kecamatan: kec.kd_kecamatan,
                kd_kota_kab: kec.kd_kota_kabupaten,
                kd_provinsi: kec.kota_kabupaten.kd_provinsi,
            }));

            const $select = $("#select_lokasi_lahir");

            $select.empty().select2({
                data: options,
                placeholder: "-- Ketik provinsi / kota / kecamatan --",
                dropdownParent: $("#modalEditPersonalKayawan"),
                width: "100%",
                allowClear: true,
            });

            $select.val(null).trigger("change");

            $select.off("change").on("change", () => {
                const selected = $select.select2("data")[0];
                if (!selected) return;

                this.dataPersonalKaryawan.kd_kecamatan_lahir =
                    selected.kd_kecamatan;

                this.dataPersonalKaryawan.kd_kota_kab_lahir =
                    selected.kd_kota_kab;

                this.dataPersonalKaryawan.kd_provinsi_lahir =
                    selected.kd_provinsi;
            });
        },
        initSelectLokasiTinggal() {
            const options = this.dataKecamatan.map((kec) => ({
                id: kec.kd_kecamatan,
                text: `${kec.kota_kabupaten.provinsi.nama_provinsi} / ${kec.kota_kabupaten.nama_kota_kabupaten} / ${kec.nama_kecamatan}`,
                data: kec,
            }));

            const $select = $("#select_lokasi_tinggal");

            $select.empty().select2({
                data: options,
                placeholder: "-- Ketik provinsi / kota / kecamatan --",
                dropdownParent: $("#modalEditPersonalKayawan"),
                width: "100%",
                allowClear: true,
            });

            $select.val(null).trigger("change");

            $select.off("change").on("change", () => {
                const selected = $select.select2("data")[0];
                if (!selected) return;

                this.dataPersonalKaryawan.kd_kecamatan_tinggal =
                    selected.data.kd_kecamatan;
                this.dataPersonalKaryawan.kd_kota_kab_tinggal =
                    selected.data.kd_kota_kabupaten;
                this.dataPersonalKaryawan.kd_provinsi_tinggal =
                    selected.data.kota_kabupaten.kd_provinsi;
            });
        },
        setLokasiIfExist() {
            if (
                !this.dataPersonalKaryawan.kd_provinsi_lahir ||
                !this.dataPersonalKaryawan.kd_kota_kab_lahir ||
                !this.dataPersonalKaryawan.kd_kecamatan_lahir
            ) {
                $("#select_lokasi_lahir").val(null).trigger("change");
                return;
            }

            const selected = this.dataKecamatan.find(
                (kec) =>
                    kec.kd_kecamatan ===
                        this.dataPersonalKaryawan.kd_kecamatan_lahir &&
                    kec.kd_kota_kabupaten ===
                        this.dataPersonalKaryawan.kd_kota_kab_lahir &&
                    kec.kota_kabupaten.kd_provinsi ===
                        this.dataPersonalKaryawan.kd_provinsi_lahir
            );

            if (!selected) {
                $("#select_lokasi_lahir").val(null).trigger("change");
                return;
            }

            const option = new Option(
                `${selected.kota_kabupaten.provinsi.nama_provinsi} / ${selected.kota_kabupaten.nama_kota_kabupaten} / ${selected.nama_kecamatan}`,
                selected.kd_kecamatan,
                true,
                true
            );

            $("#select_lokasi_lahir").append(option).trigger("change");
        },
        setLokasiTinggalIfExist() {
            if (
                !this.dataPersonalKaryawan.kd_provinsi_tinggal ||
                !this.dataPersonalKaryawan.kd_kota_kab_tinggal ||
                !this.dataPersonalKaryawan.kd_kecamatan_tinggal
            ) {
                $("#select_lokasi_tinggal").val(null).trigger("change");
                return;
            }

            const selected = this.dataKecamatan.find(
                (kec) =>
                    kec.kd_kecamatan ===
                        this.dataPersonalKaryawan.kd_kecamatan_tinggal &&
                    kec.kd_kota_kabupaten ===
                        this.dataPersonalKaryawan.kd_kota_kab_tinggal &&
                    kec.kota_kabupaten.kd_provinsi ===
                        this.dataPersonalKaryawan.kd_provinsi_tinggal
            );

            if (!selected) return;

            const option = new Option(
                `${selected.kota_kabupaten.provinsi.nama_provinsi} / ${selected.kota_kabupaten.nama_kota_kabupaten} / ${selected.nama_kecamatan}`,
                selected.kd_kecamatan,
                true,
                true
            );

            $("#select_lokasi_tinggal").append(option).trigger("change");
        },
        setAgamaIfExist() {
            if (!this.dataPersonalKaryawan.nama_agama) return;

            const found = this.dataReligion.find(
                (r) =>
                    r.nama_agama.toLowerCase() ===
                    this.dataPersonalKaryawan.nama_agama.toLowerCase()
            );

            console.log("found", found);

            if (found) {
                this.dataPersonalKaryawan.kd_agama = found.kd_agama;
            }
        },
        onToggleAlamatTinggal() {
            if (this.isAlamatTinggalSama) {
                console.log("dmalm", this.dataPersonalKaryawan);

                this.dataPersonalKaryawan.kd_provinsi_tinggal =
                    this.dataPersonalKaryawan.kd_provinsi_lahir;
                this.dataPersonalKaryawan.kd_kota_kab_tinggal =
                    this.dataPersonalKaryawan.kd_kota_kab_lahir;
                this.dataPersonalKaryawan.kd_kecamatan_tinggal =
                    this.dataPersonalKaryawan.kd_kecamatan_lahir;

                this.dataPersonalKaryawan.alamat_tinggal =
                    this.dataPersonalKaryawan.alamat_lahir;

                this.$nextTick(() => {
                    this.setLokasiTinggalIfExist();
                });
            } else {
                this.dataPersonalKaryawan.kd_provinsi_tinggal = "";
                this.dataPersonalKaryawan.kd_kota_kab_tinggal = "";
                this.dataPersonalKaryawan.kd_kecamatan_tinggal = "";
                this.dataPersonalKaryawan.alamat_tinggal = "";

                $("#select_lokasi_tinggal").val(null).trigger("change");
            }
        },
        onInputKtp(e) {
            let value = e.target.value;

            value = value.replace(/\D/g, "");

            if (value.length > 16) {
                value = value.slice(0, 16);
            }

            this.dataPersonalKaryawan.no_ktp = value;
            this.errorKtp = "";
        },
        async kecamatan() {
            try {
                const data = await getAllDataKecamatan();
                this.dataKecamatan = data || [];

                this.$nextTick(() => {
                    this.setLokasiIfExist();
                });
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
</style>
