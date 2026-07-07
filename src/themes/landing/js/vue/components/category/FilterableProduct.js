export default {
    name: 'FilterableProduct',
  
    emits: ['onchange-input', 'onapply-filters', 'onreset-filters'],
  
    props: {
      productCount: { type: Number, required: true, default: 0 },
      materials: { type: Array, default: () => [] },
      features: { type: Array, default: () => [] },
      certifications: { type: Array, default: () => [] },
      filterLoading: { type: Boolean, default: false },
      resetFilter: { type: Boolean, default: false },
      selectedFilters: {
        type: Object,
        default: () => ({
          material_id: null,
          material_name: null,
          feature_id: null,
          feature_name: null,
          weight_id: null,
          certificate_id: null,
          certificate_name: null
        })
      }
    },
  
    data() {
      return {
        autocomplete: {
          materials: { query: '', show: false, resource: 'finishes' },
          features: { query: '', show: false, resource: 'variants' },
          certifications: { query: '', show: false, resource: 'documents' }
        },
  
        selectedMaterial: null,
        selectedFeature: null,
        selectedCertification: null,
        selectedWeight: null
      };
    },
  
    mounted() {
      // Initialize input values from selectedFilters
      if (this.selectedFilters.material_id) {
        this.autocomplete.materials.query = this.selectedFilters.material_name;
        this.selectedMaterial = {
          id: this.selectedFilters.material_id,
          name: this.selectedFilters.material_name
        };
      }
  
      if (this.selectedFilters.feature_id) {
        this.autocomplete.features.query = this.selectedFilters.feature_name;
        this.selectedFeature = {
          id: this.selectedFilters.feature_id,
          name: this.selectedFilters.feature_name
        };
      }
  
      if (this.selectedFilters.certificate_id) {
        this.autocomplete.certifications.query = this.selectedFilters.certificate_name;
        this.selectedCertification = {
          id: this.selectedFilters.certificate_id,
          name: this.selectedFilters.certificate_name
        };
      }

       // Weight
      if (this.selectedFilters.weight_id) {
        this.selectedWeight = this.selectedFilters.weight_id;
      }
    },
  
    methods: {
      /* ---------------- AUTOCOMPLETE ---------------- */
      handleAutocomplete(event, type) {
        const value = event.target.value;
        this.autocomplete[type].query = value;
        this.autocomplete[type].show = true;

        // If user clears input, reset selected object
        if (value === '') {
          if (type === 'materials') this.selectedMaterial = null;
          if (type === 'features') this.selectedFeature = null;
          if (type === 'certifications') this.selectedCertification = null;
        }
  
        // emit to parent API call
        this.$emit('onchange-input', {
          search_query: value,
          resource_type: this.autocomplete[type].resource
        });
      },
  
      selectItem(type, item) {
        this.autocomplete[type].query = item.name;
        this.autocomplete[type].show = false;
  
        if (type === 'materials') this.selectedMaterial = item;
        if (type === 'features') this.selectedFeature = item;
        if (type === 'certifications') this.selectedCertification = item;
      },
  
      hideDropdown(type) {
        setTimeout(() => {
          this.autocomplete[type].show = false;
        }, 150);
      },
  
      /* ---------------- WEIGHT ---------------- */
      getWeights() {
        return [
          { id: '', name: 'Select Weight' },
          { id: '1-10', name: '1-10 kg' },
          { id: '11-20', name: '11-20 kg' },
          { id: '21-30', name: '21-30 kg' },
          { id: '31-40', name: '31-40 kg' },
          { id: '41-50', name: '41-50 kg' },
          { id: '51-60', name: '51-60 kg' },
          { id: '61-70', name: '61-70 kg' },
          { id: '71-80', name: '71-80 kg' },
          { id: '81-90', name: '81-90 kg' },
          { id: '91-100', name: '91-100 kg' },
          { id: '101-110', name: '101-110 kg' },
          { id: '111-120', name: '111-120 kg' },
          { id: '121-130', name: '121-130 kg' },
          { id: '131-140', name: '131-140 kg' },
          { id: '141-150', name: '141-150 kg' },
          { id: '151-160', name: '151-160 kg' },
          { id: '161-170', name: '161-170 kg' },
          { id: '171-180', name: '171-180 kg' },
          { id: '181-190', name: '181-190 kg' },
          { id: '191-200', name: '191-200 kg' }
        ];
      },
  
      handleWeightChange(e) {
        this.selectedWeight = e.target.value;
      },
  
      /* ---------------- APPLY ---------------- */
      handleApplyFilters() {
        this.$emit('onapply-filters', {
          per_page: 40,
          current_page: 1,
          offset: 0,
          material_id: this.selectedMaterial?.id,
          material_name: this.selectedMaterial?.name,
          feature_id: this.selectedFeature?.id,
          feature_name: this.selectedFeature?.name,
          weight_id: this.selectedWeight,
          certificate_id: this.selectedCertification?.id,
          certificate_name: this.selectedCertification?.name
        });
      },
      /* ---------------- RESET ---------------- */
      handleResetFilters() {
        this.selectedMaterial = null;
        this.selectedFeature = null;
        this.selectedCertification = null;
        this.selectedWeight = ''; // empty string
        this.autocomplete.materials.query = '';
        this.autocomplete.features.query = '';
        this.autocomplete.certifications.query = '';
        this.$emit('onreset-filters', {
          per_page: 40,
          current_page: 1,
          offset: 0,
          material_id: null,
          material_name: null,
          feature_id: null,
          feature_name: null,
          weight_id: '', // empty string
          certificate_id: null,
          certificate_name: null,
          reset: true
        });
      }
    },
    template: /* html */ `
    <div class="th-filters-wrapper mb-30">
      <div class="th-filters-form">
        <div class="row">
  
          <!-- MATERIAL -->
          <div class="col-6 col-md-3">
            <div class="autocomplete">
              <input
                type="text"
                class="form-control form-control-lg bg-white"
                placeholder="Autocomplete Material"
                autocomplete="off"
                v-model="autocomplete.materials.query"
                @input="handleAutocomplete($event, 'materials')"
                @focus="autocomplete.materials.show = true"
                @blur="hideDropdown('materials')"
              />
  
              <ul class="dropdown-menu w-100 position-absolute"
                :class="{ show: autocomplete.materials.show }" v-if="materials.length > 0">
                <li
                  class="dropdown-item"
                  v-for="item in materials"
                  :key="item.id"
                  @mousedown.prevent="selectItem('materials', item)">
                  {{ item.name }}
                </li>
              </ul>
            </div>
          </div>
  
          <!-- FEATURE -->
          <div class="col-6 col-md-3">
            <div class="autocomplete">
              <input
                type="text"
                class="form-control form-control-lg bg-white"
                placeholder="Autocomplete Features"
                autocomplete="off"
                v-model="autocomplete.features.query"
                @input="handleAutocomplete($event, 'features')"
                @focus="autocomplete.features.show = true"
                @blur="hideDropdown('features')"
              />
  
              <ul class="dropdown-menu w-100 position-absolute"
                :class="{ show: autocomplete.features.show }" v-if="features.length > 0">
                <li
                  class="dropdown-item"
                  v-for="item in features"
                  :key="item.id"
                  @mousedown.prevent="selectItem('features', item)">
                  {{ item.name }}
                </li>
              </ul>
            </div>
          </div>
  
          <!-- WEIGHT -->
          <div class="col-6 col-md-2">
            <select
            class="form-select form-select-lg bg-white"
            v-model="selectedWeight"
            placeholder="Select Weight"
          >
            <option v-for="w in getWeights()" :key="w.id" :value="w.id">
              {{ w.name }}
            </option>
          </select>
        </div>
          <!-- CERTIFICATION -->
          <div class="col-6 col-md-2">
            <div class="autocomplete">
              <input
                type="text"
                class="form-control form-control-lg bg-white"
                placeholder="Autocomplete Certifications"
                autocomplete="off"
                v-model="autocomplete.certifications.query"
                @input="handleAutocomplete($event, 'certifications')"
                @focus="autocomplete.certifications.show = true"
                @blur="hideDropdown('certifications')"
              />
  
              <ul class="dropdown-menu w-100 position-absolute"
                :class="{ show: autocomplete.certifications.show }" v-if="certifications.length > 0">
                <li
                  class="dropdown-item"
                  v-for="item in certifications"
                  :key="item.id"
                  @mousedown.prevent="selectItem('certifications', item)">
                  {{ item.name }}
                </li>
              </ul>
            </div>
          </div>
  
          <!-- BUTTON -->
          <div class="col-6 col-md-1">
            <button
              class="th-custom-filter-btn w-100 py-2"
              @click="handleApplyFilters">
              <i class="fa fa-filter"></i> 
                <span v-if="!filterLoading">Filter</span> 
                  <span v-if="filterLoading">
                    <div class="spinner-border" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                </span>
            </button>
          </div>

           <!-- CLEAR FILTER BUTTON -->
           <div class="col-6 col-md-1">
           <button
             class="th-secondary-btn w-100 py-2"
             @click="handleResetFilters">
             <i class="fa fa-eraser"></i> 
             <span v-if="!resetFilter">Reset</span> 
             <span v-if="resetFilter">
               <div class="spinner-border" role="status">
                 <span class="visually-hidden">Resetting...</span>
               </div>
             </span>
           </button>
         </div>
  
        </div>
  
        <div class="row" v-if="false">
          <div class="col th-filters-search-result">
            <p>{{ productCount }} results</p>
          </div>
        </div>
      </div>
    </div>
    `
  };
  