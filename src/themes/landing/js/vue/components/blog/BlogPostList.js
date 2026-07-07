import pinboardStore from '../../store/pinboardStore.js';
const BlogPostList = {
    name: 'BlogPostList',
    props: {
      
    },
    data() {
        return {
            localError: null,
            pinboardLoadingId: null
        }
    },
    computed: {
        totalPostsCount() {
            return this.$store ? this.$store.state.total : 0;
        },
        storeLoadedPosts() {
            return this.$store ? this.$store.state.loadedPosts : [];
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
            return loaded < this.totalPostsCount;
        },
    },
    updated() {
      // Re-init tooltips after reactive DOM updates.
      if (this._pinboardTooltipRaf) return;
      this._pinboardTooltipRaf = window.requestAnimationFrame(() => {
        this._pinboardTooltipRaf = null;
        this.initPinboardTooltips();
      });
    },
    mounted() {
        // Component just displays posts from store
        // No need to load on mount - posts are loaded by button click
        this.$nextTick(() => {
          this.initPinboardTooltips();
        });
    },
    methods: {
      link(slug) {
        return `/blog/${slug}`;
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

          if (hasJqueryTooltip) {
            const $el = window.jQuery(el);
            const alreadyInit = $el.data('bs.tooltip') || $el.data('bs.Tooltip');

            $el
              .attr('title', '')
              .attr('data-original-title', tooltipText);

            if (!alreadyInit) {
              $el.tooltip();
            }
            return;
          }

          // Native fallback tooltip
          el.setAttribute('title', tooltipText);
        });
      },
      async addToPinboard(loadedPost) {
        const { post_id, name, excerpt, feature_image_thumb, slug } = loadedPost;
        if (!post_id) return;
        if (this.pinboardLoadingId === post_id) return;

        this.pinboardLoadingId = post_id;
        const itemData = {
          model_id: parseInt(post_id),
          model_type: 'post',
          title: name,
          description: excerpt,
          image: feature_image_thumb || '/img/pinboard/pinboard img 1.png',
          product_url: `/blog/${slug}`,
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
      <div class="blog-post-list-component">
          <div class="row project-list-row" v-if="storeLoadedPosts.length > 0">
            <div class="col-12 col-sm-6 col-md-4 mb-80 project-list-gap project-list-item d-flex flex-column" 
                 v-for="(loadedPost, index) in storeLoadedPosts" 
                 :key="'post-' + loadedPost.id + '-' + index"
                 style="animation: slideDownBlogApp 0.5s ease;">
              <div class="list-img position-relative">
                <a :href="loadedPost.link"  class="hover position-absolute top-left-10" style="color: #fff; font-size: 1.5rem;" v-show="loadedPost.is_admin">
                  <i class="fa-solid fa-pen-circle"></i>
                </a>
                <div class="img-container">
                  <a :href="link(loadedPost.slug)" class="hover" >
                    <img :src="loadedPost.feature_image_thumb" :alt="loadedPost.name" class="w-100" />
                  </a>
                </div>
                <div 
                    @click="addToPinboard(loadedPost)"
                    data-toggle="tooltip"
                    data-bs-toggle="tooltip"
                    data-placement="top"
                    role="button"
                    tabindex="0"
                    data-bs-original-title="Add to Pinboard"
                    data-original-title="Add to Pinboard"
                    :data-id="loadedPost.post_id" 
                    data-model="post" 
                    :data-title="loadedPost.title" 
                    :data-description="loadedPost.excerpt" 
                    :data-image="loadedPost.feature_image_thumb"       
                    class="th-add-to-pinboard th-pinboard-tooltip position-absolute top-right-30"
                    :class="{ 'disabled': pinboardLoadingId === loadedPost.post_id }"
                    :style="{ pointerEvents: pinboardLoadingId === loadedPost.post_id ? 'none' : 'auto' }"
                    >
                  <div
                    v-if="pinboardLoadingId === loadedPost.post_id"
                    class="spinner-border spinner-border-sm text-primary"
                    role="status"
                  >
                    <span class="visually-hidden">Loading...</span>
                  </div>
                  <i v-else class="fa-solid fa-plus"></i>
                </div>
              </div>
              <div class="card-footer d-flex flex-column justify-content-between" style="flex: 1">
                <div>
                  <a :href="link(loadedPost.slug)" class="hover" >
                    <h6 class="th-blog-title" >{{ loadedPost.name }}</h6>
                    <div class="th-blog-excerpt">{{ loadedPost.excerpt }}</div>
                  </a>
                </div>
                <div>   
                  <a :href="link(loadedPost.slug)" class="hover" >
                    <div class="link th-read-more-btn th-mt-16">
                      <div class="th-link-text">Read More</div>
                    </div>
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

export default BlogPostList;
