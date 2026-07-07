import ThumbnailOptions from './ThumbnailOptions.js';
export default {
    name: 'FilterableOptionGroup',
    components: {
        ThumbnailOptions
    },
    emits: [
        'option-selected'
    ],
    props: {
        group: {
            type: Object,
            required: true
        },
        defaultOptionImage: {
            type: String,
            default: () => 'https://dummyimage.com/120x120/444/fff&text=F'
        }
    },
    template: /* html */ `
    <div class="accordion-body th-accordion-body">
        <div class="th-colors-selection">
            <thumbnail-options
                :group="group"
                :default-option-image="defaultOptionImage"
                @option-selected="$emit('option-selected', $event)"
            ></thumbnail-options>
        </div>
    </div>
    `

}