<template>
    <div v-if="showCreateNewProjectModal" class="modal fade backdrop-static show d-block" id="createNewProjectModal"
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
                                @click.prevent="handleCreateNewProjectClick($event)">
                                Save Project and continue
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</template>