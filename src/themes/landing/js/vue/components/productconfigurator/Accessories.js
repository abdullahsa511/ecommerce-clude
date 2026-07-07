export default {
    name: 'Accessories',
    props: {
        accessories: {
            type: Array,
            required: true,
            default: []
        }
    },
    data() {
        return {
            selectedAccessories: []
        }
    },
    emits: ['toggle-accessories'],
    methods: {
        toggleAccessory(accessoryItem) {
            if (accessoryItem.is_selected) {
                // SELECT
                if (!this.selectedAccessories.some(
                    item => item.product_accessories_id === accessoryItem.product_accessories_id
                )) {
                    this.selectedAccessories.push(accessoryItem);
                }
            } else {
                // DESELECT
                this.selectedAccessories = this.selectedAccessories.filter(
                    item => item.product_accessories_id !== accessoryItem.product_accessories_id
                );
            }
            this.$emit('toggle-accessories', this.selectedAccessories);
        }
    },
    mounted() {
        this.accessories.forEach(group => {
            group.accessories.forEach(item => {
                if (item.is_selected) {
                    this.selectedAccessories.push(item);
                }
            });
        });
    },    
    template: /* html */ `
    <div class="th-accessories">
        <div class="card">
            <div class="card-header">
                <div class="th-accessories-header-text">
                    <h2 class="card-title">Accessories</h2>
                    <p class="th-accessories-subtitle">Add optional accessories for your selected variant</p>
                </div>
                <div class="th-accessories-optional">
                    <button type="button">OPTIONAL</button>
                </div>
            </div>
            <div class="card-body">
                <div class="th-accessories-content" v-for="accessory in accessories" :key="accessory.parent_product_id">
                    <h5 class="th-accessories-title">{{ accessory.parent_product_name }}</h5>
                    <div class="th-accessories-list">
                        <div class="th-accessories-item" v-for="accessoryItem in accessory.accessories" :key="accessoryItem.product_accessories_id">
                            <input
                                type="checkbox"
                                v-model="accessoryItem.is_selected"
                                @change="toggleAccessory(accessoryItem)"
                            />
                            <div class="th-accessories-item-image">
                                <img v-if="accessoryItem.image" :src="accessoryItem.image" alt="Digital Lock - Keypad">
                                <i v-else class="fa-regular fa-image"></i>
                            </div>
                            <div class="th-item-info">
                                <h6>{{ accessoryItem.title }}</h6>
                                <p>{{ accessoryItem.description }}</p>
                            </div>
                            <div class="th-item-price">
                                <p>{{ "+$"+ accessoryItem.price }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="th-accessories-footer-inner">
                    <i class="fa-light fa-circle-check"></i>
                    <p>Accessories are compatible with selected variant</p>
                </div>
            </div>
        </div>
    </div>
    `
}