
<template>
    <div class="th-pinboard-bottom">
        <div v-show="loggedInUser">
            <div class="th-pinboard-lower" id="th-pinboard-user" v-show="showAddComment">
                <div class="th-offcanvas-containr">
                    <div class="th-add-comment-panel">
                        <div class="th-add-comment-panel-header">
                            <div class="th-add-comment-panel-title">Add Comment</div>
                            <a href="javascript:void(0)" class="th-add-comment-panel-collapse"
                                @click.prevent="showAddComment = false">
                                &mdash; Collapse
                            </a>
                        </div>

                        <textarea v-model="comment"
                            :style="fb.errors.addCommentItemToPinboard ? { border: '1px solid red' } : {}"
                            @input="clearError('addCommentItemToPinboard')" ref="addCommentTextarea"
                            class="comment-box th-offcanvas-comment-box th-off-large-commentbox th-add-comment-textarea"
                            placeholder="Add A Comment"></textarea>

                        <div class="th-doc-actions d-flex flex-column th-add-comment-actions">
                            <div class="d-flex w-100 th-add-comment-preview-row" v-if="commentFiles.length">
                                <div v-for="(file, idx) in commentFiles" :key="file.tmp_name || idx" class="d-flex"
                                    style="width: 100px; height: 100px; background-color: #f0f0f0; position: relative; overflow: hidden;">
                                    <span class="remove-btn" :id="'removeBtn-' + idx"
                                        @click="removeCommentImage(file, idx)"
                                        style="position: absolute; top: 0px; right: 0px; z-index: 5; background: rgba(207, 30, 30, 0.9); padding: 2px 4px; border-radius: 12px; cursor: pointer;"><i
                                            class="fa-solid fa-xmark"></i></span>
                                    <img :src="file.objectURL" alt="Image" :title="file.name"
                                        style="width: 100%; height: 100%; object-fit: cover;" />
                                </div>
                            </div>

                            <div class="d-flex justify-content-between w-100 th-add-comment-bottom-row">
                                <label class="th-add-comment-upload-label th-btn-gray text-capitalize mr-10"
                                    style="cursor:pointer; margin-bottom:0;">
                                    Upload Image +
                                    <input type="file" accept="image/*" style="display:none;"
                                        @change="uploadCommentImage($event)" />
                                </label>

                                <a id="add-comment-button"
                                    class="th-add-comment-submit-btn th-btn-primary text-capitalize"
                                    @click.prevent="submitComment">
                                    Add To Pinboard
                                </a>

                                <a id="update-pinboard-button" class="th-btn-gray text-capitalize border"
                                    style="display: none;">
                                    <span class="mr-5">Update Pinboard</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="th-pinboard-guest" class="th-mt-40">
            <!-- <start> project name and email input field -->
            <div class="d-flex flex-column gap-2" v-if="!loggedInUser">
                <!-- project name input field -->
                <div class="form-group">
                    <input type="text" class="form-control" id="project-name" name="project-name"
                        placeholder="Project Name" v-model="pinboard.job_title"
                        :class="{ 'is-invalid': fb.errors.job_title }">
                    <span class="invalid-feedback" v-if="fb.errors.job_title">{{ fb.errors.job_title
                        }}</span>
                </div>
                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email Address" id="email"
                        v-model="customer.email" :class="{ 'is-invalid': fb.errors.email }">
                    <span class="invalid-feedback" v-if="fb.errors.email">{{ fb.errors.email }}</span>
                </div>
            </div>
            <!-- <end> project name and email input field -->
            <div class="">
                <div class="d-flex flex-column gap-2 pinboard-offcanvas-footer-btns" v-if="loggedInUser">

                    <button v-show="loggedInUser" v-if="!showAddComment" type="button"
                        class="th-add-comment-toggle-btn mb-50" @click.prevent="showAddComment = true">
                        <span class="th-add-comment-toggle-plus">+</span>
                        <span class="th-add-comment-toggle-text">Add Comment</span>
                    </button>

                    <input ref="cameraCaptureInput" type="file" accept="image/*" capture="environment" class="d-none"
                        @change="onCameraCaptureChange" />
                    <button v-show="loggedInUser" type="button" class="th-add-comment-toggle-btn mb-50"
                        @click.prevent="triggerCameraCapture">
                        <span class="th-add-comment-toggle-plus"><i class="fa-solid fa-image"></i></span>
                        <span class="th-add-comment-toggle-text">Add Image</span>
                    </button>

                    <a href="/account/virtual-pinboard" class="th-btn-primary text-capitalize mb-10 border w-100"
                        id="pinboard-link">
                        <span class="mr-5">Manage Pinboard</span>
                    </a>
                    <!-- <a href="/" class="th-btn-gray text-capitalize mr-10 border w-100" id="pinboard-browse-link">
            <span class="mr-5">Continue Browsing</span>
        </a> -->
                    <button type="button"
                        class="text-reset th-btn-gray text-capitalize mr-10 border w-100 text-decoration-none"
                        data-bs-dismiss="offcanvas" aria-label="Close">
                        Continue Browsing
                    </button>


                </div>
                <div v-else>
                    <button type="button" id="create-new-project-button"
                        class="th-btn-primary text-capitalize w-100 mb-2"
                        :class="{ 'disabled': fb.loading.createPinboard, 'is-invalid': fb.errors.createPinboard }"
                        data-bs-toggle="modal" data-bs-target="#guestSignupModal" :disabled="fb.loading.createPinboard"
                        @click="checkAndCreateTemporaryProject()">
                        <span class="mr-5" id="create-new-project-button-text">Save Project and
                            Continue</span>
                        <span v-if="fb.loading.createPinboard" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                    </button>
                    <span class="invalid-feedback" v-if="fb.errors.createPinboard">{{
                        fb.errors.createPinboard }}</span>
                </div>
            </div>
        </div>
    </div>
</template>