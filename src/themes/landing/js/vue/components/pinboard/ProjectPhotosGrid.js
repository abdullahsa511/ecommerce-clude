export default {
    name: 'ProjectPhotosGrid',
    props: {
        images: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            selectedImage: null,
        };
    },
    mounted() {
        document.addEventListener('keydown', this.handleModalKeydown);
    },
    beforeDestroy() {
        document.removeEventListener('keydown', this.handleModalKeydown);
    },
    methods: {
        getImageSource(image) {
            const source = image?.photo || '';
            const isBlobUrl = typeof source === 'string' && source.startsWith('blob:');
            if (isBlobUrl) {
                return source;
            }
            return image.photo;
        },
        openImageModal(image) {
            if (!image) return;
            this.selectedImage = image;
        },
        closeImageModal() {
            this.selectedImage = null;
        },
        currentImageIndex() {
            if (!this.selectedImage) return -1;
            return this.images.findIndex((img) => img === this.selectedImage);
        },
        showNextImage() {
            if (!this.selectedImage || !this.images.length) return;
            const currentIndex = this.currentImageIndex();
            const nextIndex = currentIndex === -1 ? 0 : (currentIndex + 1) % this.images.length;
            this.selectedImage = this.images[nextIndex];
        },
        showPrevImage() {
            if (!this.selectedImage || !this.images.length) return;
            const currentIndex = this.currentImageIndex();
            const prevIndex = currentIndex === -1
                ? 0
                : (currentIndex - 1 + this.images.length) % this.images.length;
            this.selectedImage = this.images[prevIndex];
        },
        handleModalKeydown(event) {
            if (!this.selectedImage || !event) return;
            if (event.key === 'Escape') {
                this.closeImageModal();
                return;
            }
            if (event.key === 'ArrowRight') {
                this.showNextImage();
                return;
            }
            if (event.key === 'ArrowLeft') {
                this.showPrevImage();
            }
        },
        selectedImageAltText() {
            if (!this.selectedImage) return 'Project photo';
            const selectedIndex = this.images.findIndex((img) => img === this.selectedImage);
            return this.selectedImage.description || `Project photo ${selectedIndex + 1}`;
        },
    },
    template: /* html */ `
        <div class="pinboard-project-photos">
            <h5 class="pinboard-project-photos-title">
                Project Photos
                <span>(taken on iPad camera)</span>
            </h5>

            <div v-if="images.length" class="pinboard-project-photos-grid">
                <div
                    v-for="(image, index) in images"
                    :key="image.photo + '-' + index"
                    class="pinboard-project-photos-item"
                >
                    <img
                        :src="getImageSource(image)"
                        :alt="image.description || ('Project photo ' + (index + 1))"
                        style="cursor: zoom-in;"
                        @click="openImageModal(image)"
                    />
                </div>
            </div>

            <div v-else class="text-muted small">
                No project photos yet.
            </div>

            <div
                v-if="selectedImage"
                class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                style="z-index: 1080; background: rgba(0,0,0,0.85);"
                @click="closeImageModal"
            >
                <button
                    type="button"
                    class="btn btn-sm btn-outline-light position-absolute"
                    style="top: 24px; right: 24px; width: 36px; height: 36px; border-radius: 999px; padding: 0; font-size: 22px; line-height: 1;"
                    @click.stop="closeImageModal"
                    aria-label="Close image preview"
                >
                    &times;
                </button>
                <button
                    v-if="images.length > 1"
                    type="button"
                    class="btn btn-sm btn-outline-light position-absolute"
                    style="left: 24px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 999px; padding: 0; font-size: 24px; line-height: 1;"
                    @click.stop="showPrevImage"
                    aria-label="Previous image"
                >
                    &#8249;
                </button>
                <button
                    v-if="images.length > 1"
                    type="button"
                    class="btn btn-sm btn-outline-light position-absolute"
                    style="right: 24px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 999px; padding: 0; font-size: 24px; line-height: 1;"
                    @click.stop="showNextImage"
                    aria-label="Next image"
                >
                    &#8250;
                </button>
                <img
                    :src="getImageSource(selectedImage)"
                    :alt="selectedImageAltText()"
                    style="max-width: calc(100vw - 160px); max-height: calc(100vh - 160px); object-fit: contain;"
                    @click.stop
                />
            </div>
        </div>
    `,
};
