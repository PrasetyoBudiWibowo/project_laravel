<div id="layoutSidenav_nav" class="vh-100 d-flex flex-column">
    <div id="sidebarApp" class="flex-fill overflow-auto">
        <nav class="sb-sidenav accordion sb-sidenav-dark h-100" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <template v-for="(menu, index) in menus" :key="index">
                        <div v-if="menu.heading" class="sb-sidenav-menu-heading">@{{ menu.heading }}</div>

                        <a v-if="!menu.children" class="nav-link" :href="menu.route">
                            <div class="sb-nav-link-icon"><i :class="menu.icon"></i></div>
                            @{{ menu.label }}
                        </a>

                    <div v-else>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" :data-bs-target="'#collapse' + index" aria-expanded="false" :aria-controls="'collapse' + index">
                            <div class="sb-nav-link-icon"><i :class="menu.icon"></i></div>
                            @{{ menu.label }}
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" :id="'collapse' + index" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav accordion" :id="'nested' + index">
                                <a v-for="(child, cIndex) in menu.children" :key="cIndex" class="nav-link" :href="child.route">
                                    <i class="fas fa-circle me-2" style="font-size: 0.6rem;"></i> @{{ child.label }}
                                </a>
                            </nav>
                        </div>
                    </template>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                Start Bootstrap
            </div>
        </nav>
    </div>

</div>
<script src="{{ asset('js/layouts/sidebar.js') }}"></script>