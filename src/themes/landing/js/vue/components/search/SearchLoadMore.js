/**
 * Same UX as themes/landing ResourceLoadMore — explicit load-more control for search results.
 */
export default {
  name: 'SearchLoadMore',

  props: {
    id: { type: String, required: false },
  },
  emits: ['load-more'],

  methods: {
    loadMoreButton() {
      this.$emit('load-more');
    },
  },
  template: /* html */ `
    <div class="row">
        <div class="col-12">
        <div class="d-flex justify-content-center">
            <button
            :id="id"
            @click.prevent="loadMoreButton"
            class="th-btn-gray text-capitalize mt-50"
            >
            <span class="mr-5">Load More</span>
            </button>
        </div>
        </div>
    </div>
    `,
};
