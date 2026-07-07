import ManagePinboard from "./ManagePinboard.js";

export default {
  name: "AccountPinboardList",
  components: {
    ManagePinboard,
  },
  data() {
    return {
      showEmbeddedPinboard: false,
      managePinboardLoading: false,
      managePinboardError: null,
    };
  },

  computed: {
    projectLists() {
      const list = this.$store.getters.projectLists;
      return Array.isArray(list) ? list : [];
    },
    pinboard(){
      return this.$store.getters.pinboard;
  },
  projectMenuItems() {
      return this.$store.getters.projectItems;
  },
  items: {
      get() {
          return this.$store.getters.pinboardItems || [];
      },
      set(value) {
          console.log('items set component :- ', value);
          this.$store.dispatch('reorderPinboardItems', value);
      }
  },
  },

  created() {
    this.$store.dispatch("getProjectLists");
  },

  methods: {
    onManagePinboardOffcanvasHidden() {
      this.showEmbeddedPinboard = false;
      this.managePinboardError = null;
    },

    getKey(item, index) {
      return `${item.model_type}-${item.model_id}-${index}`;
    },

    async openManagePinboardOffcanvas(pinboardId) {
      if (!pinboardId) return;

      this.managePinboardLoading = true;
      this.managePinboardError = null;
      this.showEmbeddedPinboard = false;

      const result = await this.$store.dispatch(
        "getProjectItemsByPinboardId",
        pinboardId,
      );

      // if (
      //   result &&
      //   result.success &&
      //   !result.error &&
      //   this.$store.state.loggedInUser
      // ) {
      //   await pinboardStore.dispatch("getProjectByPinboardId", pinboardId);
      // }

      this.managePinboardLoading = false;

      if (!result || result.success === false || result.error) {
        this.managePinboardError =
          (result && result.error) ||
          "Could not load this pinboard. Please try again.";

        const el = document.getElementById("manage_pinboard_offcanvasRight");
        if (el && window.bootstrap?.Offcanvas) {
          window.bootstrap.Offcanvas.getOrCreateInstance(el).show();
        }
        return;
      }

      this.showEmbeddedPinboard = true;
      await this.$nextTick();

      const el = document.getElementById("manage_pinboard_offcanvasRight");
      if (!el || !window.bootstrap?.Offcanvas) return;

      window.bootstrap.Offcanvas.getOrCreateInstance(el).show();
    },
  },

  template: /* html */ `
  <div class="section-body">
  <div class="row">
    <div class="col-12">
      <div class="th-dash-pinboard-devider border-bottom-gray"></div>
    </div>

    <div class="col-12 th-table-content">
      <div class="th-table th-table-col-7" data-v-pinboard-table>
        <!-- HEADER -->
        <div class="th-header-wrapper">
          <div class="th-row-wrapper">
            <div class="th-cell th-pinboard-col-project" data-v-pinboard-header="name">Project</div>
            <div class="th-cell" data-v-pinboard-header="customer">Name</div>
            <div class="th-cell" data-v-pinboard-header="email">Email</div>
            <div class="th-cell th-pinboard-col-qty text-end" data-v-pinboard-header="items">Quantity</div>
            <div class="th-cell th-pinboard-col-total text-end" data-v-pinboard-header="total">Total</div>

            <!-- STATUS COLUMN -->
            <div class="th-cell th-pinboard-col-status text-center" data-v-pinboard-header="status">Status</div>

            <div class="th-cell th-pinboard-col-actions text-center" data-v-pinboard-header="actions">Actions</div>
          </div>
        </div>

        <!-- BODY -->
        <div  class="th-body-wrapper" data-v-pinboard-rows>
          <div class="th-row-wrapper" data-v-pinboard-row v-for="pinboard in projectLists" :key="pinboard.pinboard_id">
            <div class="th-cell th-pinboard-col-project font-wight-700" data-v-pinboard-name>
              {{ pinboard.pinboard_name }}
            </div>
            <div class="th-cell" data-v-pinboard-customer-name>
              {{ pinboard.customer_name }}
            </div>
            <div class="th-cell" data-v-pinboard-customer-email>
              {{ pinboard.customer_email }}
            </div>
            <div class="th-cell th-pinboard-col-qty text-end" data-v-pinboard-item-count>
              {{ pinboard.item_count }}
            </div>
            <div class="th-cell th-pinboard-col-total text-end font-weight-600" data-v-pinboard-total>
              {{ pinboard.total_price }}
            </div>

            <!-- STATUS VALUE -->
            <div class="th-cell th-pinboard-col-status text-center">
              <nobr data-v-pinboard-status class="d-inline-block th-pinboard-status-pill"> 
                {{ pinboard.pinboard_status_id == 0 ? 'Not Open In' : (pinboard.pinboard_status_id == 1 ? 'Open In' : 'Opted Out') }}
              </nobr>
            </div>

            <div class="th-cell th-pinboard-col-actions text-center" data-v-pinboard-link>
                <a href="#" role="button" class="th-btn-primary text-capitalize th-pinboard-action-btn"
                  data-v-pinboard-view
                  @click.prevent="openManagePinboardOffcanvas(pinboard.pinboard_id)">
                  View
                </a>

            </div>
          </div>
        </div>
        <!-- END BODY -->
      </div>
    </div>
  </div>

<!-- ============================== Start manage pinboard offcanvas ============================== -->
<ManagePinboard
  :items="items"
  :pinboard="pinboard"
  :loading="managePinboardLoading"
  :error="managePinboardError"
  @hidden="onManagePinboardOffcanvasHidden"
/>
<!-- ============================== end manage pinboard offcanvas ============================== -->


</div>



  `,
};
