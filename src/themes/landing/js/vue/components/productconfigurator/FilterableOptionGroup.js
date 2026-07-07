import ThumbnailOptions from './ThumbnailOptions.js';
import FilterOptions from './FilterOptions.js';
import SquareBoxOptions from './SquareBoxOptions.js';
export default {
    name: 'FilterableOptionGroup',
    components: {
        ThumbnailOptions,
        FilterOptions,
        SquareBoxOptions,
    },
    emits: [
        'option-selected', 
        'show-filter', 
        'clean-filter', 
        'apply-filter',
        'box-option-selected'
    ],
    props: {
        group: {
            type: Object,
            required: true
        },
        filteredData:{
            type: Array|Object,
            required: true,
        },
        type: {
            type: String,
            required: true,
        },
        defaultOptionImage: {
            type: String,
            default: () => 'https://dummyimage.com/120x120/444/fff&text=F'
        },
        filterOptions:{
            type: Array,
            default: () => ['Modern', 'Classic', 'Office', 'Home', 'Luxury']
        },
        filterFamilies:{
            type: Array,
            default: () => ['Chair', 'Table', 'Sofa', 'Bed', 'Desk']
        }
    },
    data() {
        return {
            filter: null,
            localFilteredData: []
        }
    },
    computed: {
        data() {
            let data = this.type ? this.filteredData[this.type] : [];
            
            if(this.filter){
                const search = this.filter.trim().toLowerCase();
                data = data.filter(item => item.name.toLowerCase().includes(search));
            }

            return data??[];
        }
    },
    watch: {
        filteredData: {
            immediate: true,
            handler(newVal) {
                this.localFilteredData = newVal || [];
            }
        }
    },
    methods: {
        handleFilterChanged(event) {
            this.filter = event.target.value;       
        }
    },
    template: /* html */ `
    <div class="accordion-body th-accordion-body">
                                            
        <!-- Thumbnail Option Group -->
        <thumbnail-options
            :group="group"
            :default-option-image="defaultOptionImage"
            @option-selected="$emit('option-selected', $event)"
        ></thumbnail-options>

        <!-- Filterable Option Group -->
        <!-- if option name is Fabric A, Fabric B, Fabric C, etc. then show the filterable option group -->
        <filter-options
            v-if="type"
            :group="group"
            :filter-options="filterOptions"
            :filter-families="filterFamilies"
            @filter-changed="handleFilterChanged"
            @show-filter="$emit('show-filter', $event)"
            @clean-filter="$emit('clean-filter', $event)"
            @apply-filter="$emit('apply-filter', $event)"
        ></filter-options>
        <!-- Square Box Option Group -->
        <square-box-options
            :group="group"
            :data="data"
            @box-option-selected="$emit('box-option-selected', $event)"
        ></square-box-options>

    </div>
    `

}