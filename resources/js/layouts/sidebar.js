import { createApp } from 'vue';

const sidebarApp = createApp({
    data() {
        return {
            menus: [
                {
                    heading: "Core",
                },
                {
                    label: "Dashboard",
                    icon: "fas fa-tachometer-alt",
                    route: "/welcome",
                },
                {
                    heading: "Interface",
                },
                {
                    label: "Pages",
                    icon: "fas fa-book-open",
                    children: [
                        {
                            label: "Register",
                            route: "/register",
                        },
                    ],
                    isSuperAdminOnly: true,
                },
                {
                    label: "Module Manager",
                    icon: "fas fa-puzzle-piece",
                    route: "/modules",
                    isSuperAdminOnly: true,
                },
                {
                    label: "Ubah Setting",
                    icon: "fas fa-user-edit",
                    route: `/edit-user/${window.encryptedUserId || ''}`,
                },
            ],
        };
    },
    mounted() {
        const isSuperAdmin = window.userData?.level_user === 'SUPER ADMIN';

        this.menus = this.menus.filter(menu => {
            return !menu.isSuperAdminOnly || isSuperAdmin;
        });
    },
});

sidebarApp.mount("#sidebarApp");
