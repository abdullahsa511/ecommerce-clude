import Comment from './Comment.js';
import Editor from './Editor.js';
import Uploader from './Uploader.js';

export default {
    components: {
        Comment,
        Editor,
        Uploader,
    },
    emits: ['delete-comment','upvote-comment','reply-comment'],
    name: 'CommentAccordian',
    props: {
        quoteAccount: {
            type: String,
            required: false,
            default: '',
        },
        commentData: {
            type: Array,
            required: false,
            default: () => ([])
        },
        totalComments: {
            type: Number,
            required: false,
            default: null,
        },
        loading: {
            type: Boolean,
            required: false,
            default: false,
        },
        errors: {
            type: [String, Object],
            required: false,
            default: null,
        },
        modelType: {
            type: String,
            required: false,
            default: 'Quote',
        },
        searchQuery: {
            type: String,
            required: false,
            default: '',
        },
    },
    data() {
        return {
            expanded: false,
            activeUser: 'John Doe',
            filteredComments: [
                {
                    identifier: '1',
                    content: 'Comment 1',
                },
                {
                    identifier: '2',
                    content: 'Comment 2',
                },
            ],
            loadedCommentCount: 2,
            commentMeta: {
                pagination: {
                    total: 10,
                },
            },
            showShowAllButton: true,
            showingJobComments: false,
            isDriverInstaller: false,
            expandedComments: [],
            loadingMore: false,
            targetComment: null,
            commentSection: 'Internal',
            isMobile: false,
            allowedFileTypes: null,
            modalImage: null,
            replyEditorCommentKey: null,
            replyCommentData: '',
            isReplySubmitting: false,
        };
    },
    methods: {
        resolveCommentKey(comment, index) {
            if (comment && comment.comment_id !== undefined && comment.comment_id !== null) {
                return String(comment.comment_id);
            }
            return String(index);
        },
        isCommentExpanded(commentKey) {
            const key = String(commentKey);
            return this.expandedComments.includes(key);
        },
        toggleCommentPanel(commentKey) {
            const key = String(commentKey);
            const expandedIndex = this.expandedComments.indexOf(key);
            if (expandedIndex !== -1) {
                this.expandedComments.splice(expandedIndex, 1);
                return;
            }
            this.expandedComments.push(key);
        },
        accordionHeaderId(commentKey) {
            const key = String(commentKey);
            return `comment-${key}-header`;
        },
        accordionContentId(commentKey) {
            const key = String(commentKey);
            return `comment-${key}-content`;
        },
        // Accordion animation start
        beforeAccordionEnter(el) {
            el.style.height = '0';
            el.style.opacity = '0';
            el.style.overflow = 'hidden';
            el.style.transition = 'height 0.3s ease, opacity 0.3s ease';
        },
        accordionEnter(el) {
            requestAnimationFrame(() => {
                el.style.height = `${el.scrollHeight}px`;
                el.style.opacity = '1';
            });
        },
        afterAccordionEnter(el) {
            el.style.height = 'auto';
            el.style.opacity = '1';
            el.style.overflow = '';
            el.style.transition = '';
        },
        beforeAccordionLeave(el) {
            el.style.height = `${el.scrollHeight}px`;
            el.style.opacity = '1';
            el.style.overflow = 'hidden';
            el.style.transition = 'height 0.3s ease, opacity 0.3s ease';
        },
        accordionLeave(el) {
            requestAnimationFrame(() => {
                el.style.height = '0';
                el.style.opacity = '0';
            });
        },
        afterAccordionLeave(el) {
            el.style.height = '0';
            el.style.opacity = '0';
            el.style.overflow = '';
            el.style.transition = '';
        },
        // Accordion animation end
        toggleExpanded() {
            this.expanded = !this.expanded;
        },


        isReplyEditorOpen(comment, index) {
            const key = this.resolveCommentKey(comment, index);
            return this.replyEditorCommentKey === key;
        },
        toggleReplyEditor(comment, index) {
            const key = this.resolveCommentKey(comment, index);
            if (this.replyEditorCommentKey === key) {
                this.replyEditorCommentKey = null;
                this.replyCommentData = '';
                return;
            }
            if (this.replyEditorCommentKey !== null) {
                this.replyCommentData = '';
            }
            this.replyEditorCommentKey = key;
        },
        submitReplyComment(comment) {
            if (this.isReplySubmitting || this.loading) {
                return;
            }

            const trimmedReply = this.replyCommentData ? this.replyCommentData.trim() : '';
            if (!trimmedReply) {
                return;
            }

            this.isReplySubmitting = true;
            this.$emit('reply-comment', comment.comment_id, comment.user_id, trimmedReply);
        },



        getFormattedDate(date) {
            // Format date as "Wed 21/1/26 1:54pm"
            if (!date) return '';
            const dateObj = new Date(date);
            if (isNaN(dateObj)) return date;

            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const dow = days[dateObj.getDay()];
            const day = dateObj.getDate();
            const month = dateObj.getMonth() + 1;
            const year = String(dateObj.getFullYear()).slice(-2);

            let hours = dateObj.getHours();
            const minutes = String(dateObj.getMinutes()).padStart(2, '0');
            const period = hours >= 12 ? 'pm' : 'am';
            hours = hours % 12;
            if (hours === 0) hours = 12;

            return `${dow} ${day}/${month}/${year} ${hours}:${minutes}${period}`;
        },
        openImageModal(photo) {
            this.modalImage = photo;
            document.body.style.overflow = 'hidden';
        },
        closeImageModal() {
            this.modalImage = null;
            document.body.style.overflow = '';
        },
        getCommentReplies(comment) {
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
        isParentComment(comment) {
            if (!comment || typeof comment !== 'object') {
                return false;
            }

            return comment.parent_id === null
                || comment.parent_id === undefined
                || Number(comment.parent_id) === 0;
        },
        handleKeyDown(event) {
            if (event.key === 'Escape' && this.modalImage) {
                this.closeImageModal();
            }
        },
        downloadFile(photo) {
            const link = document.createElement('a');
            link.href = photo.download_url;
            link.download = photo.download_url;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
    },
    watch: {
        loading(isLoading) {
            if (isLoading || !this.isReplySubmitting) {
                return;
            }

            this.isReplySubmitting = false;

            if (!this.errors) {
                this.replyCommentData = '';
                this.replyEditorCommentKey = null;
            }
        },
        commentData(newComments, previousComments) {
            if (!Array.isArray(newComments) || !newComments.length) {
                return;
            }

            if (!Array.isArray(previousComments) || !previousComments.length) {
                return;
            }

            if (newComments.length <= previousComments.length) {
                return;
            }

            if (newComments[0] === previousComments[0]) {
                return;
            }

            const newFirstKey = String(this.resolveCommentKey(newComments[0], 0));
            const expandedIndex = this.expandedComments.indexOf(newFirstKey);

            if (expandedIndex !== -1) {
                this.expandedComments.splice(expandedIndex, 1);
            }
        },
    },
    mounted() {
        if (this.commentData && this.commentData.length) {
            const firstKey = this.resolveCommentKey(this.commentData[0], 0);
            this.expandedComments = [firstKey];
        }
        // Add keyboard event listener for ESC key
        document.addEventListener('keydown', this.handleKeyDown);


    },
    beforeUnmount() {
        // Remove keyboard event listener
        document.removeEventListener('keydown', this.handleKeyDown);
    },
    template: /* html */ `
    <div class="comments-container pt-50">
        <div class="comments-container-top">
            <div class="expand-all-container">
                <button class="th-comment-btn-mini" type="button">
                    <span class="fa fa-angle-down"></span>
                    <span class="p-ink"></span>
                </button>
            </div>
            <div class="comments-container-top-right">
                <div class="comments-meta-info">
                    <small>{{ commentData.length }} of {{ totalComments != null ? totalComments : commentData.length }}</small>
                </div>
            </div>
        </div>

        <div class="p-accordion p-component" v-if="commentData.length > 0" v-for="(comment, index) in commentData" :key="resolveCommentKey(comment, index)">
            <div :class="['p-accordion-tab', { 'p-accordion-tab-active': isCommentExpanded(resolveCommentKey(comment, index)) }]">
                <div :class="['p-accordion-header', { 'p-highlight': isCommentExpanded(resolveCommentKey(comment, index)) }, 'mb-5']">
                    <a role="tab"
                        class="p-accordion-header-link"
                        tabindex="0"
                        :aria-expanded="isCommentExpanded(resolveCommentKey(comment, index))"
                        :id="accordionHeaderId(resolveCommentKey(comment, index))"
                        :aria-controls="accordionContentId(resolveCommentKey(comment, index))"
                        @click.prevent="toggleCommentPanel(resolveCommentKey(comment, index))"
                        @keydown.enter.prevent="toggleCommentPanel(resolveCommentKey(comment, index))"
                        @keydown.space.prevent="toggleCommentPanel(resolveCommentKey(comment, index))">
                        <span :class="['p-accordion-toggle-icon', 'pi', isCommentExpanded(resolveCommentKey(comment, index)) ? 'pi-chevron-down' : 'pi-chevron-right']"></span>
                        <div class="d-flex align-items-center gap-2">
                            <i :class="['fa-solid', isCommentExpanded(resolveCommentKey(comment, index)) ? 'fa-angle-down' : 'fa-angle-right']"></i>
                            <span class="p-accordion-header-text">
                            {{modelType}} &gt;
                                <a href="" target="_blank" onclick="event.stopPropagation()"> {{quoteAccount}} </a> 
                              
                                &gt; {{getFormattedDate(comment.created_at)}} 
                            </span>
                            <div class="ms-auto">
                                <button class="border-0 bg-transparent" type="button" @click.stop="$emit('delete-comment', comment.comment_id)">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </a>
                </div>
                <transition
                    @before-enter="beforeAccordionEnter"
                    @enter="accordionEnter"
                    @after-enter="afterAccordionEnter"
                    @before-leave="beforeAccordionLeave"
                    @leave="accordionLeave"
                    @after-leave="afterAccordionLeave">
                    <div class="p-toggleable-content"
                        role="region"
                        :id="accordionContentId(resolveCommentKey(comment, index))"
                        :aria-labelledby="accordionHeaderId(resolveCommentKey(comment, index))"
                        :aria-hidden="!isCommentExpanded(resolveCommentKey(comment, index))"
                        v-show="isCommentExpanded(resolveCommentKey(comment, index))">
                        <div class="p-accordion-content">
                        <div class="comment-display" id="comment_ca676e34-e751-447f-bf11-530cdad9fbea" ismobile="false">
                           <!-- <div class="comment-user-image">
                                <div class="p-avatar p-component p-avatar-image p-avatar-circle p-avatar-xl">
                                    <img src="/img/account-dashboard/profile-pic.png" alt="profile picture">
                                </div>
                            </div> -->
                            <div class="comment-content">
                                <div class="comment-top-content">
                                    <div class="comment-user-and-likes">
                                        <div class="comment-user-name d-flex">
                                            <span>{{comment.email}}</span>
                                            <div class="comment-upvotes">
                                                <div class="upvote-button-container">
                                                    <button @click="$emit('upvote-comment', comment.comment_id, comment.user_id || 7, comment.uuid)"
                                                        :class="['p-button p-component p-button-icon-only p-button-link', comment.liked ? 'is-liked' : 'user-liked']"
                                                        :aria-pressed="comment.liked ? 'true' : 'false'"
                                                        :title="comment.liked ? 'Unlike' : 'Like'"
                                                        type="button">
                                                        <span class="fas fa-thumbs-up p-button-icon big-icon"
                                                              :style="{ color: comment.liked ? '#16a34a' : '#9aa1a8' }"></span>
                                                        <span class="p-ink"></span>
                                                    </button>
                                                </div>
                                                <button class="upvote-count">
                                                    <span>{{comment.votes}}</span>
                                                </button>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="comment-time">{{getFormattedDate(comment.created_at)}}</div>
                                </div>
                                <div class="auto-comment-mention-tags"></div>
                                <div class="comment-message">
                                    <p>{{ comment.content }}</p>
                                </div>
                                <div v-if="comment.commentPhoto && comment.commentPhoto.length > 0" class="comment-attachments">
                                    <div class="file-preview" v-for="photo in comment.commentPhoto" :key="photo.comment_photo_id">
                                        <img v-if="photo.extension === 'WEBP'" :src="photo.objectURL" alt="comment photo" @click="openImageModal(photo)" style="cursor: pointer;">
                                        <img v-else :src="photo.objectURL" alt="comment photo" @click="downloadFile(photo)" style="cursor: pointer;">
                                    </div>
                                </div>
                                <div class="comment-footer">
                                </div>
                            </div>
                        </div>
                        <!-- ============ reply comment display ============ -->
                        <div class="comment-replies" v-if="isParentComment(comment) && getCommentReplies(comment).length > 0">
                            <div class="comment-display reply-display list-complete-item" v-for="reply in getCommentReplies(comment)" :key="reply.comment_id">
                                <div class="comment-user-image">
                                   <!-- <div class="p-avatar p-component p-avatar-image p-avatar-circle p-avatar-lg">
                                        <img src="/media/design-resource/icons/profile-pic.webp" alt="profile picture">
                                    </div> -->
                                    <div class="reply-connector"></div>
                                </div> 
                                <div class="comment-content">
                                    <div class="comment-top-content">
                                        <div class="comment-user-and-likes">
                                            <div class="comment-user-name d-flex">
                                                <span class="name">{{ reply.email || reply.author }}</span>
                                                <div class="comment-upvotes">
                                                    <div class="upvote-button-container">
                                                        <button :class="['p-button p-component p-button-icon-only p-button-link', reply.liked ? 'is-liked' : 'user-liked']"
                                                            :aria-pressed="reply.liked ? 'true' : 'false'"
                                                            :title="reply.liked ? 'Unlike' : 'Like'"
                                                            type="button" @click="$emit('upvote-comment', reply.comment_id, reply.user_id || 7, reply.uuid)">
                                                            <span class="fas fa-thumbs-up p-button-icon big-icon"
                                                                  :style="{ color: reply.liked ? '#16a34a' : '#9aa1a8' }"></span>
                                                            <span class="p-button-label">&nbsp;</span>
                                                            <span class="p-ink"></span>
                                                        </button>
                                                    </div>
                                                    <button class="upvote-count">
                                                        <span>{{ reply.votes }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                            
                                        </div>
                                        <div class="comment-time">{{getFormattedDate(reply.created_at)}}</div>
                                    </div>
                                    <div class="auto-comment-mention-tags"></div>
                                    <div class="comment-message">
                                        <p>{{ reply.content }}</p>
                                    </div>
                                    <div v-if="reply.commentPhoto && reply.commentPhoto.length > 0" class="comment-attachments">
                                        <div class="file-preview" v-for="photo in reply.commentPhoto" :key="photo.comment_photo_id">
                                            <img v-if="photo.extension === 'WEBP'" :src="photo.objectURL" alt="comment photo" @click="openImageModal(photo)" style="cursor: pointer;">
                                            <img v-else :src="photo.objectURL" alt="comment photo" @click="downloadFile(photo)" style="cursor: pointer;">
                                        </div>
                                    </div>
                                    <div class="comment-footer"></div>
                                </div>
                            </div>
                        </div>
                        <transition name="reply-editor-fade">
                            <div v-show="isReplyEditorOpen(comment, index)" class="comment-editor reply-editor">
                                <div class="reply-editor-main">
                                    <div class="comment-user-image">

                                    </div>
                                    <div class="reply-editor-input">
                                        <textarea v-model="replyCommentData" :id="'replyCommentData-' + resolveCommentKey(comment, index)" class="form-control" rows="3" placeholder="Enter your reply here..."></textarea>
                                      
                                    </div>
                                </div>
                                <div class="editor-buttons reply-buttons">
                                    <div v-if="errors">
                                        <small class="p-error">{{ errors }}</small>
                                    </div>
                                    <div>
                                        <button class="th-btn-cancel p-button-link" type="button" @click="toggleReplyEditor(comment, index)">
                                            <span class="p-button-label">Cancel</span>
                                        </button>
                                        <button class="th-btn-reply-submit" type="button" :disabled="isReplySubmitting || loading" @click="submitReplyComment(comment)">
                                            <span v-if="isReplySubmitting || loading" class="p-button-label">
                                                <i class="fas fa-spin fa-spinner-third me-1"></i>
                                                Submitting...
                                            </span>
                                            <span v-else class="p-button-label">Submit</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </transition>

                        <transition name="reply-button-fade">
                            <div class="comment-reply-button" v-show="!isReplyEditorOpen(comment, index) && comment.is_reply === 0">
                                <button class="th-btn-outline-reply p-button-link" type="button" @click="toggleReplyEditor(comment, index)">
                                    <span class="fas fa-reply p-button-icon p-button-icon-left"></span>
                                    <span class="p-button-label">Reply</span>
                                </button>
                            </div>
                        </transition>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
        <div v-else class="text-center py-4">
            <p v-if="(searchQuery || '').trim()" class="mb-0">Data not found</p>
            <p v-else class="mb-0">No comments found</p>
        </div>

        <!-- Image Modal -->
        <div v-if="modalImage" class="image-modal" @click="closeImageModal">
            <div class="image-modal-content" @click.stop>
                <button class="image-modal-close" @click="closeImageModal" type="button">
                    <i class="fa-solid fa-times"></i>
                </button>
                <img :src="modalImage.objectURL" alt="Full size image" class="image-modal-img">
            </div>
        </div>
    </div>
    `
}