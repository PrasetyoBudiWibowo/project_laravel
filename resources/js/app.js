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

const app = createApp({});

app.component('edit-user', editUser);
app.component('user-register', userRegister);
app.component('sidebar-utama', sidebarUtama);

app.use(Antd)

app.mount('#app');