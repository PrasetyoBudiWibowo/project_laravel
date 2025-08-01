<template>
    <div id="sidebarApp">
        <ul class="nav flex-column">
            <template v-for="(menu, index) in filteredMenus" :key="index">
                <li v-if="menu.heading" class="nav-item nav-heading">
                    <span>{{ menu.heading }}</span>
                </li>

                <li v-else-if="menu.children" class="nav-item">
                    <a
                        href="#"
                        class="nav-link"
                        @click.prevent="toggleCollapse(index)"
                    >
                        <i :class="menu.icon"></i>
                        <span class="ms-2">{{ menu.label }}</span>
                        <i
                            :class="[
                                'ms-auto',
                                openCollapseIndex === index
                                    ? 'fas fa-chevron-down'
                                    : 'fas fa-chevron-right',
                            ]"
                        ></i>
                    </a>
                    <ul
                        v-show="openCollapseIndex === index"
                        class="nav flex-column ms-3"
                    >
                        <li
                            v-for="(child, cIndex) in menu.children"
                            :key="cIndex"
                            class="nav-item"
                        >
                            <a :href="child.route" class="nav-link">
                                <i :class="child.icon"></i>
                                <span class="ms-2">{{ child.label }}</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li v-else class="nav-item">
                    <a :href="menu.route" class="nav-link">
                        <i :class="menu.icon"></i>
                        <span class="ms-2">{{ menu.label }}</span>
                    </a>
                </li>
            </template>
        </ul>
    </div>
</template>

<script>
export default {
    name: "SidebarUtama",
    data() {
        return {
            openCollapseIndex: null,
            menusStatic: [
                { heading: "Core" },
                {
                    label: "Dashboard",
                    icon: "fas fa-tachometer-alt",
                    route: "/welcome",
                },
                { heading: "Interface" },
                {
                    label: "Module Manager",
                    icon: "fas fa-puzzle-piece",
                    route: "/modules",
                    isSuperAdminOnly: true,
                },
                {
                    label: "Pages",
                    icon: "fas fa-book-open",
                    children: [
                        { label: "Register", route: "/register" },
                        {
                            label: "User Register",
                            route: "/user/user-register",
                        },
                    ],
                    isSuperAdminOnly: true,
                },
                {
                    label: "Wilayah",
                    icon: "fa-solid fa-location-dot",
                    children: [
                        {
                            label: "Provinsi",
                            icon: "fa-regular fa-circle",
                            route: "/wilayah/provinsi",
                        },
                        {
                            label: "Kota/Kabupten",
                            icon: "fa-regular fa-circle",
                            route: "",
                        },
                        {
                            label: "Kecamatan",
                            icon: "fa-regular fa-circle",
                            route: "",
                        },
                    ],
                },
                {
                    label: "Ubah Setting",
                    icon: "fas fa-user-edit",
                    route: `/edit-user/${window.encryptedUserId || ""}`,
                },
            ],
        };
    },
    computed: {
        filteredMenus() {
            const isSuperAdmin = window.userData?.level_user === "SUPER ADMIN";

            let menus = this.menusStatic.map((menu) => {
                if (menu.label === "Ubah Setting") {
                    return {
                        ...menu,
                        route: `/edit-user/${window.encryptedUserId || ""}`,
                    };
                }
                return menu;
            });

            menus = menus
                .map((menu) => {
                    if (menu.isSuperAdminOnly && !isSuperAdmin) return null;

                    if (menu.children) {
                        const filteredChildren = menu.children.filter(
                            (child) => !child.isSuperAdminOnly || isSuperAdmin
                        );
                        if (filteredChildren.length === 0) return null;
                        return {
                            ...menu,
                            children: filteredChildren,
                        };
                    }
                    return menu;
                })
                .filter(Boolean);

            return menus;
        },
    },
    methods: {
        toggleCollapse(index) {
            this.openCollapseIndex =
                this.openCollapseIndex === index ? null : index;
        },
    },
};
</script>
