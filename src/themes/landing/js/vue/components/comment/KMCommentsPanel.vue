<template>
  <!-- Error display -->
  <div v-if="commentsLoading" class="panel-loader">
    <ProgressSpinner style="width: 50px; height: 50px" />
  </div>
  <template v-if="loadingError">
    <div class="comment-error-message">
      <Message severity="error">{{ loadingError }}</Message>
      <Button label="Retry" icon="fas fa-redo" @click="loadComments" />
    </div>
  </template>
  <div v-else class="comment-panel-container" :class="{ 'customer-comments': commentSection === 'Customer' && !commentsLoading }">
    <!-- Comment Panel Header And TabMenu -->
    <div class="side-panel-header">
      <div class="side-panel-title">
        <h2 v-html="shortTitle" v-tooltip.bottom="titleTooltip"></h2>
      </div>
      <div class="side-panel-menu">
        <div class="top-level-tab-menu comment-menu">
          <TabMenu :model="commentsTabMenu">
            <template #item="{ item }">
              <span
                href="#"
                class="p-menuitem-link"
                :class="{ 'p-highlight': item.label === commentSection }"
                @click="commentSection = item.label"
              >
                <span class="p-menuitem-text">{{ item.label }}</span>
                <Badge
                  v-if="commentCounts[item.label]"
                  :value="commentCounts[item.label]"
                />
              </span>
            </template>
          </TabMenu>
          <div class="menu-manager-overview">
            <span v-if="projectManager"><b>Project Manager:</b> {{ projectManager }}</span>
            <span v-if="accountManager"><b>Account Manager:</b> {{ accountManager }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="side-panel-content comment-sidebar">
      <!-- Search Bar -->
      <div class="comment-util-bar">
        <div class="comment-util-left">
          <div class="comment-search-bar p-input-icon-right">
            <SearchBar
              ref="searchBar"
              v-model="searchTerm"
              placeholder="Search Comments..."
              :autofocus="true"
              @submit="searchComments"
            />
            <i
              v-if="searchTerm"
              class="fal fa-times clickable-input-icon"
              @click="clearFilter"
            />
          </div>
        </div>
        <div class="comment-util-right">
          <div class="comment-search-buttons">
            <div class="comment-search-left">
              <div>
                <Button
                  @click="searchComments"
                  :disabled="!searchTerm && !userSearchTerm"
                >
                  <span class="p-button-label">Search</span>
                  <i
                    v-if="loading['commentsFilter']"
                    class="p-button-icon ml-2 fas fa-spin fa-spinner-third"
                    style="color: #ffffff"
                  />
                </Button>
              </div>
              <div>
                <Button @click="clearFilter" :disabled="!filterApplied">
                  <span class="p-button-label">Clear</span>
                </Button>
              </div>
              <div>
                <button
                  class="settings-button"
                  @click="searchOptionsVisible = !searchOptionsVisible"
                >
                  <i class="fas fa-cog" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Search Options -->
      <transition name="p-toggleable-content">
        <div v-if="searchOptionsVisible" class="search-options">
          <div class="search-option-selects">
            <MultiSelect
              v-model="searchOptions.searchFor"
              :options="searchOptions.searchForOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Search For"
            />
            <MultiSelect
              v-model="searchOptions.include"
              :options="searchOptions.searchIncludeOptions"
              optionLabel="label"
              optionValue="value"
              placeholder="Include"
            />
          </div>
        </div>
      </transition>
      <!-- New Comment Editor -->
      <div :ref="newCommentRef" class="comment-editor" :class="{ 'customer-section-editor': commentSection === 'Customer' }">
        <div v-if="customerMentions" class="comment-author-tag">
          <span v-for="user in customerMentions" :key="user.identifier" class="mr-2">@{{ user.fullName }}</span>
        </div>
        <Editor
          :key="commentSection"
          ref="commentEditor"
          :outerRef="newCommentEditorRef"
          v-model="newComment"
          editorStyle="minHeight: 80px"
          placeholder="Type your comment here..."
          :embeddedFiles="embeddedFiles"
          :allowMentions="commentSection === 'Internal'"
          :error="errors?.newComment?.comment"
          :acceptedFileTypes="allowedFileTypes"
          :targetUser="targetUser"
          :showToDriver="showToDriver"
          autofocus
          @mention="mentionUser"
          @remove-mention="removeMention"
          @file-embed="embedFiles"
          @remove-embedded-file="removeEmbeddedFile"
          @submit="createComment"
        />
        <!-- Attachments Uploader -->
        <div v-if="attachmentsVisible" class="attachments-container">
          <div class="attachment-uploader">
            <Uploader
              name="files[]"
              url="/api/comments/upload-attachments"
              :customUpload="true"
              :auto="true"
              :multiple="true"
              :showMobileCamera="isMobile"
              :maxFileSize="20971520"
              :fileLimit="100"
              chooseLabel="Browse"
              :accept="allowedFileTypes"
              @uploader="uploadAttachments"
            >
            </Uploader>
          </div>
          <!-- Attachments Table -->
          <div class="attachments-list" v-if="attachedFiles?.length">
            <DataTable :value="attachedFiles" responsiveLayout="scroll">
              <Column style="width: 10%">
                <template #body="{ data }">
                  <img
                    v-if="isImage(data)"
                    role="presentation"
                    :alt="data.originalName || data.name"
                    :src="data.path ? '/' + encodeURIComponent(data.path) : data.objectURL"
                  />
                  <i
                    v-else-if="data.fileType"
                    :class="getFileIcon(data)"
                    class="file-icon"
                  />
                </template>
              </Column>
              <Column
                style="width: 20%; min-width: 100px; overflow-wrap: anywhere"
              >
                <template #body="{ data }">
                  {{ data.originalName || data.name }}
                </template>
              </Column>
              <Column
                v-if="showAttachmentOptions"
                header="Show to Manufacturing"
                style="width: 20%"
              >
                  <template #body="{ data }">
                    <div class="p-d-flex p-jc-center">
                      <Checkbox v-model="data.sm" :binary="true" :disabled="data.purchaseOrderIdentifier === 'None'"/>
                    </div>
                  </template>
              </Column>
              <Column v-if="showAttachmentOptions && showtoDrawingSetOptions" header="Drawing Set" style="width: 20%">
                <template #body="{ data }">
                  <div class="p-d-flex p-jc-center">
                    <Checkbox v-model="data.ds" :binary="true" />
                  </div>
                </template>
              </Column>
              <Column v-if="showAttachmentOptions" header="Linked Purchase Orders" style="width: 20%">
                <template #body="{ data }">
                  <Dropdown
                    v-model="data.purchaseOrderIdentifier"
                    :options="purchaseOrderOptions"
                    optionLabel="label"
                    optionValue="value"
                    :disabled="isLpo || isWorkcenter"
                    @change="onPurchaseOrderChange($event, data)"
                  />
                </template>
              </Column>
              <Column style="width: 5%; text-align: center">
                <template #body="{ data }">
                  <div class="upload-progress-spinner">
                    <i
                      class="fad fa-spinner fa-spin"
                      v-show="uploadProgress[data.groupName] < 100"
                    />
                    <span class="upload-progress-percent">{{
                      uploadProgress[data.groupName] + "%"
                    }}</span>
                  </div>
                </template>
              </Column>
              <Column style="width: 5%; text-align: center">
                <template #body="slotProps">
                  <div
                    class="clickable-icon"
                    @click="
                      removeAttachment({
                        identifier: slotProps.data.identifier,
                      })
                    "
                  >
                    <i class="fas fa-times" />
                  </div>
                </template>
              </Column>
            </DataTable>
          </div>
        </div>
        <p class="mt-2" v-if="errors?.newComment">
          <small class="p-error">{{ errors?.newComment }}</small>
        </p>
        <p class="mt-2" v-if="errors?.commentAttachment">
          <small class="p-error">{{ errors?.commentAttachment }}</small>
        </p>
        <div class="editor-buttons">
          <div class="p-d-flex p-ai-center p-jc-center" style="gap: 1rem;" v-if="isLogisticComment">
            <Checkbox v-model="showToDriver" :binary="true" />
            <span class="font-weight-bold">Show to Service Units</span>
          </div>
          <div v-else></div>
          <div class="p-d-flex p-ai-center p-jc-center" style="gap: 1rem;">
            <div>
              <Button
                :label="attachmentsVisible ? 'Hide' : 'Attachments'"
                @click="attachmentsVisible = !attachmentsVisible"
              />
            </div>
            <div>
              <Button
                :disabled="createCommentDisabled"
                @click="createComment"
              >
                <span class="p-button-label">Comment</span>
                <i
                  v-if="loading?.newComment"
                  class="p-button-icon ml-2 fas fa-spin fa-spinner-third"
                  style="color: #ffffff"
                />
              </Button>
            </div>
          </div>
        </div>
      </div>
      <!-- Comments Thread -->
      <div v-if="activeUser" ref="commentsContainer" class="comments-container">
        <template v-if="filteredComments">
          <div class="comments-container-top">
            <div class="expand-all-container">
              <Button
                class="p-button-info"
                :icon="
                  expandedComments.length
                    ? 'far fa-chevron-down'
                    : 'far fa-chevron-right'
                "
                @click="toggleAllCommentExpand"
              />
            </div>
            <div class="comments-container-top-right">
              <div class="comments-meta-info">
                <small>{{
                  `${filteredComments.length} of ${
                    commentMeta?.pagination?.total ?? 0
                  }`
                }}</small>
                <div
                  v-if="loadedCommentCount < commentMeta?.pagination?.total"
                  class="clickable-icon p-d-inline-flex"
                  v-tooltip.top="'Load All'"
                  @click="loadAllComments"
                >
                  <i class="fas fa-comment-lines main-icon" />
                  <span class="fal fa-arrow-down sub-icon" />
                </div>
              </div>
              <div
                v-if="showShowAllButton"
                class="comments-show-all"
              >
                <Button
                  v-if="!showingJobComments && !isDriverInstaller"
                  label="Show Job Comments"
                  @click="loadJobComments"
                />
                <Button
                  v-else
                  :label="`Show ${commentType.displayName} Comments`"
                  @click="loadComments"
                />
              </div>
            </div>
          </div>
          <Accordion
            :multiple="true"
            v-model:activeIndex="expandedComments"
            lazy
          >
            <AccordionTab
              v-for="comment in filteredComments"
              :key="comment.identifier"
            >
              <template #header>
                <div class="d-flex align-items-center">
                  <span
                    class="p-accordion-header-text"
                    v-html="getCommentHeader(comment)"
                  ></span>
                  <span v-if="comment.model.internalLogisticsThread">
                    <i class="fas fa-lock ml-2" v-tooltip.top="'This comment is only visible in this logistics thread and cannot be viewed anywhere else.'"></i>
                  </span>
                  <span v-if="comment.model.deletedLogistic">
                    <i class="fas fa-truck ml-2" v-tooltip.top="comment.model.logisticType.name + ' deleted on ' + formatDate(comment.model.deletedLogisticOn)"></i>
                  </span>
                </div>
              </template>
              <Comment
                :comment="comment"
                :job="job"
                :jobIdentifier="jobIdentifierLocal"
                :documentOrganisation="documentOrganisation"
                :replyEditor="replyingComment === comment.identifier"
                :replyMentions="replyMentions"
                :replyFiles="replyFiles"
                :loading="loading?.newReply || loading?.updateComment"
                :error="errors?.newReply || errors?.updateComment"
                :editingId="editingComment?.identifier"
                :class="{
                  highlight: comment.identifier === targetComment?.identifier,
                }"
                :allowMentions="commentSection === 'Internal'"
                :isMobile="isMobile"
                :acceptedFileTypes="allowedFileTypes"
                @show-gallery="showAttachmentGallery($event, comment)"
                @upvote="upvoteComment(comment.identifier)"
                @remove-upvote="removeCommentUpvote($event, comment.identifier)"
                @upvote-reply="upvoteReply($event, comment.identifier)"
                @remove-upvote-reply="
                  removeReplyUpvote($event, comment.identifier)
                "
                @start-reply="onCommentReplyStart(comment.identifier)"
                @cancel-reply="clearReplyData"
                @reply-mention="addReplyMention($event)"
                @reply-remove-mention="removeReplyMention($event)"
                @reply-embed-files="embedReplyFiles($event)"
                @reply-remove-embedded-file="removeEmbeddedReplyFile($event)"
                @create-reply="createReply($event)"
                @edit-init="
                  onCommentEditStart($event.identifier, $event.replyIdentifier)
                "
                @edit-cancel="onCommentEditCancel"
                @edit-add-mention="addEditMention($event)"
                @edit-remove-mention="removeEditMention($event)"
                @edit-embed-files="editAddFile($event)"
                @edit-remove-embedded-file="editRemoveFile($event)"
                @edit-submit="submitCommentEdit(comment.identifier, $event)"
                @delete="deleteComment"
              />
            </AccordionTab>
          </Accordion>
          <div
            v-if="loadingMore"
            class="p-d-flex p-ai-center p-jc-center"
            style="height: 100px"
          >
            <ProgressSpinner style="width: 50px; height: 50px" />
          </div>
        </template>
      </div>
      <FileViewer
        v-if="attachmentGalleryVisible"
        :visible="attachmentGalleryVisible"
        @update:visible="onGalleryHide"
        :items="galleryItems"
        :activeIndex="activeGalleryIndex"
        @rotated="rotateFile"
      />
    </div>
  </div>
