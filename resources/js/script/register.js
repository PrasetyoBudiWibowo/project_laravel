import { createApp } from "vue";
import axios from "axios";

const register = createApp({
    data() {
        return {
            levels: [],
            selectedLevel: "",
            username: "",
            password: "",
        };
    },
    mounted() {
        const token = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        console.log('dsdasdafasf', token)
        axios.defaults.headers.common["X-CSRF-TOKEN"] = token;
        this.fetchLevels();
    },
    methods: {
        async fetchLevels() {
            this.levels = await getLevelUser();
            this.$nextTick(() => {
                defaultSelect2("#id_usr_level", "-- Pilih Level --");
            });

            $("#id_usr_level").on("change", (e) => {
                this.selectedLevel = e.target.value;
            });
        },
        confirmRegister() {
            Swal.fire({
                title: "Konfirmasi",
                text: "Pastikan Username dan Password Sudah Benar",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    this.register();
                }
            });
        },
        register() {
            let namaUser = this.username;
            let password = this.password;
            let selectedLevel = this.selectedLevel;

            let requireValue = [
                { value: namaUser, message: "user name tidak boleh kosong" },
                { value: password, message: "password tidak boleh kosong" },
                { value: selectedLevel, message: "Level user harus di isi" },
            ];
            if (!validasiBanyakInputan(requireValue)) return;

            let dataToSave = {
                id_usr_level: selectedLevel,
                nama_user: namaUser,
                password: password,
            };

            
        },
    },
});

register.mount("#register-app");
