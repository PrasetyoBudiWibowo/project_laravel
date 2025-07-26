require('./bootstrap');

import { createApp } from 'vue'
import editUser from './components/user/editUser.vue';

const app = createApp({
    data() {
        return {
            message: 'Hello Vue!'
        }
    },
    components: { editUser }
})

app.mount('#app')