import FilterableOptionGroup from './FilterableOptionGroup.js';
import RectangularButtonOptionGroup from './RectangularButtonOptionGroup.js';
import ThumbnailOptionGroup from './ThumbnailOptionGroup.js';
import ProductActions from './ProductActions.js';
export default {
    name: 'ProductConfigurator',
    props: {},
    components: {
        FilterableOptionGroup,
        RectangularButtonOptionGroup,
        ThumbnailOptionGroup,
        ProductActions
    },

    data() {
        return {
            selectedVariant: null,
            selectedOptions: {}, // { groupId: option }
            selectedItems: {},
            selectedItem: {},
            // selectedAccessories: [],
            // defaultOptionImage: 'https://dummyimage.com/120x120/444/fff&text=K',
            defaultOptionImage: '/media/Logo/krost-logo.png',
            activeVariantItemIndex: 0,
            selectedVariantId: 0,
            expandedGroupIndex: null,
            // optionByFinishesData: {},
            productQuantity:{
                quantity: 1,
                minQty: 1,
                maxQty: 100,
            },
            textileType: '',

        };
    },

    computed: {
        cartObject() {
            return this.$store ? this.$store.getters.cartObject : {};
        },
        cartStatus() {
            return this.$store ? this.$store.getters.cartStatus : false;
        },
        product() {
            return this.$store ? this.$store.getters.currentProduct : {};
        },
        variants() {
            return this.$store ? this.$store.getters.currentVariants : [];
        },
        activeVariant() {
            return this.$store ? this.$store.getters.activeVariant : {};
        },
        modelData() {
            return this.$store ? this.$store.getters.modelData : [];
        },
        activeGroups() {
            return this.$store ? this.$store.getters.activeGroups : [];
        },
        activeItems() {
            return this.$store ? this.$store.getters.activeItems : [];
        },
        activeItem(){
            const activeItem = this.$store ? this.$store.getters.activeItem : {};
            // console.log('activeItem', activeItem);
            // console.log('activeItem dimensions_image', activeItem?.dimensions_image);
            console.log('activeItem code ', activeItem?.item_code ?? '');
        
            const width = activeItem?.display_width ?? '';
            const height = activeItem?.display_height ?? '';
            const depth = activeItem?.display_depth ?? '';

            // console.log('width : ' + width);
            // console.log('height : ' + height);
            // console.log('depth : ' + depth);

            const dimensionsTabButton = document.getElementById('dimensions-tab');
            const specsTabButton = document.getElementById('specs-tab');
            const dimensionsTabSection = document.getElementById('dimensions');
            const dimensionsMobileTabSection = document.getElementById('dimensionsMobile');
            const specsTabSection = document.getElementById('specs');
            if(width || height || depth){
                // remove class d-none from dimensions section
                if(dimensionsTabButton) dimensionsTabButton.classList.remove('d-none');
                if(dimensionsTabSection) dimensionsTabSection.classList.remove('d-none');
                if(dimensionsMobileTabSection) dimensionsMobileTabSection.classList.add('opacity-1');
                if(dimensionsMobileTabSection) dimensionsMobileTabSection.classList.remove('d-none');
            }else{
                // add class d-none to dimensions section
                if(dimensionsTabButton) dimensionsTabButton.classList.add('d-none');
                if(dimensionsTabButton) dimensionsTabButton.classList.remove('active');
                if(dimensionsTabSection) dimensionsTabSection.classList.add('d-none');
                if(dimensionsMobileTabSection) dimensionsMobileTabSection.classList.remove('opacity-1');
                if(dimensionsMobileTabSection) dimensionsMobileTabSection.classList.add('d-none');
                if(specsTabButton) specsTabButton.classList.add('active');
                if(specsTabSection) {
                    specsTabSection.classList.add('show');
                    specsTabSection.classList.add('active');
                }
                if(dimensionsTabSection) {
                    dimensionsTabSection.classList.remove('show');
                    dimensionsTabSection.classList.remove('active');
                    dimensionsMobileTabSection.classList.add('d-none');
                }
            }
        
            const widthEl = document.getElementById('width-dimension');
            const heightEl = document.getElementById('height-dimension');
            const depthEl = document.getElementById('depth-dimension');

            const widthElMobile = document.getElementById('width-dimension-mobile');
            const heightElMobile = document.getElementById('height-dimension-mobile');
            const depthElMobile = document.getElementById('depth-dimension-mobile');
            // show image in dimensions section
            const dimensionsImageEl = document.getElementById('dimensions-image');
            if (dimensionsImageEl) dimensionsImageEl.src = activeItem?.dimensions_image ?? '/img/product-detail/tab image.png';
        
            if (widthEl) widthEl.innerText = width;
            if (heightEl) heightEl.innerText = height;
            if (depthEl) depthEl.innerText = depth;
            if (widthEl)  widthElMobile.innerText = width;
            if (heightEl) heightElMobile.innerText = height;
            if (depthEl)  depthElMobile.innerText = depth;

            return activeItem;
        },
        activeOptions() {
            return this.$store ? this.$store.getters.activeOptions : [];
        },
        loading() {
            return this.$store ? this.$store.state.loading : false;
        },
        error() {
            return this.$store ? this.$store.state.error : null;
        },
        finishesDataByGrade() {
            return this.$store ? this.$store.getters.finishesDataByGrade : [];
        },
        accessories() {
            return this.$store ? this.$store.getters.accessories : [];
        },
        textiles() {
            return this.$store ? this.$store.getters.textiles : [];
        },
    },

    mounted() {
        if (!this.variants.length) {
            this.$store.dispatch("loadProductConfigurator");
        }
        this.$nextTick(() => this.initVariantSwiper());
    },
    methods: {
        initVariantSwiper() {
            if (this.loading || this.error || !this.variants.length) return;
            const el = this.$refs.variantSwiper;
            if (!el || !(el instanceof HTMLElement)) return;
            try {
                if (window.variantSwiper) {
                    window.variantSwiper.destroy(true, true);
                }
                window.variantSwiper = new Swiper(el, {
                    grabCursor: true,
                    slidesPerView: 3,
                    spaceBetween: 12,
                    breakpoints: {
                        1200: { slidesPerView: 4, spaceBetween: 12 },
                        768: { slidesPerView: 2, spaceBetween: 12 },
                        0: { slidesPerView: 2, spaceBetween: 8 },
                    },
                    navigation: {
                        nextEl: el.querySelector('.swiper-button-next'),
                        prevEl: el.querySelector('.swiper-button-prev'),
                    },
                });
            } catch (err) {
                console.error('Swiper init failed:', err);
            }
        },
        toggleGroup(index) {
            this.expandedGroupIndex = this.expandedGroupIndex === index ? null : index;
        },
        selectVariant(variant) {
            this.selectedVariantId = variant.product_variant_id;
            this.$store.dispatch('selectVariant', variant);
        },
        selectOption($event) {
            // this.$store.dispatch('selectOption', $event);   
            // if selected option is Fabric A, Farbic B, Fabric C then get the tags 
            const optionName = $event.option_name.toLowerCase();        
            if (['fabric a', 'fabric b', 'fabric c'].includes(optionName)) {
                this.textileType = $event.option_name;
                if(!this.textiles[optionName]) {
                    this.$store.dispatch('getTextilesDataByGrade', $event.option_name);
                }
            }else{
                this.textileType = '';
            } 
            this.$store.dispatch('selectOption', $event);
              
        },
        selectItem(item) {
            this.$store.dispatch('selectItem', item);
        },
        handleShowFilter(group) {
            console.log('Show filter requested', group);
        },
        handleCleanFilter(group) {
            console.log('Clean filter requested', group);
        },
        handleApplyFilter(group) {
            console.log('Apply filter requested', group);
        },
        getSelectedOptionName(group) {
            return group.productOptions.find(opt => opt.selected)?.option_name || '';
        },
        isFirstGroup(index) {
            return index === 0;
        },
        isLastGroup(index) {
            return index === this.activeGroups.length - 1;
        },
        getGroupHeadingId(index) {
            return `heading-${index}`;
        },
        getGroupCollapseId(index) {
            return `collapse-${index}`;
        },
        selectBoxOption(item) {
            console.log('selectBoxOption', item);
            this.$store.dispatch('selectBoxOption', item);
        },
        increaseQuantity() {
            if (this.productQuantity.quantity < this.productQuantity.maxQty) {
                this.productQuantity.quantity++;
                // this.getTotalQuantity();
                this.$store.dispatch('getTotalQuantity', this.productQuantity.quantity);
            }
        },
        decreaseQuantity() {
            if (this.productQuantity.quantity > this.productQuantity.minQty) {
                this.productQuantity.quantity--;
                // this.getTotalQuantity();
                this.$store.dispatch('getTotalQuantity', this.productQuantity.quantity);
            }else{
                //decrease quantity button disabled
                this.decreaseQuantityButtonDisabled = true;
            }
        },
        // getTotalQuantity(){
        //     this.$store.dispatch('getTotalQuantity', this.productQuantity.quantity);
        // },
        addToPinboard() {
            // console.log('Add to pinboard requested', this.cartObject);
            this.$store.dispatch('addToPinboard', this.cartObject);
        },
        toggleAccessories(accessories) {
            // dispatch to store
            this.$store.dispatch('toggleAccessories', accessories);
        },
    },

    watch: {
        loading(newVal) {
            if (!newVal) {
                this.$nextTick(() => this.initVariantSwiper());
            }
        },
        cartObject: {
            handler(newVal) {
                console.log('cartObject in watch', newVal);
                // console.log('cartObject changed', newVal);
            },
            deep: true
        }
   
    },

    template: /* html */ `
    <div class="product-configurator-app-component">
        <div v-if="loading" class="th-configurator-loader d-flex flex-column align-items-center justify-content-center py-5">
            <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
            <span class="visually-hidden">Loading...</span>
        </div>
        <div v-else-if="error" class="alert alert-danger text-center" role="alert">
            {{ error }}
        </div>
        <div v-else class="row">
            <div class="col-md-6">
                <div class="threed-section">
                    <!-- <p class="scan-instruction">Scan QR to view in your space</p> -->

                    <div class="img-container" v-if="activeItem">
                        <img 
                            v-if="activeItem.quote_image && activeItem.quote_image !== '0'" 
                            :src="activeItem.quote_image" 
                            :alt="activeItem.description">
                        <img 
                            v-else 
                            src="/media/Logo/krost-logo.png" 
                            alt="Krost">
                    </div>
                    

                    <!-- <div class="qr-code">
                         3D Symbol below the QR code 
                        <div class="threed-container">
                        <img 
                            src="/img/product-detail/3d-logo.png" alt="" 
                            v-if="activeItem.quote_image && activeItem.quote_image !== '0'" >
                        </div>
                    </div> -->
    
        
                    <!-- Thumbnail Selection for 3D View -->
                   <!-- <div class="thumbnail-selection" v-if="activeItems.length">
                        <div
                        class="thumbnail"
                        v-for="(item, index) in activeItems"
                        :key="item.item_code"
                        :class="{ 'selected': activeItem.item_code === item.item_code }"
                        @click="selectItem(item)"
                        >
                            <img :src="item.quote_image" :alt="item.description">
                        </div>
                    </div> -->
                </div>
            </div>
            
    
            <div class="th-product-model col-md-6">
                <div class="model-column">
                    <div class="th-variant-list" >
                        <div class="pb-3">
                       
                            <h6 class="th-title-20 font-weight-700 pl-20 pt-20">Variants</h6>
                        </div>
                        <div class="swiper th-variant-card-list" ref="variantSwiper">
                            <div class="swiper-wrapper">
                                <div 
                                    class="swiper-slide variant-card" 
                                    v-for="variant in variants" 
                                    :key="variant.product_variant_id"
                                    :class="{ 'selected': activeVariant.product_variant_id === variant.product_variant_id }"
                                    @click="selectVariant(variant)"
                                >
                                    <div class="variant-image-wrapper">
                                        <img v-if="variant.image" :src="variant.image" :alt="variant.variant_name"/>
                                        <img v-else src="/media/Logo/krost-logo.png" alt="Krost" style="object-fit: contain;">
                                    </div>
                                    <div class="variant-content">
                                        <div class="variant-label">{{ variant.variant_name }}</div>
                                        <!--<span class="variant-selected-badge" v-if="selectedVariantId === variant.product_variant_id">Selected</span>-->
                                    </div>
                                </div>
                            </div>
                            <div class="swiper-button-prev" aria-label="Previous variants"></div>
                            <div class="swiper-button-next" aria-label="Next variants"></div>
                        </div>
                    </div>
    
    
                    <div class="accordion th-product-accordion" id="th-product-configurator-accordion">
                        <template>
                            <div 
                                class="accordion-item th-accordion-item" 
                                v-for="(group, index) in activeGroups"
                                :key="group.product_option_group_id"
                            >
                                <template >
                                    <h4 class="accordion-header th-accordion-header" :id="getGroupHeadingId(index)">
                                        <button
                                            :class="['accordion-button', 'th-accordion-button', { collapsed: expandedGroupIndex !== index }]"
                                            type="button" data-bs-toggle="collapse"  :data-bs-target="'#' + getGroupCollapseId(index)"
                                            :aria-expanded="expandedGroupIndex === index" :aria-controls="getGroupCollapseId(index)"
                                            @click="toggleGroup(index)"
                                        >
                                            <div class="th-config-label flex items-center">
                                                <span class="font-size-16">
                                                   <!-- <span>{{ index + 1 }} </span> -->
                                                    <span>{{ group.option_group_name }}</span>
                                                </span>
                                                <span class="th-config-value font-size-16">
                                                    {{ getSelectedOptionName(group) }}
                                                </span>
                                            </div>
                                            <span class="th-config-icon">{{ expandedGroupIndex === index ? '-' : '+' }}</span>
                                        </button>
                                    </h4>
                                    <div :id="getGroupCollapseId(index)"  class="accordion-collapse th-accordion-collapse collapse" 
                                        :aria-labelledby="getGroupHeadingId(index)" data-bs-parent="#th-product-configurator-accordion">
                                      <filterable-option-group v-if="group.group_type === 'filterable'"
                                            :group="group"
                                            :filteredData="textiles"
                                            :type="textileType"
                                            @option-selected="selectOption"
                                            @show-filter="handleShowFilter"
                                            @clean-filter="handleCleanFilter" 
                                            @apply-filter="handleApplyFilter"
                                            @box-option-selected="selectBoxOption"
                                        />
                                        <thumbnail-option-group v-else-if="group.group_type === 'thumbnail'"
                                            :group="group"
                                            :default-option-image="defaultOptionImage"
                                            @option-selected="selectOption"
                                        />
                                        <rectangular-button-option-group v-else
                                            :group="group"
                                            @option-selected="selectOption"
                                        />
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <span class="text-primary font-weight-700" v-if="activeItem?.item_code">SKU: {{ activeItem?.item_code ? activeItem.item_code : '' }}</span>
                    <!-- Product actions quantity, add to pinboard, order online -->
                    <product-actions 
                        :cartStatus="cartStatus"
                        :quantity="cartObject.quantity" 
                        :modelData="modelData"
                        :accessories="accessories"
                        @increase-quantity="increaseQuantity"
                        @decrease-quantity="decreaseQuantity"
                        @add-to-pinboard="addToPinboard"
                        @toggle-accessories="toggleAccessories"
                    />

                    
                </div>
            </div>
        </div>
    </div>
    `,
};
