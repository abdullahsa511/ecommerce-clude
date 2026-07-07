// pinboard store add to pinboard call 
import pinboardStore from '../../store/pinboardStore.js';
const ProjectList = {
    name: 'ProjectList',
    props: {
      
    },
    data() {
        return {
            localError: null,
            pinboardLoadingId: null
        }
    },
    computed: {
        totalListsCount() {
            return this.$store ? this.$store.state.total : 0;
        },
        storeLoadedLists() {
            return this.$store ? this.$store.state.loadedLists : [];
        },
        loading() {
            return this.$store ? this.$store.state.loading : false;
        },
        error() {
            return this.$store ? this.$store.state.error : this.localError;
        },
        currentPage() {
            return this.$store ? this.$store.state.current_page : 1;
        },
        perPage() {
            return this.$store ? this.$store.state.per_page : 21;
        },
        hasMore() {
            const loaded = this.currentPage * this.perPage;
            return loaded < this.totalListsCount;
        },
    },
    updated() {
      // Re-init tooltips after reactive DOM updates (without using `watch`).
      if (this._pinboardTooltipRaf) return;
      this._pinboardTooltipRaf = window.requestAnimationFrame(() => {
        this._pinboardTooltipRaf = null;
        this.initPinboardTooltips();
      });
    },
    mounted() {
        // Component just displays lists from store
        // No need to load on mount - lists are loaded by button click
        // console.log('ProjectList mounted. Loaded lists:', this.storeLoadedLists.length);
        this.$nextTick(() => {
          this.initPinboardTooltips();
        });
    },
    methods: {
      link(slug) {
        return `/projects/${slug}`;
      },
      initPinboardTooltips() {
        const root = this.$el;
        if (!root) return;

        const tooltipEls = root.querySelectorAll(
          '.th-pinboard-tooltip[data-toggle="tooltip"]'
        );
        if (!tooltipEls || tooltipEls.length === 0) return;

        const hasJqueryTooltip =
          window.jQuery && typeof window.jQuery.fn.tooltip === 'function';

        tooltipEls.forEach((el) => {
          const tooltipText =
            el.getAttribute('data-bs-original-title') ||
            el.getAttribute('data-original-title') ||
            el.getAttribute('title') ||
            'Add to Pinboard';

          // Bootstrap 4 (jQuery) path.
          if (hasJqueryTooltip) {
            const $el = window.jQuery(el);
            // Avoid re-initializing if tooltip instance already exists.
            const alreadyInit =
              $el.data('bs.tooltip') || $el.data('bs.Tooltip');

            $el
              .attr('title', '')
              .attr('data-original-title', tooltipText);

            if (!alreadyInit) {
              $el.tooltip();
            }
            return;
          }

          // Native browser fallback (at least shows on hover).
          el.setAttribute('title', tooltipText);
        });
      },
      async addToPinboard(loadedList) {
        const { project_id, title, description, image_thumb, slug } = loadedList;
        if (!project_id) return;
        if (this.pinboardLoadingId === project_id) return;

        this.pinboardLoadingId = project_id;
        const itemData = {
          model_id: parseInt(project_id),
          model_type: 'project',
          title,
          description,
          image: image_thumb || '/img/pinboard/pinboard img 1.png',
          product_url: `/projects/${slug}`,
        };
        try {
          await pinboardStore.dispatch('addToPinboard', itemData);
        } finally {
          setTimeout(() => {
            this.pinboardLoadingId = null;
          }, 200);
        }
      },
    },
    template: /* html */`
      <div class="project-list-component">

      <div class="row project-list-row th-projects-list" v-if="storeLoadedLists.length > 0">

					<div class="col-12 col-sm-6 col-md-4 project-item project-list-gap project-list-item d-flex flex-column gap-17"
            v-for="(loadedList, index) in storeLoadedLists" :key="'project-' + loadedList.id + '-' + index" 
            style="animation: slideDownBlogApp 0.5s ease;">
						<div class="list-img position-relative">
							<a :href="loadedList.edit_link" class="hover position-absolute top-left-10" style="color: #fff; font-size: 1.5rem;" v-show="loadedList.is_admin">
								<i class="fa-solid fa-pen-circle"></i>
							</a>
							<div class="img-container">
								<a :href="link(loadedList.slug)" class="hover">
									<img :src="loadedList.image_thumb" :alt="loadedList.title"  class="w-100" loading="lazy"/>
								</a>
							</div>
							<div
                  data-toggle="tooltip"
                  data-bs-toggle="tooltip"
                  data-placement="top"
                  title="Add to Pinboard"
                  role="button"
                  tabindex="0"
                  data-bs-original-title="Add to Pinboard"
                  data-original-title="Add to Pinboard"
                  @click="addToPinboard(loadedList)"
                  :data-id="loadedList.project_id" 
                  data-model="project" 
                  :data-title="loadedList.title" 
                  :data-description="loadedList.description" 
                  :data-image="loadedList.image_thumb"       
                  class="th-add-to-pinboard th-pinboard-tooltip position-absolute top-right-30"
                  :class="{ 'disabled': pinboardLoadingId === loadedList.project_id }"
                  :style="{ pointerEvents: pinboardLoadingId === loadedList.project_id ? 'none' : 'auto' }"
                  >
								<div
                  v-if="pinboardLoadingId === loadedList.project_id"
                  class="spinner-border spinner-border-sm text-primary"
                  role="status"
                >
                  <span class="visually-hidden">Loading...</span>
                </div>
                <i v-else class="fa-solid fa-plus"></i>
							</div>
						</div>

						<div class="card-footer th-card-footer d-flex flex-column justify-content-between gap-13"
							style="flex: 1">
							<div>
								<div class="th-label font-weight-600" style="display: none;">{{ loadedList.label }}</div>
								<a :href="link(loadedList.slug)" class="hover">
									<h3 class="pt-2 th-project-title th-title th-title-mb-15">{{ loadedList.title }}</h3>
                  <div class="font-weight-400">{{ loadedList.preview_text }}</div>
								</a>
							</div>
							<div>
								<a :href="link(loadedList.slug)" class="hover">
									<div class="link th-read-more-btn">
										<div class="th-link-text">View Project</div>
									</div>
                    <!-- <div class="th-link-icon-btn">
											<i class="fa-regular fa-arrow-up degree-60"></i>
										</div>
                    -->
								</a>
							</div>
						</div>

					</div>
				</div>

        <div v-if="loading" class="text-center p-4">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div v-if="error" class="alert alert-danger">
            <strong>Error:</strong> {{ error }}
          </div>
          
      </div>
    `
};

export default ProjectList;
