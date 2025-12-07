require('./bootstrap');

import { createApp } from 'vue';
import '../css/app.css';
import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/reset.css'
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

import PrimeVue from 'primevue/config';
import Lara from '@primevue/themes/lara';

import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';

import editUser from './components/user/editUser.vue';
import register from './components/user/register.vue';
import userRegister from './components/user/userRegister.vue';
import sidebarUtama from './components/layouts/sidebarUtama.vue';
import provinsi from './components/wilayah/provinsi.vue';
import kotaKabupaten from './components/wilayah/kotaKabupaten.vue';
import kecamatan from './components/wilayah/kecamatan.vue';
import module from './components/module/setting/module.vue';
import aksesModule from './components/module/setting/aksesModule.vue';
import menuSidebar from './components/module/setting/menuSidebar.vue';
import hakAksesMenu from './components/module/setting/hakAksesMenu.vue';
import dasboardHrd from './components/module/hrd/dasboardHrd.vue';
import masterKaryawan from './components/module/hrd/masterData/karyawan/masterKaryawan.vue';
import editKaryawan from './components/module/hrd/masterData/karyawan/editKaryawan.vue';
import masterDivisi from './components/module/hrd/masterData/masterDivisi.vue';
import departement from './components/module/hrd/masterData/departement/departement.vue';
import posisi from './components/module/hrd/masterData/posisi/posisi.vue';
import masterJabatan from './components/module/hrd/masterData/jabatan/masterJabatan.vue';

const app = createApp({});

app.component('edit-user', editUser);
app.component('register', register);
app.component('user-register', userRegister);
app.component('sidebar-utama', sidebarUtama);
app.component('provinsi', provinsi);
app.component('kota-kabupaten', kotaKabupaten);
app.component('kecamatan', kecamatan);
app.component('module', module);
app.component('akses-module', aksesModule);
app.component('menu-sidebar', menuSidebar);
app.component('hak-akses-menu', hakAksesMenu);
app.component('dasboard-hrd', dasboardHrd);
app.component('master-karyawan', masterKaryawan);
app.component('edit-master-karyawan', editKaryawan);
app.component('master-divisi', masterDivisi);
app.component('master-departement', departement);
app.component('master-posisi', posisi);
app.component('master-jabatan', masterJabatan);

app.use(Antd)
app.use(PrimeVue, {
    theme: {
        preset: Lara, 
        options: {
            darkModeSelector: '.dark-mode'
        }
    }
});

app.use(ToastService);

app.component('DataTable', DataTable);
app.component('Column', Column);
app.component('Dialog', Dialog);
app.component('Toast', Toast);

app.mount('#app');