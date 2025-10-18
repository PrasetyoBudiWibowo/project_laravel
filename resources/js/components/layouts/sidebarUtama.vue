<template>
    <div id="sidebarApp">
        <ul class="nav flex-column">
            <template
                v-for="(menu, index) in filteredMenus"
                :key="menu.label || index"
            >
                <!-- Heading -->
                <li v-if="menu.heading" class="nav-item nav-heading">
                    <span>{{ menu.heading }}</span>
                </li>

                <!-- Menu dengan children -->
                <li v-else-if="menu.children" class="nav-item">
                    <a
                        href="#"
                        class="nav-link"
                        @click.prevent="toggleCollapse(index)"
                    >
                        <i :class="menu.icon || ''"></i>
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

                    <!-- Anak-anak menu -->
                    <ul
                        v-show="openCollapseIndex === index"
                        class="nav flex-column ms-3"
                    >
                        <template
                            v-for="(child, cIndex) in menu.children"
                            :key="child.label || cIndex"
                        >
                            <li v-if="child.children" class="nav-item">
                                <a
                                    href="#"
                                    class="nav-link"
                                    @click.prevent="
                                        toggleSubCollapse(`${index}-${cIndex}`)
                                    "
                                >
                                    <i :class="child.icon || ''"></i>
                                    <span class="ms-2">{{ child.label }}</span>
                                    <i
                                        :class="[
                                            'ms-auto',
                                            subCollapseIndex ===
                                            `${index}-${cIndex}`
                                                ? 'fas fa-chevron-down'
                                                : 'fas fa-chevron-right',
                                        ]"
                                    ></i>
                                </a>

                                <ul
                                    v-show="
                                        subCollapseIndex ===
                                        `${index}-${cIndex}`
                                    "
                                    class="nav flex-column ms-3"
                                >
                                    <li
                                        v-for="(
                                            subChild, scIndex
                                        ) in child.children"
                                        :key="subChild.label || scIndex"
                                        class="nav-item"
                                    >
                                        <a
                                            :href="subChild.route"
                                            class="nav-link"
                                            :class="{
                                                active: isActiveRoute(
                                                    subChild.route
                                                ),
                                            }"
                                        >
                                            <i :class="subChild.icon || ''"></i>
                                            <span class="ms-2">{{
                                                subChild.label
                                            }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li v-else class="nav-item">
                                <a
                                    :href="child.route"
                                    class="nav-link"
                                    :class="{
                                        active: isActiveRoute(child.route),
                                    }"
                                >
                                    <i :class="child.icon || ''"></i>
                                    <span class="ms-2">{{ child.label }}</span>
                                </a>
                            </li>
                        </template>
                    </ul>
                </li>

                <!-- Menu tanpa children -->
                <li v-else class="nav-item">
                    <a
                        :href="menu.route"
                        class="nav-link"
                        :class="{ active: isActiveRoute(menu.route) }"
                    >
                        <i :class="menu.icon || ''"></i>
                        <span class="ms-2">{{ menu.label }}</span>
                    </a>
                </li>
            </template>
        </ul>
    </div>

    <loadingData :visible="loadingMenu" message="Loading" />
</template>

<script>
import loadingData from "../loading/loadingData.vue";

