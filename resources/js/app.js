import { createApp } from 'vue';
import '../css/app.css';
import Antd from 'ant-design-vue'
import 'ant-design-vue/dist/reset.css'
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import editUser from './components/user/editUser.vue';
import userRegister from './components/user/userRegister.vue';

const app = createApp({});

app.component('edit-user', editUser);
app.component('user-register', userRegister);

app.use(Antd)
app.mount('#app');