export default {
    name: 'ThumbnailOptions',
    props: {
        group: {
            type: Object,
            required: true
        },
        defaultOptionImage: {
            type: String,
            default: 'https://dummyimage.com/120x120/444/fff&text=F'
        }
    },
    
    emits: ['option-selected'],
    methods: {
        handleSelectOption(option) {
            this.$emit('option-selected', option);
        },
        getOptionImage(option) {
            // console.log('option hex color', option.hex_color, 'option option image', option.option_image);
            if (!option) {
                return this.defaultOptionImage;
            }
            if (typeof option.option_image === 'string' && option.option_image) {
                return option.option_image;
            }
            if (option.option_image && option.option_image) {
                return option.option_image;
            }
            return this.defaultOptionImage;
        },
        formatPrice(price) {
            const parsed = parseFloat(price);
            if (!isFinite(parsed) || parsed === 0) {
                return '$0';
            }
            const absolute = Math.abs(parsed);
            const formatted = absolute % 1 === 0 ? absolute.toFixed(0) : absolute.toFixed(2).replace(/\.?0+$/, '');
            const sign = parsed > 0 ? '+$' : '-$';
            return `${sign}${formatted}`;
        }
    },

    // watch: {
    //
    // },

    template: /* html */ `
    <div class="th-product-options-list">
        <div
            class="th-product-option-card"
            v-for="option in group.productOptions || []"
            :key="option.product_option_id"
            :class="{
                'th-product-option-selected': option.selected,
                'th-product-option-primary': option.selected
            }"
            @click="handleSelectOption(option)"
        >
                <div class="th-product-option-image"  v-if="option.hex_color" :style="{ backgroundColor: option.hex_color, width: '100px', height: '85px' }">
                </div>
                <div class="th-product-option-image" v-else>
                    <img 
                        :src="getOptionImage(option)" 
                        :alt="option.option_name"
                    />
                </div>
            <div class="th-product-option-image" v-else>
                <div class="th-product-option-image-color" :style="{ backgroundColor: option.hex_color }"></div>
            </div>
            <div class="th-product-option-label">{{ option.option_name }}</div>
            <span v-if="option.selected" class="th-product-option-badge th-product-option-badge-primary">Selected</span>
            <!--<span v-else class="th-product-option-price">{{ formatPrice(option.price) }}</span>-->
        </div>
    </div>
    `
}