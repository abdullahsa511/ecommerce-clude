export default {
    name: 'ResourceSidebar',
  
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
        <div class="col-lg-4"></div>

        <div class="col-lg-8 col-12">
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
  


