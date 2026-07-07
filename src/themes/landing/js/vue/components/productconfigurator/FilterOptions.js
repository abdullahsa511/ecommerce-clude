export default {
    name: 'FilterOptions',
    props: {
        group: {
            type: Object,
            required: true
        },
        filter: {
            type: String,
            default: null
        },
        filterOptions:{
            type: Array,
            required: true
        },
        filterFamilies:{
            type: Array,
            required: true
        }
    },
    emits: ['show-filter', 'clean-filter', 'apply-filter', 'filter-changed'],

    data(){
        return {
            isVisible: false,

            selectedTags: [],
            selectedFamilies: [],

            showTagsDropdown: false,
            showFamilyDropdown: false,
        };
    },

    computed: {
        // tagLabel() {
        //     return this.selectedTags.length
        //         ? `Tags (${this.selectedTags.length})`
        //         : 'Tags (0)';
        // },
        // familyLabel() {
        //     return this.selectedFamilies.length
        //         ? `Family (${this.selectedFamilies.length})`
        //         : 'Family (0)';
        // }
    },


    methods: {
        handleShowFilter() {
            this.isVisible = !this.isVisible;
            this.$emit('show-filter', this.group);
        },
        handleCleanFilter(event) {
            if (event && typeof event.preventDefault === 'function') {
                event.preventDefault();
            }
            this.$emit('clean-filter', this.group);
        },
        handleApplyFilter() {
            this.$emit('apply-filter', this.group);
        },
        handleFilterChanged(filter) {
            this.$emit('filter-changed', filter);
        }
    },
    template: /* html */ `
    <div class="th-filter-section">
        <div class="th-filter-separator"></div>
            <div class="th-filter-controls bg-white shadow-sm rounded-1">
                <div class="input-group align-items-center">
                    
                    <!-- Icon -->
                    <span class="input-group-text border-0 bg-transparent">
                        <i class="fa fa-search text-muted"></i>
                    </span>
            
                    <!-- Input -->
                    <input 
                        type="text"
                        class="form-control border-0 shadow-none bg-transparent"
                        placeholder="Search or filter..."
                        v-model="filter"
                        @input="handleFilterChanged($event)"
                    >
            
                    <!-- Clear Button -->
                    <button 
                        v-if="filter"
                        class="btn border-0 bg-transparent"
                        @click="filter = ''; handleFilterChanged($event)"
                    >
                        <i class="fa fa-times text-danger"></i>
                    </button>
            
                </div>
        </div>
    </div>
    `
}