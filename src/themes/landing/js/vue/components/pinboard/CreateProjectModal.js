export default {
    name: 'CreateProjectModal',
    props: {
        show: {
            type: Boolean,
            default: false,
        },
        loggedInUser: {
            type: Object,
            default: () => ({}),
        },
        customer: {
            type: Object,
            default: () => ({}),
        },
        loading: {
            type: Boolean,
            default: false,
        },
        errorMessage: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            newProjectName: '',
        };
    },
    watch: {
        show(visible) {
            if (!visible) {
                this.newProjectName = '';
                return;
            }

            this.$nextTick(() => {
                const nameInput = this.$el && this.$el.querySelector('#new-project-name');
                if (nameInput) {
                    nameInput.classList.remove('is-invalid');
                    nameInput.focus();
                }
            });
        },
    },
    methods: {
        handleCloseCreateNewProjectModal() {
            this.newProjectName = '';
            this.$emit('close');
        },
        async handleCreateNewProjectClick(event) {
            event.preventDefault();

            const name = (this.newProjectName || '').trim();
            const nameInput = this.$el && this.$el.querySelector('#new-project-name');
            if (!name) {
                if (nameInput) nameInput.classList.add('is-invalid');
                return;
            }
            if (nameInput) nameInput.classList.remove('is-invalid');

            const payload = {
                job_title: name,
                customer_id: this.customer?.customer_id || null,
                user_id: this.loggedInUser?.user_id || null,
                form_type: null,
                pinboard_items: [],
            };

            await this.$emit('create-project', payload);
        },
    },
    template: 
    /* html */ 
    `
    <div v-if="show" class="modal fade backdrop-static show d-block" id="createNewProjectModal"
        tabindex="-1" aria-labelledby="createNewProjectModalLabel" data-bs-backdrop="false"
        style="position: fixed; inset: 0; background-color: rgba(0,0,0,0.5); z-index: 1040;" aria-modal="true"
        role="dialog" ref="createNewProjectModal">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-custom-width">
            <div class="modal-content px-80 py-60">

                <!-- Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="createNewProjectModalLabel">
                        Create New Project
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        @click="handleCloseCreateNewProjectModal"></button>
                </div>

                <!-- Body -->
                <div class="modal-body" id="create-new-project-form-container">
                    <div class="gap-10">

                        <!-- Project Name -->
                        <div class="th-form-row">
                            <div class="th-field">
                                <div class="th-input-group d-flex align-items-center">
                                    <input type="text" id="new-project-name" class="form-control"
                                        placeholder="Enter your project name" v-model="newProjectName"
                                        @input="$event.target.classList.remove('is-invalid')" />
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="pt-20">
                            <button type="button" id="create-new-project-btn"
                                class="th-btn-primary text-capitalize w-100"
                                :class="{ 'disabled': loading }"
                                :disabled="loading"
                                @click.prevent="handleCreateNewProjectClick($event)">
                                Save Project and continue
                                <span v-if="loading" class="spinner-border spinner-border-sm ms-2" role="status" aria-hidden="true"></span>
                            </button>
                            <span class="invalid-feedback mt-2 d-block" v-if="errorMessage">{{ errorMessage }}</span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
    `,
}