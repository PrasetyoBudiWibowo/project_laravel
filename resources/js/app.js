require('./bootstrap');

import { createApp } from 'vue';
import '../css/app.css';
import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/reset.css'
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import editUser from './components/user/editUser.vue';
import userRegister from './components/user/userRegister.vue';
import sidebarUtama from './components/layouts/sidebarUtama.vue';
import provinsi from './components/wilayah/provinsi.vue';
import kotaKabupaten from './components/wilayah/kotaKabupaten.vue';
import kecamatan from './components/wilayah/kecamatan.vue';
import module from './components/module/setting/module.vue'

const app = createApp({});

app.component('edit-user', editUser);
app.component('user-register', userRegister);
app.component('sidebar-utama', sidebarUtama);
app.component('provinsi', provinsi);
app.component('kota-kabupaten', kotaKabupaten);
app.component('kecamatan', kecamatan);
app.component('module', module);

app.use(Antd)

app.mount('#app');