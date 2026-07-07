export default {
    name: 'Editor',
    emits: ['update-content'],
    props: {
        hasError: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            quillInstance: null,
            editorContainer: null,
        };
    },
    mounted(){
        this.initQuill();
    },
    watch: {
        hasError: {
            immediate: true,
            handler(newValue) {
                this.$nextTick(() => {
                    this.applyErrorState(newValue);
                });
            }
        }
    },
    methods: {
        initQuill() {
            if (!window.Quill) {
                throw new Error('Quill library is not available on the window object');
            }

            const editorElement = this.$refs.editor;
            if (!editorElement) {
                throw new Error('Editor element not found');
            }

            if (this.quillInstance) {
                return this.quillInstance;
            }

            this.quillInstance = new window.Quill(editorElement, {
                theme: 'snow'
            });

            this.editorContainer = editorElement;

            this.quillInstance.on('text-change', () => {
                const quillRoot = editorElement.querySelector('.ql-editor');
                const text = quillRoot ? quillRoot.innerText : '';
                this.$emit('update-content', text);
            });

            this.applyErrorState(this.hasError);
        },
        applyErrorState(isError) {
            const container = (this.$refs && this.$refs.editor) ? this.$refs.editor : this.editorContainer;
            if (!container) {
                return;
            }

            const wrapper = (this.$refs && this.$refs.editorWrapper) ? this.$refs.editorWrapper : null;

            if (wrapper) {
                wrapper.classList.toggle('th-comment-editor-container--error', !!isError);
            }

            container.style.borderColor = '';
            container.style.boxShadow = '';
        },
        clear() {
            if (this.quillInstance) {
                this.quillInstance.setText('');
            }
            this.applyErrorState(false);
        },
        focusEditor() {
            if (this.quillInstance) {
                this.quillInstance.focus();
            }
        }
    }, 
    template: /* html */ `
    <div>
        <div class="form-group th-comment-editor-container" ref="editorWrapper">
            <div id="editor" ref="editor" class="th-comment-editor"></div>
        </div>
    </div>
    `
}