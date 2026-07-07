export default {
    name: 'SquareBoxOptions',

    props: {
        group: {
            type: Object,
            required: true
        },
        data: {
            type: Array,
            required: true,
        }
    },

    emits: ['box-option-selected'],

    computed: {
        selectedBoxOption() {
            return this.$store ? this.$store.getters.selectedBoxOption : {};
        }
    },

    mounted() {
        this.initTooltips();
    },

    updated() {
        this.initTooltips();
    },

    methods: {
        initTooltips() {
            // Use global bootstrap.Tooltip
            if (window.bootstrap && window.bootstrap.Tooltip) {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                tooltipTriggerList.forEach(el => {
                    if (!el._tooltip) {
                        el._tooltip = new bootstrap.Tooltip(el);
                    }
                });
            }
        }
    },

    template: /* html */ `
    <div class="th-square-box-option-group">
        <div class="th-square-box-option-group-item">
            <div 
                class="th-square-image-wrapper"
                v-for="item in data"
                :key="item.id"
                :class="{ 'selected': item.id === selectedBoxOption.id }"

                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-bs-html="true"
                :title="\`<b>\${item.name}</b>\`"
            >
                <img 
                    :src="item.image" 
                    :alt="item.name" 
                    @click="$emit('box-option-selected', item)"
                >
            </div>
        </div>
    </div>
    `
};