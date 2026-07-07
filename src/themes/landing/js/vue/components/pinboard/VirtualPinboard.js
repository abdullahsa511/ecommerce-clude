export default {
    name: 'VirtualPinboard',
  
    data() {
      return {
        quantities: {},
        editingItems: {},
        additionalComment: '',
        additionalCommentError: false,
        // keep track of the description edits
        editPinboardItemDescriptions: {},
        // open hide edit description
        editPinboardItemEditing: {},
        editPinboardItemComments: {},
        // time slot booking
        selectedDate: null,
        email: '',
        selectedDateFormatted: '',
        modalInstance: null,
        selectedSlot: null, // currently active slot ID
        selectedSlotValue: '', // currently active slot value
        checkedSlot: null, // v-model bound value
        morningSlots: [
          { id: "time-slot-1", value: "09:00:00", label: "9:00 AM" },
          { id: "time-slot-2", value: "09:30:00", label: "9:30 AM" },
          { id: "time-slot-3", value: "10:00:00", label: "10:00 AM" },
          { id: "time-slot-4", value: "10:30:00", label: "10:30 AM" },
          { id: "time-slot-5", value: "11:00:00", label: "11:00 AM" },
          { id: "time-slot-6", value: "11:30:00", label: "11:30 AM" },
          { id: "time-slot-7", value: "12:00:00", label: "12:00 PM" },
          { id: "time-slot-8", value: "12:30:00", label: "12:30 PM" },
        ],
        eveningSlots: [
          { id: "time-slot-9", value: "13:00:00", label: "1:00 PM" },
          { id: "time-slot-10", value: "13:30:00", label: "1:30 PM" },
          { id: "time-slot-11", value: "14:00:00", label: "2:00 PM" },
          { id: "time-slot-12", value: "14:30:00", label: "2:30 PM" },
          { id: "time-slot-13", value: "15:00:00", label: "3:00 PM" },
          { id: "time-slot-14", value: "15:30:00", label: "3:30 PM" },
          { id: "time-slot-15", value: "16:00:00", label: "4:00 PM" },
          { id: "time-slot-16", value: "16:30:00", label: "4:30 PM" },
        ],
      };
    },
    computed: {
      pinboard(){
        return this.$store.getters.pinboard;
      },
      items() {
          return this.$store.getters.pinboardItems || [];
      },
      commentFiles() {
          return this.$store.getters.commentFiles || [];
      },
      customer: {
          get() {
              return this.$store.getters.customer || {};
          },
          set(value) {
              // normalize to string and commit to store so inputs update reactively
              this.$store.commit('SET_CUSTOMER', value);
          }
      },
      loggedInUser() {
          return this.$store.getters.loggedInUser;
      },
      fb() {
          return this.$store.getters.fb;
      },
      bookedData() {
          return this.$store.getters.bookedData || [];
      },
      nearestShowroom() {
          // console.log("nearestShowroom =", this.$store.getters.nearestShowroom);
          return this.$store.getters.nearestShowroom || [];
      }
    },
    // local filters for this component
    filters: {
      capitalize(value) {
        if (value === null || value === undefined) return '';
        const str = String(value);
        return str.length === 0 ? '' : str.charAt(0).toUpperCase() + str.slice(1);
      }
    },
    created() {
      this.$store.dispatch('getPinboard', { userId: 1 });
    },
    mounted() {
      // Clean up intervals when component is destroyed
      if (this.$refs.timeSlotsModal && window.bootstrap) {
          this.modalInstance = new bootstrap.Modal(
              this.$refs.timeSlotsModal
          );
      }
      // email from local storage
      const userAuthDetails = JSON.parse(localStorage.getItem('userAuthDetails') || '{}');
      if(userAuthDetails && userAuthDetails.email){
        this.email = userAuthDetails.email;
      }
    },
    methods: {
      getKey(item, index) {
        return `${item.model_type}-${item.model_id}-${index}`;
      },
      // comment add and edit
      async removePinboardItem(pinboardItem, index) {
          await this.$store.dispatch('removePinboardItem', { pinboardItem, index });
      },
      async addPinboardItemComment(item, index, newComments = false) {
        const key = this.getKey(item, index);
        const comment = newComments ? item.newComments[0] : (item.comments && item.comments[0]) || '';
        await this.$store.dispatch('addPinboardItemComment', { pinboard_item_id: item.pinboard_item_id, index, comment });
        this.$set(this.editPinboardItemComments, key, false);
      },
      updateItemComment(item, property, value) {
        if (!Array.isArray(item[property])) {
          this.$set(item, property, []);
        }
      
        this.$set(item[property], 0, value);
      },
      // description add and edit
      editItemDescription(item, index) {
        const key = this.getKey(item, index);
        this.$set(this.editPinboardItemEditing, key, true);
        this.$set(this.editPinboardItemDescriptions, key, item.description);
      },
      updatePinboardItemDescription(item, index) {
        const key = this.getKey(item, index);
        const newDescription = this.editPinboardItemDescriptions[key] ?? item.description ?? '';
        console.log('newDescription=',  newDescription);
        this.$store.dispatch('updatePinboardItemDescription', { pinboard_item_id: item.pinboard_item_id, index, description: newDescription });
        this.$set(this.editPinboardItemEditing, key, false);
      },
      uploadCommentItemImage(event) {
        const file = event?.target?.files && event.target.files[0];
        if (!file) return;
        const objectURL = URL.createObjectURL(file);
        this.$store.dispatch('uploadCommentItemImage', { file, objectURL });
      },
      removeCommentItemImage(file, index) {
        this.$store.dispatch('removeCommentItemImage', { file, index });
      },

      // old
      removeFromPinboard(id, type, ev) {
        this.$store.dispatch('removeFromPinboard', {
          model_id: id,
          model_type: type
        });
      },
  
      cloneItem(item) {
        return { ...item, _isDragPreview: true };
      },
      uploadCommentItemImage(event) {
        const file = event?.target?.files && event.target.files[0];
        if (!file) return;
        const objectURL = URL.createObjectURL(file);
        this.$store.dispatch('uploadCommentItemImage', { file, objectURL });
    },
    
    viewProduct(name, model_type) {
      // replace spaces with hyphens
      let cleanName = name.replace(/ /g, '-').toLowerCase();
      let url = '';
      if(model_type === 'product') {
        url = `/products/desks-modesty-panels`;
      } else if(model_type === 'project') {
        url = `/projects/${cleanName}`;
      } else if(model_type === 'post') {
        url = `/blog/${cleanName}`;
      } else if(model_type === 'comment') {
        url = `/comments/${cleanName}`;
      }
      return url;
    },

    async addCommentItemToPinboard() {
        // collect comment text and files from store
        const commentText = (this.additionalComment || '').trim();

        // validation: require at least 5 characters
        if (commentText.length < 2) {
            this.additionalCommentError = true;
            return;
        }
        // clear any previous error
        this.additionalCommentError = false;

        // prepare payload for store action
        const payload = {
            comment: commentText,
        };

        try {
            await this.$store.dispatch('addCommentItemToPinboard', payload);
            // clear local UI state on success
            this.additionalComment = '';
            this.additionalCommentError = false;
        } catch (err) {
            console.error('Failed to submit comment', err);
        }
    },
    async handleBookingClick(type) {

        // validate selected slot value
        if(!this.selectedSlotValue){
          alert('Please select a time slot');
          return;
        }

          // if(!this.pinboard.pinboard_id){
          //   alert('Please save the pinboard first to book a virtual meeting');
          //   return;
          // }

          const bookingData = {
              showroom_contact_id: this.nearestShowroom?.showroom_contact_id ?? 1,
              customer_id: this.customer?.customer_id ?? 120,
              email: this.email,
              showroom_id: this.nearestShowroom?.showroom_id ?? 1,
              pinboard_id: this.pinboard?.pinboard_id ?? 61,
              tour_type: 'physicalTour',
              date: this.selectedDate,
              meeting_time: this.selectedSlotValue,
              time_zone: 'Asia/Dhaka',
            };

          // console.log("bookingData =", bookingData);

          const response = await this.$store.dispatch('bookNow', bookingData);
          console.log("response bookNow virtual pinboard =", response);
          if(response && response.success){
              const modalEl = this.$refs.timeSlotsModal;
              if (modalEl && window.bootstrap) {
                  const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                  modal.hide();
              }
              // remove spinner-border spinner-border-sm me-2
              const button = document.getElementById('th-book-time-btn');

              if (button) {
                  // remove spinner
                  const spinner = button.querySelector('.spinner-border');
                  if (spinner) spinner.remove();
          
                  // reset text
                  button.textContent = 'Book Now';
          
                  // enable button again
                  button.disabled = false;
              }
              // open new window
              this.checkPinboardProcess('showroom-visit');
          }else{
            alert(response.message);
          }
  },
  async handleShowroomVisitBooking(event) {
      event.preventDefault();
      this.showBookingModal = true;
      setTimeout(() => {
          window.initFlatpickr();
          window.initTimezoneChoices();
          const calendarContainer = document.querySelector(".flatpickr-days");
          const component = this;
          if (calendarContainer) {
            calendarContainer.addEventListener("click", async function (e) {
              const day = e.target.closest(".flatpickr-day");
              if (day &&
                  !day.classList.contains("disabled") &&
                  !day.classList.contains("flatpickr-prev-month") &&
                  !day.classList.contains("flatpickr-current-month") &&
                  !day.classList.contains("flatpickr-next-month")) {
                  // Get date from aria-label
                  const selectedDate = day.getAttribute("aria-label");
                  const formattedDate = new Date(selectedDate).toLocaleDateString('en-GB');
                  const parts = formattedDate.split('/');
                  const selectedDateFormatted = `${parts[2]}-${parts[1]}-${parts[0]}`;
                  component.selectedDate = selectedDateFormatted;
                  console.log("selectedDateFormatted =", selectedDateFormatted);
                  document.getElementById("ts-selected-date").textContent = selectedDateFormatted;
                  // component.selectedDate = formattedDate;
                  // console.log("component.selectedDate =", component.selectedDate);
                  await component.$store.dispatch('getBookedData', selectedDateFormatted);
                  component.openModal();
                  console.log(component.selectedDate, "component.selectedDate");
                 
                  const bookedTimes = component.bookedData.map((row) => (row && row.meeting_time ? row.meeting_time : ''))
                    .filter(Boolean);
                    console.log("bookedTimes =", bookedTimes);
                  //   component.openModal();
                  // remove spinner-border spinner-border-sm me-2
                  component.markBookedTimeSlots(bookedTimes);
              }
            });
          }
      }, 200);
  },
  async changeDate(date) {
    if(!date) return;
    this.selectedDate = date;
    document.getElementById("ts-selected-date").textContent = date;
    await this.$store.dispatch('getBookedData', date);
    const bookedTimes = this.$store.getters.bookedData?.map((row) => (row && row.meeting_time ? row.meeting_time : ''))
      .filter(Boolean);
    this.markBookedTimeSlots(bookedTimes);
  },
  checkPinboardProcess(type) {
    console.log("pinboard process type =", this.pinboard.pinboard_id);
      // Save data
      localStorage.setItem(
        'pinboard_processed',
        JSON.stringify({
          pinboard_id: this.pinboard.pinboard_id,
          processed_method: type
        })
      );
    
      // Open new tab
      window.open(`/pinboards/${this.pinboard.pinboard_id}/booking/${type}`, '_blank');
    
      // Check every 10 seconds for up to 5 minutes
      const maxTime = 5 * 60 * 1000; // 5 minutes
      const intervalTime = 10 * 1000; // 10 seconds
      let elapsed = 0;
    
      const interval = setInterval(() => {
        const data = localStorage.getItem('pinboard_processed');
    
        if (!data) {
          // Data missing → reload immediately
          location.reload();
          clearInterval(interval);
          return;
        }
    
        elapsed += intervalTime;
        if (elapsed >= maxTime) {
          // Stop after 5 minutes
          clearInterval(interval);
        }
      }, intervalTime);
  },
  finalHref(type) {
      return `/pinboards/${this.pinboard.pinboard_id}/booking/${type}`;
  },
  finalPageRedirect(type, event) {
      event.preventDefault();
      this.checkPinboardProcess(type);
      // console.log("finalPageRedirect type =", type);
      // window.open(this.finalHref(type), '_blank');
  },
    // time slot booking
  async handleDayClick(e) {
      this.openModal();
  },

  openModal() {
      const modalEl = this.$refs.timeSlotsModal;
  
      if (modalEl && window.bootstrap) {
          const modal = new bootstrap.Modal(modalEl);
          modal.show();
      }
  },
  resetAllTimeSlots() {
      this.bookedTimes = [];
      // your existing reset logic
  },
  markBookedTimeSlots(bookedTimes) {
      console.log("Booked bookedTimes:", bookedTimes);
    
      const slots = document.querySelectorAll('.th-time-slot');
    
      // STEP 1: Reset সব slot (IMPORTANT)
      slots.forEach((slot) => {
        const checkbox = slot.querySelector('input[type="checkbox"]');
        const icon = slot.querySelector('i');
    
        slot.classList.remove('active', 'disabled', 'time-slot-disabled');
        slot.removeAttribute('disabled');
        slot.classList.add('no-hover');
    
        if (checkbox) {
          checkbox.disabled = false;
          checkbox.classList.remove('d-none', 'time-slot-disabled');
        }
    
        if (icon) {
          icon.classList.add('d-none');
        }
      });
    
      // STEP 2: booked list prepare
      const bookedSet = new Set(
        (Array.isArray(bookedTimes) ? bookedTimes : [])
          .map((time) => String(time || '').trim())
          .filter(Boolean)
      );
    
      // যদি empty হয়, এখানেই stop (reset already done)
      if (bookedSet.size === 0) return;
    
      // STEP 3: apply booked state
      slots.forEach((slot) => {
        const checkbox = slot.querySelector('input[type="checkbox"]');
        const icon = slot.querySelector('i');
    
        if (!checkbox) return;
    
        const value = String(checkbox.value || '').trim();
    
        if (!bookedSet.has(value)) return;
    
        slot.classList.add('active', 'disabled', 'time-slot-disabled');
        slot.setAttribute('disabled', 'disabled');
        slot.classList.remove('no-hover');
    
        checkbox.checked = false;
        checkbox.disabled = true;
        checkbox.classList.add('d-none', 'time-slot-disabled');
    
        if (icon) icon.classList.remove('d-none');
      });
    },
    markBookedTimeSlots_new(bookedTimes) {
      const bookedSet = new Set(
        (Array.isArray(bookedTimes) ? bookedTimes : [])
          .map(time => String(time).trim())
          .filter(Boolean)
      );
    
      document.querySelectorAll('.th-time-slot').forEach((slot) => {
        const checkbox = slot.querySelector('input[type="checkbox"]');
        if (!checkbox) return;
    
        const timeValue = String(checkbox.value || '').trim();
    
        if (bookedSet.has(timeValue)) {
          // booked → hide
          slot.classList.add('d-none');
          checkbox.checked = false;
          checkbox.disabled = true;
        } else {
          // available → show
          slot.classList.remove('d-none');
          checkbox.disabled = false;
        }
      });
    },
    toggleSlot(event, slotId, slotValue) {
      event.preventDefault();
        const slot = event.currentTarget.closest('.th-time-slot');
        if (!slot || slot.classList.contains('disabled') || slot.classList.contains('time-slot-disabled')) {
          return;
        }

        document.querySelectorAll('.th-time-slot:not(.disabled):not(.time-slot-disabled)')
        .forEach(el => {
          el.classList.remove('active', 'selected');
        });

        if (slot) {
            slot.classList.add('selected', 'active');
        }
        
        // this.selectedSlot = slotId;
        // this.checkedSlot = slotId;
        this.selectedSlotValue = slotValue;
      }
    },  
    template: /* html */ `
    <div>
      <transition-group tag="div" name="pinboard">
        <div
          id="dashboard-pinboard-item"
          class="th-section"
          key="pinboard"
        >
          <div class="section-body">

            <!-- HEADER -->
            <div class="row">
              <div class="col-md-12">
                <div class="th-pinbaoder-item-title d-flex">
                  <h3 class="th-pinboard-header">Virtual Pinboard</h3>
                </div>
              </div>

              <div class="col-md-12 mt-30">
                <div class="th-dash-pinboard-devider border-bottom-gray"></div>
              </div>
            </div>

            <!-- ITEMS -->
            <div class="row">
              <div
                v-for="(item, index) in items"
                :key="getKey(item, index)"
                class="col-md-12 mt-40"
              >
                <div class="th-pinboard-item">
                  <div class="row">

                    <!-- IMAGE -->
                    <div class="col-md-4">
                      <div class="th-pinboard-item-img border">
                        <img
                          :src="item.photo"
                          :alt="item.title"
                          class="img-fluid"
                        />
                      </div>
                    </div>

                    <!-- INFO -->
                    <div class="col-md-8">
                      <div class="th-pinboard-item-info">
                        <div class="card-body position-relative">

                          <!-- REMOVE -->
                          <button
                            type="button"
                            class="btn-close position-absolute top-0 end-0 p-2"
                            @click.prevent="removePinboardItem(item, index, $event)"
                          ></button>

                          <div class="th-pinboard-info-body">

                            <!-- TYPE -->
                            <h4 class="fw-bold">
                              {{ item.model_type | capitalize }}
                            </h4>

                            <!-- TITLE -->
                            <p class="th-title-20">
                              {{ item.title }}
                            </p>

                            <!-- COMMENT IMAGES -->
                            <div
                              class="th-pinboard-item-circle"
                              v-if="item.comment_images && item.comment_images.length"
                            >
                              <div class="th-circle-items">
                                <img
                                  v-for="(img, i) in item.comment_images"
                                  :key="i"
                                  :src="img"
                                  class="rounded-circle border border-light"
                                />
                              </div>
                            </div>

                            <!-- ACCESSORIES -->
                            <div
                              class="th-item-product"
                              v-if="item.accessories && item.accessories.length"
                            >
                              <span class="mb-2 th-title-20 text-success">
                                Accessories:
                              </span>
                              <div class="th-item-footer">
                                <div class="th-tag-name">
                                  <div
                                    class="th-tag"
                                    v-for="accessory in item.accessories"
                                    :key="accessory.product_accessories_id"
                                  >
                                    {{ accessory.title }}
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- DESCRIPTION -->
                            <div class="th-pinboard-item-edit mt-3">
                              <div class="border d-flex justify-content-between w-100">

                                <div class="w-100">
                                  <blockquote
                                    v-if="!editPinboardItemEditing[getKey(item, index)]"
                                  >
                                    <p class="fst-italic">
                                      "{{ item.description }}"
                                    </p>
                                  </blockquote>

                                  <div v-else class="p-2">
                                    <textarea
                                      class="form-control"
                                      rows="3"
                                      v-model="editPinboardItemDescriptions[getKey(item, index)]"
                                    ></textarea>
                                  </div>
                                </div>

                                <button class="btn">
                                  <i
                                    class="fa-solid fa-pencil"
                                    v-if="!editPinboardItemEditing[getKey(item, index)]"
                                    @click="editItemDescription(item, index)"
                                  ></i>

                                  <i
                                    class="fa-solid fa-check"
                                    v-else
                                    @click="updatePinboardItemDescription(item, index)"
                                  ></i>
                                </button>

                              </div>
                            </div>

                             <!-- COMMENT INPUT -->
                          <input
                          v-if="!(item.comments && item.comments.length)"
                          type="text"
                          class="form-control mt-3"
                          placeholder="Add A Comment"
                          @input="updateItemComment(item, 'newComments', $event.target.value)"
                          :value="(item.comments && item.comments[0]) || ''"
                        />

                        <!-- ACTIONS -->
                        <div class="d-flex justify-content-between mt-3">
                          <div class="th-doc-actions d-flex gap-2">
                            <a
                              v-if="!(item.comments && item.comments.length)"
                              class="th-btn-primary text-capitalize"
                              @click.prevent="addPinboardItemComment(item, index, true)"
                            >
                              Add Comment
                            </a>

                            <a
                              class="th-btn-gray text-capitalize"
                              target="_blank"
                              :href="viewProduct(item.title ?? '', item.model_type)"
                            >
                              View Product
                              <i class="fa-solid fa-arrow-up degree-60"></i>
                            </a>
                          </div>
                        </div>

                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </transition-group>
      <!-- EMPTY STATE -->
      <div v-if="!fb.loading.getPinboard && items.length === 0" class="text-center py-4">
        No items found
      </div>
      <div id="dashboard-pinboard-comment" class="th-section">
      <div class="section-body">
        <div class="row">
    
          <div class="col-md-12">
            <h4 class="th-pinboard-comment-title">Additional notes</h4>
            <div class="th-pinboard-lower" id="th-pinboard-user">
              <div class="th-offcanvas-containr">
                <!-- <h4 class="th-ofcanvas-comment-title">Additional Notes</h4> -->
                <textarea
                  v-model="additionalComment"
                  :style="additionalCommentError ? { border: '1px solid red' } : {}"
                  @input="additionalCommentError = false"
                  class="comment-box th-offcanvas-comment-box th-off-large-commentbox"
                  placeholder="Add A Comment"
                ></textarea>
    
                <!-- image upload section -->
                <div class="th-doc-actions d-flex flex-column">
                  <div class="d-flex w-100" style="gap: 15px; flex-wrap: wrap;">
                    <div
                      v-for="(file, idx) in commentFiles"
                      :key="file.tmp_name || idx"
                      class="d-flex"
                      style="width: 100px; height: 100px; background-color: #f0f0f0; position: relative; overflow: hidden;"
                    >
                      <span
                        class="remove-btn"
                        :id="'removeBtn-' + idx"
                        @click="removeCommentItemImage(file, idx)"
                        style="position: absolute; top: 0px; right: 0px; z-index: 5; background: rgba(207, 30, 30, 0.9); padding: 2px 4px; border-radius: 12px; cursor: pointer;"
                      >
                        <i class="fa-solid fa-xmark"></i>
                      </span>
                      <img
                        :src="file.objectURL"
                        alt="Image"
                        :title="file.name"
                        style="width: 100%; height: 100%; object-fit: cover;"
                      />
                    </div>
                  </div>
    
                  <div class="d-flex justify-content-between w-100">
                    <div class="d-flex justify-content-start">
                      <div class="flex flex-column">
                        <label
                          class="th-btn-gray text-capitalize mr-10 border"
                          style="cursor:pointer; margin-bottom:0;"
                        >
                          Upload Image +
                          <input type="file" accept="image/*" style="display:none;" @change="uploadCommentItemImage($event)" />
                        </label>
                      </div>
                    </div>
    
                    <div class="d-flex justify-content-end">
                      <a
                        id="add-comment-button"
                        class="th-btn-primary text-capitalize"
                        @click.prevent="addCommentItemToPinboard"
                      >
                        <span class="mr-5">Add Comment</span>
                      </a>
                      <a
                        id="update-pinboard-button"
                        class="th-btn-gray text-capitalize border"
                        style="display: none;"
                      >
                        <span class="mr-5">Update Pinboard</span>
                      </a>
                    </div>
                  </div>
    
                </div>
              </div>
            </div>
          </div>
    
          <div class="col-md-12 d-flex justify-content-center pt-20">
            <div class="th-pinboard-comment-border-bottom border-bottom-gray w-100"></div>
          </div>
    
          <div class="col-md-12">
            <div class="th-doc-actions d-flex justify-content-end th-pt-30">
              <button
                type="button"
                class="th-btn-gray text-capitalize"
                data-bs-toggle="modal"
                data-bs-target="#staticBackdrop"
              >
                <span class="mr-5">Send to sell</span>
                <i class="fa-regular fa-arrow-up degree-60"></i>
              </button>
            </div>
          </div>
    
        </div>
      </div>
    </div>
    
    <!-- modal section -->
    <div
      class="modal fade"
      id="staticBackdrop"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
      tabindex="-1"
      aria-labelledby="staticBackdropLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
    
          <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
    
          <div class="modal-body">
            <div class="px-30 sendToSell-modal-body">
              <h4>How would you like to review your project with a Krost Consultant?</h4>
              <div class="d-flex flex-column gap-4 button-group">
    
                <!-- Talk on the Phone -->
                <a class="text-capitalize text-start" href="tel:+2586232325" @click.prevent="finalPageRedirect('phone-call', $event)">
                  <i class="fa fa-phone"></i>
                  <span>Talk on the Phone</span>
                </a>
    
                <!-- Discuss via Email -->
                <a
                  class="text-capitalize text-start"
                  href="mailto:sales@krost.com.au?subject=Krost%20Consultation%20Request"
                  @click.prevent="finalPageRedirect('email', $event)"
                >
                  <i class="fa-solid fa-envelope"></i>
                  <span>Discuss via Email</span>
                </a>
    
                <!-- Book Showroom Visit (another modal) -->
                <button
                  type="button"
                  class="text-capitalize text-start"
                  data-bs-toggle="modal"
                  id="bookShowroomVisitButton"
                  @click.prevent="handleShowroomVisitBooking($event)"
                  data-bs-target="#bookShowroomVisitModal"
                >
                  <i class="fa fa-building"></i>
                  <span>Book Showroom Visit</span>
                </button>
    
                <!-- Book a Virtual Meeting -->
                <a
                  href="#"
                  class="text-capitalize text-start"
                  target="_blank"
                  rel="noopener noreferrer"
                  @click.prevent="finalPageRedirect('virtual-meeting', $event)"
                >
                  <i class="fa fa-video"></i>
                  <span>Book a Virtual Meeting</span>
                </a>
    
              </div>
            </div>
          </div>
    
          <div class="text-center py-30 sendToSellCancleBtn">
            <button type="button" class="th-btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
    
        </div>
      </div>
    </div>
    
    <!-- showroom visit modal -->
    <div
      class="modal fade"
      id="bookShowroomVisitModal"
      data-bs-backdrop="static"
      data-bs-keyboard="false"
      tabindex="-1"
      aria-labelledby="bookShowroomVisitModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content modelBorderRadius">
    
          <div class="modal-header">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
    
          <div class="modal-body">    
              <form id="bookingModalForm" class="booking-modal-form">
                <div class="booking-modal-content">
                  <div class="row">
                      <div class="col-md-6">
                      <div class="th-booking-left-section">
                          <div class="th-booking-selected-member-image">
                          <img src="/img/logo_black.png" alt="">
                          </div>
                          <div class="th-booking-location-details">
                          <div id="showroomLoader" class="d-none">
                              <div class="spinner-border text-primary" role="status">
                              <span class="visually-hidden">Loading...</span>
                              </div>
                          </div>
                          <!-- member details -->
                          <div class="th-booking-selected-member d-flex flex-column mb-20">
                          <div class="th-member-info p-0">
                                <h3 class="font-size-20" data-v-booknow-section_title="">Meet With Our Consultant</h3>
                                <!--<p class="th-member-name" data-v-booknow-name>{{ nearestShowroom?.contact_name }}</p>-->
                            </div>
                              <div class="th-booking-member-avatar">
                                  <img :src="nearestShowroom?.image" alt="Member Avatar" data-v-booknow-member_image  style="width: 270px; height: 215px; border-radius: 10px;"/>
                              </div>
                          </div>
                          <!-- showroom details -->
                          <h4 class="font-weight-600" id="showroomName">{{ nearestShowroom?.title }}</h4>
                          <p class="font-weight-400 color-black">
                              <i class="fa-solid fa-location-dot"></i>
                              <span id="showroomAddress"> {{ nearestShowroom?.address }}</span>
                          </p>
                          <div class="d-flex th-booking-tour-option my-15">
                              <div class="d-flex align-items-center gap-2">
                                  <input type="radio" id="physicalTour" name="tour_type" checked>
                                  <label for="physicalTour">Physical Tour</label>
                              </div>
                          </div>
                          </div>
                          <div class="th-booking-description">
                        <!-- <p class="font-weight-400 color-black mb-15">Lorem ipsum dolor sit amet consectetur. Nam dignissim
                              at vitae faucibus. Lectus ac elit morbi nisl.</p>
                          <p class="font-weight-400 color-black">Vestibulum neque nunc mattis mauris. Vitae lorem volutpat
                              gravida augue aliquet at nulla.</p>
                          -->
                          </div>
                      </div>

                      </div>
                      <div class="col-md-6 th-booking-calendar-section">
                      <h3 class="mt-50 font-weight-600">Select a Date & Time</h3>
                      <div class="booking-calendar-wrapper p-0">
                          <div class="th-booking-calendar" @click="handleDayClick($event)">
                            <input class="d-none" type="text" placeholder="Select Date.." data-input />
                          </div>
                      </div>
                      </div>
                  </div>
                </div>
            </form>
    
            <div id="pinboardSuccessMessage" class="th-pinboard-success-message d-none">
              <div class="text-center">
                <h3 class="font-weight-700">Welcome To Krost</h3>
                <p class="font-weight-400">
                  Your account is ready. We’ve successfully saved your selection to your new <br />
                  <strong id="pinboardName"></strong> board
                </p>
              </div>
              <div class="text-center py-50">
                <a href="/" class="th-btn-primary text-capitalize w-100">Continue Pinning</a>
                <button onclick="window.location.href='/projects'" class="th-btn-transparent text-capitalize w-100 py-2 mt-30">
                  View Project 1 <i class="fa-regular fa-arrow-up degree-60"></i>
                </button>
              </div>
            </div>

            <div class="modal fade backdrop-static" id="timeSlotsModal" tabindex="-1" aria-labelledby="timeSlotsModalLabel" data-bs-backdrop="false" style="position:fixed; inset:0; background-color: rgba(0,0,0,0.5); z-index:1040;"
            aria-modal="true" role="dialog" ref="timeSlotsModal">
                <div class="modal-dialog modal-dialog-centered modal-lg modal-custom-width">
                    <div class="modal-content px-80 py-60">
                    <div class="modal-header">
                    <h5 class="modal-title" id="timeSlotsModalLabel">Booking for Showroom Tour</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="booking-form-container">
                        <div class="gap-10">
                        <!-- 1. consultant -->
                       <!-- <div class="th-form-row">
                            <label for="ts-consultant">Consultant</label>
                            <div class="th-field">
                                <span id="ts-consultant-name" class="th-consultant-name">Devon Lane</span>
                            </div>
                        </div> -->
                        <!-- 2. date -->
                        <div class="th-form-row">
                            <label for="ts-selected-date">Date</label>
                            <div class="th-field">
                                <div class="th-input-group d-flex align-items-center">
                                    <i class="fa-solid fa-calendar th-input-icon"></i>
                                    <input type="date" id="ts-selected-date" class="form-control th-date-input" :value="selectedDate" @change="changeDate($event.target.value)">
                                </div>
                            </div>
                        </div>
                        <!-- 3. time zone -->
                        <div class="th-form-row th-timezone-selector">
                            <label for="choose-timezone">Time Zone</label>
                            <div class="th-field">
                                <div class="th-input-group d-flex align-items-center">
                                    <i class="fa-solid fa-globe th-input-icon"></i>
                                    <select class="form-control th-choices-select" name="choose-members"
                                        id="choose-timezone" placeholder="This is a placeholder" disabled>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- 4. duration -->
                        <!-- 6. email -->
                        <div class="th-form-row">
                            <label for="ts-email-not-logged-in-email">Email</label>
                            <div class="th-field">
                                <div class="th-input-group d-flex align-items-center" id="ts-email-not-logged-in-email-container">
                                    <i class="fa-solid fa-envelope th-input-icon"></i>
                                    <input type="email" id="ts-email-not-logged-in-email" class="form-control"
                                        placeholder="Enter your email" style="border: none !important;" :value="email">
                                </div>
                            </div>
                        </div>
    
                        <!-- 5. time slots/pick a time -->
                        <div class="th-form-row th-pt-20">
                            <label>Pick a time</label>
                            <div class="th-field">
                                <strong class="d-block">Choose a slot below</strong>
                            </div>
                        </div>
                    </div>
                    <div class="ts-slots-blocks th-pt-20">
                        <div class="ts-slots-block ts-slots-morning">
                            <div id="ts-slots-morning" class="ts-slots-grid d-flex flex-wrap gap-2">
                                <div
                                v-for="slot in morningSlots"
                                :key="slot.id"
                                class="th-time-slot"
                                :class="{
                                    active: selectedSlot === slot.id,
                                    'no-hover': selectedSlot !== slot.id,
                                    selected: selectedSlot === slot.id
                                }"
                                @click="toggleSlot($event, slot.id, slot.value)"
                                >
                                <i class="fa-light fa-arrow-right" :class="{ 'd-none': selectedSlot !== slot.id }"></i>
                                <input
                                    type="checkbox"
                                    class="d-none"
                                    :id="slot.id"
                                    :value="slot.value"
                                    v-model="checkedSlot"
                                />
                                <label :for="slot.id">{{ slot.label }}</label>
                                </div>
                            </div>
                        </div>                                          

                        <div class="ts-slots-block ts-slots-evening">
                            <div id="ts-slots-evening" class="ts-slots-grid d-flex flex-wrap gap-2">
                                <div
                                v-for="slot in eveningSlots"
                                :key="slot.id"
                                class="th-time-slot"
                                :class="{
                                    active: selectedSlot === slot.id,
                                    'no-hover': selectedSlot !== slot.id,
                                    selected: selectedSlot === slot.id,
                                }"
                                @click="toggleSlot($event, slot.id, slot.value)"
                                >
                                <i class="fa-light fa-arrow-right" :class="{ 'd-none': selectedSlot !== slot.id }"></i>
                                <input
                                    type="checkbox"
                                    class="d-none"
                                    :id="slot.id"
                                    :value="slot.value"
                                    v-model="checkedSlot"
                                />
                                <label :for="slot.id">{{ slot.label }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="ts-no-slots" style="display:none;">No available slots for the selected date/time zone.</div>
                    <div class="pt-60">
                        <button type="button" id="th-book-time-btn" class="th-btn-primary text-capitalize" @click.prevent="handleBookingClick('showroom-visit', $event)">Book</button>
                    </div>
                    </div>
                    <div class="col-md-7 d-none">
                        <div class="th-calendar-container">
                            <div class="booking-calendar-wrapper">
                                <div class="th-booking-calendar">
                                    <input class="d-none" type="text" placeholder="Select Date.." data-input />
                                </div>
                            </div>
                            <div class="th-timezone-selector pt-50">
                                <h4 data-v-booknow-calendar_title>Time Zone</h4>
                                <div class="th-input-group">
                                    <i class="fa-solid fa-globe"></i>
                                    <select class="form-control th-choices-select" name="choose-members" id="choose-timezone"
                                        placeholder="This is a placeholder"></select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
          </div>
        </div>
      </div>
    </div>
    
      
      
    </div>
    `
    
  };
  