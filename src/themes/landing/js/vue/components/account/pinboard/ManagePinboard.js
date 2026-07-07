import EmailVerificationModal from "../../pinboard/EmailVerificationModal.js";
import CommunicationModal from "../../pinboard/CommunicationModal.js";
import BookingCalendarModal from "../../pinboard/BookingCalendarModal.js";
import BookingTimeModal from "../../pinboard/BookingTimeModal.js";
import CreateProjectModal from "../../pinboard/CreateProjectModal.js";
import LiveCameraModal from "../../pinboard/LiveCameraModal.js";
import AddImageModal from "../../pinboard/AddImageModal.js";
import ImagePreviewModal from "../../pinboard/ImagePreviewModal.js";

const MAX_COMMENT_IMAGE_SIZE_MB = 5;
const MAX_COMMENT_IMAGE_SIZE_BYTES = MAX_COMMENT_IMAGE_SIZE_MB * 1024 * 1024;

export default {
  name: "ManagePinboard",
  components: {
    EmailVerificationModal,
    CommunicationModal,
    BookingCalendarModal,
    BookingTimeModal,
    CreateProjectModal,
    LiveCameraModal,
    AddImageModal,
    ImagePreviewModal,
  },
  data() {
    return {
      showProjectModal: false,
      showBookingModal: false,
      showBookingTimeModal: false,
      bookingSelectedDate: "",
      showAddComment: false,
      editingItems: {},
      comment: "",
      showProjectDropdown: false,
      showCreateNewProjectModal: false,
      selectedProjectId: "",
      projectSearchQuery: "",
      isEditingProjectTitle: false,
      editableProjectTitle: "",
      isSavingProjectTitle: false,
      localFilter: {
        searchValue: "",
      },
      _autocompleteDebounceId: null,
      showAddImageModal: false,
      addImageModalError: false,
      cameraImagePreview: "",
      addImageTitle: "",
      addImageComment: "",
      _cameraBlobUrl: null,
      showLiveCamera: false,
      _mediaStream: null,
      showImagePreviewModal: false,
      previewImageSrc: '',
      previewImageAlt: '',
      localItems: [],
      isMobile: typeof window !== "undefined" ? window.innerWidth < 540 : false,
      isTablet:
        typeof window !== "undefined"
          ? window.innerWidth >= 540 && window.innerWidth < 1200
          : false,
    };
  },
  props: {
    items: {
      type: Array,
      default: () => [],
    },
    pinboard: {
      type: Object,
      default: () => {},
    },
    loading: Boolean,
    error: String,
  },

  emits: [
    "hidden",
    "remove-item",
    "add-comment",
    "edit-comment",
    "update-comment",
  ],
  computed: {
    fb() {
      return this.$store.getters.fb || { errors: {}, loading: {} };
    },
    commentFiles() {
      return this.$store.getters.commentFiles || [];
    },
    loggedInUser() {
      return this.$store.getters.loggedInUser;
    },
    projectMenuItems() {
      const items = this.$store.getters.projectItems;
      return Array.isArray(items) ? items : [];
    },
    filteredProjectMenuItems() {
      const query = String(this.projectSearchQuery || "").trim().toLowerCase();
      if (!query) return this.projectMenuItems || [];
      return (this.projectMenuItems || []).filter((item) =>
        String(item?.pinboard_name || "").toLowerCase().includes(query),
      );
    },
    displayProjectTitle() {
      const sid = this.selectedProjectId;
      if (sid === "" || sid === null || sid === undefined) return "Project";
      const selected = this.projectMenuItems.find(
        (entry) => String(entry.pinboard_id) === String(sid),
      );
      return selected && selected?.pinboard_name ? selected?.pinboard_name : "Project";
    },
    autoCompletePlaceholderText() {
      return "Search to add products...";
    },
    disableAutocomplete() {
      return (
        !this.$store.getters.loggedInUser ||
        !this.$store.getters.loggedInUser.email
      );
    },
    autocompleteOpen() {
      return this.$store.getters.autocompleteOpen;
    },
    autocompleteSuggestions() {
      return this.$store.getters.autocompleteSuggestions;
    },
  },
  watch: {
    items: {
      immediate: true,
      handler(nextItems) {
        this.localItems = Array.isArray(nextItems) ? [...nextItems] : [];
      },
    },
    pinboard: {
      immediate: true,
      handler(nextPinboard) {
        if (nextPinboard && nextPinboard.pinboard_id) {
          this.selectedProjectId = nextPinboard.pinboard_id;
        }
      },
    },
  },
  methods: {
    getLiveCameraVideoElement() {
      return this.$refs.liveCameraModal && this.$refs.liveCameraModal.$refs
        ? this.$refs.liveCameraModal.$refs.liveCameraVideo
        : null;
    },

    getKey(item, index) {
      return `${item.model_type}-${item.model_id}-${index}`;
    },

    onHidden() {
      this.$emit("hidden");
    },
    clearError(field) {
      this.$store.commit("CLEAR_ERROR", field);
    },
    async runAutocompleteSearch() {
      const q = (this.localFilter.searchValue || "").trim();
      await this.$store.dispatch("searchPinboardAutocomplete", q);
    },
    scheduleAutocompleteSearch() {
      if (this._autocompleteDebounceId != null) {
        clearTimeout(this._autocompleteDebounceId);
      }
      this._autocompleteDebounceId = setTimeout(() => {
        this._autocompleteDebounceId = null;
        this.runAutocompleteSearch();
      }, 200);
    },
    handleAutocomplete() {
      this.scheduleAutocompleteSearch();
    },
    onAutocompleteFocus() {
      if ((this.localFilter.searchValue || "").trim()) {
        this.scheduleAutocompleteSearch();
      }
    },
    handleClearAutocomplete() {
      this.localFilter.searchValue = "";
      this.$store.commit("SET_AUTOCOMPLETE_SUGGESTIONS", []);
      this.$store.commit("SET_AUTOCOMPLETE_OPEN", false);
      if (this._autocompleteDebounceId != null) {
        clearTimeout(this._autocompleteDebounceId);
        this._autocompleteDebounceId = null;
      }
    },
    async selectAutocompleteProduct(product) {
      if (!product) return;
      const parts = product.model_type.split("-");
      const modelId = parts.pop(); // "4"
      const modelType = parts.join("-").toLowerCase(); // "product"

      try {
        const payload = {
          model_id: modelId,
          model_type: modelType,
          title: product.title,
          photo: product.dataSrc,
          product_url: product.product_url,
          quantity: 1,
          unit_price: 0,
          description: product.sku ? `Demo · ${product.sku}` : "",
          language_id: 1,
          comments: [],
        };
        await this.$store.dispatch("addToPinboard", payload);
      } catch (e) {
        console.error("addToPinboard from autocomplete failed", e);
      }
      this.handleClearAutocomplete();
    },
    async removePinboardItem(pinboardItem, index) {
      await this.$store.dispatch("removePinboardItem", {
        pinboardItem,
        index,
      });
      this.localItems = [...(this.$store.getters.pinboardItems || [])];
    },
    async reorderPinboardItems(event) {
      const reorderedItems =
        event && Array.isArray(event.items)
          ? event.items
          : Array.isArray(this.localItems)
            ? this.localItems
            : [];

      // console.log("reorderedItems", reorderedItems);
      await this.$store.dispatch("reorderPinboardItems", reorderedItems);
    },
    async addPinboardItemComment(item, index, newComments = false) {
      const key = this.getKey(item, index);
      const newCommentValue = Array.isArray(item?.newComments) && item.newComments.length > 0 ? item.newComments[0] : "";
      const existingCommentValue = this.getPrimaryComment(item);
      const comment = newComments ? newCommentValue : existingCommentValue;
      console.log("comment component :- ", comment);
      await this.$store.dispatch("addPinboardItemComment", {
        pinboard_item_id: item.pinboard_item_id,
        index,
        comment,
      });
      // Reset editing state after saving
      this.$set(this.editingItems, key, false);
    },
    editItemComment(item, index) {
      const key = this.getKey(item, index);
      this.$set(this.editingItems, key, true);
      this.$nextTick(() => {
        const wrapper = this.$el.querySelector(`[data-edit-key="${key}"]`);
        if (wrapper) {
          const textarea = wrapper.querySelector(".item-comment-box");
          if (textarea) {
            textarea.style.height = "auto";
            textarea.style.height = Math.max(textarea.scrollHeight, 46) + "px";
          }
        }
      });
    },
    updateItemComment(item, property, value) {
      this.ensureItemCommentBucket(item, property);
      this.$set(item[property], 0, value);
    },
    ensureItemCommentBucket(item, property) {
      if (!Array.isArray(item[property])) {
        this.$set(item, property, [""]);
        return;
      }
      if (item[property].length === 0) {
        this.$set(item[property], 0, "");
      }
    },
    getPrimaryComment(item) {
      if (!Array.isArray(item?.comments) || item.comments.length === 0) return "";
      return item.comments[0] || "";
    },
    hasPrimaryComment(item) {
      return this.getPrimaryComment(item).trim().length > 0;
    },
    capitalizeText(value) {
      if (value === null || value === undefined) return "";
      const text = String(value);
      if (!text.length) return "";
      return text.charAt(0).toUpperCase() + text.slice(1);
    },
    toggleItemNote(item) {
      this.$set(item, "_showNote", !item._showNote);
    },
    onDragStart() {},
    onDragEnd() {
      this.$nextTick(() => {
        this.reorderPinboardItems({ items: [...this.localItems] });
      });
    },
    cloneItem(item) {
      return { ...item, _isDragPreview: true };
    },
    // ------------------------- camera capture (aligned with Pinboard.js; store = managePinboardStore) -------------------------
    triggerCameraCapture() {
      this.openLiveCameraOrFallback();
    },
    openLiveCameraOrFallback() {
      const canUseLiveCamera =
        typeof navigator !== "undefined" &&
        navigator.mediaDevices &&
        typeof navigator.mediaDevices.getUserMedia === "function" &&
        (typeof window === "undefined" || window.isSecureContext !== false);
      if (!canUseLiveCamera) {
        this.openNativeCameraFilePicker();
        return;
      }
      navigator.mediaDevices
        .getUserMedia({
          video: { facingMode: { ideal: "environment" } },
          audio: false,
        })
        .then((stream) => {
          this._mediaStream = stream;
          this.showLiveCamera = true;
          this.$nextTick(() => this.attachLiveCameraStream(stream));
        })
        .catch(() => {
          this.openNativeCameraFilePicker();
        });
    },
    attachLiveCameraStream(stream) {
      const video = this.getLiveCameraVideoElement();
      if (!video) return;
      video.srcObject = stream;
      const play = () => {
        video.play().catch(() => {});
      };
      if (video.readyState >= 2) {
        play();
      } else {
        video.addEventListener("loadedmetadata", play, { once: true });
      }
    },
    stopLiveCameraStream() {
      if (this._mediaStream) {
        this._mediaStream.getTracks().forEach((t) => t.stop());
        this._mediaStream = null;
      }
      const video = this.getLiveCameraVideoElement();
      if (video && video.srcObject) {
        video.srcObject = null;
      }
      this.showLiveCamera = false;
    },
    cancelLiveCamera() {
      this.stopLiveCameraStream();
    },
    snapLiveCameraPhoto() {
      const video = this.getLiveCameraVideoElement();
      if (!video || !video.videoWidth) {
        this.cancelLiveCamera();
        return;
      }
      const canvas = document.createElement("canvas");
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      canvas.getContext("2d").drawImage(video, 0, 0);
      canvas.toBlob(
        (blob) => {
          this.stopLiveCameraStream();
          if (!blob || !blob.type.startsWith("image/")) return;
          this.revokeCameraBlobUrl();
          this._cameraBlobUrl = URL.createObjectURL(blob);
          this.cameraImagePreview = this._cameraBlobUrl;
          this.addImageTitle = "";
          this.addImageComment = "";
          this.addImageModalError = false;
          this.showAddImageModal = true;
        },
        "image/jpeg",
        0.92
      );
    },
    openNativeCameraFilePicker() {
      const input = this.$refs.cameraCaptureInput;
      if (input) input.click();
    },
    revokeCameraBlobUrl() {
      if (this._cameraBlobUrl) {
        URL.revokeObjectURL(this._cameraBlobUrl);
        this._cameraBlobUrl = null;
      }
    },
    onCameraCaptureChange(event) {
      const input = event.target;
      const file = input.files && input.files[0];
      input.value = "";
      this.prepareCameraImageFromFile(file);
    },
    handleLiveCameraFileSelect(file) {
      this.stopLiveCameraStream();
      this.prepareCameraImageFromFile(file);
    },
    prepareCameraImageFromFile(file) {
      if (!file || !file.type.startsWith("image/")) return;

      this.revokeCameraBlobUrl();
      this._cameraBlobUrl = URL.createObjectURL(file);
      this.cameraImagePreview = this._cameraBlobUrl;
      this.addImageTitle = "";
      this.addImageComment = "";
      this.addImageModalError = false;
      this.showAddImageModal = true;
    },
    cancelAddCameraImage() {
      this.showAddImageModal = false;
      this.cameraImagePreview = "";
      this.addImageTitle = "";
      this.addImageComment = "";
      this.addImageModalError = false;
      this.revokeCameraBlobUrl();
    },
    async confirmAddCameraImage() {
      const titleIn = (this.addImageTitle || "").trim();
      const comment = (this.addImageComment || "").trim();
      if (!titleIn && !comment) {
        this.addImageModalError = true;
        return;
      }
      this.addImageModalError = false;
      const photo = this._cameraBlobUrl;
      if (!photo) return;

      const pb = this.$store.getters.pinboard || this.pinboard;
      const pinboardId = pb?.pinboard_id;
      if (!pinboardId) {
        console.error("addToPinboard camera image: missing pinboard_id");
        return;
      }

      const title = titleIn || (comment ? comment.slice(0, 72) : "Pinboard image");
      const payload = {
        model_id: pinboardId,
        model_type: "images",
        title,
        photo,
        quantity: 1,
        unit_price: 0,
        description: title || comment,
        language_id: 1,
        comments: comment ? [comment] : [],
      };
      try {
        await this.$store.dispatch("addToPinboard", payload);
      } catch (e) {
        console.error("addToPinboard camera image failed", e);
        return;
      }
      this.showAddImageModal = false;
      this.cameraImagePreview = "";
      this.addImageTitle = "";
      this.addImageComment = "";
      this.addImageModalError = false;
      this.revokeCameraBlobUrl();
      this.localItems = [...(this.$store.getters.pinboardItems || [])];
    },
    // ------------------------- end camera capture -------------------------
    async submitComment() {
      const commentText = (this.comment || '').trim();
      // const commentTextarea = this.$refs.addCommentTextarea;
      if (commentText.length < 2) {
          this.$store.commit('SET_ERROR', {
              key: 'addCommentItemToPinboard',
              error: 'Please enter at least 2 characters.',
          });
          return;
      }
      this.$store.commit('CLEAR_ERROR', 'addCommentItemToPinboard');
      try {
          const res = await this.$store.dispatch('addCommentItemToPinboard', commentText);
          if (res?.success) {
              this.comment = '';
              this.showAddComment = false;
          }
      } catch (err) {
          console.error('Failed to submit comment', err);
      }
    },
    removeCommentImage(file, index) {
      this.$store.dispatch("removeCommentItemImage", { file, index });
    },
    isCommentPinboardItem(item) {
      return item && String(item.model_type || '').toLowerCase() === 'comment';
    },
    openCommentImagePreview(item) {
      if (!this.isCommentPinboardItem(item) || !item.photo) return;
      this.previewImageSrc = item.photo;
      this.previewImageAlt = item.title || item.description || 'Comment image';
      this.showImagePreviewModal = true;
    },
    closeCommentImagePreview() {
      this.showImagePreviewModal = false;
      this.previewImageSrc = '';
      this.previewImageAlt = '';
    },
    uploadCommentImage(event) {
      const file = event?.target?.files && event.target.files[0];
      if (!file) return;
      if (file.size > MAX_COMMENT_IMAGE_SIZE_BYTES) {
        const uploadedSizeMb = Number(file.size / (1024 * 1024)).toFixed(3);
        this.$store.commit("SET_ERROR", {
          key: "addCommentItemToPinboard",
          error: `File size is too large. Your file size is ${uploadedSizeMb}MB. Maximum size is ${MAX_COMMENT_IMAGE_SIZE_MB}MB.`,
        });
        if (event.target) event.target.value = "";
        return;
      }
      this.$store.commit("CLEAR_ERROR", "addCommentItemToPinboard");
      const objectURL = URL.createObjectURL(file);
      this.$store.dispatch("uploadCommentItemImage", { file, objectURL });
      if (event.target) event.target.value = "";
    },
    getProjectInitial(projectName) {
      const safeName = String(projectName || "").trim();
      return safeName ? safeName.charAt(0).toUpperCase() : "?";
    },
    openProjectTitleEditor() {
      if (this.isSavingProjectTitle) return;
      this.showProjectDropdown = false;
      this.isEditingProjectTitle = true;
      this.editableProjectTitle = this.displayProjectTitle || "";
      this.$nextTick(() => {
        const input = this.$refs.projectTitleInput;
        if (input && typeof input.focus === "function") {
          input.focus();
          if (typeof input.select === "function") input.select();
        }
      });
    },
    async saveProjectTitle() {
      try {
        const payload = {
          pinboard_id: this.pinboard?.pinboard_id,
          pinboard_name: this.editableProjectTitle,
        };
        const response = await this.$store.dispatch("updateProjectTitle", payload);
        if (response && response.success) {
          this.isEditingProjectTitle = false;
        } else {
          console.error("Project title update failed", response.message);
          this.editableProjectTitle = this.displayProjectTitle;
          this.isEditingProjectTitle = false;
        }
      } catch (e) {
        console.error("Project title update failed", e);
      }
    },
    handleScreenResize() {
      this.isMobile = window.innerWidth < 540;
      this.isTablet =
        window.innerWidth >= 540 && window.innerWidth < 1200;
    },
    changeProject(pinboardId) {
      this.selectedProjectId = pinboardId;
      this.projectSearchQuery = "";
      this.showProjectDropdown = false;
      this.$store.dispatch("getProjectByPinboardId", pinboardId);
    },
    openNewProjectModal() {
      this.showProjectDropdown = false;
      this.showCreateNewProjectModal = true;
    },
    handleCloseCreateNewProjectModal() {
      this.showCreateNewProjectModal = false;
    },
    async handleCreateNewProject(payload) {
      const normalizedPayload = {
        ...payload,
        customer_id:
          payload?.customer_id ||
          this.pinboard?.customer_id ||
          null,
        user_id:
          payload?.user_id ||
          this.loggedInUser?.user_id ||
          null,
      };
      try {
        const response = await this.$store.dispatch("createNewProject", normalizedPayload);
        if (response && response.success && response.data?.pinboard_id) {
          this.selectedProjectId = response.data.pinboard_id;
          this.showCreateNewProjectModal = false;
          return;
        }
        console.error("Failed to create new project", response?.error || "Unknown error");
      } catch (e) {
        console.error("Failed to create new project", e);
      }
    },
  },
  mounted() {
    this.handleScreenResize();
    window.addEventListener("resize", this.handleScreenResize);
    this._closeProjectMenuOnOutsideClick = (e) => {
      if (this.isEditingProjectTitle) {
        const input = this.$refs.projectTitleInput;
        if (input && !input.contains(e.target)) {
          this.saveProjectTitle();
        }
      }
    };
    document.addEventListener("click", this._closeProjectMenuOnOutsideClick);
  },
  beforeDestroy() {
    window.removeEventListener("resize", this.handleScreenResize);
    if (this._closeProjectMenuOnOutsideClick) {
        document.removeEventListener('click', this._closeProjectMenuOnOutsideClick);
    }
    if (this._autocompleteDebounceId != null) {
        clearTimeout(this._autocompleteDebounceId);
    }
    this.stopLiveCameraStream();
    this.revokeCameraBlobUrl();
    // if (this.bookingOtpTimerRef) {
    //     clearInterval(this.bookingOtpTimerRef);
    //     this.bookingOtpTimerRef = null;
    // }
},

  template: /* html */ `
  <div class="offcanvas offcanvas-end th-account-manage-pinboard-offcanvas d-flex flex-column bg-gray pinboard-app-root"
    style="--bs-offcanvas-width: min(100%, 720px);" tabindex="-1" id="manage_pinboard_offcanvasRight"
    @hidden.bs.offcanvas="onHidden">
    <div class="pinboard-close-btn">
      <button type="button" data-bs-dismiss="offcanvas" aria-label="Close Manage Pinboard"
        class="btn btn-link p-1 border-0 text-body pinboard-offcanvas-close flex-shrink-0 d-xl-none">
        <i aria-hidden="true" class="fa-solid fa-xmark fa-lg"></i>
      </button>
    </div>

    <div class="offcanvas-header th-header-upper d-md-flex justify-content-between align-items-center" v-if="!isTablet">
      <div class="d-flex align-items-center">
        <div class="offcanvas-header th-header-lower">
          <h5 id="offcanvasRightLabel2">Manage Pinboard</h5>
        </div>
      </div>
      <div class="d-flex align-items-center justify-content-end pinboard-header-info"
        v-if="loggedInUser && loggedInUser.email">
        <div class="pinboard-header-project-wrap text-end position-relative">
          <div class="pinboard-project-trigger-line d-inline-flex align-items-center gap-1 flex-wrap justify-content-end">
            <i class="fa-solid fa-pencil"
              style="font-size: 12px; margin-right: 5px; cursor: pointer;"
              @click.stop="openProjectTitleEditor"></i>
            <input
              v-if="isEditingProjectTitle"
              ref="projectTitleInput"
              type="text"
              class="form-control form-control-sm pinboard-project-name-input"
              v-model="editableProjectTitle"
              @click.stop
              @keydown.enter.prevent="saveProjectTitle"
              @blur="saveProjectTitle"
              :disabled="isSavingProjectTitle"
              style="height: 30px; min-width: 160px; max-width: 230px; padding: 2px 8px; font-size: 16px; font-weight: 700; border-radius: 6px; border: 1px solid #d5d9e2;"
            />
            <span v-else class="pinboard-project-name fw-bold">{{ displayProjectTitle }}</span>
          </div>
          <div class="pinboard-project-email text-muted small">{{ loggedInUser.email }}</div>
        </div>
      </div>
    </div>

    <div class="pinboard-header-tablet d-flex align-items-center justify-content-between"
      v-if="loggedInUser && isTablet">
      <div class="pinboard-header-left">
        <h6 class="mb-0 text-truncate">Manage Pinboard</h6>
      </div>
      <div class="pinboard-header-center text-center position-relative">
        <div class="d-inline-flex align-items-center gap-1">
          <span class="pinboard-project-name fw-bold text-truncate d-inline-block" style="max-width: 200px;">
            {{ pinboard?.pinboard_name ?? 'Project' }}
          </span>
        </div>
      </div>
      <div class="pinboard-header-right d-flex align-items-center gap-2">
        <span class="pinboard-email small text-muted text-truncate">{{ loggedInUser.email }}</span>
      </div>
    </div>

    <div class="offcanvas-body">
      <div class="th-pinboard th-pinboard--tablet-split">
        <div class="row">
          <div class="col-12 col-lg-12 pinboard-offcanvas-col pinboard-offcanvas-col--main">
            <div class="mb-20 pb-0 pinboard-offcanvas-search" v-show="loggedInUser && loggedInUser.email">
              <div class="autocomplete position-relative w-100">
                <i class="fa-solid fa-search text-muted"
                  style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); z-index: 11; pointer-events: none;"
                  aria-hidden="true"></i>
                <input type="text" class="form-control th-choices-select z-index-10 font-size-16" id="choose-product-name-manage"
                  :placeholder="autoCompletePlaceholderText" autocomplete="off" :disabled="disableAutocomplete"
                  @input="handleAutocomplete" @focus="onAutocompleteFocus" v-model="localFilter.searchValue"
                  style="padding:11px 36px 11px 45px;" />
                <i class="fa fa-close hover" @click.prevent="handleClearAutocomplete" v-show="localFilter.searchValue"
                  style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 11;"
                  role="button" aria-label="Clear search"></i>
                <ul v-show="autocompleteOpen && autocompleteSuggestions.length"
                  class="dropdown-menu show pinboard-autocomplete-list w-100 shadow-sm mt-1"
                  style="max-height: 260px; overflow-y: auto; z-index: 1060;">
                  <li v-for="row in autocompleteSuggestions" :key="'ac-' + row.id"
                    class="dropdown-item py-2 d-flex align-items-center gap-2" style="cursor: pointer;"
                    @mousedown.prevent="selectAutocompleteProduct(row)">
                    <img :src="row.dataSrc" :alt="row.title" width="48" height="36" class="rounded flex-shrink-0"
                      style="object-fit: contain;" />
                    <span class="text-truncate small">
                      <span class="d-block fw-semibold">{{ row.title }}</span>
                      <span class="text-muted" v-if="row.sku">{{ row.sku }}</span>
                    </span>
                  </li>
                </ul>
              </div>
            </div>

            <div class="row pinboard-main">
              <div id="pinboard-items" class="th-pinboard-upper th-ofc-pinboard-item-upper pinboard-items-scroll">
                <div v-if="loading" class="pinboard-loading-placeholder">
                  <div v-for="n in 3" :key="'pinboard-loading-' + n"
                    style="height:150px;border:1px solid #cfcfcf; background:white;border-radius:6px;margin-bottom:12px;"></div>
                </div>

                <draggable v-if="!loading && Array.isArray(localItems)" v-model="localItems" :handle="'.draggable-handle'"
                  :clone="cloneItem" ghost-class="pinboard-ghost" chosen-class="pinboard-chosen" drag-class="pinboard-drag"
                  @start="onDragStart" @end="onDragEnd" tag="div">
                  <transition-group>
                    <div v-for="(item, index) in localItems" :key="getKey(item, index)" class="row th-pinboard-item">
                      <div v-if="item._isDragPreview" class="pinboard-drag-preview">
                        <img :src="item.photo" class="thumb" />
                        <span class="title">{{ item.title }}</span>
                        <i class="fa-solid fa-xmark close-icon"></i>
                      </div>

                      <template v-else>
                        <div class="pinboard col-md-12">
                          <div class="card-item">
                            <div class="card-left ml-5 mr-5">
                              <a :href="item.product_url" target="_blank" v-if="item.product_url">
                                <img :src="item.photo" :alt="item.title" />
                              </a>
                              <img
                                v-else-if="isCommentPinboardItem(item) && item.photo"
                                :src="item.photo"
                                :alt="item.title"
                                style="cursor: zoom-in;"
                                role="button"
                                tabindex="0"
                                @click="openCommentImagePreview(item)"
                                @keydown.enter.prevent="openCommentImagePreview(item)"
                                @keydown.space.prevent="openCommentImagePreview(item)"
                              />
                              <img :src="item.photo" :alt="item.title" v-else />
                            </div>

                            <div class="card-content">
                              <div class="card-header">
                                <div>
                                  <h3><a :href="item.product_url" target="_blank" v-if="item.product_url">{{ capitalizeText(item.title) }}</a><span v-else>{{ capitalizeText(item.title) }}</span></h3>
                                  <p class="type">{{ item.model_type === 'images' ? 'Image' : capitalizeText(item.model_type) }}</p>
                                </div>

                                <div class="card-actions pr-10 align-items-center">
                                  <div class="text-darkgrey draggable-handle">
                                    <i class="fa fa-list"></i>
                                  </div>
                                  <div class="remove-pinboard-btn text-darkgrey border-0 bg-transparent">
                                    <i class="fa fa-times" :data-id="item.model_id" :data-model="item.model_type"
                                      @click.prevent="removePinboardItem(item, index)"></i>
                                  </div>
                                </div>
                              </div>

                            <div class="th-item-product" v-if="item.accessories && item.accessories.length > 0">
                              <span class="mb-2 th-title-20 text-success">Accessories:</span>
                              <div class="th-item-footer">
                                <div class="th-tag-name">
                                  <div class="th-tag" v-for="accessory in item.accessories"
                                    :key="accessory.product_accessories_id">
                                    {{ accessory.title }}
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="card-footer">
                              <span @click="toggleItemNote(item)" v-if="!hasPrimaryComment(item)">
                                {{ item._showNote ? '− Hide Note' : '+ Add Note' }}
                              </span>

                              <div v-if="item._showNote || hasPrimaryComment(item)">
                                <div class="th-pinboard-item-edit mt-3" v-if="hasPrimaryComment(item)" :data-edit-key="getKey(item, index)">
                                  <div class="th-pinboard-edit-wrapper d-flex justify-content-between w-100">
                                    <div class="w-100 th-pinboard-edit-content">
                                      <div v-if="editingItems[getKey(item, index)]" class="p-2">
                                        <textarea class="form-control item-comment-box border-0 p-0" rows="1"
                                          :value="getPrimaryComment(item)"
                                          @input="updateItemComment(item, 'comments', $event.target.value)"></textarea>
                                      </div>
                                      <div v-else class="p-2 text-muted th-display-pre-line th-pinboard-view-text">
                                        {{ getPrimaryComment(item) }}
                                      </div>
                                    </div>

                                    <button class="btn" style="width: 100px;">
                                      <span v-if="!editingItems[getKey(item, index)]" @click="editItemComment(item, index)">
                                        <i class="fa-solid fa-pencil"></i>
                                        Edit
                                      </span>
                                      <span v-else @click="addPinboardItemComment(item, index)">
                                        <i class="fa-solid fa-check"></i>
                                        Post
                                      </span>
                                    </button>
                                  </div>
                                </div>

                                <div class="th-pinboard-item-comment mt-3" v-else>
                                  <div class="d-flex align-items-start gap-2 cccc">
                                    <textarea class="form-control item-comment-box" placeholder="Add a Note" rows="1"
                                      @input="updateItemComment(item, 'newComments', $event.target.value)"
                                      :value="getPrimaryComment(item)"></textarea>

                                    <button class="th-btn-primary-post text-capitalize "
                                      @click.prevent="addPinboardItemComment(item, index, true)">
                                      Post
                                    </button>
                                  </div>
                                </div>
                              </div>
                            </div>
                            </div>
                          </div>
                        </div>
                      </template>
                    </div>
                  </transition-group>
                </draggable>

                <div v-if="!loading && localItems.length === 0" class="text-center py-4">
                  No items found
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-lg-12 px-0 pinboard-offcanvas-col pinboard-offcanvas-col--actions position-absolute bottom-0">
            <div class="th-pinboard-bottom">
              <div v-show="loggedInUser">
                <transition name="pinboard-add-comment-collapse">
                  <div class="th-pinboard-lower mb-15" id="th-pinboard-user" v-show="showAddComment">
                    <div class="th-offcanvas-containr">
                      <div class="th-add-comment-panel">
                        <div class="th-add-comment-panel-header">
                          <div class="th-add-comment-panel-title">Add Comment</div>
                          <a href="javascript:void(0)" class="th-add-comment-panel-collapse"
                            @click.prevent="showAddComment = false">
                            &mdash; Collapse
                          </a>
                        </div>

                        <textarea
                          v-model="comment"
                          @input="clearError('addCommentItemToPinboard')" ref="addCommentTextarea"
                          class="comment-box th-offcanvas-comment-box th-off-large-commentbox th-add-comment-textarea"
                          placeholder="Add A Comment"></textarea>
                        <div v-if="fb.errors.addCommentItemToPinboard" class="invalid-feedback d-block mt-1">
                          {{ fb.errors.addCommentItemToPinboard }}
                        </div>

                        <div class="th-doc-actions d-flex flex-column th-add-comment-actions">
                          <div class="d-flex w-100 th-add-comment-preview-row" v-if="commentFiles.length">
                            <div v-for="(file, idx) in commentFiles" :key="file.tmp_name || idx" class="d-flex"
                              style="width: 100px; height: 100px; background-color: #f0f0f0; position: relative; overflow: hidden;">
                              <span class="remove-btn" :id="'removeBtn-' + idx" @click="removeCommentImage(file, idx)"
                                style="position: absolute; top: 6px; right: 6px; z-index: 5; background: rgba(207, 30, 30, 0.9); padding: 5px 6px; border-radius: 12px; cursor: pointer; line-height: 1;">
                                <i class="fa-solid fa-xmark"></i>
                              </span>
                              <img :src="file.objectURL" alt="Image" :title="file.name"
                                style="width: 100%; height: 100%; object-fit: cover;"/>
                            </div>
                          </div>

                          <div class="d-flex justify-content-between w-100 th-add-comment-bottom-row">
                            <label 
                             v-if="!commentFiles.length"
                            class="th-add-comment-upload-label th-btn-gray text-capitalize mr-10"
                              style="cursor:pointer; margin-bottom:0;">
                              Upload Image +
                              <input type="file" accept="image/*" style="display:none;" @change="uploadCommentImage($event)" />
                            </label>

                            <a id="add-comment-button" class="th-add-comment-submit-btn th-btn-primary text-capitalize"
                              @click.prevent="submitComment">
                              Add To Pinboard <i class="fa fa-circle-notch ms-2" v-if="fb.loading.addCommentItemToPinboard"></i>
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </transition>
              </div>

              <div id="th-pinboard-guest" class="">
                <div v-if="loggedInUser">
                  <div class="d-flex flex-column gap-25 pinboard-offcanvas-footer-btns" v-if="!isMobile">
                    <div class="d-flex flex-row gap-20">
                      <button
                        v-show="loggedInUser"
                        v-if="!showAddComment"
                        type="button"
                        class="th-add-comment-toggle-btn"
                        @click.prevent="showAddComment = true">
                        <span class="th-add-comment-toggle-plus">+</span>
                        <span class="th-add-comment-toggle-text">Add Comment</span>
                      </button>

                      <input
                        ref="cameraCaptureInput"
                        type="file"
                        accept="image/*"
                        capture="environment"
                        class="d-none"
                        @change="onCameraCaptureChange"
                      />
                      <button
                        v-show="loggedInUser"
                        type="button"
                        class="th-add-comment-toggle-btn"
                        @click.prevent="triggerCameraCapture">
                        <span class="th-add-comment-toggle-plus"><i class="fa-solid fa-image"></i></span>
                        <span class="th-add-comment-toggle-text">Add Image</span>
                      </button>
                    </div>
                    <!-- <div class="d-flex flex-row gap-20">
                      <button
                        type="button"
                        class="text-reset th-btn-gray text-capitalize border w-100 text-decoration-none"
                        data-bs-dismiss="offcanvas"
                        aria-label="Close">
                        Continue Browsing
                      </button>
                    </div> -->
                  </div>

                  <div
                    class="d-flex justify-content-between align-items-center pinboard-offcanvas-footer-icons"
                    v-if="isMobile">
                    <button
                      v-if="!showAddComment"
                      class="icon-btn"
                      @click.prevent="showAddComment = true">
                      <i class="fa-solid fa-comment m-0"></i>
                    </button>
                    <button class="icon-btn" @click.prevent="triggerCameraCapture">
                      <i class="fa-solid fa-image m-0"></i>
                    </button>
                    <button class="icon-btn" data-bs-dismiss="offcanvas" aria-label="Close">
                      <i class="fa-solid fa-xmark m-0"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <live-camera-modal
    v-if="showLiveCamera"
    ref="liveCameraModal"
    @cancel="cancelLiveCamera"
    @take-photo="snapLiveCameraPhoto"
    @select-file="handleLiveCameraFileSelect"
  ></live-camera-modal>

  <image-preview-modal
    v-if="showImagePreviewModal && previewImageSrc"
    :image-src="previewImageSrc"
    :image-alt="previewImageAlt"
    @close="closeCommentImagePreview"
  ></image-preview-modal>

  <add-image-modal
    v-if="showAddImageModal"
    :camera-image-preview="cameraImagePreview"
    :add-image-title="addImageTitle"
    :add-image-comment="addImageComment"
    :add-image-modal-error="addImageModalError"
    @cancel="cancelAddCameraImage"
    @confirm="confirmAddCameraImage"
    @update-title="addImageTitle = $event; addImageModalError = false"
    @update-comment="addImageComment = $event; addImageModalError = false"
  ></add-image-modal>

  <create-project-modal
    :show="showCreateNewProjectModal"
    :logged-in-user="loggedInUser"
    :customer="{ customer_id: pinboard?.customer_id || null }"
    :loading="fb.loading.createNewProject"
    :error-message="fb.errors.createNewProject"
    @close="handleCloseCreateNewProjectModal"
    @create-project="handleCreateNewProject"
  ></create-project-modal>
  </div>

  `,
};
