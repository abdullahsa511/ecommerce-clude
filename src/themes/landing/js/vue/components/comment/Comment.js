import Editor from './Editor.js';
import Reply from './Reply.js';

export default {
  name: 'Comment',
  components: {
    Editor,
    Reply,
  },
  props: {
    comment: {
      type: Object,
      required: false,
      default: () => ({
        user: {
          fullName: 'John Doe',
        },
        identifier: 1,
        emoji: '👍',
        embeddedFiles: [
          {
            identifier: '1',
            originalName: 'Image.jpg',
          },
          {
            identifier: '2',
            originalName: 'Image.jpg',
          },
        ],
        attachements: [
          {
            identifier: '1',
            originalName: 'Image.jpg',
          },
          {
            identifier: '2',
            originalName: 'Image.jpg',
          },
        ],
        replies: [
          {
            identifier: '1',
            fullName: 'John Doe',
          },
          {
            identifier: '2',
            fullName: 'Jane Doe',
          },
        ],
        additionalModel: {
          identifier: '1',
          fullName: 'John Doe',
        },
        additionalModelIdentifier: '1',
      }),
    },
    image: {
      type: String,
      required: false,
      default: () => '/img/account-dashboard/profile-pic.png',
    },
    // userContactLink: {
    //     type: String,
    //     required: false,
    //     default: () => "https://google.com",
    // },
    editing: {
      type: Boolean,
      required: false,
      default: false,
    },
    autoMentions: {
      type: Object,
      required: false,
      default: () => ({
        identifier: '1',
        fullName: 'John Doe',
        email: 'john.doe@example.com',
        phone: '1234567890',
        address: '123 Main St, Anytown, USA',
        city: 'Anytown',
        state: 'CA',
        zip: '12345',
        country: 'USA',
      }),
    },
    formattedContent: {
      type: String,
      required: false,
      default: '',
    },
    commentEditorContainerRef: {
      type: Object,
      required: false,
      default: null,
    },
    embeddedFilesCopy: {
      type: Object,
      required: false,
      default: null,
    },
    true: {
      type: String,
      required: false,
      default: '',
    },
    allowMentions: {
      type: String,
      required: false,
      default: 'Internal',
    },
    acceptedFileTypes: {
      type: String,
      required: false,
      default: '',
    },


    commentType: {
      type: String,
      required: false,
      default: '',
    },
    job: {
      type: String,
      required: false,
      default: '',
    },
    jobIdentifier: {
      type: String,
      required: false,
      default: '',
    },
    contextOrganisation: {
      type: String,
      required: false,
      default: '',
    },
    replyEditor: {
      type: Object,
      required: false,
      default: () => ({
        identifier: '1',
        fullName: 'John Doe',
      }),
    },
    allowMentions: {
      type: String,
      required: false,
      default: '',
    },
    context: {
      type: String,
      required: false,
      default: '',
    },

  },

  emits: ['addCommentMention', 'removeCommentMention', 'embedCommentFiles', 'removeEmbeddedCommentFile', 'submitEdit', 'show-gallery', 'likeReply', 'unlikeReply', 'onReplyEditStart', 'edit-cancel', 'edit-add-mention', 'edit-remove-mention', 'edit-embed-files', 'edit-remove-embedded-file', 'submitReplyEdit', 'deleteReply', 'addReplyMention', 'removeReplyMention', 'embedReplyFiles', 'removeEmbeddedReplyFile', 'submitReplyEdit', 'deleteReply', 'toggleReplyEditor', 'createReply'],

  data() {
    return {
      label: null,
      icon: null,
      canEdit: true,
      canDelete: true,
      canReply: true,
      canUpvote: true,
      canDownvote: true,
      loading: false,
      error: false,
      editingId: 1,
      updatedComment: null,
      userContactLink: null,
      replyEditorContainerRef: null,
      replyAutoMentions: [
        {
          identifier: '1',
          fullName: 'John Doe',
        },
        {
          identifier: '2',
          fullName: 'Jane Doe',
        },
      ],
      replyFiles: [
        {
          identifier: '1',
          originalName: 'Image.jpg',
        },
        {
          identifier: '2',
          originalName: 'Image.jpg',
        },
      ],
      newReply: null,
    };
  },
  computed: {
    containerClass() {
      return 'p-avatar p-component';
    },

  },
  methods: {
    getImage() {
      return this.comment.user.profileThumbnailPic;
    },
    getFileIcon(file) {
      return file.type?.includes('image') ? 'fas fa-file-image' : 'fas fa-file';
    },
    commentUpdateRef() {
      return this.$refs.commentUpdateRef;
    },
    addCommentMention(event) {
      this.$emit('addCommentMention', event);
    },
    removeCommentMention(event) {
      this.$emit('removeCommentMention', event);
    },
    embedCommentFiles(event) {
      this.$emit('embedCommentFiles', event);
    },
    removeEmbeddedCommentFile(event) {
      this.$emit('removeEmbeddedCommentFile', event);
    },
    submitEdit(event) {
      this.$emit('submitEdit', event);
    },
    replyEditorRef(el) {
      this.replyEditorContainerRef = el;
    },

    addReplyMention(event) {
      this.$emit('addReplyMention', event);
    },
    removeReplyMention(event) {
      this.$emit('removeReplyMention', event);
    },
    embedReplyFiles(event) {
      this.$emit('embedReplyFiles', event);
    },
    removeEmbeddedReplyFile(event) {
      this.$emit('removeEmbeddedReplyFile', event);
    },
    submitReplyEdit(event) {
      this.$emit('submitReplyEdit', event);
    },
    deleteReply(event) {
      this.$emit('deleteReply', event);
    },
    toggleReplyEditor() {
      this.replyEditor = !this.replyEditor;
    },
    createReply() {
      this.$emit('createReply');
    },
  },


  watch: {
    'comment.identifier': {
      handler(newVal) {
        console.log('Check Date for getting the comment', newVal);
      },
      deep: true,
      immediate: true,
    },
  },


  template: /* html */ `
    <div class="comment">
      <div
        v-if="!editing"
        class="comment-display"
        id="comment_1"
        v-bind="$attrs"
      >
        <div class="comment-user-image">
          <!-- Avatar component -->
          <div :class="containerClass">
            <img :src="image" v-if="image">
            <span class="iconClass" v-else></span>
            <span class="avatar-emoji" v-if="comment?.emoji">{{ comment.emoji }}</span>
          </div>
        </div>
        <div class="comment-content">
          <div class="comment-top-content">
            <div class="comment-user-and-likes">
              <div class="comment-user-name">
                <a v-if="userContactLink" :href="userContactLink" target="_blank">{{ comment?.user?.fullName }}</a>
                <span v-else>{{ comment?.user?.fullName }}</span>
              </div>
              <div class="comment-upvotes">
                <div class="upvote-button-container">
                  <button
                    icon="fas fa-thumbs-up"
                    class="p-button-link"
                  />
                </div>
                <button
                  class="upvote-count"
                  title="3 upvotes"
                >
                  <span> 3 </span>
                </button>
              </div>
            </div>
            <div class="comment-time"></div>
          </div>
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
              title="3 upvotes"
            >
              <!-- <img v-if="isImage(file)" :src="'/' + encodeURIComponent(file.path)" /> -->
              <template>
                <i
                  :class="'fas fa-file-image'"
                  class="file-icon"
                />
                <caption>{{ file.originalName }} </caption>
              </template>
            </div>
          </div>
          <div v-if="comment.attachements?.length" class="comment-attachments">
            <div
              v-for="(file, index) of comment.attachements"
              :key="file.identifier"
              class="file-preview"
              title="1 attachment"
            >
              <template>
                <i
                  :class="getFileIcon(file)"
                  class="file-icon"
                />
                <caption>{{ file.originalName }} 1 attachment</caption>
              </template>
            </div>
          </div>
          <div class="comment-footer">
            <div v-if="canEdit" class="edit-delete-buttons">
              <div class="clickable-icon">
                <i class="fas fa-pencil icon-grey" />
              </div>
              <div
                v-if="canDelete"
                class="clickable-icon"
              >
                <i class="fas fa-trash-alt icon-grey" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        v-else
        :ref="commentUpdateRef"
        class="comment-editor comment-update"
      >
        <Editor
          v-if="false"
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
          <div class="clickable-icon">
            <i class="fas fa-times" />
          </div>
          <div class="clickable-icon">
            <i v-if="!loading" class="fas fa-check"></i>
            <i v-else class="p-button-icon ml-2 fas fa-spin fa-spinner-third" />
          </div>
        </div>
      </div>
      <div v-if="false" class="comment-replies">
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
            <!-- Avatar component -->
            <div :class="containerClass">
              <img :src="image" v-if="image">
              <span class="p-avatar-text" v-else></span>
              <span :class="iconClass" v-else></span>
              <span class="avatar-emoji" v-if="comment?.emoji">{{ comment.emoji }}</span>
            </div>
          </div>
          <div class="reply-editor-input">
            <div class="auto-comment-mention-tags">
              <span v-for="mention in replyAutoMentions" :key="mention.identifier" class="mr-2">@{{ mention.fullName }}</span>
            </div>
            <Editor
              v-if="false"
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
    </div>
    `
}