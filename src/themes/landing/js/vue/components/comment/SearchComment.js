export default {
    name: 'SearchComment',
    emits: ['search'],
    props: {
        loading: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            searchQuery: '',
        };
    },
    methods: {
        submitSearch() {
            const query = this.searchQuery != null ? String(this.searchQuery).trim() : '';
            this.$emit('search', query);
        },
    },
    template: /* html */ `
    <div class="mt-20 mb-20">
        <div class="th-comment-search-box">
            <form action="" class="th-form th-custom-quote-form" @submit.prevent="submitSearch">
                <div class="form-group th-input-icon mb-0">
                    <i class="fa-solid fa-search"></i>
                    <input name="search" id="search" class="form-control"
                        v-model.trim="searchQuery"
                        autocomplete="off"
                        placeholder="Search comments">
                </div>
                <button type="submit" class="th-btn text-capitalize bg-gray text-black" :disabled="loading">
                   <i class="fa-solid fa-circle-notch" v-if="loading"></i> <span v-else>Search</span>
                </button>
            </form>
        </div>

    </div>
    `
}