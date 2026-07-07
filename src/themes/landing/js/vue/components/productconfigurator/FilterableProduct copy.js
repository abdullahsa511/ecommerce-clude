export default {
    name: 'FilterableProduct',
    components: {
    },
    emits: [
        'onchange-input', 
        'onapply-filters',
    ],
    props: {
        productCount: {
            type: Number,
            required: true,
            default: 0
        },
        materials: {
            type: Array,
            required: false,
            default: []
        },
        features: {
            type: Array,
            required: false,
            default: []
        },
        certifications: {
            type: Array,
            required: false,
            default: []
        }
    },
    watch: {
        certifications: {
            handler(newVal) {
                console.log('certifications watcher', newVal);
            },
            deep: true
        }
    },
    methods: {
        handleInputChange(event, resource_type) {
            console.log('handleInputChange event', event, resource_type);
            this.$emit('onchange-input', {search_query: event.target.value, resource_type: resource_type});
        },
        getWeights() {
            const weights = [
                {id: '1-10', name: '1-10 kg'},
                {id: '11-20', name: '11-20 kg'},
                {id: '21-30', name: '21-30 kg'},
                {id: '31-40', name: '31-40 kg'},
                {id: '41-50', name: '41-50 kg'},
                {id: '51-60', name: '51-60 kg'},
                {id: '61-70', name: '61-70 kg'},
                {id: '71-80', name: '71-80 kg'},
                {id: '81-90', name: '81-90 kg'},
                {id: '91-100', name: '91-100 kg'}];
            return weights;
        },
        handleApplyFilters() {
            this.$emit('onapply-filters', this.selectedMaterial, this.selectedFeature, this.selectedWeight, this.selectedCertification);
        }
    },
    template: /* html */ `
    <div class="th-filters-wrapper">
        <div class="th-filters-form">
            <div class="row">
                <div class="col-6 col-sm-6 col-md-3 col-lg-3">
                   
                        <!-- <input list="materials" id="materials-choice" name="materials-choice" class="form-control form-control-lg mb-3 bg-white" placeholder="Autocomplete Material" @input="handleInputChange($event, 'finishes')" /> -->

                        <!-- <datalist id="materials">
                          <option v-for="material in materials" :key="material.id" :value="material.name"></option>
                        </datalist> -->
               
                </div>

                <div class="col-6 col-sm-6 col-md-3 col-lg-3">
                    <div class="autocomplete">
                        <input type="text" class="form-control form-control-lg mb-3 bg-white" placeholder="Autocomplete Features" @input="handleInputChange($event, 'variants')" />
                        <ul class="dropdown-menu show d-none" id="dropdown-menu" @click="handleAutocompleteClick">
                        </ul>
                    </div>
                </div>

                <div class="col-6 col-sm-6 col-md-2 col-lg-2">
                    <select class="form-select form-select-lg mb-3 bg-white" @change="handleWeightChange($event)">
                        <option selected>select Weight</option>
                        <option v-for="weight in getWeights()" :key="weight.id" :value="weight.id">{{ weight.name }}</option>
                    </select>
                </div>

                <div class="col-6 col-sm-6 col-md-3 col-lg-3">
                    <input list="certifications" id="certifications-choice" name="certifications-choice" class="form-control form-control-lg mb-3 bg-white" placeholder="Autocomplete Certifications" @input="handleInputChange($event, 'documents')" />

                    <datalist id="certifications">
                       <option v-for="certification in materials" :key="certification.id" :value="certification.name"></option>
                    </datalist>
                   
                </div>
                <div class="col-6 col-sm-6 col-md-1 col-lg-1">
                    <button class="th-btn-primary text-capitalize w-100 py-2" @click="handleApplyFilters">Filter</button>
                </div>
            </div>
            <div class="row">
                <div class="col d-flex th-filters-tags">
                <div class="th-tag">
                    <span class="me-2">Tag Name Here</span>
                    <span class="btn-close" aria-hidden="true"></span>
                </div>

                <div class="th-tag">
                    <span class="me-2">Tag Name Here</span>
                    <span class="btn-close" aria-hidden="true"></span>
                </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col th-filters-search-result">
                <p>{{ productCount }} results</p>
            </div>
        </div>
    </div>
    `

}