

<script>
  import CommentUtils from "../../admin/services/Comments/CommentUtils";
  import Comment from "../../models/Comments/Comment";
  import { formatTimeAndDate } from "../../utils/DateTimeUtils";
  import { getUserProfile } from "../../utils/SessionUtils";
  import { CommentTypes } from '../../constraints/Comments/CommentTypes';
  import { getFileTypeIcon, isDoc, isImage, isPdf, isSpreadsheet, isVideo, isYoutube } from '../../utils/FileUtils';
  export default {
    name: "Comment",
    emits: [
      "upvote",
      "remove-upvote",
      "upvote-reply",
      "remove-upvote-reply",
      "start-reply",
      "cancel-reply",
      "reply-mention",
      "reply-remove-mention",
      "reply-embed-files",
      "reply-remove-embedded-file",
      "create-reply",
      "show-gallery",
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
      /** @type {import('vue').PropType<Comment>} */
      comment: {
        type: Object,
        required: true,
      },
      job: {
        type: Object,
        default: null,
      },
      jobIdentifier: String,
      documentOrganisation: {
        type: Object,
        default: null,
      },
      replyEditor: {
        type: Boolean,
        default: false,
      },
      replyMentions: {
        type: Array,
        default: null,
      },
      replyFiles: {
        type: Array,
        default: null,
      },
      loading: {
        type: Boolean,
        default: false,
      },
      error: {
        type: Boolean,
        default: false,
      },
      allowMentions: {
        type: Boolean,
        default: true,
      },
      context: {
        type: String,
        default: 'Internal'
      },
      editingId: String,
      userIsCustomer: {
        type: Boolean,
        default: false
      },
      userReplyMentions: {
        type: Array,
        default: null
      },
      acceptedFileTypes: {
        type: String,
        default: null,
      },
    },
    data() {
      return {
        commentEditorContainerRef: null,
        replyEditorContainerRef: null,
        newReply: null,
        currentUser: getUserProfile(),
        updatedComment: this.comment.content,
        embeddedFilesCopy: null,
        autoMentions: [],
        replyAutoMentions: [],
      };
    },
    computed: {
      editing() {
        return this.editingId === this.comment.identifier;
      },
      commentType() {
        let model = _.find(_.values(CommentTypes), { model: this.comment.model?.header?.context ?? 'Job' });
        if (!model) model = _.find(_.values(CommentTypes), { model: this.comment.model?.name ?? 'Job' });
        return model;
      },
      upvoteUsers() {
        if (this.comment.upvotes?.length) {
          let message = "Liked by ";
          this.comment.upvotes.forEach((like, index, array) => {
            if (index === 0) message += like.user.fullName;
            else if (index === array.length - 1)
              message += " and " + like.user.fullName;
            else message += ", " + like.user.fullName;
          });
          message += ".";
          return message;
        }
      },
      userIsAuthor() {
        return this.currentUser?.identifier === this.comment.user.identifier;
      },
      contextOrganisation() {
        return this.documentOrganisation || this.comment.model?.organisation;
      },
      userContact() {
        if (!this.comment?.user?.orgContacts?.length) return null;
        let contact;
        if (this.contextOrganisation) {
          contact = _.find(this.comment.user.orgContacts, c => {
            return c.organisation?.identifier === this.contextOrganisation.identifier;
          });
        } else {
          contact = this.comment.user.orgContacts[0];
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
        return _.find(this.comment.upvotes, function (uv) {
          return uv.user?.identifier === cu.identifier;
        });
      },
      userUpvoted() {
        const cu = this.currentUser;
        return this.userUpvoteObject?.identifier !== undefined;
      },
      formattedContent() {
        return this.comment.content;
        // return EmojiUtils.wrapEmoji(this.comment.content);
      },
    },
    mounted() {
      this.parseAutoMentions();
    },
    watch: {
      replyEditor(isEditing) {
        if (!isEditing) {
          this.newReply = null;
        }
      },
    },
    methods: {
      commentUpdateRef(el) {
        this.commentEditorContainerRef = el;
      },
      replyEditorRef(el) {
        this.replyEditorContainerRef = el;
      },
      formatDate(date) {
        return formatTimeAndDate(date);
      },
      likeComment() {
        if (!this.userUpvoted) {
          this.$emit("upvote");
        } else {
          this.$emit("remove-upvote", this.userUpvoteObject.identifier);
        }
      },
      likeReply(replyIdentifier) {
        this.$emit("upvote-reply", replyIdentifier);
      },
      unlikeReply(upvoteId, replyId) {
        this.$emit("remove-upvote-reply", {
          upvoteIdentifier: upvoteId,
          replyIdentifier: replyId,
        });
      },
      checkUserLiked() {
        const cu = this.currentUser;
        return _.some(this.comment.upvotes, function (uv) {
          return uv.user?.identifier === cu.identifier;
        });
      },
      toggleReplyEditor() {
        if (this.replyEditor) {
          this.newReply = null;
          this.$emit("cancel-reply");
        } else {
          this.$emit("start-reply");
        }
      },
      addReplyMention(event) {
        this.$emit("reply-mention", { identifier: event.id });
      },
      removeReplyMention(event) {
        this.$emit("reply-remove-mention", { identifier: event.identifier });
      },
      embedReplyFiles(files) {
        this.$store
          .dispatch("comments/uploadEmbeddedFiles", {
            files: files,
            model: this.comment.isCustomer ? this.commentType.model : this.commentType.fileModel ?? this.commentType.model,
            modelIdentifier:
              this.comment.isCustomer ? this.comment.model.identifier
                : this.commentType.fileModel === "Job"
                ? this.job?.identifier ?? this.jobIdentifier
                : this.comment.model.identifier,
          })
          .then((uploadedFiles) => {
            this.embeddedFilesCopy = (this.embeddedFilesCopy || []).concat(
              uploadedFiles
            );
            this.$emit("reply-embed-files", uploadedFiles);
          });
      },
      removeEmbeddedReplyFile(event) {
        const identifier = event.identifier;
        this.$emit("reply-remove-embedded-file", { identifier });
      },
      createReply() {
        const mentions = CommentUtils.getMentions(
          this.$refs.replyEditor.$refs.editorElement
        );
        this.$emit("create-reply", { reply: this.newReply, mentions });
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
      isVideo(file) {
        return isVideo(file);
      },
      isYoutube(file) {
        return isYoutube(file);
      },
      isPdf(file) {
        return isPdf(file);
      },
      showGallery(attachments, index, model) {
        this.$emit("show-gallery", { 
          attachments, 
          index, 
          model,
          comment: this.comment 
        });
      },
      editComment() {
        if (!this.userIsAuthor) return;
        this.$emit("edit-init", { identifier: this.comment.identifier });
        this.embeddedFilesCopy = _.cloneDeep(this.comment.embeddedFiles);
      },
      addCommentMention(event) {
        this.$emit("edit-add-mention", { identifier: event.id });
      },
      removeCommentMention(event) {
        this.$emit("edit-remove-mention", { identifier: event.identifier });
      },
      embedCommentFiles(files) {
        this.$store
          .dispatch("comments/uploadEmbeddedFiles", {
            files: files,
            model: this.comment.isCustomer ? this.commentType.model : this.commentType.fileModel ?? this.commentType.model,
            modelIdentifier:
              this.comment.isCustomer ? this.comment.model.identifier
                : this.commentType.fileModel === "Job"
                ? this.job?.identifier ?? this.jobIdentifier
                : this.comment.model.identifier,
          })
          .then((uploadedFiles) => {
            this.embeddedFilesCopy = (this.embeddedFilesCopy || []).concat(
              uploadedFiles
            );
            this.$emit("edit-embed-files", uploadedFiles);
          });
      },
      removeEmbeddedCommentFile(event) {
        const identifier = event.identifier;
        this.embeddedFilesCopy = _.reject(this.embeddedFilesCopy, { identifier });
        this.$emit("edit-remove-embedded-file", { identifier });
      },
      cancelEdit() {
        this.$emit("edit-cancel");
        this.updatedComment = this.comment.content;
        this.embeddedFilesCopy = null;
      },
      submitEdit() {
        const mentions = CommentUtils.getMentions(
          this.$refs.commentEditor.$refs.editorElement
        );
        this.$emit("edit-submit", { comment: this.updatedComment, mentions });
      },
      deleteComment() {
        if (!this.userIsAuthor) return;
        if (this.comment.replies?.length) return;
        this.$emit("delete", {
          comment: this.comment
        });
      },
      onReplyEditStart(replyIdentifier) {
        this.$emit("edit-init", {
          identifier: this.comment.identifier,
          replyIdentifier,
        });
      },
      submitReplyEdit(event, replyIdentifier) {
        this.$emit("edit-submit", {
          comment: event.comment,
          mentions: event.mentions,
          replyIdentifier: replyIdentifier,
        });
      },
      deleteReply(reply) {
        this.$emit("delete", {
          comment: this.comment,
          reply: reply
        });
      },
      parseAutoMentions() {
        if (!this.$refs.commentContent) return;
        const allMentions = this.comment.mentions ?? [];
        let explicitMentionIds = [];
        const explicitMentionTags = Array.from(this.$refs.commentContent.getElementsByClassName('mention'));
        if (explicitMentionTags.length) {
          explicitMentionIds = explicitMentionTags.map(el => el.dataset.id);
        }
        const implicitMentions = allMentions.filter(m => !explicitMentionIds.includes(m.identifier));
        this.autoMentions = _.uniqBy(implicitMentions, 'identifier');
        if (this.userIsCustomer) {
          const managerMentions = (this.userReplyMentions ?? []);
          this.replyAutoMentions = _.uniqBy(managerMentions, 'identifier');
        } else {
          this.replyAutoMentions = [this.comment.user];
        }
      }
    },
  };
</script>

<template>
  <div
    class="comment-display"
    :id="'comment_' + comment.identifier"
    v-bind="$attrs"
  >
    <div class="comment-user-image">
      <Avatar
        :label="comment.user.name.charAt(0) + comment.user.familyName.charAt(0)"
        :image="comment.user.profileThumbnailPic"
        size="xlarge"
        shape="circle"
      />
    </div>
    <div class="comment-content">
      <div class="comment-top-content">
        <div class="comment-user-and-likes">
          <div class="comment-user-name">
            <a v-if="userContactLink" :href="userContactLink" target="_blank">{{ comment.user.fullName + (userOrganisation ? ' (' + userOrganisation + ')' : '') }}</a>
            <span v-else>{{ comment.user.fullName }}</span>
            <div class="comment-upvotes">
              <div class="upvote-button-container">
                <Button
                  icon="fas fa-thumbs-up"
                  class="p-button-link"
                  :class="{ 'user-liked': userUpvoted }"
                  @click.stop="likeComment"
                />
              </div>
              <button
                v-if="comment.upvotes?.length"
                class="upvote-count"
                v-tooltip.top="upvoteUsers"
              >
                <span>{{ comment.upvotes?.length }}</span>
              </button>
            </div>
          </div>
        </div>
        <div class="comment-time">{{ formatDate(comment.modified) }}</div>
      </div>
      <template v-if="!editing">
        <div class="auto-comment-mention-tags">
          <span v-for="mention in autoMentions" :key="mention.identifier" class="mr-2">@{{ mention.fullName }}</span>
        </div>
        <div ref="commentContent" class="comment-message" v-html="formattedContent"></div>
        <div
          v-if="comment.embeddedFiles?.length"
          class="embedded-file-container"
        >
          <div
            v-for="(file, index) of comment.embeddedFiles"
            :key="file.identifier"
            class="embedded-file-preview"
            v-tooltip.top="file.originalName"
            @click.stop="showGallery(comment.embeddedFiles, index, 'embedded')"
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
        <div v-if="comment.attachements?.length" class="comment-attachments">
          <div
            v-for="(file, index) of comment.attachements"
            :key="file.identifier"
            class="file-preview"
            v-tooltip.top="file.originalName"
            @click.stop="showGallery(comment.attachements, index, 'attachment')"
          >
            <img v-if="isImage(file)" :src="'/' + encodeURIComponent(file.path)" />
            <template v-else>
              <i
                :class="getFileIcon(file)"
                class="file-icon"
              />
              <caption>{{ file.originalName }} {{}}</caption>
            </template>
          </div>
        </div>
        <div class="comment-footer">
          <div v-if="canEdit" class="edit-delete-buttons">
            <div class="clickable-icon" @click.stop="editComment">
              <i class="fas fa-pencil icon-grey" />
            </div>
            <div
              v-if="!comment.replies?.length"
              class="clickable-icon"
              @click.stop="deleteComment"
            >
              <i class="fas fa-trash-alt icon-grey" />
            </div>
          </div>
        </div>
      </template>
      <div v-else :ref="commentUpdateRef" class="comment-editor comment-update">
        <Editor
          ref="commentEditor"
          :outerRef="commentEditorContainerRef"
          v-model="updatedComment"
          :embeddedFiles="embeddedFilesCopy"
          :autofocus="true"
          :allowMentions="allowMentions"
          :acceptedFileTypes="acceptedFileTypes"
          @mention="addCommentMention"
          @remove-mention="removeCommentMention"
          @file-embed="embedCommentFiles"
          @remove-embedded-file="removeEmbeddedCommentFile"
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
  <div v-if="comment.replies?.length" class="comment-replies">
    <transition-group name="list-complete">
      <Reply
        v-for="(reply, index) in comment.replies"
        :key="reply.identifier"
        :comment="comment"
        :commentType="commentType"
        :reply="reply"
        :job="job"
        :jobIdentifier="jobIdentifier"
        :contextOrganisation="contextOrganisation"
        :loading="loading"
        :editingId="editingId"
        :showConnector="index < comment.replies.length - 1 || replyEditor"
        :allowMentions="allowMentions"
        :context="context"
        :additionalModel="comment.additionalModel"
        :additionalModelIdentifier="comment.additionalModelIdentifier"
        :acceptedFileTypes="acceptedFileTypes"
        @show-gallery="
          $emit('show-gallery', {
            attachments: reply.embeddedFiles,
            index: $event.index,
          })
        "
        @upvote="likeReply(reply.identifier)"
        @remove-upvote="unlikeReply($event, reply.identifier)"
        @edit-init="onReplyEditStart(reply.identifier)"
        @edit-cancel="$emit('edit-cancel')"
        @edit-add-mention="$emit('edit-add-mention', { identifier: $event.id })"
        @edit-remove-mention="
          $emit('edit-remove-mention', { identifier: $event.identifier })
        "
        @edit-embed-files="$emit('edit-embed-files', $event)"
        @edit-remove-embedded-file="
          $emit('edit-remove-embedded-file', { identifier: $event.identifier })
        "
        @edit-submit="submitReplyEdit($event, reply.identifier)"
        @delete="deleteReply(reply)"
      />
    </transition-group>
  </div>
  <div
    v-if="replyEditor"
    :ref="replyEditorRef"
    class="comment-editor reply-editor"
  >
    <div class="reply-editor-main">
      <div class="comment-user-image">
        <Avatar
          :label="currentUser.name.charAt(0) + currentUser.familyName.charAt(0)"
          :image="currentUser.profileThumbnailPic"
          shape="circle"
          size="large"
        />
      </div>
      <div class="reply-editor-input">
        <div class="auto-comment-mention-tags">
          <span v-for="mention in replyAutoMentions" :key="mention.identifier" class="mr-2">@{{ mention.fullName }}</span>
        </div>
        <Editor
          ref="replyEditor"
          :outerRef="replyEditorContainerRef"
          v-model="newReply"
          :embeddedFiles="replyFiles"
          type="reply"
          :autofocus="true"
          :allowMentions="allowMentions"
          :acceptedFileTypes="acceptedFileTypes"
          :error="error"
          @mention="addReplyMention"
          @remove-mention="removeReplyMention"
          @file-embed="embedReplyFiles"
          @remove-embedded-file="removeEmbeddedReplyFile"
          @submit="createReply"
        />
      </div>
    </div>
    <div class="editor-buttons reply-buttons">
      <div v-if="error">
        <small class="p-error">{{ error }}</small>
      </div>
      <div>
        <Button label="Cancel" @click.stop="toggleReplyEditor" />
      </div>
      <div>
        <FeedbackButton
          label="Reply"
          :loading="loading"
          :error="error ? 'Error' : ''"
          :disabled="!newReply && !replyFiles?.length"
          @click.stop="createReply"
         />
      </div>
    </div>
  </div>
  <div v-else class="comment-reply-button">
    <Button
      class="p-button-link"
      icon="fas fa-reply"
      label="Reply"
      @click.stop="toggleReplyEditor"
    />
  </div>
</template>
