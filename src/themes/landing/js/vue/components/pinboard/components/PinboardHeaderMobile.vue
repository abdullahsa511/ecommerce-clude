<script>
export default {
    data() {
        return {
            loggedInUser: false,
            showProjectDropdown: false,
            showCreateNewProjectModal: false,
            newProjectName: '',
            selectedProjectId: '',
            localFilter: {
                searchValue: '',
            },
        };
    },
    computed: {
        displayProjectTitle() {
            const sid = this.selectedProjectId;
            if (sid === '' || sid === null || sid === undefined) {
                return 'Project';
            }
            const p = this.projectMenuItems.find(
                (x) => String(x.pinboard_id) === String(sid)
            );
            return p && p.pinboard_name ? p.pinboard_name : 'Project';
        },
        projectMenuItems() {
            return this.$store.getters.projectItems;
        },
    },
    watch: {
        projectMenuItems: {
            handler(items) {
                if (!items || !items.length) {
                    return;
                }
                const sid = this.selectedProjectId;
                const inList = (id) =>
                    id !== null &&
                    id !== undefined &&
                    id !== '' &&
                    items.some((x) => String(x.pinboard_id) === String(id));
                if (inList(sid)) {
                    return;
                }
                const pbId = this.pinboard && this.pinboard.pinboard_id;
                if (inList(pbId)) {
                    this.selectedProjectId = pbId;
                    return;
                }
                this.selectedProjectId = items[0].pinboard_id;
            },
            immediate: true,
        },
        'pinboard.pinboard_id'(id) {
            const items = this.projectMenuItems;
            if (!items || !items.length || id === null || id === undefined || id === '') {
                return;
            }
            if (!items.some((x) => String(x.pinboard_id) === String(id))) {
                return;
            }
            this.selectedProjectId = id;
        },
    },
    methods: {
        changeProject(pinboardId) {
            this.selectedProjectId = pinboardId;
            this.showProjectDropdown = false;
            this.$store.dispatch('getProjectByPinboardId', pinboardId);
        },
        openNewProjectModal() {
            this.showProjectDropdown = false;
            this.showCreateNewProjectModal = true;
        },
    },
}
</script>
<template>
    <div class="offcanvas-header th-header-upper d-flex d-md-none flex-column gap-2 align-items-stretch">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="offcanvas-header th-header-lower mb-0 flex-grow-1 min-w-0">
                <h5 class="mb-0">Virtual Pinboard</h5>
            </div>
            <button type="button" class="btn btn-link p-1 border-0 text-body pinboard-offcanvas-close flex-shrink-0"
                data-bs-dismiss="offcanvas" aria-label="Close Virtual Pinboard">
                <i class="fa-solid fa-xmark fa-lg" aria-hidden="true"></i>
            </button>
        </div>
        <div class="d-flex pinboard-header-info w-100" v-if="loggedInUser && loggedInUser.email">
            <div
                class="pinboard-header-project-wrap pinboard-header-project-wrap--mobile text-start position-relative w-100">
                <div
                    class="pinboard-project-trigger-line d-inline-flex align-items-center gap-1 flex-wrap justify-content-start">
                    <span class="pinboard-project-name fw-bold">{{ displayProjectTitle }}</span>
                    <button type="button" class="pinboard-project-chevron-btn btn btn-link p-0 border-0 align-baseline"
                        :aria-expanded="showProjectDropdown ? 'true' : 'false'" aria-haspopup="true"
                        aria-label="Choose project" @click.stop="showProjectDropdown = !showProjectDropdown">
                        <i class="fa-solid fa-chevron-down pinboard-project-chevron"
                            :class="{ 'is-open': showProjectDropdown }"></i>
                    </button>
                </div>
                <div class="pinboard-project-email text-muted small">{{ loggedInUser.email }}</div>

                <div v-show="showProjectDropdown" class="pinboard-project-menu pinboard-project-menu--mobile"
                    @click.stop>
                    <button type="button" class="pinboard-project-menu-item pinboard-project-menu-create"
                        @click="openNewProjectModal()">
                        + Create New Project…
                    </button>
                    <div class="pinboard-project-menu-divider" aria-hidden="true"></div>
                    <button v-for="item in projectMenuItems" :key="'mob-' + item.pinboard_id" type="button"
                        class="pinboard-project-menu-item d-flex justify-content-between align-items-center gap-2"
                        :class="{ 'is-active': String(item.pinboard_id) === String(selectedProjectId) }"
                        @click="changeProject(item.pinboard_id)">
                        <span class="pinboard-project-menu-label text-truncate">{{ item.pinboard_name }}</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>