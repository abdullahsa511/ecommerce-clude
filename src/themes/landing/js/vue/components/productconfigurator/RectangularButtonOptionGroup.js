
export default {
    name: 'FilterableOptionGroup',
    emits: [
        'option-selected',
        'get-tags'
    ],
    props: {
        group: {
            type: Object,
            required: true
        }
    },
    methods: {
        formatPrice(price) {
            const parsed = parseFloat(price);
            if (!isFinite(parsed)) {
                return '$0';
            }
            if (parsed === 0) {
                return '$0';
            }
            const absolute = Math.abs(parsed);
            const formatted = absolute % 1 === 0 ? absolute.toFixed(0) : absolute.toFixed(2).replace(/\.?0+$/, '');
            const sign = parsed > 0 ? '+$' : '-$';
            return `${sign}${formatted}`;
        },
        handleSelectOption(option) {
            this.$emit('option-selected', option);
        },
    },
    computed: {
       
    },
    template: /* html */ `
    <div class="accordion-body th-accordion-body">
            <div class="th-height-range-options">
                <div
                    class="th-height-option"
                    v-for="option in group.productOptions || []"
                    :key="option.product_option_id"
                    :class="{ 'th-height-option-selected': option.selected }"
                >                
                    <a
                        class="th-height-button"
                        :class="{ 'th-height-button-selected': option.selected }"
                        href="#"
                        @click.prevent="handleSelectOption(option)"
                    >
                        {{ option.option_name }}
                    </a>
                    <!--<span v-if="option.selected" class="th-height-badge">SELECTED</span>-->     
                    <!--<span v-else class="th-height-price">{{ formatPrice(option.price) }}</span>-->
                </div>
            </div>
        </div>
    </div>
    `

}