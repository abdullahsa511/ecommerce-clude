import Editor from '../comment/Editor.js';
import managePinboardStore from '../../store/managePinboardStore.js';
import {
    executeRecaptcha,
    getRecaptchaConfig,
    getRecaptchaProjectAction,
    preloadRecaptcha,
} from '../../../recaptcha-v3.js';
export default {
    name: 'ProjectSubmissionModal',
    components: {
        Editor,
    },
    props: {
        customer: {
            type: Object,
            default: () => ({}),
        },
        pinboardTitle: {
            type: String,
            default: '',
        },
        pinboardItemCount: {
            type: [Number, String],
            default: 0,
        },
        pinboardId: {
            type: [Number, String],
            default: null,
        },
        pinboardUuid: {
            type: String,
            default: null,
        },
        loggedInUser: {
            type: Object,
            default: null,
        },
        showBookingModal: {
            type: Boolean,
            default: false,
        },
        showProjectSuccessMessage: {
            type: Boolean,
            default: true,
        },
        submissionType: {
            type: String,
            default: 'email',
        },
        page:  { type: String, default: "virtual_pinboard" },
    },
    data() {
        return {
            modalInstance: null,
            onHidden: null,
            additionalNotes: '',
            uploadedFiles: [],
            isDragOver: false,
            uploadError: '',
            message: '',
            isSubmitting: false,
            maxUploadFiles: 3,
            maxFileSizeBytes: 15 * 1024 * 1024,
            hasValidationError: false,
            recaptchaError: '',
        };
    },
    mounted() {
        if (this.recaptchaEnabled) {
            preloadRecaptcha(this.recaptchaSiteKey);
        }

        this.$nextTick(() => {
            if (!window.bootstrap || !this.$el) return;
            this.modalInstance = new bootstrap.Modal(this.$el, {
                backdrop: false,
            });
            this.onHidden = () => {
                this.$emit('close-project-submission');
            };
            this.$el.addEventListener('hidden.bs.modal', this.onHidden);
            this.modalInstance.show();
        });
    },
    beforeDestroy() {
        this.blurModalFocus();
        if (this.$el && this.onHidden) {
            this.$el.removeEventListener('hidden.bs.modal', this.onHidden);
        }
        if (this.modalInstance) {
            this.modalInstance.dispose();
            this.modalInstance = null;
        }
    },
    computed: {
        recaptchaSiteKey() {
            return getRecaptchaConfig().siteKey;
        },
        recaptchaEnabled() {
            return this.recaptchaSiteKey !== '';
        },
        projectInitial() {
            const title = String(this.pinboardTitle || '').trim();
            return title ? title.charAt(0).toUpperCase() : 'P';
        },
        displayProjectTitle() {
            return String(this.pinboardTitle || '').trim() || 'Project';
        },
        displayItemCount() {
            const count = Number(this.pinboardItemCount);
            if (!Number.isFinite(count) || count < 0) return 0;
            return count;
        },
        itemLabel() {
            return this.displayItemCount === 1 ? 'item' : 'items';
        },
    },
    methods: {
        blurModalFocus() {
            const ae = document.activeElement;
            if (ae && this.$el && typeof this.$el.contains === 'function' && this.$el.contains(ae)) {
                ae.blur();
            }
        },
        closeModal() {
            this.blurModalFocus();
            if (this.modalInstance) {
                this.modalInstance.hide();
                return;
            }
            this.$emit('close-project-submission');
        },
        triggerFilePicker() {
            if (this.$refs.projectFileInput) {
                this.$refs.projectFileInput.click();
            }
        },
        onDropzoneDragOver() {
            this.isDragOver = true;
        },
        onDropzoneDragLeave() {
            this.isDragOver = false;
        },
        onDropzoneDrop(event) {
            this.isDragOver = false;
            this.handleSelectedFiles(event.dataTransfer ? event.dataTransfer.files : null);
        },
        onFileInputChange(event) {
            this.handleSelectedFiles(event.target ? event.target.files : null);
            if (event.target) {
                event.target.value = '';
            }
        },
        handleSelectedFiles(fileList) {
            if (!fileList || !fileList.length) {
                return;
            }

            const remainingSlots = this.maxUploadFiles - this.uploadedFiles.length;
            if (remainingSlots <= 0) {
                this.uploadError = `You can upload up to ${this.maxUploadFiles} files.`;
                return;
            }

            const files = Array.from(fileList);
            const filesToAdd = [];
            let oversizedFiles = 0;
            let duplicateFiles = 0;

            files.forEach((file) => {
                if (filesToAdd.length >= remainingSlots) return;

                if (file.size > this.maxFileSizeBytes) {
                    oversizedFiles += 1;
                    return;
                }

                const isDuplicate = this.uploadedFiles.some(
                    (uploadedFile) => uploadedFile.name === file.name && uploadedFile.size === file.size,
                );
                if (isDuplicate) {
                    duplicateFiles += 1;
                    return;
                }

                filesToAdd.push({
                    id: `${Date.now()}-${Math.random().toString(36).slice(2, 8)}`,
                    name: file.name,
                    size: file.size,
                    extension: this.getFileExtension(file.name),
                    uploadedLabel: 'uploaded just now',
                    raw: file,
                });
            });

            this.uploadedFiles = [...this.uploadedFiles, ...filesToAdd];

            const ignoredForLimit = Math.max(
                0,
                files.length - filesToAdd.length - oversizedFiles - duplicateFiles,
            );
            const messages = [];
            if (oversizedFiles > 0) {
                messages.push(`Each file must be ${this.formatUploadLimit()} or less (${oversizedFiles} skipped).`);
            }
            if (ignoredForLimit > 0) {
                messages.push(`Only ${this.maxUploadFiles} files are allowed.`);
            }
            if (duplicateFiles > 0) {
                messages.push(`${duplicateFiles} duplicate file skipped.`);
            }

            this.uploadError = messages.join(' ');
        },
        removeUploadedFile(fileId) {
            this.uploadedFiles = this.uploadedFiles.filter((file) => file.id !== fileId);
            if (this.uploadedFiles.length < this.maxUploadFiles) {
                this.uploadError = '';
            }
        },
        getFileExtension(fileName) {
            const parts = fileName.split('.');
            if (parts.length < 2) return 'FILE';
            return parts[parts.length - 1].toUpperCase().slice(0, 4);
        },
        formatFileSize(fileSize) {
            if (fileSize < 1024 * 1024) {
                return `${Math.max(1, Math.round(fileSize / 1024))} KB`;
            }
            return `${(fileSize / (1024 * 1024)).toFixed(1)} MB`;
        },
        formatUploadLimit() {
            return `${Math.round(this.maxFileSizeBytes / (1024 * 1024))} MB`;
        },
        validateUploadsBeforeSubmit() {
            if (this.uploadedFiles.length > this.maxUploadFiles) {
                this.uploadError = `You can upload up to ${this.maxUploadFiles} files.`;
                return false;
            }

            const oversizedFile = this.uploadedFiles.find(
                (file) => Number(file?.size || 0) > this.maxFileSizeBytes,
            );
            if (oversizedFile) {
                this.uploadError = `Each file must be ${this.formatUploadLimit()} or less.`;
                return false;
            }

            return true;
        },
        onAdditionalNotesUpdate(content) {
            this.additionalNotes = content || '';
        },
        resolveEmail() {
            return String(
                this.customer?.email ||
                this.customer?.customer_email ||
                this.loggedInUser?.email ||
                '',
            ).trim();
        },
        async postFormData(url, payload) {
            const response = await fetch(url, {
                method: 'POST',
                body: payload,
            });
            const contentType = response?.headers?.get('content-type') || '';
            if (contentType.toLowerCase().includes('application/json')) {
                const result = await response.json();
                return {
                    ...result,
                    status: response.status,
                    success: response.ok ? result?.success !== false : false,
                };
            }
            if (response.status === 413) {
                return {
                    success: false,
                    status: 413,
                    message: `Each file must be ${this.formatUploadLimit()} or less.`,
                };
            }
            return {
                success: false,
                status: response.status,
                message: response.ok ? 'Unexpected server response' : 'Request failed',
            };
        },
        async submitProjectSubmission() {
            this.message = '';
            this.uploadError = '';
            this.recaptchaError = '';

            if (!this.pinboardId) {
                this.message = 'Pinboard not found';
                return;
            }

            const email = this.resolveEmail();
            if (!email) {
                this.message = 'Email not found for this request';
                return;
            }

            if (!this.validateUploadsBeforeSubmit()) {
                return;
            }

            const formData = new FormData();
            formData.append('pinboard_id', this.pinboardId);
            formData.append('email', email);
            formData.append('note', this.additionalNotes.trim());
            formData.append('submission_type', this.submissionType || 'email');

            this.isSubmitting = true;
            try {
                if (this.recaptchaEnabled) {
                    try {
                        const recaptchaToken = await executeRecaptcha(
                            this.recaptchaSiteKey,
                            getRecaptchaProjectAction(),
                        );
                        formData.append('g-recaptcha-response', recaptchaToken);
                    } catch (recaptchaErr) {
                        console.error('reCAPTCHA execute failed', recaptchaErr);
                        this.recaptchaError =
                            'reCAPTCHA verification failed. Please refresh the page and try again.';
                        return;
                    }
                }

                this.uploadedFiles.forEach((file) => {
                    if (file?.raw) formData.append('files[]', file.raw);
                });

                const result = await this.postFormData('/api/booking-email-service-requests', formData);
                if (result?.error || result?.success === false || result.status == 422) {
                    const errMsg = result?.message || result?.error || 'Request failed';
                    if (String(errMsg).toLowerCase().includes('recaptcha')) {
                        this.recaptchaError = errMsg;
                    } else {
                        this.message = errMsg;
                    }
                    return;
                }

               await managePinboardStore.commit('UPDATE_PINBOARD_STATUS', { pinboardId: this.pinboardId });

               if (this.page === 'manage_pinboard') {
                window.open(`/pinboards/email-confirmation/${this.pinboardUuid}`, '_blank');  
                this.$emit("submit-success");
                this.closeModal();   
               }else{
                window.location.href = `/pinboards/email-confirmation/${this.pinboardUuid}`;
               }
            //    window.location.href = redirectUrl;
                // this.$emit('submit-success', result);
                // this.closeModal();

            } catch (e) {
                this.message = e?.message || 'Request failed';
            } finally {
                this.isSubmitting = false;
            }
        },
        handleEditorUpdate(content) {
            this.additionalNotes = content;

            if (this.hasValidationError) {
                const trimmed = content ? content.trim() : '';
                if (trimmed.length) {
                    this.hasValidationError = false;
                }
            }
        },
    },
    template: /* html */ `
    <div
        class="modal fade th-pinboard-modal backdrop-static"
        id="pinboardBookingCalendarModal"
        tabindex="-1"
        aria-labelledby="exampleModalLongTitle"
        aria-hidden="true"
        data-bs-backdrop="false"
        style="position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:1070;"
    >
        <div class="pinboard-modal-container">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content modelBorderRadius">
                    <div class="modal-header">
                        <button
                            type="button"
                            class="btn-close"
                            aria-label="Close"
                            @click="closeModal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class=" th-project-submission-modal">
                            <h3 class="th-project-submission-title">Anything else we should know?</h3>
            

                            <div class="th-project-summary-card">
                                <div class="th-project-summary-thumb">{{ projectInitial }}</div>
                                <div class="th-project-summary-content">
                                    <h3 class="th-project-summary-name">{{ displayProjectTitle }}</h3>
                                    <p class="th-project-summary-meta">{{ displayItemCount }} {{ itemLabel }} </p>
                                </div>
                                <span class="th-project-summary-tag">Project</span>
                            </div>

                            <div class="th-submission-field">
                                <div class="th-submission-field-head">
                                    <label class="th-submission-label">Additional notes</label>
                                    <span class="th-submission-optional">Optional</span>
                                </div>
                                <Editor 
                                    ref="commentEditor"
                                    :has-error="hasValidationError"
                                    @update-content="handleEditorUpdate"
                                />
                            </div> 



                            <div class="th-submission-field">
                                <div class="th-submission-field-head">
                                    <label class="th-submission-label">Attachments</label>
                                    <span class="th-submission-optional">Optional · max 3 files · 15 MB each</span>
                                </div>
                                <div
                                    class="th-upload-dropzone"
                                    :class="{ 'is-drag-over': isDragOver }"
                                    role="button"
                                    tabindex="0"
                                    @click="triggerFilePicker"
                                    @keydown.enter.prevent="triggerFilePicker"
                                    @keydown.space.prevent="triggerFilePicker"
                                    @dragover.prevent="onDropzoneDragOver"
                                    @dragleave.prevent="onDropzoneDragLeave"
                                    @drop.prevent="onDropzoneDrop"
                                >
                                    <span class="th-upload-dropzone-icon">
                                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                                    </span>
                                    <p>Click to upload <span>or drag and drop</span></p>
                                    <small>.PDF, .PNG, .JPG, .DOCX, .CSV, .XLSX up to 15 MB each</small>
                                </div>
                                <input
                                    ref="projectFileInput"
                                    type="file"
                                    class="th-hidden-file-input"
                                    multiple
                                    @change="onFileInputChange"
                                />

                                <ul class="th-uploaded-files" v-if="uploadedFiles.length">
                                    <li v-for="file in uploadedFiles" :key="file.id">
                                        <span class="th-file-type">{{ file.extension }}</span>
                                        <div class="th-file-content">
                                            <strong>{{ file.name }}</strong>
                                            <small>{{ formatFileSize(file.size) }} · {{ file.uploadedLabel }}</small>
                                        </div>
                                        <button type="button" aria-label="Remove file" @click="removeUploadedFile(file.id)">×</button>
                                    </li>
                                </ul>
                                <p v-if="uploadError" class="th-upload-error">{{ uploadError }}</p>
                            </div>

                            <div class="th-submission-actions">
                                <button type="button" class="th-btn-cancel" @click="closeModal">Cancel</button>
                                <p v-if="recaptchaError" class="th-upload-error w-100 mb-2">{{ recaptchaError }}</p>
                                <button type="button" class="th-btn-submit" :disabled="isSubmitting" @click="submitProjectSubmission">
                                    <span
                                        v-if="isSubmitting"
                                        class="spinner-border spinner-border-sm me-2"
                                        role="status"
                                        aria-hidden="true"
                                    ></span>
                                    Submit enquiry
                                </button>
                            </div>
                            <p v-if="message" class="th-upload-error mt-2">{{ message }}</p>
                            <p v-if="recaptchaEnabled" class="small text-muted mt-2 mb-0">
                                This site is protected by reCAPTCHA and the Google
                                <a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Privacy Policy</a>
                                and
                                <a href="https://policies.google.com/terms" target="_blank" rel="noopener noreferrer">Terms of Service</a>
                                apply.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
`,
};