</template>

<script>
  import { mapState } from "vuex";
  import CommentService from "../../admin/services/Comments/CommentService";
  import FileService from "../../admin/services/Files/FileService";
  import { CommentTypes } from "../../constraints/Comments/CommentTypes";
  import { CommentSearchOptions } from "../../constraints/Comments/CommentConstraints";
  import NotificationButton from "../buttons/NotificationButton.vue";
  import SearchBar from "../inputs/KMSearchBar.vue";
  import Comment from "./Comment.vue";
  import Uploader from "../inputs/KMUploader.vue";
  import {
    formatTimeAndDate,
    parseDateString,
  } from "../../utils/DateTimeUtils";
  import CommentUtils from "../../admin/services/Comments/CommentUtils";
  import StringUtils from "../../utils/StringUtils";
  import Button from "../../components/buttons/KMButton.vue";
  import { getFileTypeIcon, InternalAllowedFileTypes, isDoc, isImage, isPdf, isSpreadsheet, isVideo, isYoutube } from '../../utils/FileUtils';
  import PermissionsHandler from '../../utils/Permissions/PermissionsHandler';
  import { Permissions } from '../../constraints/Permissions/KMPermissions';
import { DomHandler } from 'primevue/utils';
import { clearErrorsForIdentifier } from '../../utils/Feedback/FeedbackUtils';

  /** @typedef {import('../../../../models/Comments/Comment')} Comment */

  export default {
    components: { SearchBar, NotificationButton, Comment, Uploader, Button },
    emits: ["hide", 'commentCreated','commentDeleted', 'upvoteComment','removeCommentUpvote', 'upvoteReply', 'removeReplyUpvote', 'updateFollowerBroadcast','updateFollowerBroadCast'],
    props: {
      job: Object,
      jobIdentifier: String,
      orderIdentifier: String,
      docType: String,
      document: Object,
      modelType: String,
      model: Object,
      modelIdentifier: String,
      followerIdentifier: String,
      followerName: String,
      stream: Number,
      section: {
        type: String,
        default: 'Internal'
      },
      targetComment: Object,
      additionalModelType: {
        type: String,
        default: null,
      },
      additionalModel: Object,
      additionalModelIdentifier: {
        type: String,
        default: null,
      },
      targetUser: {
        type: Object,
        default: null
      },
    },
    beforeCreate() {
      if (!this.$store.hasModule("comments")) {
        this.$store.registerModule("comments", CommentService);
      }
    },
    data() {
      return {
        /** @type {'Internal'|'Customer'} */
        commentSection: this.section,
        selectedDate: null,
        searchTerm: "",
        searchOptions: _.cloneDeep(CommentSearchOptions),
        searchOptionsVisible: false,
        newCommentEditorRef: null,
        newComment: null,
        allowedFileTypes: InternalAllowedFileTypes,
        embeddedFiles: null,
        attachedFiles: null,
        mentions: null,
        attachmentsVisible: false,
        expandedComments: [],
        filteredComments: null,
        replyingComment: null,
        replyMentions: null,
        replyFiles: null,
        editingComment: null,
        editingExistingMentions: null,
        editingNewMentions: null,
        editingRemovedMentions: null,
        editingFiles: null,
        attachmentGalleryVisible: false,
        galleryModel: null,
        recentlyClosedAttachmentGallery: false,
        activeGalleryIndex: 0,
        galleryItems: null,
        userSuggestions: [],
        userSearchTerm: "",
        filterApplied: false,
        commentsLoading: true,
        showingJobComments: false,
        focusedComment: this.targetComment,
        loadingMore: false,
        loadingError: null,
        uploadInProgress: false,
        isMobile: DomHandler.getViewport().width < 1025,
        showToDriver: false,
      };
    },
    resizeListener: null,
    keyboardListener: null,
    computed: {
      isLogisticComment(){
        return this.docType === "logisticdate";
      },
      isDriverInstaller(){
        return PermissionsHandler.isDriverInstaller();
      },
      jobIdentifierLocal() {
        return this.jobIdentifier ?? this.job?.identifier;
      },
      routeIdentifier() {
        return this.commentType?.apiRoute === "stream-level"
          ? this.jobIdentifierLocal
          : this.modelIdentifier ?? this.model.identifier;
      },
      apiRoute() {
        return this.commentType?.apiRoute;
      },
      loadMoreRequest() {
        let request = {
          loadedCount: this.comments.length,
        };
        if (this.showingJobComments || this.commentType.model === "Job") {
          request.jobIdentifier = this.jobIdentifierLocal;
        } else if (this.commentType.apiRoute === "stream-level") {
          request.jobIdentifier = this.jobIdentifierLocal;
          request.stream = this.stream;
        } else {
          request.model = this.commentType.model;
          request.modelIdentifier = this.model.identifier;
        }
        return request;
      },
      ...mapState({
        /** @returns {Comment[]} */
        comments: (state) => state.comments.comments,
        commentMeta: (state) => state.comments.commentMeta,
        workcenterContext: (state) => state.comments.workcenterContext,
        activeUser: (state) => state.global.activeUser,
        loading: (state) => state.global.loading,
        success: (state) => state.global.success,
        errors: (state) => state.global.errors,
        uploadProgress: (state) => state.comments.uploadProgress,
      }),
      loadedCommentCount() {
        return this.comments.length;
      },
      sortedComments() {
        return _.reverse(
          _.sortBy(this.comments, function (c) {
            return parseDateString(c.created);
          })
        );
      },
      commentCounts() {
        return {
          Internal: 0,
          Customer: 0
        }
      },
      showAttachmentOptions() {
        return this.commentSection === 'Internal' && this.commentType.fileModel === 'Job';
      },
      showtoDrawingSetOptions() {
        return (this.commentType.model === 'Order'
        || this.commentType.model === 'OrdersItem'
        || this.commentType.model === 'QuotesItem'
        || this.commentType.model === 'Quote'
        || this.commentType.model === 'Drawing'
        || this.commentType.model === 'LogisticDate'
        || this.commentType.model === 'WorkcenterItem'
        || this.commentType.model === 'PurchaseOrder'
        );
      },
      // hasPurchaseOrderOptions() {
      //   return ["order", "ordersitem"].includes(this.docType) && this.document || this.isLpo || this.isWorkcenter;
      // },
      purchaseOrderOptions() {
        const noneOption = { label: "None", value: "None" };
        const createPurchaseOrderOption = (po) => ({
          label: po.reference,
          value: po.identifier,
        });
        const addNoneOption = (options) => [noneOption, ...options];
        if (["order", "ordersitem", "logisticdate"].includes(this.docType) && this.document) {
          if (this.document?.document === "Purchase Order") {
            const options = [createPurchaseOrderOption(this.document)];
            return addNoneOption(options);
          } else {
            const options = this.document.purchaseOrders.map(createPurchaseOrderOption);
            return addNoneOption(options);
          }
        }
        if (this.isLpo) {
          return addNoneOption([createPurchaseOrderOption(this.model)]);
        }
        if (this.isWorkcenter) {
          if (this.document?.document === "Purchase Order") {
            const options = [createPurchaseOrderOption(this.document)];
            return addNoneOption(options);
          } else {
            const options = this.document.purchaseOrders.map(createPurchaseOrderOption);
            return addNoneOption(options);
          }
        }
        return [];
      },
      accountManager() {
        if (this.document) {
          return this.document?.accountManager?.fullName || this.document?.order?.accountManager?.fullName;
        } else if (this.model?.order) {
          return this.model?.order?.accountManager?.fullName;
        }
        return null;
      },
      projectManager() {
        if (this.document) {
          return this.document?.projectManager?.fullName || this.document?.order?.projectManager?.fullName;
        } else if (this.model?.order) {
          return this.model?.order?.projectManager?.fullName;
        }
        return null;
      },
      isLpo() {
        return this.docType === "purchaseorder";
      },
      isWorkcenter() {
        return this.additionalModelType === "purchaseorder";
      },
      commentType() {
        return CommentTypes[this.docType];
      },
      hasCustomerSection() {
        return this.commentType?.showCustomer && PermissionsHandler.userCan(Permissions.CAN_COMMENT_CUSTOMER);
      },
      documentOrganisation() {
        return this.document?.organisation || null;
      },
      showShowAllButton() {
        return this.commentSection === 'Internal'
          && this.commentType.fileModel === 'Job'
          && this.commentType.model !== 'Job';
      },
      commentsTabMenu() {
        const menu = [
          {
            label: "Internal",
          }
        ];
        if (this.hasCustomerSection) menu.push({
          label: "Customer",
        });
        return menu;
      },
      createCommentDisabled() {
        //if (!this.newComment && !this.attachedFiles?.length && !this.embeddedFiles?.length) return true;
        if (!this.newComment) return true;
        if (this.uploadInProgress) return true;
        return false;
      },
      commentTitle() {
        // console.log(this.commentType);
        // console.log(this.model);
        // console.log(this.followerName);
        return CommentUtils.getCommentTitle(this.commentType, this.model, false, this.followerName);
      },
      shortTitle() {
        return StringUtils.shorten(this.commentTitle, 60);
      },
      titleTooltip() {
        if (this.commentTitle.length > 60) {
          return this.commentTitle;
        }
        return null;
      },
      customerMentions() {
        return null;
        //if (this.commentSection !== 'Customer') return null;
        let customerMentions = [];
        if (this.model?.billTo?.length) {
          customerMentions.push(...this.model.billTo);
        }
        if (this.model?.shipTo?.length) {
          customerMentions.push(...this.model.shipTo);
        }
        return _.uniqBy(customerMentions, 'identifier');
      }
    },
    created() {
      this.loadComments();
    },
    mounted() {
      this.onWindowResize();
      this.resizeListener = this.onWindowResize;
      window.addEventListener("resize", this.resizeListener);
      this.keyboardListener = this.onKeydown.bind(this);
      setTimeout(() => {
        window.addEventListener("keydown", this.keyboardListener);
      }, 100);

      if (this.targetUser && this.targetUser.identifier) {
        setTimeout(() => {
          this.showToDriver = true;
        }, 0);
      }
    },
    beforeUnmount() {
      this.$store.unregisterModule("comments");
    },
    unmounted() {
      window.removeEventListener("resize", this.resizeListener);
      window.removeEventListener("keydown", this.keyboardListener);
    },
    watch: {
      section(value) {
        this.commentSection = value;
      },
      commentSection() {
        this.loadComments();
        this.clearNewComment();
        this.clearReplyData();
        this.clearUpdateData();
      },

      targetComment(value) {
        this.focusedComment = value;
        const commentInFeed = _.find(this.filteredComments, { identifier: value.identifier });
        let allRepliesInFeed = false;
        if (commentInFeed) {
          allRepliesInFeed = _.every(value.replies ?? [], r => _.some(commentInFeed.replies ?? [], { identifier: r.identifier }));
        }
        if (!commentInFeed || !allRepliesInFeed) {
          this.loadComments();
        } else {
          this.scrollToTargetComment(true);
        }
      },
      sortedComments(newValue) {
        this.filteredComments = [...newValue];
        this.setExpandedComments();
      },
      success: {
        handler(success) {
          if (success.newComment) {
            setTimeout(() => {
              this.clearNewComment();
              this.attachmentsVisible = false;
              document.body.click();
            });
          } else if (success.newReply) {
            this.replyingComment = null;
            this.clearReplyData();
          } else if (success.updateComment) {
            this.editingComment = null;
            this.clearUpdateData();
          }
        },
        deep: true,
      },
    },
    methods: {
      toggleShowToDriver(){
        if(!this.showToDriver){
          // Remove driver mention
          // this.mentions = _.reject(this.mentions, { identifier: this.targetUser.identifier });
        }
      },
      rotateFile(event){
        this.$store.dispatch("comments/updateAttachment", {
          ...event, comment:this.activeGalleryComment, model: this.galleryModel
        });
      },
      loadComments() {
        this.loadingError = null;
        this.commentsLoading = true;
        if (this.commentSection === 'Internal') {
          this.$store
            .dispatch("comments/getComments", {
              commentType: CommentTypes[this.docType],
              identifier: this.routeIdentifier,
              stream: this.stream,
              customer: this.commentSection === 'Customer',
              followerIdentifier: this.followerIdentifier,
              orderIdentifier: this.orderIdentifier,
              additionalModelIdentifier: this.additionalModelIdentifier
            })
            .then(() => {
              this.showingJobComments = false;
              this.scrollToTargetComment();
            })
            .catch((error) => {
              this.loadingError = error;
            })
            .finally(() => (this.commentsLoading = false));
        } else if (this.commentSection === 'Customer') {
          this.$store
            .dispatch("comments/getCustomerComments", {
              model: this.commentType.model,
              identifier: this.model.identifier
            })
            .then(() => {
              this.showingJobComments = false;
              this.scrollToTargetComment();
            })
            .catch((error) => {
              this.loadingError = error;
            })
            .finally(() => (this.commentsLoading = false));
        }
      },
      loadAllComments() {
        this.loadingError = null;
        this.loadingMore = true;
        this.focusedComment =
          this.filteredComments[this.filteredComments.length - 1];
        this.scrollToTargetComment(true);
        this.$store
          .dispatch("comments/loadMoreComments", this.loadMoreRequest)
          .catch((error) => {
            this.loadingError = error;
          })
          .finally(() => (this.loadingMore = false));
      },
      loadJobComments() {
        this.loadingError = null;
        this.commentsLoading = true;
        this.$store
          .dispatch("comments/getComments", {
            commentType: CommentTypes.job,
            identifier: this.jobIdentifierLocal,
            stream: null,
          })
          .then(() => {
            this.showingJobComments = true;
            this.focusedComment = null;
          })
          .catch((error) => {
            this.loadingError = error;
          })
          .finally(() => (this.commentsLoading = false));
      },
      scrollToTargetComment(smooth = false) {
        setTimeout(() => {
          if (this.focusedComment && this.$refs.commentsContainer) {
            const previousTarget =
              this.$refs.commentsContainer.querySelector(".targeted-comment");
            if (previousTarget)
              previousTarget.classList.remove("targeted-comment");
            const el = this.$refs.commentsContainer.querySelector(
              "#comment_" + this.focusedComment.identifier
            );
            if (el) {
              const accordionTab = el.parentElement?.parentElement?.parentElement;
              if (accordionTab) accordionTab.classList.add("targeted-comment");
              el.scrollIntoView({
                block: "center",
                inline: "nearest",
                behavior: smooth ? "smooth" : "auto",
              });
            }
          }
        }, 0);
      },
      newCommentRef(el) {
        this.newCommentEditorRef = el;
      },
      getCommentHeader(comment) {
        return CommentUtils.getCommentHeaderWithLink(comment);
      },
      formatDate(date) {
        return formatTimeAndDate(date);
      },
      setExpandedComments() {
        const modelType = this.commentType.model;
        if (this.filteredComments.length < 30) {
          this.expandedComments = _.range(this.filteredComments.length);
        } else {
          const indexes = _.map(
            _.keys(
              _.pickBy(this.filteredComments, function (c) {
                return c.model?.name === modelType;
              })
            ),
            Number
          );
          this.expandedComments = indexes;
        }
      },
      expandAllComments() {
        this.expandedComments = _.range(this.filteredComments.length);
      },
      toggleAllCommentExpand() {
        if (this.expandedComments.length === 0) {
          this.expandedComments = _.range(this.filteredComments.length);
        } else {
          this.expandedComments = [];
        }
      },
      searchComments() {
        if (!this.searchTerm) {
          this.filteredComments = [...this.sortedComments];
          return;
        };
        if (this.commentMeta.pagination?.total_pages > 1) {
          this.sendFilterRequest();
        } else {
          const query = this.searchTerm.trim().toLowerCase();
          const filtered = this.comments.filter((c) =>
            this.searchOptions.include.includes(c.model?.name)
          );
          const scores = {};
          filtered.forEach((c) => {
            let score = 0;
            if (
              this.searchOptions.searchFor.includes("author") &&
              c.user?.fullName?.toLowerCase().includes(query)
            )
              score = 6;
            else if (
              this.searchOptions.searchFor.includes("content") &&
              c.content?.toLowerCase().includes(query)
            )
              score = 5;
            else if (
              this.searchOptions.searchFor.includes("content") &&
              _.some(c.replies, (r) => r.content.toLowerCase().includes(query))
            )
              score = 5;
            else if (c.model?.reference?.toLowerCase().includes(query)) score = 4;
            else if (c.model?.name?.toLowerCase().includes(query)) score = 3;
            else if (c.model?.title?.toLowerCase().includes(query)) score = 2;
            else if (
              _.some(c.attachements, (f) =>
                f.originalName.toLowerCase().includes(query)
              )
            )
              score = 1;
            scores[c.identifier] = score;
          });
          const matches = filtered.filter((c) => scores[c.identifier] > 0);
          const sorted = matches.sort(
            (c1, c2) => scores[c2.identifier] - scores[c1.identifier]
          );
          this.filteredComments = sorted;
          this.expandAllComments();
        }
        this.filterApplied = true;
      },
      sendFilterRequest() {
        const request = {
          jobIdentifier: this.jobIdentifierLocal,
          modelIdentifier: this.model.identifier,
          model: this.commentType.model,
          search: this.searchTerm,
          comment: this.searchOptions.searchFor.includes("content"),
          author: this.searchOptions.searchFor.includes("author"),
          quote: this.searchOptions.include.includes("Quote"),
          order: this.searchOptions.include.includes("Order"),
          lpo: this.searchOptions.include.includes("PurchaseOrder"),
          dpo: this.searchOptions.include.includes("DirectpurchaseOrder"),
          orderItem: this.searchOptions.include.includes("OrdersItem"),
          logistic: this.searchOptions.include.includes("LogisticDate"),
          manufacturing: this.searchOptions.include.includes("WorkcenterItem"),
        };
        if (this.stream) request.streamId = this.stream;
        this.commentsLoading = true;
        this.$store.dispatch("comments/filterComments", request).then(() => {
          this.commentsLoading = false;
        });
      },
      clearFilter() {
        this.selectedDate = null;
        this.searchTerm = "";
        this.commentsLoading = true;
        this.searchOptions = _.cloneDeep(CommentSearchOptions);
        if (this.commentMeta.total_pages > 0) {
          this.loadComments();
        } else {
          setTimeout(() => {
            this.filteredComments = this.comments;
            this.setExpandedComments();
            this.commentsLoading = false;
          }, 0);
        }
        this.filterApplied = false;
      },
      mentionUser(event) {
        console.log(event);
        this.mentions = this.mentions || [];
        this.mentions.push({ identifier: event.id });
      },
      removeMention(event) {
        this.mentions = _.reject(this.mentions, { identifier: event.identifier });
      },
      embedFiles(files) {
        this.uploadInProgress = true;
        this.$store
          .dispatch("comments/uploadEmbeddedFiles", {
            files: files,
            model: this.commentType.fileModel ?? this.commentType.model,
            modelIdentifier:
              this.commentType.fileModel === "Job"
                ? this.jobIdentifierLocal
                : this.model.identifier,
          })
          .then((uploadedFiles) => {
            this.embeddedFiles = (this.embeddedFiles || []).concat(uploadedFiles);
          })
          .finally(() => this.uploadInProgress = false);
      },
      removeEmbeddedFile(event) {
        const identifier = event.identifier;
        this.$store.dispatch("comments/deleteFile", { identifier });
        this.embeddedFiles = _.reject(this.embeddedFiles, { identifier });
      },
      uploadAttachments(event) {
        console.log(event);
        const files = event.files;
        const attachments = files.map((f) => {
          return {
            name: f.name,
            groupName: files[0].name,
            objectURL: f.objectURL,
            type: f.type,
            sm: false,
            ds: false,
            progress: 0,
            purchaseOrderIdentifier: "None",
          };
        });
        this.attachedFiles = (this.attachedFiles || []).concat(attachments);
        this.uploadInProgress = true;
        this.$store
          .dispatch("comments/uploadAttachedFiles", {
            files,
            model: this.commentType.fileModel ?? this.commentType.model,
            modelIdentifier:
              this.commentType.fileModel === "Job"
                ? this.jobIdentifierLocal
                : this.model.identifier,
          })
          .then((fileRefs) => {
            for (let file of fileRefs) {
              const index = _.findIndex(this.attachedFiles, {
                name: file.originalName,
              });
              this.attachedFiles[index] = _.merge(
                this.attachedFiles[index],
                file
              );
              this.attachedFiles[index].progress = 100;
              this.attachedFiles[index].sm = this.isWorkcenter ? true : false;
              this.attachedFiles[index].ds = false;
              this.attachedFiles[index].purchaseOrderIdentifier = this.isLpo
                ? this.model.identifier
                : this.isWorkcenter
                ? this.additionalModelIdentifier
                : "None";
            }
            console.log(this.attachedFiles);
          })
          .catch(error => {
            this.attachedFiles = _.filter(this.attachedFiles, f => _.findIndex(attachments, a => a.name === f.name) === -1);
          })
          .finally(() => this.uploadInProgress = false);
      },
      removeAttachment({ identifier }) {
        this.attachedFiles = _.reject(this.attachedFiles, { identifier });
        this.$store.dispatch("comments/deleteFile", { identifier });
      },
      createComment() {
        const commentData = {
          jobIdentifier: this.jobIdentifierLocal ?? null,
          comment: this.newComment || null,
          streamId: this.stream ?? null,
          model: this.commentType.model,
          modelIdentifier: this.model.identifier,
          additionalModel: this.additionalModelType,
          additionalModelIdentifier: this.additionalModelIdentifier,
          isCustomer: this.commentSection === 'Customer',
          showToDriver: this.showToDriver,
        };
        if(this.commentType.model === 'LogisticDate' && this.orderIdentifier){
          commentData.orderIdentifier = this.orderIdentifier;
        }
        if (this.attachedFiles?.length) {
          commentData.attachments = this.attachedFiles.map((f) => {
            let attachment = {
              identifier: f.identifier,
              sm: f.sm,
              ds: f.ds,
            };
            if (f.purchaseOrderIdentifier !== "None")
              attachment.purchaseOrderIdentifier = f.purchaseOrderIdentifier;
            return attachment;
          });
        }
        if (this.embeddedFiles?.length) {
          commentData.embeddedFiles = this.embeddedFiles.map((f) => {
            return { identifier: f.identifier };
          });
        }
        const mentions = CommentUtils.getMentions(
          this.$refs.commentEditor.$refs.editorElement
        );
        if (mentions?.length) {
          commentData.mentions = mentions;
        }
        if (this.additionalModelType && this.additionalModelIdentifier) {
          const modelType = CommentTypes[this.additionalModelType];
          if (modelType) {
            commentData.additionalModel = modelType.model;
            commentData.additionalModelIdentifier =
              this.additionalModelIdentifier;
          }
        }
        if(this.followerIdentifier) commentData.followerIdentifier = this.followerIdentifier;
        this.$store.dispatch("comments/createComment", commentData)
        .then(() => {
          this.$emit('commentCreated', {comment: commentData, identifier: this.model.identifier});
          if(this.followerIdentifier){
            const identifier = this.followerIdentifier;
            this.$emit('updateFollowerBroadCast', {comment: commentData, identifier});
          }
        });
      },
      clearNewComment() {
        this.newComment = null;
        this.mentions = null;
        this.attachedFiles = null;
        this.embeddedFiles = null;
      },
      isImage(file) {
        return /^image\//.test(file.type);
      },
      upvoteComment(commentIdentifier) {

        this.$store.dispatch("comments/likeComment", {
          commentIdentifier,
        }).then((upvote) => {
          this.$emit('upvoteComment', {commentIdentifier, upvote});
          const commentData = {
            jobIdentifier: this.jobIdentifierLocal ?? null,
            comment: this.newComment || null,
            streamId: this.stream ?? null,
            model: this.commentType.model,
            modelIdentifier: this.model.identifier,
            isCustomer: this.commentSection === 'Customer',
          };
          if(this.followerIdentifier){
              const identifier = this.followerIdentifier;
              this.$emit('updateFollowerBroadCast', {comment: commentData, identifier});
          }
        });
      },
      removeCommentUpvote(upvoteIdentifier, commentIdentifier) {
        this.$store.dispatch("comments/removeCommentLike", {
          upvoteIdentifier,
          commentIdentifier,
        }).then(() => {
          this.$emit('removeCommentUpvote', {commentIdentifier, upvoteIdentifier});
          const commentData = {
            jobIdentifier: this.jobIdentifierLocal ?? null,
            comment: this.newComment || null,
            streamId: this.stream ?? null,
            model: this.commentType.model,
            modelIdentifier: this.model.identifier,
            isCustomer: this.commentSection === 'Customer',
          };
          if(this.followerIdentifier){
              const identifier = this.followerIdentifier;
              this.$emit('updateFollowerBroadCast', {comment: commentData, identifier});
          }
        });
      },
      upvoteReply(replyIdentifier, commentIdentifier) {
        this.$store.dispatch("comments/likeReply", {
          commentIdentifier,
          replyIdentifier,
        }).then((upvote) => {
          this.$emit('upvoteReply', { commentIdentifier, replyIdentifier, upvote });
        });
      },
      removeReplyUpvote(
        { upvoteIdentifier, replyIdentifier },
        commentIdentifier
      ) {
        this.$store.dispatch("comments/removeReplyLike", {
          commentIdentifier,
          replyIdentifier,
          upvoteIdentifier,
        }).then(() => {
          this.$emit('removeReplyUpvote', { commentIdentifier, replyIdentifier, upvoteIdentifier });
        });;
      },
      onCommentReplyStart(identifier) {
        this.replyFiles = null;
        this.replyFiles = null;
        this.replyingComment = identifier;
      },
      addReplyMention(event) {
        this.replyMentions = this.replyMentions || [];
        this.replyMentions.push({ identifier: event.identifier });
      },
      removeReplyMention(event) {
        this.replyMentions = _.reject(this.replyMentions, {
          identifier: event.identifier,
        });
      },
      embedReplyFiles(event) {
        console.log(event);
        this.replyFiles = (this.replyFiles || []).concat(event);
      },
      removeEmbeddedReplyFile(event) {
        const identifier = event.identifier;
        this.$store.dispatch("comments/deleteFile", { identifier });
        this.replyFiles = _.reject(this.replyFiles, { identifier });
      },
      createReply(event) {
        const comment = _.find(this.comments, {
          identifier: this.replyingComment,
        });
        if (!comment) return;
        const replyData = {
          jobIdentifier: this.jobIdentifierLocal ?? null,
          identifier: comment.identifier,
          comment: event.reply || null,
          streamId: comment.streamId ?? this.stream ?? 0,
          isCustomer: comment.isCustomer ?? this.commentSection === 'Customer'
        };
        if (comment.model) {
          replyData.model = comment.model.name;
          replyData.modelIdentifier = comment.model.identifier;
        }
        if(comment.additionalModel){
          replyData.additionalModel = comment.additionalModel.name;
          replyData.additionalModelIdentifier = comment.additionalModel.identifier;
        }
        if (event.mentions?.length) replyData.mentions = event.mentions;
        //if (this.replyMentions?.length) replyData.mentions = this.replyMentions;
        if (this.replyFiles?.length)
          replyData.embeddedFiles = this.replyFiles.map((f) => {
            return { identifier: f.identifier };
          });
        this.$store.dispatch("comments/createReply", replyData).then(() => {
          if(this.followerIdentifier){
            const commentData = {
              jobIdentifier: this.jobIdentifierLocal ?? null,
              comment: this.newComment || null,
              streamId: this.stream ?? null,
              model: this.commentType.model,
              modelIdentifier: this.model.identifier,
              isCustomer: this.commentSection === 'Customer',
            };
            const identifier = this.followerIdentifier;
            this.$emit('updateFollowerBroadCast', {comment: commentData, identifier});
          }
        });
      },
      clearReplyData() {
        this.replyMentions = null;
        this.replyFiles = null;
        this.replyingComment = null;
      },
      clearUpdateData() {
        this.editingExistingMentions = null;
        this.editingNewMentions = null;
        this.editingRemovedMentions = null;
        this.editingFiles = null;
        this.editingComment = null;
      },
      onCommentEditStart(identifier, replyIdentifier) {
        const comment = _.find(this.comments, { identifier: identifier });
        if (replyIdentifier) {
          const reply = _.find(comment.replies, { identifier: replyIdentifier });
          this.editingComment = reply;
          this.editingExistingMentions = reply.mentions;
        } else {
          this.editingComment = comment;
          this.editingExistingMentions = comment.mentions;
        }
      },
      onCommentEditCancel() {
        this.editingComment = null;
        this.editingExistingMentions = null;
        this.editingNewMentions = null;
        this.editingRemovedMentions = null;
        this.editingFiles = null;
      },
      addEditMention(event) {
        this.editingNewMentions = this.editingNewMentions || [];
        this.editingNewMentions.push({ identifier: event.identifier });
      },
      removeEditMention(event) {
        // if (_.findIndex(this.editingNewMentions, { identifier: event.identifier }) !== -1) {
        //   this.editingNewMentions = _.reject(this.editingNewMentions, { identifier: event.identifier });
        // } else {
        //   const existingMention = _.find(this.editingExistingMentions, { identifier: event.identifier });
        //   if (existingMention) {
        //     this.editingRemovedMentions = this.editingRemovedMentions || [];
        //     this.editingRemovedMentions.push(existingMention.relatedIdentifier);
        //   }
        // }
      },
      editAddFile(event) {
        this.editingFiles = (this.editingFiles || []).concat(event);
      },
      editRemoveFile(event) {
        const identifier = event.identifier;
        this.$store.dispatch("comments/deleteFile", { identifier });
        this.editingFiles = _.reject(this.editingFiles, { identifier });
      },
      submitCommentEdit(commentIdentifier, event) {
        console.log(event);
        const comment = this.editingComment;
        const updatedData = {
          identifier: comment.identifier,
          comment: event.comment,
        };
        const exitingMentions = this.editingExistingMentions;
        const updatedMentions = event.mentions || [];
        const removedMentions = _.map(
          _.filter(exitingMentions, function (m) {
            return (
              _.findIndex(updatedMentions, { identifier: m.identifier }) === -1
            );
          }),
          "relatedIdentifier"
        );
        if (removedMentions.length) updatedData.deleteMentions = removedMentions;
        const newMentions = _.filter(updatedMentions, function (m) {
          return (
            _.findIndex(exitingMentions, { identifier: m.identifier }) === -1
          );
        });
        if (newMentions.length) updatedData.newMentions = newMentions;
        // if (this.editingNewMentions?.length) {
        //   updatedData.newMentions = this.editingNewMentions;
        // }
        // if (this.editingRemovedMentions?.length) updatedData.deleteMentions = this.editingRemovedMentions;
        if (this.editingFiles?.length) {
          updatedData.embeddedFiles = this.editingFiles.map((f) => {
            return { identifier: f.identifier };
          });
        }
        if (event.replyIdentifier) {
          this.$store.dispatch("comments/updateReply", {
            commentIdentifier,
            data: updatedData,
          });
        } else {
          this.$store.dispatch("comments/updateComment", updatedData);
        }
        if(this.followerIdentifier){
          const commentData = {
            jobIdentifier: this.jobIdentifierLocal ?? null,
            comment: this.newComment || null,
            streamId: this.stream ?? null,
            model: this.commentType.model,
            modelIdentifier: this.model.identifier,
            isCustomer: this.commentSection === 'Customer',
          };
            const identifier = this.followerIdentifier;
            this.$emit('updateFollowerBroadCast', {comment: commentData, identifier});
        }
      },
      deleteComment({ comment, reply }) {
          const commentData = {
            jobIdentifier: this.jobIdentifierLocal ?? null,
            comment: this.newComment || null,
            streamId: this.stream ?? null,
            model: this.commentType.model,
            modelIdentifier: this.model.identifier,
            isCustomer: this.commentSection === 'Customer',
          };
        if (reply) {
          this.$store.dispatch("comments/deleteReply", {
            comment,
            reply,
          }).then(() => {
            if(this.followerIdentifier){
              const identifier = this.followerIdentifier;
              this.$emit('updateFollowerBroadCast', {comment: commentData, identifier});
            }
            });;
        } else {
          this.$store.dispatch("comments/deleteComment", {
            comment,
            context: this.model,
          }).then(() => {
            this.$emit('commentDeleted', {comment: commentData})
            if(this.followerIdentifier){
              const identifier = this.followerIdentifier;
              this.$emit('updateFollowerBroadCast', {comment: commentData, identifier});
            }
            });
        }
      },
      showAttachmentGallery(event, comment) {
        const { attachments, index } = event;
        this.galleryItems = _.cloneDeep(attachments);
        this.activeGalleryIndex = index;
        this.activeGalleryComment = comment;
        this.attachmentGalleryVisible = true;
        this.galleryModel = event.model;
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
      searchUsers(event) {
        this.$store
          .dispatch("comments/searchUsers", event.query)
          .then((userData) => {
            this.userSuggestions = userData;
          });
      },
      onUserSearchSelect(event) {
        this.userSearchTerm = event.value.fullName;
      },
      onWindowResize() {
        this.isMobile = DomHandler.getViewport().width < 1025;
      },
      onGalleryHide(visible) {
        this.attachmentGalleryVisible = visible;
        if (!visible) {
          this.recentlyClosedAttachmentGallery = true;
          setTimeout(() => {
            this.recentlyClosedAttachmentGallery = false;
          }, 0);
        }
      },
      onPurchaseOrderChange(event, data) {
        if (event.value === 'None' && data.sm) {
          data.sm = false;
        }
      },
      onKeydown(event) {
        if (event.key === "Escape") {
          if (!this.recentlyClosedAttachmentGallery) {
            this.$emit('hide');
          }
        } else if (event.key === "'" && event.ctrlKey) {
          this.$emit('hide');
        }
      }
    },
  };
</script>
