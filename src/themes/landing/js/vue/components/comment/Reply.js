export default {
    name: 'Reply',
    emits: [
        "show-gallery",
        "upvote",
        "remove-upvote",
        "edit-init",
        "edit-add-mention",
        "edit-remove-mention",
        "edit-embed-files",
        "edit-remove-embedded-file",
        "edit-cancel",
        "edit-submit",
        "delete",
    ],
    props: {
        comment: {
            type: Object,
            required: false,
        },

        commentType: {
            type: Object,
            default: null
        },
        reply: {
            type: Object,
            required: false,
        },
        job: {
            type: Object,
            required: false,
            default: null,
        },
        jobIdentifier: String,
        contextOrganisation: {
            type: Object,
            required: false,
            default: null,
        },
        loading: {
            type: Boolean,
            required: false,
            default: false,
        },
        allowMentions: {
            type: Boolean,
            required: false,
            default: true,
        },
        context: {
            type: String,
            required: false,
            default: 'Internal'
        },
        acceptedFileTypes: {
            type: String,
            required: false,
            default: null,
        },
        editingId: String,
        required: false,
        showConnector: Boolean,
    },
    data() {
        return {
            replyEditorContainerRef: null,
            currentUser: 'John Doe',
            embeddedFilesCopy: null,
            updatedReply: this.reply.content,
            autoMentions: [],
        };
    },

    computed: {
        editing() {
            return this.editingId === this.reply?.identifier;
        },
        upvoteUsers() {
            if (this.reply?.upvotes?.length) {
                let message = "Liked by ";
                this.reply?.upvotes?.forEach((like, index, array) => {
                    if (index === 0) message += like?.user?.fullName;
                    else if (index === array.length - 1)
                        message += " and " + like?.user?.fullName;
                    else message += ", " + like?.user?.fullName;
                });
                message += ".";
                return message;
            }
        },
        userIsAuthor() {
            return this.currentUser?.identifier === this.reply?.user?.identifier;
        },
        userContact() {
            if (!this.reply?.user?.orgContacts?.length) return null;
            let contact;
            if (this.contextOrganisation) {
                contact = _.find(this.reply?.user?.orgContacts, c => {
                    return c?.organisation?.identifier === this.contextOrganisation?.identifier;
                });
            } else {
                contact = this.reply?.user?.orgContacts[0];
            }
            return contact;
        },
        userContactLink() {
            if (!this.userContact) return null;
            return '/contacts/view/' + this.userContact.identifier;
        },
        userOrganisation() {
            if (!this.userContact) return null;
            return this.userContact.organisation?.name;
        },
        canEdit() {
            return this.userIsAuthor && this.context === 'Internal';
        },
        userUpvoteObject() {
            const cu = this.currentUser;
            return _.find(this.reply?.upvotes, function (uv) {
                return uv?.user?.identifier === cu?.identifier;
            });
        },
        userUpvoted() {
            const cu = this.currentUser;
            return this.userUpvoteObject?.identifier !== undefined;
        },
    },
    mounted() {
        this.parseAutoMentions();
    },
    methods: {
        replyUpdateRef(el) {
            this.replyEditorContainerRef = el;
        },
        formatDate(date) {
            return '2026-01-21 12:00:00';
        },
        likeReply(replyIdentifier) {
            if (!this.userUpvoted) {
                this.$emit("upvote", replyIdentifier);
            } else {
                this.$emit("remove-upvote", this.userUpvoteObject?.identifier);
            }
        },
        checkUserLiked() {
            return false;
        },
        editReply() {
            if (!this.userIsAuthor) return;
            this.$emit("edit-init");
            this.embeddedFilesCopy = _.cloneDeep(this.reply?.embeddedFiles);
        },
        addReplyMention(event) {
            this.$emit("edit-add-mention", { identifier: event?.id });
        },
        removeReplyMention(event) {
            this.$emit("edit-remove-mention", { identifier: event?.identifier });
        },
        embedReplyFiles(files) {
            this.$store
                .dispatch("comments/uploadEmbeddedFiles", {
                    files: files,
                    model: this.commentType?.fileModel ?? this.commentType?.model,
                    modelIdentifier:
                        this.commentType?.fileModel === "Job"
                            ? this.job?.identifier ?? this.jobIdentifier
                            : this.comment?.model?.identifier,
                })
                .then((uploadedFiles) => {
                    this.embeddedFilesCopy = (this.embeddedFilesCopy ?? []).concat(
                        uploadedFiles
                    );
                    this.$emit("edit-embed-files", uploadedFiles);
                });
        },
        removeEmbeddedReplyFile(event) {
            const identifier = event?.identifier;
            this.embeddedFilesCopy = _.reject(this.embeddedFilesCopy, { identifier });
            this.$emit("edit-remove-embedded-file", { identifier });
        },
        cancelEdit() {
            this.$emit("edit-cancel");
            this.updatedReply = this.reply?.content;
            this.embeddedFilesCopy = null;
        },
        submitEdit() {
            const mentions = CommentUtils.getMentions(
                this.$refs.replyEditor.$refs.editorElement
            );
            this.$emit("edit-submit", { comment: this.updatedReply, mentions });
        },
        deleteReply() {
            if (!this.userIsAuthor) return;
            this.$emit("delete");
        },
        getFileIcon(file) {
            return getFileTypeIcon(file);
        },
        isImage(file) {
            return isImage(file);
        },
        isDoc(file) {
            return isDoc(file);
        },
        isSpreadsheet(file) {
            return isSpreadsheet(file);
        },
        isPdf(file) {
            return isPdf(file);
        },
        isVideo(file) {
            return isVideo(file);
        },
        isYoutube(file) {
            return isYoutube(file);
        },
        parseAutoMentions() {
            if (!this.$refs.replyContent) return;
            const allMentions = this.reply.mentions ?? [];
            let explicitMentionIds = [];
            const explicitMentionTags = Array.from(this.$refs.replyContent.getElementsByClassName('mention'));
            if (explicitMentionTags.length) {
                explicitMentionIds = explicitMentionTags.map(el => el.dataset.id);
            }
            const implicitMentions = allMentions.filter(m => !explicitMentionIds.includes(m.identifier));
            this.autoMentions = _.uniqBy(implicitMentions, 'identifier');
        }
    },
    template: /* html */ `
    <div>
        <div class="comment-display reply-display list-complete-item">
            <div class="comment-user-image">
            <Avatar
                :label="reply.user.name.charAt(0) + reply.user.familyName.charAt(0)"
                :image="reply.user.profileThumbnailPic"
                shape="circle"
                size="large"
            />
            <div v-if="showConnector" class="reply-connector"></div>
            </div>
            <div class="comment-content">
            <div class="comment-top-content">
                <div class="comment-user-and-likes">
                <div class="comment-user-name">
                    <a v-if="userContactLink" :href="userContactLink" target="_blank">{{ reply.user.fullName + (userOrganisation ? ' (' + userOrganisation + ')' : '') }}</a>
                    <span v-else>{{ reply.user.fullName }}</span>
                </div>
                <div class="comment-upvotes">
                    <div class="upvote-button-container">
                    <Button
                        icon="fas fa-thumbs-up"
                        class="p-button-link"
                        :class="{ 'user-liked': userUpvoted }"
                        @click.stop="likeReply"
                    />
                    </div>
                    <button
                    v-if="reply.upvotes?.length"
                    class="upvote-count"
                    v-tooltip.top="upvoteUsers"
                    >
                    <span>{{ reply.upvotes?.length }}</span>
                    </button>
                </div>
                </div>
                <div class="comment-time">{{ formatDate(reply.modified) }}</div>
            </div>
            <template v-if="!editing">
                <!-- <div class="reply-comment-author-tag">@{{ comment.user.fullName }}</div> -->
                <div class="auto-comment-mention-tags">
                <span v-for="mention in autoMentions" :key="mention.identifier" class="mr-2">@{{ mention.fullName }}</span>
                </div>
                <div ref="replyContent" class="comment-message" v-html="reply.content"></div>
                <div v-if="reply.embeddedFiles?.length" class="embedded-file-container">
                <div
                    v-for="(file, index) of reply.embeddedFiles"
                    :key="file.identifier"
                    class="embedded-file-preview"
                    v-tooltip.top="file.originalName"
                    @click.stop="$emit('show-gallery', { index })"
                >
                    <img v-if="isImage(file)" :src="'/' + encodeURIComponent(file.path)" />
                    <template v-else>
                    <i
                        :class="getFileIcon(file)"
                        class="file-icon"
                    />
                    <caption>{{ file.originalName }}</caption>
                    </template>
                </div>
                </div>
                <div class="comment-footer">
                <div v-if="canEdit" class="edit-delete-buttons">
                    <div class="clickable-icon" @click.stop="editReply">
                    <i class="fas fa-pencil icon-grey" />
                    </div>
                    <div class="clickable-icon" @click.stop="deleteReply">
                    <i class="fas fa-trash-alt icon-grey" />
                    </div>
                </div>
                </div>
            </template>
            <div v-else :ref="replyUpdateRef" class="comment-editor comment-update">
                <Editor
                ref="replyEditor"
                :outerRef="replyEditorContainerRef"
                v-model="updatedReply"
                :embeddedFiles="embeddedFilesCopy"
                type="reply"
                :autofocus="true"
                :allowMentions="allowMentions"
                :acceptedFileTypes="acceptedFileTypes"
                @mention="addReplyMention"
                @remove-mention="removeReplyMention"
                @file-embed="embedReplyFiles"
                @remove-embedded-file="removeEmbeddedReplyFile"
                @submit="submitEdit"
                />
                <div class="editor-buttons update-buttons">
                <div class="clickable-icon" @click.stop="cancelEdit">
                    <i class="fas fa-times" />
                </div>
                <div class="clickable-icon" @click.stop="submitEdit">
                    <i v-if="!loading" class="fas fa-check"></i>
                    <i v-else class="p-button-icon ml-2 fas fa-spin fa-spinner-third" />
                </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    `,
};