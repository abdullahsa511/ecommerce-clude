export class Pinboard {
    constructor(data) {
        this.uuid = data?.uuid??null;
        this.reference_number = data?.reference_number??null;
        this.pinboard_temp_id = data?.pinboard_temp_id??null;
        this.pinboard_id = data?.pinboard_id??null;
        this.company_id = data?.company_id??null;
        this.customer_id = data?.customer_id??null;
        this.contact_number = data?.contact_number??null;
        this.job_id = data?.job_id??null;
        this.dispatch_location_id = data?.dispatch_location_id??null;
        this.job_title = data?.job_title??null;
        this.pinboard_name = data?.pinboard_name??null;
        this.pinboard_description = data?.pinboard_description??null;
        this.customer_po_number = data?.customer_po_number??null;
        this.expiry_date = data?.expiry_date??null;
        this.organisation_code = data?.organisation_code??null;
        this.bill_to = data?.bill_to??null;
        this.ship_to = data?.ship_to??null;
        this.site_contacts = data?.site_contacts??null;
        this.customer_balance = data?.customer_balance??null;
        this.sales_price_list = data?.sales_price_list??null;
        this.total_bp_ex_gst = data?.total_bp_ex_gst??null;
        this.total_bp_inc_gst = data?.total_bp_inc_gst??null;
        this.total_sp_ex_gst = data?.total_sp_ex_gst??null;
        this.total_sp_inc_gst = data?.total_sp_inc_gst??null;
        this.order_discount = data?.order_discount??null;
        this.discount_rate = data?.discount_rate??null;
        this.discount_amount = data?.discount_amount??null;
        this.grand_total_sp_ex_gst = data?.grand_total_sp_ex_gst??null;
        this.grand_total_sp_inc_gst = data?.grand_total_sp_inc_gst??null;
        this.pinboard_status_id = data?.pinboard_status_id??null;
        this.total = data?.total??null;
        this.bill_instructions = data?.bill_instructions??null;
        this.bill_address = data?.bill_address??null;
        this.bill_suburb = data?.bill_suburb??null;
        this.bill_state = data?.bill_state??null;
        this.bill_postcode = data?.bill_postcode??null;
        this.bill_country = data?.bill_country??null;
        this.ship_building_name = data?.ship_building_name??null;
        this.ship_instructions = data?.ship_instructions??null;
        this.ship_address = data?.ship_address??null;
        this.ship_address_two = data?.ship_address_two??null;
        this.ship_suburb = data?.ship_suburb??null;
        this.ship_state = data?.ship_state??null;
        this.ship_postcode = data?.ship_postcode??null;
        this.ship_country = data?.ship_country??null;
        this.created_at = data?.created_at??null;
        this.updated_at = data?.updated_at??null;
        this.customer_email = data?.customer_email??null;
        let pinboardItems = data?.pinboard_items??data?.pinboardItems??[];
        try {
            pinboardItems = Array.isArray(pinboardItems) ? pinboardItems : 
            (pinboardItems ? JSON.parse(pinboardItems) : []);
        }catch(error) {
            pinboardItems = [];
        }
        this.pinboard_items = pinboardItems?.map(item => new PinboardItem(item))??[];
        this.item_images = data?.item_images?.map(image => new PinboardItemImage(image))??[];
    }
    addItem(item) {
        this.pinboard_items.push(new PinboardItem(item));
    }
    addImage(image) {
        this.item_images.push(new PinboardItemImage(image));
    }
    
}

export class PinboardItem{
    constructor(data) {
        let comments = [];
        try {
            comments = Array.isArray(data?.comments) ? data?.comments : 
            (data?.comments ? JSON.parse(data?.comments) : []);
        }catch(error) {
            console.error('Error creating PinboardItem:', error);
        }
        this.uuid = data?.uuid??null;
        this.model = new PinboardItemModel(data?.model??null);
        this.title = data?.title??null;
        this.photo = data?.photo??data?.image??null;
        this.options = data?.options??null;
        this.comments = (typeof comments === 'string') ? [comments] : comments;
        this.newComments = data?.newComments??[];
        this.model_id = data?.model_id??null;
        this.quantity = data?.quantity??null;
        this.created_at = data?.created_at??null;
        this.model_type = data?.model_type??null;
        this.sort_order = data?.sort_order??null;
        this.unit_price = data?.unit_price??null;
        this.updated_at = data?.updated_at??null;
        this.description = data?.description??null;
        this.language_id = data?.language_id??null;
        this.pinboard_id = data?.pinboard_id??null;
        this.pinboard_temp_id = data?.pinboard_temp_id??null;
        this.total_price = data?.total_price??null;
        this.pinboard_item_id = data?.pinboard_item_id??null;
        this.pinboard_temp_item_id = data?.pinboard_temp_item_id??null;
        this.accessories = data?.accessories?.map(accessory => new PinboardItemAccessory(accessory))??[];
        this.comment_images = data?.comment_images??[];
        this.product_url = data?.product_url??null;
    }
}

export class PinboardItemModel {
    constructor(data) {
        this.post_id = data?.post_id??null;
        this.media_id = data?.media_id??null;
        this.comment_id = data?.comment_id??null;
        this.post_image = data?.post_image??null;
        this.post_title = data?.post_title??null;
        this.product_id = data?.product_id??null;
        this.project_id = data?.project_id??null;
        this.media_image = data?.media_image??null;
        this.media_title = data?.media_title??null;
        this.comment_image = data?.comment_image??null;
        this.comment_title = data?.comment_title??null;
        this.product_image = data?.product_image??null;
        this.product_title = data?.product_title??null;
        this.project_image = data?.project_image??null;
        this.project_title = data?.project_title??null;
    }
}

export class PinboardItemAccessory {
    constructor(data) {
        this.description = data?.description??null;
        this.image = data?.image??null;
        this.is_selected = data?.is_selected??null;
        this.item_code = data?.item_code??null;
        this.item_id = data?.item_id??null;
        this.price = data?.price??null;
        this.product_accessories_id = data?.product_accessories_id??null;
        this.product_id = data?.product_id??null;
        this.title = data?.title??null;
    }
}

export class PinboardItemImage {
    constructor(data) {
        this.uuid = data?.uuid??null;
        this.post_id = data?.post_id??null;
        this.media_id = data?.media_id??null;
        this.comment_id = data?.comment_id??null;
        this.photo = data?.photo??null;
        this.description = data?.description??null;
        this.post_image = data?.post_image??null;
        this.post_title = data?.post_title??null;
        this.product_id = data?.product_id??null;
        this.project_id = data?.project_id??null;
        this.media_image = data?.media_image??null;
        this.media_title = data?.media_title??null;
        this.comment_image = data?.comment_image??null;
        this.comment_title = data?.comment_title??null;
        this.product_image = data?.product_image??null;
        this.product_title = data?.product_title??null;
        this.project_image = data?.project_image??null;
        this.project_title = data?.project_title??null;
    }
}