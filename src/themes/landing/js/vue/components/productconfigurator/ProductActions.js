import Accessories from './Accessories.js';


export default {
    name: 'ProductActions',
    components: {
        Accessories
    },
    data() {
        return {
            minQty: 1,
            maxQty: 100
        }
    },
    emits: ['toggle-accessories'],
    props: {
        quantity: {
            type: Number,
            required: true
        },
        modelData: {
            type: Array,
            required: true
        },
        cartStatus: {
            type: Boolean,
            required: false,
            default: false
        },
        accessories: {
            type: Array,
            required: true,
            default: []
        }
    },
    created() {
        // console.log('this is ProductActions component');
        // console.log('this is quantity', this.quantity);
        // console.log('this is modelData', this.modelData);
        // console.log('this is accessories', this.accessories);
    },



    template: /* html */ `
    <div class="th-product-quantity-container">
        <div class="th-product-quantity-header">
            <span class="th-product-quantity-label">Quantity</span>

            <div class="th-product-quantity-controls">
                <button
                    type="button"
                    @click="$emit('decrease-quantity')"
                    class="th-quantity-btn th-quantity-btn--minus"
                    :disabled="quantity <= minQty"
                    aria-label="Decrease quantity"
                >
                    <i class="fa-regular fa-minus"></i>
                </button>

                <span class="th-quantity-value">{{ quantity }}</span>

                <button
                    type="button"
                    @click="$emit('increase-quantity')"
                    class="th-quantity-btn th-quantity-btn--plus"
                    :disabled="quantity >= maxQty"
                    aria-label="Increase quantity"
                >
                    <i class="fa-solid fa-plus"></i>
                </button>
            </div>
        </div>

        <!-- Hidden but functional input -->
        <input
            type="number"
            v-model.number="quantity"
            class="th-quantity-native-input"
        />

        <button type="button" @click="$emit('add-to-pinboard')" class="th-add-to-cart-btn">
            <!-- {{ cartStatus ? 'Update Pinboard' : 'Add To Pinboard' }} -->
            Add To Pinboard
        </button>

        <Accessories
            v-if="accessories.length"
            @toggle-accessories="$emit('toggle-accessories', $event)"
            :accessories="accessories"
        />


        <!-- order online button -->
        <div class="th-product-order-online">
            <div class="th-link @@classPadding">
                <div class="th-link-text pr-5"> 
                <a href="#">Order Online</a>
                </div>
                <div class="th-link-icon-btn"> <i class="fa-regular fa-arrow-up degree-60"></i> </div>
            </div>
            <div class="file-formats">
                <div class="th-file-formats-text">
                   <a
                        v-for="model in modelData"
                        :key="model.design_resource_document_id"
                        :href="model.format"
                        :download="'model.' + model.format"
                        class="th-file-format-link"
                    >
                    <span> .{{ model.format }}</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- end order online button -->
    </div>
    `
}   