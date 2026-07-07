export default {
    name: 'FilterOptions',
    props: {
        group: {
            type: Object,
            required: true
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
    emits: ['show-filter', 'clean-filter', 'apply-filter'],

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
        tagLabel() {
            return this.selectedTags.length
                ? `Tags (${this.selectedTags.length})`
                : 'Tags (0)';
        },
        familyLabel() {
            return this.selectedFamilies.length
                ? `Family (${this.selectedFamilies.length})`
                : 'Family (0)';
        }
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
        }
    },
    template: /* html */ `
    <div class="th-filter-section">
        <div class="th-filter-separator"></div>
        <div class="th-filter-controls">
            <button class="th-filter-show-btn" @click="handleShowFilter">
            <i :class="isVisible ? 'fa-solid fa-eye-slash' : 'fa-solid fa-filter'"></i>
            {{ isVisible ? 'Hide Filter' : 'Show Filter' }}
            </button>
            <a href="#" class="th-filter-clean-link" @click="handleCleanFilter">Clean Filter</a>
        </div>
        <transition name="fade" >
            <div v-if="isVisible">
        <div class="th-filter-separator"></div>
        <div class="th-filter-dropdowns">
            <button class="th-filter-dropdown" @click="showTagsDropdown = !showTagsDropdown">
            {{ tagLabel }}
            <i class="fa-solid fa-chevron-down"></i>
            </button>

            <div class="dropdown-menu show-tags-dropdown" v-if="showTagsDropdown">
                <label v-for="tag in tags" :key="tag">
                    <input
                        type="checkbox"
                        :value="tag"
                        :checked="selectedTags.includes(tag)"
                        @change="toggleTag(tag)"
                    />
                    {{ tag }}
                </label>
            </div>

            <div class="selected-items">
                <span v-for="tag in selectedTags" :key="tag">{{ tag }}</span>
            </div>




            <button class="th-filter-dropdown">
                Fimily(0)
                <i class="fa-solid fa-chevron-down"></i>
            </button>
        </div>
        <div class="th-filter-separator"></div>
        </div>
        </transition>
    </div>
    `
}