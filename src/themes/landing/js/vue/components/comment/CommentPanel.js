import Editor from './Editor.js';
import SearchComment from './SearchComment.js';
import Uploader from './Uploader.js';
import Comment from './Comment.js';
import CommentAccordian from './CommentAccordian.js';

export default {
    name: 'CommentPanel',
    data() {
        return {
            commentSearchQuery: '',
            commentSearchLoading: false,
            commentEditorContainerRef: null,
            replyEditorContainerRef: null,
            newReply: null,
            replyAutoMentions: [],
            replyFiles: null,
            profileThumbnailPic: null,
            commentContent: '', 
            attachments: [],
            hasValidationError: false
        };

    },
  
    components: {
        SearchComment,
        Editor,
        Uploader,
        Comment,
        CommentAccordian,
    },
   
    computed: {
        commentData() {
        
            // This returns an empty array if `$store` is not available. 
            // If $store exists, it returns the latest value of commentData from the Vuex store (which is expected to be an array).
            // If commentData in the store is not an array, fallback to empty array.
            if (this.$store && Array.isArray(this.$store.getters.commentData)) {
                return this.$store.getters.commentData;
            }
            return [];
        },
        filteredCommentData() {
            const list = this.commentData;
            const q = (this.commentSearchQuery || '').trim().toLowerCase();
            if (!q) {
                return list;
            }
            return list.filter((comment) => {
                if (this.commentMatchesSearchQuery(comment, q)) {
                    return true;
                }
                const replies = this.getCommentRepliesForFilter(comment);
                return replies.some((reply) => this.commentMatchesSearchQuery(reply, q));
            });
        },
        hasActiveCommentSearch() {
            return Boolean((this.commentSearchQuery || '').trim());
        },
        commentSearchNoResults() {
            return this.hasActiveCommentSearch
                && !this.commentSearchLoading
                && this.filteredCommentData.length === 0;
        },
        // old state for quote
        activeQuoteId() {
            if (this.$store && this.$store.getters.quoteId) {
                return this.$store.getters.quoteId;
            }
            return this.quoteId || '';
        },
        activeQuoteAccount() {
            if (this.$store && this.$store.getters.quoteAccount) {
                return this.$store.getters.quoteAccount;
            }
            return this.quoteAccount || '';
        },
        // new state for model
        activeModelId() {
            if (this.$store && this.$store.getters.modelId) {
                return this.$store.getters.modelId;
            }
            return this.modelId || '';
        },
        activeModelUuid() {
            if (this.$store && this.$store.getters.modelUuid) {
                return this.$store.getters.modelUuid;
            }
            return this.modelUuid || '';
        },
        activeModelRef() {
            if (this.$store && this.$store.getters.modelRef) {
                return this.$store.getters.modelRef;
            }
            return this.modelRef || '';
        },
        activeModelType() {
            if (this.$store && this.$store.getters.modelType) {
                return this.$store.getters.modelType;
            }
            return this.modelType || '';
        },
        loading() {
            if (this.$store) {
                return Boolean(this.$store.getters.loading);
            }
            return false;
        },
        errors() {
            if (this.$store) {
                return this.$store.getters.error;
            }
            return null;
        },
        userContactLink() {
            if (!this.userContact) return null;
            return '/contacts/view/' + this.userContact.identifier;
          },
    },

    mounted() {
        console.log('CommentPanel mounted with model uuid', this.activeModelUuid);
        // console.log('CommentPanel mounted with quote data', {
        //     quoteId: this.activeModelId,
        //     quoteAccount: this.activeModelRef
        // });
    },

    methods: {
        getCommentRepliesForFilter(comment) {
            if (!comment || typeof comment !== 'object') {
                return [];
            }
            if (Array.isArray(comment.replay)) {
                return comment.replay;
            }
            if (Array.isArray(comment.reply)) {
                return comment.reply;
            }
            return [];
        },
        commentMatchesSearchQuery(item, q) {
            if (!item || typeof item !== 'object' || !q) {
                return false;
            }
            const rawContent = item.content != null ? String(item.content) : '';
            const plainContent = rawContent.replace(/<[^>]*>/g, ' ');
            const haystack = [
                item.user_name,
                item.author,
                plainContent,
                rawContent,
            ]
                .filter((part) => part != null && String(part).length)
                .join(' ')
                .toLowerCase();
            return haystack.includes(q);
        },
        async onCommentSearch(query) {
            this.commentSearchLoading = true;
            this.commentSearchQuery = query != null ? String(query) : '';
            await this.$nextTick();
            await new Promise((resolve) => {
                requestAnimationFrame(() => requestAnimationFrame(resolve));
            });
            this.commentSearchLoading = false;
        },
        handleEditorUpdate(content) {
            this.commentContent = content;

            if (this.hasValidationError) {
                const trimmed = content ? content.trim() : '';
                if (trimmed.length) {
                    this.hasValidationError = false;
                }
            }
        },
        async submitComment(files) {
            const trimmedContent = this.commentContent ? this.commentContent.trim() : '';
            if (!trimmedContent) {
                this.hasValidationError = true;
                if (this.$refs && this.$refs.commentEditor && typeof this.$refs.commentEditor.focusEditor === 'function') {
                    this.$refs.commentEditor.focusEditor();
                }
                return;
            }

            const formData = new FormData();
            // formData.append('model_id', this.activeModelId);
            formData.append('model_uuid', this.activeModelUuid);
            formData.append('model_ref', this.activeModelRef);
            formData.append('content', trimmedContent);
            formData.append('model_type', this.activeModelType);
            formData.append('type', 'comment'); // comment, reply

            const attachmentList = Array.isArray(files) ? files : [];
            attachmentList.forEach((item) => {
                if (item && item.file) {
                    formData.append('attachments[]', item.file);
                }
            });
        
            try {
                await this.$store.dispatch('submitComment', formData);
                this.hasValidationError = false;
                this.commentContent = '';

                if (this.$refs && this.$refs.commentEditor && typeof this.$refs.commentEditor.clear === 'function') {
                    this.$refs.commentEditor.clear();
                }

                if (this.$refs && this.$refs.uploader && typeof this.$refs.uploader.clearAttachments === 'function') {
                    this.$refs.uploader.clearAttachments();
                }

            } catch (error) {
                console.error('Failed to submit comment:', error);
            }
        },

        deleteComment(commentId) {
            this.$store.dispatch('deleteComment', commentId);
        },

        upvoteComment(commentId, user_id, uuid) {
            console.log('uuid', uuid);
            console.log('commentId', commentId);
            console.log('user_id', user_id);
            this.$store.dispatch('upvoteComment',{commentId: commentId, user_id: user_id, uuid: uuid});
        },

        async replyCommentSubmit(commentId, user_id, replyContent, uuid = null) {
            return this.$store.dispatch('replyCommentSubmit',{commentId: commentId, user_id: user_id, replyContent: replyContent, is_reply: true, uuid: uuid});
        },

    },

    template: /* html */ `
    <div class="th-comment-panel">
        
        <!-- Header -->
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title" id="offcanvasRightTopLabel">Comment Feed</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <!-- Body -->
        <div class="offcanvas-body">
            <div class="th-comment-wrapper">
                <div class="d-flex gap-2 align-items-center th-comment-breadcrumbs">
                    <span class="fw-semibold">{{ activeModelType }}</span>
                    <span class="th-breadcrumb-separator" aria-hidden="true">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                    <span class="text-uppercase">{{ activeModelRef || 'N/A' }}</span>
                    <span class="th-breadcrumb-separator" aria-hidden="true">
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                    <span class="text-truncate">{{ activeModelId || 'N/A' }}</span>
                </div>

                <SearchComment :loading="commentSearchLoading" @search="onCommentSearch" />

                <div class="th-comment-box">
                    <form action="" class="th-form th-custom-quote-form">

                        <Editor 
                        ref="commentEditor"
                        :has-error="hasValidationError"
                        @update-content="handleEditorUpdate"
                        />
                        
                        <Uploader 
                        ref="uploader"
                        :loading="loading"
                        @submit-comment="submitComment"
                        />
                    </form>
                </div>

               <!-- <div v-if="commentSearchNoResults && filteredCommentData.length === 0" class="th-comment-search-empty text-center py-4">
                    <p class="mb-0">No comments found</p>
                </div> -->

                <CommentAccordian 
                    v-if="!commentSearchNoResults && filteredCommentData.length > 0"
                    :commentData="filteredCommentData"
                    :totalComments="commentData.length"
                    :searchQuery="commentSearchQuery"
                    :quoteAccount="activeModelRef"
                    :loading="loading"
                    :errors="errors"
                    @delete-comment="deleteComment"
                    @upvote-comment="upvoteComment"
                    @reply-comment="replyCommentSubmit"
                    :modelType="activeModelType"        
                />

            </div>
        </div> 
       
    </div>
    `
}
