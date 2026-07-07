export default {
    name: 'Uploader',
    props: {
        loading: {
            type: Boolean,
            default: false,
        },
    },
    emits: ['submit-comment'],
    data() {
        return {
            selectedFiles: [],
            showAttachmentArea: false,
        };
    },
    computed: {
        commentData() {
            return this.$store ? this.$store.getters.commentData : {};
        }
    },
    mounted(){
        
    },
    methods: {
        toggleAttachmentArea() {
            this.showAttachmentArea = !this.showAttachmentArea;
        },
        handleBrowseClick() {
            const input = this.$refs ? this.$refs.attachmentInput : null;
            if (input) {
                input.click();
            }
        },
        handleFileChange(event) {
            const files = event?.target?.files;
            if (!files || !files.length) {
                this.clearAttachments(true);
                return;
            }

            this.revokeObjectURLs();
            this.selectedFiles = Array.from(files).map(file => ({
                file,                       // ✅ FULL File object
                name: file.name,
                size: file.size,
                type: file.type,
                objectURL: URL.createObjectURL(file) // optional (preview)
            }));


        },
        revokeObjectURLs(files = this.selectedFiles) {
            if (!Array.isArray(files) || !files.length) {
                return;
            }

            files.forEach((item) => {
                if (item && item.objectURL) {
                    URL.revokeObjectURL(item.objectURL);
                }
            });
        },
        clearAttachments(preserveAttachmentArea = false) {
            this.revokeObjectURLs();
            this.selectedFiles = [];

            if (this.$refs && this.$refs.attachmentInput) {
                this.$refs.attachmentInput.value = '';
            }

            if (!preserveAttachmentArea) {
                this.showAttachmentArea = false;
            }
        },
        emitSubmit() {
            this.$emit('submit-comment', this.selectedFiles);
        },
        formatFileSize(bytes) {
            if (typeof bytes !== 'number') {
                return '';
            }

            const units = ['KB', 'MB', 'GB'];
            let size = bytes / 1024;
            let unitIndex = 0;

            while (size >= 1024 && unitIndex < units.length - 1) {
                size /= 1024;
                unitIndex += 1;
            }

            return `${size.toFixed(1)} ${units[unitIndex]}`;
        },
        
    },
    beforeDestroy() {
        this.revokeObjectURLs();
    },
    beforeUnmount() {
        this.revokeObjectURLs();
    },
    watch: {
        commentData(value) {
            if (typeof value === 'string') {
                const trimmed = value.trim();
                if (!trimmed && this.$store) {
                    this.$store.commit('SET_COMMENT_DATA', '');
                }
                return;
            }

            // if (value && typeof value === 'object') {
            //     console.log('Uploader received comment data', {
            //         quoteId: value.quoteId || '',
            //         quoteAccount: value.quoteAccount || ''
            //     });
            // }
        }
    },
    template: /* html */ `
    <div>

        <div class=" form-group">
           

            <div 
                class="th-comment-attachment" 
                id="comment-attachment-area" 
                v-show="showAttachmentArea"
                :class="{
                  'v-enter-active': showAttachmentArea,
                  'v-leave-active': !showAttachmentArea
                }"
            >
                <div class="th-comment-attachment__dropzone">
                    <input ref="attachmentInput" type="file" multiple
                        class="th-comment-attachment__input"
                        @change="handleFileChange" />
                    <span class="th-comment-attachment__icon" aria-hidden="true">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </span>
                    <p class="th-comment-attachment__title">Drag & Drop your files here</p>
                    <p class="th-comment-attachment__subtitle">or</p>
                    <button type="button"
                        class="th-btn bg-gray text-black th-comment-attachment__browse"
                        @click.prevent="handleBrowseClick">
                        Browse
                    </button>
                </div>
                <ul class="th-comment-attachment__files" v-if="selectedFiles.length">
                    <li v-for="file in selectedFiles" :key="file.name + file.size" class="th-comment-attachment__file">
                        <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                        <span class="th-comment-attachment__file-name">{{ file.name }}</span>
                        <span class="th-comment-attachment__file-size">{{ formatFileSize(file.size) }}</span>
                    </li>
                </ul>
            </div>

            <div class="d-flex gap-2 mt-5">
                <button type="button"
                    class="text-capitalize bg-black text-white w-100 th-attachment-toggle"
                    @click.prevent="toggleAttachmentArea"
                    :aria-expanded="showAttachmentArea ? 'true' : 'false'"
                    aria-controls="comment-attachment-area">
                    <i class="fa-solid fa-paperclip"></i>
                    {{ showAttachmentArea ? 'Hide' : 'Attachment' }}
                </button>
                <button
                    type="button"
                    class="th-btn text-capitalize bg-gray text-black w-100"
                    @click="emitSubmit"
                    :disabled="loading">
                    <span
                        v-if="loading"
                        class="spinner-border spinner-border-sm me-2"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    <span>Comment</span>
                    <i class="fa-solid fa-arrow-right ms-1" v-if="!loading"></i>
                </button>
            </div>
        </div>

    </div>
    `
}