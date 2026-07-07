<?php

declare(strict_types=1);

class AddForeignKeys
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to add foreign key constraints
     */
    public function up(): void
    {
        // First drop any existing foreign keys
        $this->dropForeignKeys();
        
        // Now add the foreign keys
        $this->addForeignKeys();
    }
    
    /**
     * Add foreign key constraints
     */
    private function addForeignKeys(): void
    {
        $queries = [
            // User Address foreign keys
            "ALTER TABLE `user_address` 
             ADD CONSTRAINT `fk_user_address_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_user_address_country` 
             FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE",

            // Product Review foreign keys
            "ALTER TABLE `product_review` 
             ADD CONSTRAINT `fk_product_review_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_review_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_review_parent` 
             FOREIGN KEY (`parent_id`) REFERENCES `product_review` (`product_review_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Content foreign keys
            "ALTER TABLE `product_content` 
             ADD CONSTRAINT `fk_product_content_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Meta foreign keys
            "ALTER TABLE `product_meta` 
             ADD CONSTRAINT `fk_product_meta_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Image foreign keys
            "ALTER TABLE `product_image` 
             ADD CONSTRAINT `fk_product_image_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Option foreign keys
            "ALTER TABLE `product_option` 
             ADD CONSTRAINT `fk_product_option_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Option Value foreign keys
            "ALTER TABLE `product_option_value` 
             ADD CONSTRAINT `fk_product_option_value_product_option` 
             FOREIGN KEY (`product_option_id`) REFERENCES `product_option` (`product_option_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Order Product foreign keys
            "ALTER TABLE `order_product` 
             ADD CONSTRAINT `fk_order_product_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_product_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE",

            // User Points foreign keys
            "ALTER TABLE `user_points` 
             ADD CONSTRAINT `fk_user_points_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // User Wishlist foreign keys
            "ALTER TABLE `user_wishlist` 
             ADD CONSTRAINT `fk_user_wishlist_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_user_wishlist_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Taxonomy Item foreign keys
            "ALTER TABLE `taxonomy_item` 
             ADD CONSTRAINT `fk_taxonomy_item_taxonomy` 
             FOREIGN KEY (`taxonomy_id`) REFERENCES `taxonomy` (`taxonomy_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_taxonomy_item_parent` 
             FOREIGN KEY (`parent_id`) REFERENCES `taxonomy_item` (`taxonomy_item_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Taxonomy Item Content foreign keys
            /**
             * "ALTER TABLE `taxonomy_item_content` 
             *  ADD CONSTRAINT `fk_taxonomy_item_content_taxonomy_item` 
             *  FOREIGN KEY (`taxonomy_item_id`) REFERENCES `taxonomy_item` (`taxonomy_item_id`) 
             *  ON DELETE CASCADE ON UPDATE CASCADE",
             */
            

            // Product to Taxonomy Item foreign keys
            "ALTER TABLE `product_to_taxonomy_item` 
             ADD CONSTRAINT `fk_product_to_taxonomy_item_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_to_taxonomy_item_taxonomy_item` 
             FOREIGN KEY (`taxonomy_item_id`) REFERENCES `taxonomy_item` (`taxonomy_item_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Post to Taxonomy foreign keys
            "ALTER TABLE `post_to_taxonomy` 
             ADD CONSTRAINT `fk_post_to_taxonomy_post` 
             FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_post_to_taxonomy_taxonomy` 
             FOREIGN KEY (`taxonomy_id`) REFERENCES `taxonomy` (`taxonomy_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Subscription foreign keys
            "ALTER TABLE `subscription` 
             ADD CONSTRAINT `fk_subscription_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_subscription_order_product` 
             FOREIGN KEY (`order_product_id`) REFERENCES `order_product` (`order_product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_subscription_site` 
             FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_subscription_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_subscription_payment_address` 
             FOREIGN KEY (`payment_address_id`) REFERENCES `user_address` (`user_address_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_subscription_shipping_address` 
             FOREIGN KEY (`shipping_address_id`) REFERENCES `user_address` (`user_address_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_subscription_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_subscription_plan` 
             FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plan` (`subscription_plan_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE",

            // Subscription Log foreign keys
            "ALTER TABLE `subscription_log` 
             ADD CONSTRAINT `fk_subscription_log_subscription` 
             FOREIGN KEY (`subscription_id`) REFERENCES `subscription` (`subscription_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Subscription foreign keys
            "ALTER TABLE `product_subscription` 
             ADD CONSTRAINT `fk_product_subscription_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_subscription_subscription_plan` 
             FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plan` (`subscription_plan_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Order foreign keys
            "ALTER TABLE `order` 
             ADD CONSTRAINT `fk_order_site` 
             FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_user_group` 
             FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_billing_country` 
             FOREIGN KEY (`billing_country_id`) REFERENCES `country` (`country_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_shipping_country` 
             FOREIGN KEY (`shipping_country_id`) REFERENCES `country` (`country_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
            /**
                ADD CONSTRAINT `fk_order_shipping_status` 
                FOREIGN KEY (`shipping_status_id`) REFERENCES `shipping_status` (`shipping_status_id`) 
                ON DELETE RESTRICT ON UPDATE CASCADE,
                ADD CONSTRAINT `fk_order_status` 
                FOREIGN KEY (`order_status_id`) REFERENCES `order_status` (`order_status_id`) 
                ON DELETE RESTRICT ON UPDATE CASCADE,
            */
            

             ADD CONSTRAINT `fk_order_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_currency` 
             FOREIGN KEY (`currency_id`) REFERENCES `currency` (`currency_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE",

            // Order Log foreign keys
            "ALTER TABLE `order_log` 
             ADD CONSTRAINT `fk_order_log_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Order Meta foreign keys
            "ALTER TABLE `order_meta` 
             ADD CONSTRAINT `fk_order_meta_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Admin foreign keys
            "ALTER TABLE `admin` 
             ADD CONSTRAINT `fk_admin_role` 
             FOREIGN KEY (`role_id`) REFERENCES `admin_role` (`role_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE",

            // Admin Failed Login foreign keys
            "ALTER TABLE `admin_failed_login` 
             ADD CONSTRAINT `fk_admin_failed_login_admin` 
             FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Admin Password Resets foreign keys
            "ALTER TABLE `admin_password_resets` 
             ADD CONSTRAINT `fk_admin_password_resets_admin` 
             FOREIGN KEY (`email`) REFERENCES `admin` (`email`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Attribute Content foreign keys
            "ALTER TABLE `attribute_content` 
             ADD CONSTRAINT `fk_attribute_content_attribute` 
             FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`attribute_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_attribute_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Attribute Group Content foreign keys
            "ALTER TABLE `attribute_group_content` 
             ADD CONSTRAINT `fk_attribute_group_content_attribute_group` 
             FOREIGN KEY (`attribute_group_id`) REFERENCES `attribute_group` (`attribute_group_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_attribute_group_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Attribute foreign keys
            "ALTER TABLE `attribute` 
             ADD CONSTRAINT `fk_attribute_attribute_group` 
             FOREIGN KEY (`attribute_group_id`) REFERENCES `attribute_group` (`attribute_group_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Comment foreign keys
            "ALTER TABLE `comment` 
             ADD CONSTRAINT `fk_comment_post` 
             FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_comment_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_comment_parent` 
             FOREIGN KEY (`parent_id`) REFERENCES `comment` (`comment_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Coupon Product foreign keys
            "ALTER TABLE `coupon_product` 
             ADD CONSTRAINT `fk_coupon_product_coupon` 
             FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`coupon_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_coupon_product_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Coupon Taxonomy foreign keys
            "ALTER TABLE `coupon_taxonomy` 
             ADD CONSTRAINT `fk_coupon_taxonomy_coupon` 
             FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`coupon_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_coupon_taxonomy_taxonomy_item` 
             FOREIGN KEY (`taxonomy_item_id`) REFERENCES `taxonomy_item` (`taxonomy_item_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Coupon Log foreign keys
            "ALTER TABLE `coupon_log` 
             ADD CONSTRAINT `fk_coupon_log_coupon` 
             FOREIGN KEY (`coupon_id`) REFERENCES `coupon` (`coupon_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_coupon_log_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_coupon_log_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Voucher foreign keys
            "ALTER TABLE `voucher` 
             ADD CONSTRAINT `fk_voucher_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Voucher Log foreign keys
            "ALTER TABLE `voucher_log` 
             ADD CONSTRAINT `fk_voucher_log_voucher` 
             FOREIGN KEY (`voucher_id`) REFERENCES `voucher` (`voucher_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_voucher_log_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Field foreign keys
            "ALTER TABLE `field` 
             ADD CONSTRAINT `fk_field_field_group` 
             FOREIGN KEY (`field_group_id`) REFERENCES `field_group` (`field_group_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Field Content foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `field_content` 
             ADD CONSTRAINT `fk_field_content_field` 
             FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_field_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
             */

            // Field Group Content foreign keys
            "ALTER TABLE `field_group_content` 
             ADD CONSTRAINT `fk_field_group_content_field_group` 
             FOREIGN KEY (`field_group_id`) REFERENCES `field_group` (`field_group_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_field_group_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Field Value foreign keys
            "ALTER TABLE `field_value` 
             ADD CONSTRAINT `fk_field_value_field` 
             FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Field Value Content foreign keys
            "ALTER TABLE `field_value_content` 
             ADD CONSTRAINT `fk_field_value_content_field_value` 
             FOREIGN KEY (`field_value_id`) REFERENCES `field_value` (`field_value_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_field_value_content_field` 
             FOREIGN KEY (`field_id`) REFERENCES `field` (`field_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_field_value_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Region foreign keys
            "ALTER TABLE `region` 
             ADD CONSTRAINT `fk_region_country` 
             FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Region to Region Group foreign keys
            "ALTER TABLE `region_to_region_group` 
             ADD CONSTRAINT `fk_region_to_region_group_country` 
             FOREIGN KEY (`country_id`) REFERENCES `country` (`country_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_region_to_region_group_region` 
             FOREIGN KEY (`region_id`) REFERENCES `region` (`region_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_region_to_region_group_region_group` 
             FOREIGN KEY (`region_group_id`) REFERENCES `region_group` (`region_group_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Media Content foreign keys
            "ALTER TABLE `media_content` 
             ADD CONSTRAINT `fk_media_content_media` 
             FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_media_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Menu Item foreign keys
            "ALTER TABLE `menu_item` 
             ADD CONSTRAINT `fk_menu_item_menu` 
             FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_menu_item_parent` 
             FOREIGN KEY (`parent_id`) REFERENCES `menu_item` (`menu_item_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Menu Item Content foreign keys
            "ALTER TABLE `menu_item_content` 
             ADD CONSTRAINT `fk_menu_item_content_menu_item` 
             FOREIGN KEY (`menu_item_id`) REFERENCES `menu_item` (`menu_item_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_menu_item_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Menu Item Meta foreign keys
            "ALTER TABLE `menu_item_meta` 
             ADD CONSTRAINT `fk_menu_item_meta_menu_item` 
             FOREIGN KEY (`menu_item_id`) REFERENCES `menu_item` (`menu_item_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Menu to Site foreign keys
            "ALTER TABLE `menu_to_site` 
             ADD CONSTRAINT `fk_menu_to_site_menu` 
             FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_menu_to_site_site` 
             FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Menu Type Content foreign keys
            "ALTER TABLE `menu_type_content` 
             ADD CONSTRAINT `fk_menu_type_content_menu_type` 
             FOREIGN KEY (`menu_type_id`) REFERENCES `menu_type` (`menu_type_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_menu_type_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Post to Menu foreign keys
            "ALTER TABLE `post_to_menu` 
             ADD CONSTRAINT `fk_post_to_menu_post` 
             FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_post_to_menu_menu` 
             FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Option Content foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `option_content` 
             ADD CONSTRAINT `fk_option_content_option` 
             FOREIGN KEY (`option_id`) REFERENCES `option` (`option_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_option_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
             */

            // Option Value foreign keys
            "ALTER TABLE `option_value` 
             ADD CONSTRAINT `fk_option_value_option` 
             FOREIGN KEY (`option_id`) REFERENCES `option` (`option_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Option Value Content foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `option_value_content` 
             ADD CONSTRAINT `fk_option_value_content_option_value` 
             FOREIGN KEY (`option_value_id`) REFERENCES `option_value` (`option_value_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_option_value_content_option` 
             FOREIGN KEY (`option_id`) REFERENCES `option` (`option_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_option_value_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
             */

            // Product Option foreign keys
            "ALTER TABLE `product_option` 
             ADD CONSTRAINT `fk_product_option_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
             /**
             * ADD CONSTRAINT `fk_product_option_option` 
             * FOREIGN KEY (`option_id`) REFERENCES `option` (`option_id`) 
             * ON DELETE CASCADE ON UPDATE CASCADE",
             */

            // Product Option Value foreign keys
            "ALTER TABLE `product_option_value` 
             ADD CONSTRAINT `fk_product_option_value_product_option` 
             FOREIGN KEY (`product_option_id`) REFERENCES `product_option` (`product_option_id`) 
             /** 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_option_value_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_option_value_option` 
             FOREIGN KEY (`option_id`) REFERENCES `option` (`option_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_option_value_option_value` 
             FOREIGN KEY (`option_value_id`) REFERENCES `option_value` (`option_value_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Order Product Option foreign keys
            "ALTER TABLE `order_product_option` 
             ADD CONSTRAINT `fk_order_product_option_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_product_option_order_product` 
             FOREIGN KEY (`order_product_id`) REFERENCES `order_product` (`order_product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_product_option_product_option` 
             FOREIGN KEY (`product_option_id`) REFERENCES `product_option` (`product_option_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Order Shipment foreign keys
            "ALTER TABLE `order_shipment` 
             ADD CONSTRAINT `fk_order_shipment_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Order Total foreign keys
            "ALTER TABLE `order_total` 
             ADD CONSTRAINT `fk_order_total_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Order Voucher foreign keys
            "ALTER TABLE `order_voucher` 
             ADD CONSTRAINT `fk_order_voucher_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_voucher_voucher` 
             FOREIGN KEY (`voucher_id`) REFERENCES `voucher` (`voucher_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Return foreign keys
            "ALTER TABLE `return` 
             ADD CONSTRAINT `fk_return_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_return_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_return_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_return_reason` 
             FOREIGN KEY (`return_reason_id`) REFERENCES `return_reason` (`return_reason_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_return_resolution` 
             FOREIGN KEY (`return_resolution_id`) REFERENCES `return_resolution` (`return_resolution_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_return_status` 
             FOREIGN KEY (`return_status_id`) REFERENCES `return_status` (`return_status_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Return Log foreign keys
            "ALTER TABLE `return_log` 
             ADD CONSTRAINT `fk_return_log_return` 
             FOREIGN KEY (`return_id`) REFERENCES `return` (`return_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_return_log_return_status` 
             FOREIGN KEY (`return_status_id`) REFERENCES `return_status` (`return_status_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Post Content foreign keys
            "ALTER TABLE `post_content` 
             ADD CONSTRAINT `fk_post_content_post` 
             FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_post_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Post Content Meta foreign keys
            "ALTER TABLE `post_content_meta` 
             ADD CONSTRAINT `fk_post_content_meta_post` 
             FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_post_content_meta_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Post Content Revision foreign keys
            "ALTER TABLE `post_content_revision` 
             ADD CONSTRAINT `fk_post_content_revision_post` 
             FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_post_content_revision_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_post_content_revision_admin` 
             FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Post Meta foreign keys
            "ALTER TABLE `post_meta` 
             ADD CONSTRAINT `fk_post_meta_post` 
             FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Post to Site foreign keys
            "ALTER TABLE `post_to_site` 
             ADD CONSTRAINT `fk_post_to_site_post` 
             FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_post_to_site_site` 
             FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Taxonomy Content foreign keys
            "ALTER TABLE `taxonomy_content` 
             ADD CONSTRAINT `fk_taxonomy_content_taxonomy` 
             FOREIGN KEY (`taxonomy_id`) REFERENCES `taxonomy` (`taxonomy_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_taxonomy_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Taxonomy Item Meta foreign keys
            "ALTER TABLE `taxonomy_item_meta` 
             ADD CONSTRAINT `fk_taxonomy_item_meta_taxonomy_item` 
             FOREIGN KEY (`taxonomy_item_id`) REFERENCES `taxonomy_item` (`taxonomy_item_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Taxonomy to Site foreign keys
            "ALTER TABLE `taxonomy_to_site` 
             ADD CONSTRAINT `fk_taxonomy_to_site_taxonomy_item` 
             FOREIGN KEY (`taxonomy_item_id`) REFERENCES `taxonomy_item` (`taxonomy_item_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_taxonomy_to_site_site` 
             FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Length Type Content foreign keys
            "ALTER TABLE `length_type_content` 
             ADD CONSTRAINT `fk_length_type_content_length_type` 
             FOREIGN KEY (`length_type_id`) REFERENCES `length_type` (`length_type_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_length_type_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Weight Type Content foreign keys
            "ALTER TABLE `weight_type_content` 
             ADD CONSTRAINT `fk_weight_type_content_weight_type` 
             FOREIGN KEY (`weight_type_id`) REFERENCES `weight_type` (`weight_type_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_weight_type_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product foreign keys
            "ALTER TABLE `product` 
             ADD CONSTRAINT `fk_product_admin` 
             FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_parent` 
             FOREIGN KEY (`parent_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             /* Removed due to composite primary key in stock_status table
             ADD CONSTRAINT `fk_product_stock_status` 
             FOREIGN KEY (`stock_status_id`) REFERENCES `stock_status` (`stock_status_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             */
             ADD CONSTRAINT `fk_product_manufacturer` 
             FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`manufacturer_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_vendor` 
             FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`vendor_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_weight_type` 
             FOREIGN KEY (`weight_type_id`) REFERENCES `weight_type` (`weight_type_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_length_type` 
             FOREIGN KEY (`length_type_id`) REFERENCES `length_type` (`length_type_id`) 
             ON DELETE RESTRICT ON UPDATE CASCADE",

            // Product Attribute foreign keys
            "ALTER TABLE `product_attribute` 
             ADD CONSTRAINT `fk_product_attribute_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_attribute_attribute` 
             FOREIGN KEY (`attribute_id`) REFERENCES `attribute` (`attribute_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_attribute_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Content foreign keys
            "ALTER TABLE `product_content` 
             ADD CONSTRAINT `fk_product_content_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Content Meta foreign keys
            "ALTER TABLE `product_content_meta` 
             ADD CONSTRAINT `fk_product_content_meta_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_content_meta_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Content Revision foreign keys
            "ALTER TABLE `product_content_revision` 
             ADD CONSTRAINT `fk_product_content_revision_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_content_revision_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_content_revision_admin` 
             FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Discount foreign keys
            "ALTER TABLE `product_discount` 
             ADD CONSTRAINT `fk_product_discount_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_discount_user_group` 
             FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Points foreign keys
            "ALTER TABLE `product_points` 
             ADD CONSTRAINT `fk_product_points_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_points_user_group` 
             FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Promotion foreign keys
            "ALTER TABLE `product_promotion` 
             ADD CONSTRAINT `fk_product_promotion_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_promotion_user_group` 
             FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Question foreign keys
            "ALTER TABLE `product_question` 
             ADD CONSTRAINT `fk_product_question_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_question_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_question_parent` 
             FOREIGN KEY (`parent_id`) REFERENCES `product_question` (`product_question_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Related foreign keys
            "ALTER TABLE `product_related` 
             ADD CONSTRAINT `fk_product_related_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_related_related` 
             FOREIGN KEY (`product_related_id`) REFERENCES `product` (`product_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Review Media foreign keys
            "ALTER TABLE `product_review_media` 
             ADD CONSTRAINT `fk_product_review_media_review` 
             FOREIGN KEY (`product_review_id`) REFERENCES `product_review` (`product_review_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_review_media_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_review_media_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product to Digital Asset foreign keys
            "ALTER TABLE `product_to_digital_asset` 
             ADD CONSTRAINT `fk_product_to_digital_asset_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_to_digital_asset_asset` 
             FOREIGN KEY (`digital_asset_id`) REFERENCES `digital_asset` (`digital_asset_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product to Site foreign keys
            "ALTER TABLE `product_to_site` 
             ADD CONSTRAINT `fk_product_to_site_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_to_site_site` 
             FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Variant foreign keys
            "ALTER TABLE `product_variant` 
             ADD CONSTRAINT `fk_product_variant_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_variant_variant` 
             FOREIGN KEY (`product_variant_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Manufacturer to Site foreign keys
            "ALTER TABLE `manufacturer_to_site` 
             ADD CONSTRAINT `fk_manufacturer_to_site_manufacturer` 
             FOREIGN KEY (`manufacturer_id`) REFERENCES `manufacturer` (`manufacturer_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_manufacturer_to_site_site` 
             FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Vendor to Site foreign keys
            "ALTER TABLE `vendor_to_site` 
             ADD CONSTRAINT `fk_vendor_to_site_vendor` 
             FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`vendor_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_vendor_to_site_site` 
             FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Setting Content foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `setting_content` 
             ADD CONSTRAINT `fk_setting_content_site` 
             FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_setting_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
             */

            // Setting foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `setting` 
             ADD CONSTRAINT `fk_setting_site` 
             FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
             */

            // Order Subscription foreign keys
            "ALTER TABLE `order_subscription` 
             ADD CONSTRAINT `fk_order_subscription_order_product` 
             FOREIGN KEY (`order_product_id`) REFERENCES `order_product` (`order_product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_subscription_order` 
             FOREIGN KEY (`order_id`) REFERENCES `order` (`order_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_subscription_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_order_subscription_subscription_plan` 
             FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plan` (`subscription_plan_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Product Subscription foreign keys
            "ALTER TABLE `product_subscription` 
             ADD CONSTRAINT `fk_product_subscription_product` 
             FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_product_subscription_subscription_plan` 
             FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plan` (`subscription_plan_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Subscription Plan Content foreign keys
            "ALTER TABLE `subscription_plan_content` 
             ADD CONSTRAINT `fk_subscription_plan_content_subscription_plan` 
             FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plan` (`subscription_plan_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
             /* Removed due to composite primary key
             "ALTER TABLE `subscription_plan_content` 
             ADD CONSTRAINT `fk_subscription_plan_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
             */

            // Subscription Log foreign keys
            "ALTER TABLE `subscription_log` 
             ADD CONSTRAINT `fk_subscription_log_subscription` 
             FOREIGN KEY (`subscription_id`) REFERENCES `subscription` (`subscription_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
             /* Removed due to composite primary key
             "ALTER TABLE `subscription_log`
             ADD CONSTRAINT `fk_subscription_log_subscription_status` 
             FOREIGN KEY (`subscription_status_id`) REFERENCES `subscription_status` (`subscription_status_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",
             */

            // Tax Rate to User Group foreign keys
            "ALTER TABLE `tax_rate_to_user_group` 
             ADD CONSTRAINT `fk_tax_rate_to_user_group_tax_rate` 
             FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rate` (`tax_rate_id`)
             /** 
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_tax_rate_to_user_group_user_group` 
             FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Tax Rate foreign keys
            "ALTER TABLE `tax_rate` 
             ADD CONSTRAINT `fk_tax_rate_region_group` 
             FOREIGN KEY (`region_group_id`) REFERENCES `region_group` (`region_group_id`) 
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Tax Rule foreign keys
            "ALTER TABLE `tax_rule` 
             ADD CONSTRAINT `fk_tax_rule_tax_type` 
             FOREIGN KEY (`tax_type_id`) REFERENCES `tax_type` (`tax_type_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_tax_rule_tax_rate` 
             FOREIGN KEY (`tax_rate_id`) REFERENCES `tax_rate` (`tax_rate_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // User Group Content foreign keys
            "ALTER TABLE `user_group_content` 
             ADD CONSTRAINT `fk_user_group_content_user_group` 
             FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_user_group_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Digital Asset Content foreign keys
            "ALTER TABLE `digital_asset_content` 
             ADD CONSTRAINT `fk_digital_asset_content_digital_asset` 
             FOREIGN KEY (`digital_asset_id`) REFERENCES `digital_asset` (`digital_asset_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_digital_asset_content_language` 
             FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Digital Asset Log foreign keys
            "ALTER TABLE `digital_asset_log` 
             ADD CONSTRAINT `fk_digital_asset_log_digital_asset` 
             FOREIGN KEY (`digital_asset_id`) REFERENCES `digital_asset` (`digital_asset_id`) 
             /**
             ON DELETE CASCADE ON UPDATE CASCADE,
             ADD CONSTRAINT `fk_digital_asset_log_user` 
             FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) 
             */
             ON DELETE CASCADE ON UPDATE CASCADE",

            // Password Resets foreign keys
            "ALTER TABLE `password_resets` 
             ADD CONSTRAINT `fk_password_resets_user` 
             FOREIGN KEY (`email`) REFERENCES `user` (`email`) 
             ON DELETE CASCADE ON UPDATE CASCADE"
        ];
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($queries as $query) {
            try {
                $this->pdo->exec($query);
                $successCount++;
            } catch (PDOException $e) {
                // Log the error but continue with other constraints
                echo "Error on query: " . $query . "\n";
                echo "Error message: " . $e->getMessage() . "\n";
                $errorCount++;
            }
        }
        
        echo "Foreign key migration summary: $successCount constraints added successfully, $errorCount errors.\n";
    }

    /**
     * Drop existing foreign key constraints
     */
    public function dropForeignKeys(): void
    {
        $queries = [
            // User Address foreign keys
            "ALTER TABLE `user_address` 
             DROP FOREIGN KEY `fk_user_address_user`,
             DROP FOREIGN KEY `fk_user_address_country`",

            // Product Review foreign keys
            "ALTER TABLE `product_review` 
             DROP FOREIGN KEY `fk_product_review_product`,
             DROP FOREIGN KEY `fk_product_review_user`,
             DROP FOREIGN KEY `fk_product_review_parent`",

            // Product Content foreign keys
            "ALTER TABLE `product_content` 
             DROP FOREIGN KEY `fk_product_content_product`",

            // Product Meta foreign keys
            "ALTER TABLE `product_meta` 
             DROP FOREIGN KEY `fk_product_meta_product`",

            // Product Image foreign keys
            "ALTER TABLE `product_image` 
             DROP FOREIGN KEY `fk_product_image_product`",

            // Product Option foreign keys
            "ALTER TABLE `product_option` 
             DROP FOREIGN KEY `fk_product_option_product`",

            // Product Option Value foreign keys
            "ALTER TABLE `product_option_value` 
             DROP FOREIGN KEY `fk_product_option_value_product_option`",

            // Order Product foreign keys
            "ALTER TABLE `order_product` 
             DROP FOREIGN KEY `fk_order_product_order`,
             DROP FOREIGN KEY `fk_order_product_product`",

            // User Points foreign keys
            "ALTER TABLE `user_points` 
             DROP FOREIGN KEY `fk_user_points_user`",

            // User Wishlist foreign keys
            "ALTER TABLE `user_wishlist` 
             DROP FOREIGN KEY `fk_user_wishlist_user`,
             DROP FOREIGN KEY `fk_user_wishlist_product`",

            // Taxonomy Item foreign keys
            "ALTER TABLE `taxonomy_item` 
             DROP FOREIGN KEY `fk_taxonomy_item_taxonomy`,
             DROP FOREIGN KEY `fk_taxonomy_item_parent`",

            // Taxonomy Item Content foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `taxonomy_item_content` 
             DROP FOREIGN KEY `fk_taxonomy_item_content_taxonomy_item`",
             */

            // Product to Taxonomy Item foreign keys
            "ALTER TABLE `product_to_taxonomy_item` 
             DROP FOREIGN KEY `fk_product_to_taxonomy_item_product`,
             DROP FOREIGN KEY `fk_product_to_taxonomy_item_taxonomy_item`",

            // Post to Taxonomy foreign keys
            "ALTER TABLE `post_to_taxonomy` 
             DROP FOREIGN KEY `fk_post_to_taxonomy_post`,
             DROP FOREIGN KEY `fk_post_to_taxonomy_taxonomy`",

            // Subscription foreign keys
            "ALTER TABLE `subscription` 
             DROP FOREIGN KEY `fk_subscription_order`,
             DROP FOREIGN KEY `fk_subscription_order_product`,
             DROP FOREIGN KEY `fk_subscription_site`,
             DROP FOREIGN KEY `fk_subscription_user`,
             DROP FOREIGN KEY `fk_subscription_payment_address`,
             DROP FOREIGN KEY `fk_subscription_shipping_address`,
             DROP FOREIGN KEY `fk_subscription_product`,
             DROP FOREIGN KEY `fk_subscription_plan`",

            // Subscription Log foreign keys
            "ALTER TABLE `subscription_log` 
             DROP FOREIGN KEY `fk_subscription_log_subscription`",

            // Product Subscription foreign keys
            "ALTER TABLE `product_subscription` 
             DROP FOREIGN KEY `fk_product_subscription_product`,
             DROP FOREIGN KEY `fk_product_subscription_subscription_plan`",

            // Order foreign keys
            "ALTER TABLE `order` 
             DROP FOREIGN KEY `fk_order_site`,
             DROP FOREIGN KEY `fk_order_user`,
             DROP FOREIGN KEY `fk_order_user_group`,
             DROP FOREIGN KEY `fk_order_billing_country`,
             DROP FOREIGN KEY `fk_order_shipping_country`,
             /* Removed due to composite primary keys in shipping_status and order_status tables
             DROP FOREIGN KEY `fk_order_shipping_status`,
             DROP FOREIGN KEY `fk_order_status`,
             */
             DROP FOREIGN KEY `fk_order_language`,
             DROP FOREIGN KEY `fk_order_currency`",

            // Order Log foreign keys
            "ALTER TABLE `order_log` 
             DROP FOREIGN KEY `fk_order_log_order`",

            // Order Meta foreign keys
            "ALTER TABLE `order_meta` 
             DROP FOREIGN KEY `fk_order_meta_order`",

            // Admin foreign keys
            "ALTER TABLE `admin` 
             DROP FOREIGN KEY `fk_admin_role`",

            // Admin Failed Login foreign keys
            "ALTER TABLE `admin_failed_login` 
             DROP FOREIGN KEY `fk_admin_failed_login_admin`",

            // Admin Password Resets foreign keys
            "ALTER TABLE `admin_password_resets` 
             DROP FOREIGN KEY `fk_admin_password_resets_admin`",

            // Attribute Content foreign keys
            "ALTER TABLE `attribute_content` 
             DROP FOREIGN KEY `fk_attribute_content_attribute`,
             DROP FOREIGN KEY `fk_attribute_content_language`",

            // Attribute Group Content foreign keys
            "ALTER TABLE `attribute_group_content` 
             DROP FOREIGN KEY `fk_attribute_group_content_attribute_group`,
             DROP FOREIGN KEY `fk_attribute_group_content_language`",

            // Attribute foreign keys
            "ALTER TABLE `attribute` 
             DROP FOREIGN KEY `fk_attribute_attribute_group`",

            // Comment foreign keys
            "ALTER TABLE `comment` 
             DROP FOREIGN KEY `fk_comment_post`,
             DROP FOREIGN KEY `fk_comment_user`,
             DROP FOREIGN KEY `fk_comment_parent`",

            // Coupon Product foreign keys
            "ALTER TABLE `coupon_product` 
             DROP FOREIGN KEY `fk_coupon_product_coupon`,
             DROP FOREIGN KEY `fk_coupon_product_product`",

            // Coupon Taxonomy foreign keys
            "ALTER TABLE `coupon_taxonomy` 
             DROP FOREIGN KEY `fk_coupon_taxonomy_coupon`,
             DROP FOREIGN KEY `fk_coupon_taxonomy_taxonomy_item`",

            // Coupon Log foreign keys
            "ALTER TABLE `coupon_log` 
             DROP FOREIGN KEY `fk_coupon_log_coupon`,
             DROP FOREIGN KEY `fk_coupon_log_order`,
             DROP FOREIGN KEY `fk_coupon_log_user`",

            // Voucher foreign keys
            "ALTER TABLE `voucher` 
             DROP FOREIGN KEY `fk_voucher_order`",

            // Voucher Log foreign keys
            "ALTER TABLE `voucher_log` 
             DROP FOREIGN KEY `fk_voucher_log_voucher`,
             DROP FOREIGN KEY `fk_voucher_log_order`",

            // Field foreign keys
            "ALTER TABLE `field` 
             DROP FOREIGN KEY `fk_field_field_group`",

            // Field Content foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `field_content` 
             DROP FOREIGN KEY `fk_field_content_field`,
             DROP FOREIGN KEY `fk_field_content_language`",
             */

            // Field Group Content foreign keys
            "ALTER TABLE `field_group_content` 
             DROP FOREIGN KEY `fk_field_group_content_field_group`,
             DROP FOREIGN KEY `fk_field_group_content_language`",

            // Field Value foreign keys
            "ALTER TABLE `field_value` 
             DROP FOREIGN KEY `fk_field_value_field`",

            // Field Value Content foreign keys
            "ALTER TABLE `field_value_content` 
             DROP FOREIGN KEY `fk_field_value_content_field_value`,
             DROP FOREIGN KEY `fk_field_value_content_field`,
             DROP FOREIGN KEY `fk_field_value_content_language`",

            // Region foreign keys
            "ALTER TABLE `region` 
             DROP FOREIGN KEY `fk_region_country`",

            // Region to Region Group foreign keys
            "ALTER TABLE `region_to_region_group` 
             DROP FOREIGN KEY `fk_region_to_region_group_country`,
             DROP FOREIGN KEY `fk_region_to_region_group_region`,
             DROP FOREIGN KEY `fk_region_to_region_group_region_group`",

            // Media Content foreign keys
            "ALTER TABLE `media_content` 
             DROP FOREIGN KEY `fk_media_content_media`,
             DROP FOREIGN KEY `fk_media_content_language`",

            // Menu Item foreign keys
            "ALTER TABLE `menu_item` 
             DROP FOREIGN KEY `fk_menu_item_menu`,
             DROP FOREIGN KEY `fk_menu_item_parent`",

            // Menu Item Content foreign keys
            "ALTER TABLE `menu_item_content` 
             DROP FOREIGN KEY `fk_menu_item_content_menu_item`,
             DROP FOREIGN KEY `fk_menu_item_content_language`",

            // Menu Item Meta foreign keys
            "ALTER TABLE `menu_item_meta` 
             DROP FOREIGN KEY `fk_menu_item_meta_menu_item`",

            // Menu to Site foreign keys
            "ALTER TABLE `menu_to_site` 
             DROP FOREIGN KEY `fk_menu_to_site_menu`,
             DROP FOREIGN KEY `fk_menu_to_site_site`",

            // Menu Type Content foreign keys
            "ALTER TABLE `menu_type_content` 
             DROP FOREIGN KEY `fk_menu_type_content_menu_type`,
             DROP FOREIGN KEY `fk_menu_type_content_language`",

            // Post to Menu foreign keys
            "ALTER TABLE `post_to_menu` 
             DROP FOREIGN KEY `fk_post_to_menu_post`,
             DROP FOREIGN KEY `fk_post_to_menu_menu`",

            // Option Content foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `option_content` 
             DROP FOREIGN KEY `fk_option_content_option`,
             DROP FOREIGN KEY `fk_option_content_language`",
             */

            // Option Value foreign keys
            "ALTER TABLE `option_value` 
             DROP FOREIGN KEY `fk_option_value_option`",

            // Option Value Content foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `option_value_content` 
             DROP FOREIGN KEY `fk_option_value_content_option_value`,
             DROP FOREIGN KEY `fk_option_value_content_option`,
             DROP FOREIGN KEY `fk_option_value_content_language`",
             */

            // Product Option foreign keys
            "ALTER TABLE `product_option` 
             DROP FOREIGN KEY `fk_product_option_product`,
             DROP FOREIGN KEY `fk_product_option_option`",

            // Product Option Value foreign keys
            "ALTER TABLE `product_option_value` 
             DROP FOREIGN KEY `fk_product_option_value_product_option`,
             DROP FOREIGN KEY `fk_product_option_value_product`,
             DROP FOREIGN KEY `fk_product_option_value_option`,
             DROP FOREIGN KEY `fk_product_option_value_option_value`",

            // Order Product Option foreign keys
            "ALTER TABLE `order_product_option` 
             DROP FOREIGN KEY `fk_order_product_option_order`,
             DROP FOREIGN KEY `fk_order_product_option_order_product`,
             DROP FOREIGN KEY `fk_order_product_option_product_option`",

            // Order Shipment foreign keys
            "ALTER TABLE `order_shipment` 
             DROP FOREIGN KEY `fk_order_shipment_order`",

            // Order Total foreign keys
            "ALTER TABLE `order_total` 
             DROP FOREIGN KEY `fk_order_total_order`",

            // Order Voucher foreign keys
            "ALTER TABLE `order_voucher` 
             DROP FOREIGN KEY `fk_order_voucher_order`,
             DROP FOREIGN KEY `fk_order_voucher_voucher`",

            // Return foreign keys
            "ALTER TABLE `return` 
             DROP FOREIGN KEY `fk_return_order`,
             DROP FOREIGN KEY `fk_return_product`,
             DROP FOREIGN KEY `fk_return_user`",

            // Return Log foreign keys
            "ALTER TABLE `return_log` 
             DROP FOREIGN KEY `fk_return_log_return`",

            // Post Content foreign keys
            "ALTER TABLE `post_content` 
             DROP FOREIGN KEY `fk_post_content_post`,
             DROP FOREIGN KEY `fk_post_content_language`",

            // Post Content Meta foreign keys
            "ALTER TABLE `post_content_meta` 
             DROP FOREIGN KEY `fk_post_content_meta_post`,
             DROP FOREIGN KEY `fk_post_content_meta_language`",

            // Post Content Revision foreign keys
            "ALTER TABLE `post_content_revision` 
             DROP FOREIGN KEY `fk_post_content_revision_post`,
             DROP FOREIGN KEY `fk_post_content_revision_language`,
             DROP FOREIGN KEY `fk_post_content_revision_admin`",

            // Post Meta foreign keys
            "ALTER TABLE `post_meta` 
             DROP FOREIGN KEY `fk_post_meta_post`",

            // Post to Site foreign keys
            "ALTER TABLE `post_to_site` 
             DROP FOREIGN KEY `fk_post_to_site_post`,
             DROP FOREIGN KEY `fk_post_to_site_site`",

            // Taxonomy Content foreign keys
            "ALTER TABLE `taxonomy_content` 
             DROP FOREIGN KEY `fk_taxonomy_content_taxonomy`,
             DROP FOREIGN KEY `fk_taxonomy_content_language`",

            // Taxonomy Item Meta foreign keys
            "ALTER TABLE `taxonomy_item_meta` 
             DROP FOREIGN KEY `fk_taxonomy_item_meta_taxonomy_item`",

            // Taxonomy to Site foreign keys
            "ALTER TABLE `taxonomy_to_site` 
             DROP FOREIGN KEY `fk_taxonomy_to_site_taxonomy_item`,
             DROP FOREIGN KEY `fk_taxonomy_to_site_site`",

            // Length Type Content foreign keys
            "ALTER TABLE `length_type_content` 
             DROP FOREIGN KEY `fk_length_type_content_length_type`,
             DROP FOREIGN KEY `fk_length_type_content_language`",

            // Weight Type Content foreign keys
            "ALTER TABLE `weight_type_content` 
             DROP FOREIGN KEY `fk_weight_type_content_weight_type`,
             DROP FOREIGN KEY `fk_weight_type_content_language`",

            // Product foreign keys
            "ALTER TABLE `product` 
             DROP FOREIGN KEY `fk_product_admin`,
             DROP FOREIGN KEY `fk_product_parent`,
             /* Removed due to composite primary key in stock_status table
             DROP FOREIGN KEY `fk_product_stock_status`,
             */
             DROP FOREIGN KEY `fk_product_manufacturer`,
             DROP FOREIGN KEY `fk_product_vendor`,
             DROP FOREIGN KEY `fk_product_weight_type`,
             DROP FOREIGN KEY `fk_product_length_type`",

            // Product Attribute foreign keys
            "ALTER TABLE `product_attribute` 
             DROP FOREIGN KEY `fk_product_attribute_product`,
             DROP FOREIGN KEY `fk_product_attribute_attribute`,
             DROP FOREIGN KEY `fk_product_attribute_language`",

            // Product Content foreign keys
            "ALTER TABLE `product_content` 
             DROP FOREIGN KEY `fk_product_content_product`,
             DROP FOREIGN KEY `fk_product_content_language`",

            // Product Content Meta foreign keys
            "ALTER TABLE `product_content_meta` 
             DROP FOREIGN KEY `fk_product_content_meta_product`,
             DROP FOREIGN KEY `fk_product_content_meta_language`",

            // Product Content Revision foreign keys
            "ALTER TABLE `product_content_revision` 
             DROP FOREIGN KEY `fk_product_content_revision_product`,
             DROP FOREIGN KEY `fk_product_content_revision_language`,
             DROP FOREIGN KEY `fk_product_content_revision_admin`",

            // Product Discount foreign keys
            "ALTER TABLE `product_discount` 
             DROP FOREIGN KEY `fk_product_discount_product`,
             DROP FOREIGN KEY `fk_product_discount_user_group`",

            // Product Points foreign keys
            "ALTER TABLE `product_points` 
             DROP FOREIGN KEY `fk_product_points_product`,
             DROP FOREIGN KEY `fk_product_points_user_group`",

            // Product Promotion foreign keys
            "ALTER TABLE `product_promotion` 
             DROP FOREIGN KEY `fk_product_promotion_product`,
             DROP FOREIGN KEY `fk_product_promotion_user_group`",

            // Product Question foreign keys
            "ALTER TABLE `product_question` 
             DROP FOREIGN KEY `fk_product_question_product`,
             DROP FOREIGN KEY `fk_product_question_user`,
             DROP FOREIGN KEY `fk_product_question_parent`",

            // Product Related foreign keys
            "ALTER TABLE `product_related` 
             DROP FOREIGN KEY `fk_product_related_product`,
             DROP FOREIGN KEY `fk_product_related_related`",

            // Product Review Media foreign keys
            "ALTER TABLE `product_review_media` 
             DROP FOREIGN KEY `fk_product_review_media_review`,
             DROP FOREIGN KEY `fk_product_review_media_product`,
             DROP FOREIGN KEY `fk_product_review_media_user`",

            // Product to Digital Asset foreign keys
            "ALTER TABLE `product_to_digital_asset` 
             DROP FOREIGN KEY `fk_product_to_digital_asset_product`,
             DROP FOREIGN KEY `fk_product_to_digital_asset_asset`",

            // Product to Site foreign keys
            "ALTER TABLE `product_to_site` 
             DROP FOREIGN KEY `fk_product_to_site_product`,
             DROP FOREIGN KEY `fk_product_to_site_site`",

            // Product Variant foreign keys
            "ALTER TABLE `product_variant` 
             DROP FOREIGN KEY `fk_product_variant_product`,
             DROP FOREIGN KEY `fk_product_variant_variant`",

            // Manufacturer to Site foreign keys
            "ALTER TABLE `manufacturer_to_site` 
             DROP FOREIGN KEY `fk_manufacturer_to_site_manufacturer`,
             DROP FOREIGN KEY `fk_manufacturer_to_site_site`",

            // Vendor to Site foreign keys
            "ALTER TABLE `vendor_to_site` 
             DROP FOREIGN KEY `fk_vendor_to_site_vendor`,
             DROP FOREIGN KEY `fk_vendor_to_site_site`",

            // Setting Content foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `setting_content` 
             DROP FOREIGN KEY `fk_setting_content_site`,
             DROP FOREIGN KEY `fk_setting_content_language`",
             */

            // Setting foreign keys
            /* Removed due to composite primary key
            "ALTER TABLE `setting` 
             DROP FOREIGN KEY `fk_setting_site`",
             */

            "ALTER TABLE `order_subscription` 
             DROP FOREIGN KEY `fk_order_subscription_order_product`,
             DROP FOREIGN KEY `fk_order_subscription_order`,
             DROP FOREIGN KEY `fk_order_subscription_product`,
             DROP FOREIGN KEY `fk_order_subscription_subscription_plan`",

            "ALTER TABLE `product_subscription` 
             DROP FOREIGN KEY `fk_product_subscription_product`,
             DROP FOREIGN KEY `fk_product_subscription_subscription_plan`,
             DROP FOREIGN KEY `fk_product_subscription_user_group`",

            "ALTER TABLE `subscription_plan_content` 
             DROP FOREIGN KEY `fk_subscription_plan_content_subscription_plan`,
             DROP FOREIGN KEY `fk_subscription_plan_content_language`",

            "ALTER TABLE `subscription_log` 
             DROP FOREIGN KEY `fk_subscription_log_subscription`",

            "ALTER TABLE `tax_rate_to_user_group` 
             DROP FOREIGN KEY `fk_tax_rate_to_user_group_tax_rate`,
             DROP FOREIGN KEY `fk_tax_rate_to_user_group_user_group`",

            "ALTER TABLE `tax_rate` 
             DROP FOREIGN KEY `fk_tax_rate_region_group`",

            "ALTER TABLE `tax_rule` 
             DROP FOREIGN KEY `fk_tax_rule_tax_type`,
             DROP FOREIGN KEY `fk_tax_rule_tax_rate`",

            "ALTER TABLE `user_group_content` 
             DROP FOREIGN KEY `fk_user_group_content_user_group`,
             DROP FOREIGN KEY `fk_user_group_content_language`",

            "ALTER TABLE `digital_asset_content` 
             DROP FOREIGN KEY `fk_digital_asset_content_digital_asset`,
             DROP FOREIGN KEY `fk_digital_asset_content_language`",

            "ALTER TABLE `digital_asset_log` 
             DROP FOREIGN KEY `fk_digital_asset_log_digital_asset`,
             DROP FOREIGN KEY `fk_digital_asset_log_user`",

            "ALTER TABLE `password_resets` 
             DROP FOREIGN KEY `fk_password_resets_user`"
        ];

        try {
            foreach ($queries as $query) {
                $this->pdo->exec($query);
            }
            echo "Foreign key constraints dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping foreign key constraints: " . $e->getMessage() . "\n";
        }
    }
}