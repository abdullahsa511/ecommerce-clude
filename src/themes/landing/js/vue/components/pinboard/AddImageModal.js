export default {
    name: 'AddImageModal',
    props: {
        cameraImagePreview: {
            type: String,
            default: '',
        },
        addImageTitle: {
            type: String,
            default: '',
        },
        addImageComment: {
            type: String,
            default: '',
        },
        addImageModalError: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        onTitleInput(event) {
            this.$emit('update-title', event.target.value);
        },
        onCommentInput(event) {
            this.$emit('update-comment', event.target.value);
        },
    },
    template: /* html */ `
        <div
            class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3"
            style="z-index: 1060; background: rgba(0,0,0,0.45);"
            @click.self="$emit('cancel')"
        >
            <div class="th-add-image-modal bg-white rounded shadow-sm p-3 w-100" style="max-width: 400px;" @click.stop>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Add Reference Image</h6>
                    <button type="button" class="btn-close" aria-label="Close" @click="$emit('cancel')"></button>
                </div>
                <div v-if="cameraImagePreview" class="text-center mb-3">
                    <img :src="cameraImagePreview" alt="" class="img-fluid rounded" style="max-height: 200px; object-fit: contain;" />
                </div>
                <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Title</label>
                    <input
                        type="text"
                        class="form-control form-control-sm"
                        :value="addImageTitle"
                        placeholder="e.g. Boardroom layout, fabric inspiration, site photo"
                        @input="onTitleInput"
                    />
                </div>
                <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Image Notes</label>
                    <textarea
                        class="form-control form-control-sm"
                        rows="2"
                        :value="addImageComment"
                        placeholder="Add context, specific dimensions, or material requirements."
                        @input="onCommentInput"
                    ></textarea>
                </div>
                <p v-if="addImageModalError" class="text-danger small mb-2">Add a title or a note (or both).</p>
                <div class="d-flex justify-content-end gap-2 mt-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" @click="$emit('cancel')">Cancel</button>
                    <button type="button" class="btn btn-sm th-btn-primary text-capitalize" @click="$emit('confirm')">Save to Pinboard</button>
                </div>
            </div>
        </div>
    `,
};