export default {
    components: { loadingData },
    name: "SidebarUtama",
    data() {
        return {
            openCollapseIndex: null,
            subCollapseIndex: null,
            currentPath: null,
            currentUrl: null,
            listModule: [],
            dataUser: window.userData,

            loadingMenu: true,

            listMenuModule: [],
            listAksesModule: [],
            menusStatic: [
                { heading: "Core" },
                {
                    label: "Dashboard",
                    icon: "fas fa-tachometer-alt",
                    route: "/welcome",
                },
                { heading: "Interface" },
                {
                    label: "Module",
                    icon: "fa-solid fa-book",
                    children: [],
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
                            route: "/wilayah/kota-kabupaten",
                        },
                        {
                            label: "Kecamatan",
                            icon: "fa-regular fa-circle",
                            route: "/wilayah/kecamatan",
                        },
                    ],
                    isSuperAdminOnly: true,
                },
                {
                    label: "Setting",
                    icon: "fa-solid fa-gear",
                    children: [
                        {
                            label: "List",
                            icon: "fa-regular fa-circle",
                            route: "/module",
                        },
                        {
                            label: "Hak Akses Module",
                            icon: "fa-regular fa-circle",
                            route: "/akses-module-user",
                        },
                        {
                            label: "Menu",
                            icon: "fas fa-bars",
                            children: [
                                {
                                    label: "Menu Side Bar",
                                    icon: "fa-regular fa-circle",
                                    route: "/daftar-menu",
                                },
                                {
                                    label: "Akses Menu",
                                    icon: "fas fa-universal-access",
                                    route: "/hak-akses-menu",
                                },
                            ],
                        },
                    ],
                    isSuperAdminOnly: true,
                },
                {
                    label: "Ubah Setting",
                    icon: "fas fa-user-edit",
                    route: `/edit-user/${window.encryptedUserId || ""}`,
                },
            ],
        };
    },
    async mounted() {
        await this.module();
        await this.moduleWithMenu();
        await this.aksesModuleByUser();

        this.expandActiveMenu();

        // const currentUrl = window.location.href;
        // const currentPath = window.location.pathname;

        this.currentPath = window.location.pathname;

        this.loadingMenu = false;
    },
    computed: {
        filteredMenus() {
            const isSuperAdmin = window.userData?.level_user === "SUPER ADMIN";

            if (!this.currentPath || !this.listMenuModule) {
                return this.menusStatic;
            }

            const currentPath = this.currentPath;

            const moduleMatch = this.listMenuModule.find((it) =>
                currentPath.startsWith(it.url_module)
            );

            if (moduleMatch) {
                const filterMenuModule = this.listMenuModule.filter(
                    (it) => it.url_module === moduleMatch.url_module
                );

                let menuWithChildren;

                if (this.dataUser.level_user === "SUPER ADMIN") {
                    menuWithChildren = filterMenuModule.flatMap((mod) =>
                        mod.menu
                            .filter(
                                (menuItem) =>
                                    menuItem.children &&
                                    menuItem.children.length > 0
                            )
                            .map((menuItem) => ({
                                label: menuItem.nama_menu,
                                icon: "fa-regular fa-folder",
                                children: menuItem.children
                                    .slice()
                                    .sort(
                                        (a, b) =>
                                            (a.urutan || 0) - (b.urutan || 0)
                                    )
                                    .map((child) => ({
                                        label: child.nama_menu,
                                        route: child.url_menu,
                                    })),
                            }))
                    );
                } else {
                    const allowedMenus =
                        (this.dataUser.akses_menu || []).map(
                            (m) => m.menu?.nama_menu
                        ) || [];

                    menuWithChildren = filterMenuModule.flatMap((mod) =>
                        mod.menu
                            .filter(
                                (menuItem) =>
                                    menuItem.children &&
                                    menuItem.children.length > 0 &&
                                    menuItem.children.some((child) =>
                                        allowedMenus.includes(child.nama_menu)
                                    )
                            )
                            .map((menuItem) => ({
                                label: menuItem.nama_menu,
                                icon: "fa-regular fa-folder",
                                children: menuItem.children
                                    .filter((child) =>
                                        allowedMenus.includes(child.nama_menu)
                                    )
                                    .slice()
                                    .sort(
                                        (a, b) =>
                                            (a.urutan || 0) - (b.urutan || 0)
                                    )
                                    .map((child) => ({
                                        label: child.nama_menu,
                                        route: child.url_menu,
                                    })),
                            }))
                    );
                }

                if (menuWithChildren.length > 0) {
                    if (
                        this.dataUser.jumlah_akses_module === 1 &&
                        this.dataUser.level_user !== "SUPER ADMIN"
                    ) {
                        return [
                            { heading: "Core" },
                            {
                                label: `Dashboard ${moduleMatch.nama_module}`,
                                icon: "fas fa-tachometer-alt",
                                route: `${moduleMatch.url_module}`,
                            },
                            ...menuWithChildren,
                        ];
                    } else if (
                        this.dataUser.jumlah_akses_module > 1 &&
                        this.dataUser.level_user !== "SUPER ADMIN"
                    ) {
                        return [
                            { heading: "Core" },
                            {
                                label: "MENU UTAMA",
                                icon: "fa-solid fa-house",
                                route: "/welcome",
                            },
                            {
                                label: `Dashboard ${moduleMatch.nama_module}`,
                                icon: "fas fa-tachometer-alt",
                                route: `${moduleMatch.url_module}`,
                            },
                            ...menuWithChildren,
                        ];
                    } else if (this.dataUser.level_user === "SUPER ADMIN") {
                        return [
                            { heading: "Core" },
                            {
                                label: "MENU UTAMA",
                                icon: "fa-solid fa-house",
                                route: "/welcome",
                            },
                            {
                                label: `Dashboard ${moduleMatch.nama_module}`,
                                icon: "fas fa-tachometer-alt",
                                route: `${moduleMatch.url_module}`,
                            },
                            ...menuWithChildren,
                        ];
                    }

                    // return [
                    //     { heading: "Core" },
                    //     {
                    //         label: `Dashboard ${moduleMatch.nama_module}`,
                    //         icon: "fas fa-tachometer-alt",
                    //         route: `${moduleMatch.url_module}`,
                    //     },
                    //     ...menuWithChildren,
                    // ];
                }
            } else {
                return this.menusStatic
                    .map((menu) => {
                        if (menu.label === "Ubah Setting") {
                            return {
                                ...menu,
                                route: `/edit-user/${
                                    window.encryptedUserId || ""
                                }`,
                            };
                        }

                        if (menu.isSuperAdminOnly && !isSuperAdmin) return null;

                        if (menu.children) {
                            const filteredChildren = menu.children
                                .map((child) => {
                                    if (child.isSuperAdminOnly && !isSuperAdmin)
                                        return null;

                                    if (child.children) {
                                        const subFiltered =
                                            child.children.filter(
                                                (subChild) =>
                                                    !subChild.isSuperAdminOnly ||
                                                    isSuperAdmin
                                            );

                                        if (subFiltered.length === 0)
                                            return null;

                                        return {
                                            ...child,
                                            children: subFiltered,
                                        };
                                    }

                                    return child;
                                })
                                .filter(Boolean);

                            if (filteredChildren.length === 0) return null;

                            return {
                                ...menu,
                                children: filteredChildren,
                            };
                        }

                        return menu;
                    })
                    .filter(Boolean);
            }

            this.loadingMenu = false;
        },
    },
    methods: {
        toggleCollapse(index) {
            this.openCollapseIndex =
                this.openCollapseIndex === index ? null : index;
        },
        toggleSubCollapse(subKey) {
            this.subCollapseIndex =
                this.subCollapseIndex === subKey ? null : subKey;
        },
        isActiveRoute(route) {
            return window.location.pathname === route;
        },
        async aksesModuleByUser() {
            try {
                const data = await getModuleByUser();

                this.listAksesModule = data || [];
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan aksesModuleByUser: ${
                        err.statusText || err
                    }`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        async module() {
            try {
                await this.aksesModuleByUser();

                const data = await getAllModule();

                if (window.userData.level_user === "SUPER ADMIN") {
                    this.listModule = data.map((it) => ({
                        label: it.tampil_module,
                        icon: "fa-regular fa-circle",
                        route: it.url_module,
                    }));
                } else {
                    this.listModule = (this.listAksesModule || []).map(
                        (it) => ({
                            label: it.module.tampil_module,
                            icon: "fa-regular fa-circle",
                            route: it.module.url_module,
                        })
                    );
                }

                const moduleManager = this.menusStatic.find(
                    (m) => m.label === "Module"
                );

                if (moduleManager) {
                    moduleManager.children = this.listModule;
                }
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan module: ${err.statusText || err}`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        async moduleWithMenu() {
            try {
                const data = await getAllModuleWithMenu();

                this.listMenuModule = data || [];
            } catch (err) {
                Swal.fire({
                    icon: "error",
                    title: "Gagal",
                    text: `Terjadi kesalahan module: ${err.statusText || err}`,
                    confirmButtonText: "Tutup",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                });
            }
        },
        expandActiveMenu() {
            const currentPath = window.location.pathname;

            this.filteredMenus.forEach((menu, mIndex) => {
                if (menu.children) {
                    menu.children.forEach((child, cIndex) => {
                        if (child.route === currentPath) {
                            this.openCollapseIndex = mIndex;
                        } else if (child.children) {
                            child.children.forEach((subChild) => {
                                if (subChild.route === currentPath) {
                                    this.openCollapseIndex = mIndex;
                                    this.subCollapseIndex = `${mIndex}-${cIndex}`;
                                }
                            });
                        }
                    });
                } else if (menu.route === currentPath) {
                    this.openCollapseIndex = null;
                }
            });
        },
    },
};
</script>

<style>
.nav-link.active {
    font-weight: bold;
}
</style>
