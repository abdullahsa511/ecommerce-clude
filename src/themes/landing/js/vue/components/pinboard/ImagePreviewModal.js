export default {
    name: 'ImagePreviewModal',
    props: {
        imageSrc: {
            type: String,
            required: true,
        },
        imageAlt: {
            type: String,
            default: '',
        },
    },
    mounted() {
        document.addEventListener('keydown', this.handleKeydown);
        document.body.style.overflow = 'hidden';
    },
    beforeDestroy() {
        document.removeEventListener('keydown', this.handleKeydown);
        document.body.style.overflow = '';
    },
    methods: {
        handleKeydown(event) {
            if (event && event.key === 'Escape') {
                this.$emit('close');
            }
        },
    },
    template: /* html */ `
        <div
            class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
            style="z-index: 1080; background: rgba(0,0,0,0.85);"
            @click="$emit('close')"
        >
            <button
                type="button"
                class="btn btn-sm btn-outline-light position-absolute"
                style="top: 24px; right: 24px; width: 36px; height: 36px; border-radius: 999px; padding: 0; font-size: 22px; line-height: 1;"
                @click.stop="$emit('close')"
                aria-label="Close image preview"
            >
                &times;
            </button>
            <img
                :src="imageSrc"
                :alt="imageAlt || 'Image preview'"
                style="max-width: calc(100vw - 160px); max-height: calc(100vh - 160px); object-fit: contain;"
                @click.stop
            />
        </div>
    `,
};
