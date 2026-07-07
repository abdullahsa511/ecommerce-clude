-- Author : SA Technology
-- Created by: Mohammad Ali Abdullah
-- Date   : 2024-05-10
-- File   : alterSql.sql
-- SQL script to create tables for project sections, products, and images

CREATE TABLE `project_sections` (
    `section_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(191) NOT NULL,
    `slug` VARCHAR(191) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `status` VARCHAR(191) NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`section_id`),
    UNIQUE KEY `uk_project_section` (`project_id`, `section_id`),
    KEY `idx_project_id` (`project_id`),
    CONSTRAINT `fk_project_sections_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `project_section_products` (
    `section_product_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `section_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `status` JSON NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`section_product_id`),
    UNIQUE KEY `uk_section_product` (`section_id`, `product_id`),
    KEY `idx_section_id` (`section_id`),
    KEY `idx_product_id` (`product_id`),
    CONSTRAINT `fk_sections_products_section` FOREIGN KEY (`section_id`) REFERENCES `project_sections` (`section_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_sections_products_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE `project_sections_images` (
    `section_image_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `section_id` INT UNSIGNED NOT NULL,
    `image_link` VARCHAR(191) NOT NULL,
    `image` JSON NOT NULL,
    `status` JSON NOT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`section_image_id`),
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

'[{"image": 
[{"name": "News 2.png", "path": "/var/www/html/public//media/Projects/News 2.png", "size": 564773, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 2.png"}],
 "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 2.png", "sort_order": 0, "post_image_id": 69}, 
 {"image": [{"name": "News 3.png", "path": "/var/www/html/public//media/Projects/News 3.png", "size": 758410, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, 
 "objectURL": "http://localhost:8089/media/Projects/News 3.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 3.png", "sort_order": 0, "post_image_id": 70}, {"image": [{"name": "News 2.png", "path": "/var/www/html/public//media/Projects/News 2.png", "size": 564773, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 2.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 2.png", "sort_order": 0, "post_image_id": 69}, {"image": [{"name": "News 3.png", "path": "/var/www/html/public//media/Projects/News 3.png", "size": 758410, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 3.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 3.png", "sort_order": 0, "post_image_id": 70}, {"image": [{"name": "News 2.png", "path": "/var/www/html/public//media/Projects/News 2.png", "size": 564773, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 2.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 2.png", "sort_order": 0, "post_image_id": 69}, {"image": [{"name": "News 3.png", "path": "/var/www/html/public//media/Projects/News 3.png", "size": 758410, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 3.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 3.png", "sort_order": 0, "post_image_id": 70}, {"image": [{"name": "News 2.png", "path": "/var/www/html/public//media/Projects/News 2.png", "size": 564773, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 2.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 2.png", "sort_order": 0, "post_image_id": 69}, {"image": [{"name": "News 3.png", "path": "/var/www/html/public//media/Projects/News 3.png", "size": 758410, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 3.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 3.png", "sort_order": 0, "post_image_id": 70}, {"image": [{"name": "News 2.png", "path": "/var/www/html/public//media/Projects/News 2.png", "size": 564773, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 2.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 2.png", "sort_order": 0, "post_image_id": 69}, {"image": [{"name": "News 3.png", "path": "/var/www/html/public//media/Projects/News 3.png", "size": 758410, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 3.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 3.png", "sort_order": 0, "post_image_id": 70}, {"image": [{"name": "News 2.png", "path": "/var/www/html/public//media/Projects/News 2.png", "size": 564773, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 2.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 2.png", "sort_order": 0, "post_image_id": 69}, {"image": [{"name": "News 3.png", "path": "/var/www/html/public//media/Projects/News 3.png", "size": 758410, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 3.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 3.png", "sort_order": 0, "post_image_id": 70}, {"image": [{"name": "News 2.png", "path": "/var/www/html/public//media/Projects/News 2.png", "size": 564773, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 2.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 2.png", "sort_order": 0, "post_image_id": 69}, {"image": [{"name": "News 3.png", "path": "/var/www/html/public//media/Projects/News 3.png", "size": 758410, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 3.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 3.png", "sort_order": 0, "post_image_id": 70}, {"image": [{"name": "News 2.png", "path": "/var/www/html/public//media/Projects/News 2.png", "size": 564773, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 2.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 2.png", "sort_order": 0, "post_image_id": 69}, {"image": [{"name": "News 3.png", "path": "/var/www/html/public//media/Projects/News 3.png", "size": 758410, "type": "image/png", "status": {"name": "Uploaded", "severity": "success"}, "objectURL": "http://localhost:8089/media/Projects/News 3.png"}], "status": {"name": "Uploaded", "severity": "success"}, "post_id": 113, "image_link": "/media/Projects/News 3.png", "sort_order": 0, "post_image_id": 70}]'

-- 7-10-2025
-- Alter product table
-- -----------------------------------------------------------------------------------------------------------
ALTER TABLE `product`
CHANGE `km_item_id` `km_item_id` INT UNSIGNED NULL DEFAULT NULL,
CHANGE `material` `material` VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
CHANGE `weight` `weight` DECIMAL(15, 8) NULL DEFAULT '0.00000000',
CHANGE `length` `length` DECIMAL(15, 8) NULL DEFAULT '0.00000000',
CHANGE `min_order_quantity` `min_order_quantity` INT NULL DEFAULT '1',
CHANGE `carton_width` `carton_width` DECIMAL(10, 5) NULL DEFAULT '0.00000',
CHANGE `carton_depth` `carton_depth` DECIMAL(10, 5) NULL DEFAULT '0.00000',
CHANGE `carton_height` `carton_height` DECIMAL(10, 5) NULL DEFAULT '0.00000',
CHANGE `template` `template` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
CHANGE `views` `views` INT UNSIGNED NULL DEFAULT '0',
CHANGE `subtract_stock` `subtract_stock` TINYINT NULL DEFAULT '1',
CHANGE `status` `status` TINYINT NULL DEFAULT '0',
CHANGE `archive` `archive` TINYINT(1) NULL DEFAULT '0',
CHANGE `created_at` `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
CHANGE `updated_at` `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE product;

TRUNCATE TABLE product_content;

SET FOREIGN_KEY_CHECKS = 1;

-- section, section products and section images querys
-- -----------------------------------------------------------------------------------------------------------
SELECT DISTINCT
    ps.project_sections_id AS id,
    ps.title,
    ps.slug,
    ps.description,
    p.product_id,
    p.description as p_name,
    p.price,
    p.feature_description as feature,
    psi.image
FROM
    project_section_products psp
    LEFT JOIN project_sections ps ON ps.project_sections_id = psp.section_id
    LEFT JOIN project_section_images psi ON psi.section_id = ps.project_sections_id
    LEFT JOIN product p ON p.product_id = psp.product_id
    --  GROUP BY p.description, psi.image
WHERE
    ps.slug = 'living-room-collection';
-- -----------------------------------------------------------------------------------------------------------

-- Rename the table

-- Create statement for the new `showrooms` table
CREATE TABLE `showrooms` (
    `showrooms_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `title` varchar(191) NOT NULL,
    `slug` varchar(191) DEFAULT NULL,
    `description` text,
    `image` json DEFAULT NULL,
    `status` varchar(191) DEFAULT NULL,
    `sort_order` int NOT NULL DEFAULT '0',
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`showrooms_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

ALTER TABLE `project_sections`
ADD `showroom_id` INT NULL DEFAULT NULL AFTER `project_id`;

-- only obj URL
SELECT JSON_UNQUOTE(
        JSON_EXTRACT(image, '$[0].objectURL')
    ) AS objectURL, description
FROM project
WHERE
    name LIKE '%HomeCo%'
LIMIT 100

-- Hyundai Office,
-- MacKillop Family Services,
-- Tabcorp,
-- Cobbleworks,
-- Coca Cola Amatil,
-- Global Red
ALTER TABLE `project_sections`
ADD `section_code` INT NULL DEFAULT NULL AFTER `project_id`;
-- 25-10-2025
ALTER TABLE `option`
ADD COLUMN `option_code` varchar(191) DEFAULT NULL AFTER `option_id`;

ALTER TABLE `digital_asset`
ADD COLUMN `digital_asset_code` varchar(191) DEFAULT NULL AFTER `digital_asset_id`;

ALTER TABLE `attribute`
ADD COLUMN `attribute_code` varchar(191) DEFAULT NULL AFTER `attribute_id`;

ALTER TABLE `taxonomy_item`
ADD `taxonomy_item_code` VARCHAR(191) NULL DEFAULT NULL AFTER `taxonomy_item_id`;

ALTER TABLE `manufacturer`
ADD `manufacturer_code` VARCHAR(191) NULL DEFAULT NULL AFTER `manufacturer_id`;

ALTER TABLE `vendor`
ADD `vendor_code` VARCHAR(191) NULL DEFAULT NULL AFTER `vendor_id `;

-- insert statement
INSERT INTO
    digital_asset (
        digital_asset_code,
        file,
        public,
        created_at
    )
VALUES (
        'DGT-ASS-03',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-04',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-05',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-03',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-04',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-05',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-06',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-07',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-08',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-09',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-10',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-11',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-12',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-13',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-14',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-15',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-16',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-17',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-18',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-19',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-20',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-21',
        1,
        'public data',
        NOW()
    ),
    (
        'DGT-ASS-22',
        1,
        'public data',
        NOW()
    );

INSERT INTO
    attribute (
        attribute_code,
        attribute_group_id,
        sort_order
    )
VALUES ('ATTRI-01', 1, 1),
    ('ATTRI-02', 1, 2),
    ('ATTRI-03', 1, 3),
    ('ATTRI-04', 1, 4),
    ('ATTRI-05', 1, 5),
    ('ATTRI-06', 1, 6),
    ('ATTRI-07', 1, 7),
    ('ATTRI-08', 1, 8),
    ('ATTRI-09', 1, 9),
    ('ATTRI-10', 1, 10),
    ('ATTRI-11', 1, 11),
    ('ATTRI-12', 1, 12),
    ('ATTRI-13', 1, 13),
    ('ATTRI-14', 1, 14),
    ('ATTRI-15', 1, 15),
    ('ATTRI-16', 1, 16),
    ('ATTRI-17', 1, 17),
    ('ATTRI-18', 1, 18),
    ('ATTRI-19', 1, 19),
    ('ATTRI-20', 1, 20);

-- 26-10-2025
-- digital_asset query for search
SELECT digital_asset.digital_asset_id, digital_asset_content.name
FROM
    digital_asset
    JOIN digital_asset_content ON digital_asset_content.digital_asset_id = digital_asset.digital_asset_id
WHERE
    digital_asset_content.name LIKE '%a%';

INSERT INTO
    `digital_asset_content` (
        `digital_asset_id`,
        `language_id`,
        `name`
    )
VALUES (1, 1, 'Modern Office Desk'),
    (2, 1, 'Ergonomic Chair'),
    (3, 1, 'LED Monitor 27 Inch'),
    (4, 1, 'Wireless Keyboard'),
    (5, 1, 'Bluetooth Mouse'),
    (
        6,
        1,
        'Standing Desk Converter'
    ),
    (
        7,
        1,
        'Noise Cancelling Headphones'
    ),
    (8, 1, 'Conference Table'),
    (9, 1, 'Desk Lamp'),
    (10, 1, 'File Cabinet'),
    (11, 1, 'Office Sofa'),
    (12, 1, 'Whiteboard Set'),
    (13, 1, 'Projector Stand'),
    (14, 1, 'Wall Clock'),
    (15, 1, 'Bookshelf Organizer'),
    (16, 1, 'Desktop Computer'),
    (
        17,
        1,
        'Laptop Docking Station'
    ),
    (18, 1, 'Cable Management Box'),
    (19, 1, 'Smart Speaker'),
    (20, 1, 'HD Webcam'),
    (21, 1, 'Printer Stand'),
    (22, 1, 'Office Partition'),
    (23, 1, 'Visitor Chair'),
    (24, 1, 'Storage Rack'),
    (
        25,
        1,
        'Conference Microphone'
    );

-- product digital asset query.
SELECT digital_asset.digital_asset_id, digital_asset_content.name
FROM
    digital_asset
    JOIN digital_asset_content ON digital_asset_content.digital_asset_id = digital_asset.digital_asset_id
    JOIN product_to_digital_asset on product_to_digital_asset.digital_asset_id = digital_asset_content.digital_asset_id
WHERE
    product_to_digital_asset.product_id = 4;

-- product related product
SELECT product.product_id, product.product_code
FROM `product_related`
    JOIN product ON product.product_id = product_related.product_related_id
WHERE
    product_related.product_id = 4;

SELECT product_option.*
FROM `product_option`
    JOIN `option` ON `option`.option_id = product_option.product_option_id
WHERE
    product_id = 4;

INSERT INTO
    `attribute_content` (
        `attribute_id`,
        `language_id`,
        `name`
    )
VALUES (1, 1, 'Material Color'),
    (2, 1, 'Size'),
    (3, 1, 'Weight'),
    (4, 1, 'Length'),
    (5, 1, 'Width'),
    (6, 1, 'Height'),
    (7, 1, 'Volume'),
    (8, 1, 'Capacity'),
    (9, 1, 'Power'),
    (10, 1, 'Voltage'),
    (11, 1, 'Frequency'),
    (12, 1, 'Temperature'),
    (13, 1, 'Pressure'),
    (14, 1, 'Speed'),
    (15, 1, 'Durability'),
    (16, 1, 'Flexibility'),
    (17, 1, 'Material Type'),
    (18, 1, 'Finish'),
    (19, 1, 'Brand'),
    (20, 1, 'Warranty');

INSERT INTO
    `attribute_group` (
        `attribute_group_id`,
        `sort_order`
    )
VALUES (2, 2),
    (3, 3);

INSERT INTO
    `attribute_group_content` (
        `attribute_group_id`,
        `language_id`,
        `name`
    )
VALUES (1, 1, 'General Specs'),
    (2, 1, 'Technical Details'),
    (3, 1, 'Additional Features');

SELECT * FROM product_attribute pa WHERE pa.product_id = 4;

SELECT
    pa.product_id,
    a.attribute_id,
    a.attribute_code,
    a.attribute_group_id,
    a.sort_order AS attribute_sort_order,
    ac.name AS attribute_name,
    ag.attribute_group_id AS group_id,
    ag.sort_order AS group_sort_order,
    agc.name AS group_name
FROM
    product_attribute AS pa
    JOIN attribute AS a ON a.attribute_id = pa.attribute_id
    JOIN attribute_content AS ac ON ac.attribute_id = a.attribute_id
    JOIN attribute_group AS ag ON ag.attribute_group_id = a.attribute_group_id
    JOIN attribute_group_content AS agc ON agc.attribute_group_id = ag.attribute_group_id
WHERE
    pa.product_id = 4
ORDER BY pa.product_id ASC, ag.sort_order ASC, a.sort_order ASC;

-- 07-11-2025
-- Alter attribute_group table to add timestamp columns and unique key
-- Note: Skip columns that already exist or comment them out as needed
-- -----------------------------------------------------------------------------------------------------------
ALTER TABLE `attribute_group`
ADD COLUMN `deleted_at` DATETIME DEFAULT NULL AFTER `sort_order`,
ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `deleted_at`,
ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
ADD UNIQUE KEY `uq_attribute_group_code` (`code`);
-- -----------------------------------------------------------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `option_content`;

SET FOREIGN_KEY_CHECKS = 1;

ALTER TABLE `option`
ADD COLUMN `code` VARCHAR(191) NOT NULL AFTER `option_id`,
ADD COLUMN deleted_at DATETIME DEFAULT NULL AFTER sort_order,
ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `sort_order`,
ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `sort_order`,
ADD UNIQUE KEY `uq_option_code` (`code`);

-- related product duplicate qurey
INSERT INTO
    `product_related` (
        `product_id`,
        `product_related_id`
    )
VALUES (109, 104),
    (109, 105)
ON DUPLICATE KEY UPDATE
    `product_id` = VALUES(`product_id`),
    `product_related_id` = VALUES(`product_related_id`);

-- 09-11-2025
ALTER TABLE `type`
ADD COLUMN `deleted_at` DATETIME DEFAULT NULL AFTER `sort_order`,
ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `sort_order`,
ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `sort_order`,
ADD UNIQUE KEY `uq_type_name` (`type`);

-- 12-11-2025
ALTER TABLE `attribute`
CHANGE `attribute_code` `code` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL;

ALTER TABLE `mvc`.`attribute`
ADD UNIQUE `fk_attribute_code` (`code`);

-- Status related update - 16-11-2025
ALTER TABLE `stock_status`
ADD UNIQUE KEY `stock_status_name_language_id` (`name`, `language_id`);

ALTER TABLE `order_status`
ADD UNIQUE KEY `order_status_name_language_id` (`name`, `language_id`);

ALTER TABLE `payment_status`
ADD UNIQUE KEY `payment_status_name_language_id` (`name`, `language_id`);

ALTER TABLE `return_status`
ADD UNIQUE KEY `return_status_name_language_id` (`name`, `language_id`);

ALTER TABLE `shipping_status`
ADD UNIQUE KEY `shipping_status_name_language_id` (`name`, `language_id`);

ALTER TABLE `return_reason`
ADD UNIQUE KEY `return_reason_name_language_id` (`name`, `language_id`);

ALTER TABLE `return_resolution`
ADD UNIQUE KEY `return_resolution_name_language_id` (`name`, `language_id`);

ALTER TABLE `subscription_status`
ADD UNIQUE KEY `subscription_status_name_language_id` (`name`, `language_id`);

--- 18 November design_resource_document table
ALTER TABLE `design_resource_document`
ADD COLUMN `url` VARCHAR(500) NULL AFTER `name`;
--- 18-11-2025
ALTER TABLE `media`
ADD COLUMN `path` VARCHAR(500) NOT NULL AFTER `name`;

ALTER TABLE `media` ADD UNIQUE `uk_path` (`path`);

ALTER TABLE `design_resource`
ADD UNIQUE `uk_title_resource_type` (`title`, `resource_type`);

ALTER TABLE `design_resource_document`
ADD UNIQUE `ux_resource_id_media_id` (
    `design_resource_id`,
    `media_id`
);

-- 19-11-2025
DROP TABLE IF EXISTS `variant`;
-- Create statement for the new `variant` table
CREATE TABLE `variant` (
    product_variant_id int(20) unsigned NOT NULL AUTO_INCREMENT,
    product_id int unsigned NOT NULL,
    variant_name varchar(191) NOT NULL,
    variant_id int(20) DEFAULT NULL,
    code varchar(191) DEFAULT NULL,
    sort_order int NOT NULL DEFAULT '0',
    active_status tinyint(1) NOT NULL DEFAULT '1',
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at datetime DEFAULT NULL,
    PRIMARY KEY (`product_variant_id`),
    UNIQUE KEY `uq_product_variant_product_id_variant_name` (`variant_name`, `variant_id`),
    FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 19-11-2025
create table variants_item (
    variants_item_id int(20) unsigned NOT NULL AUTO_INCREMENT,
    product_variant_id int unsigned NOT NULL comment 'from varainat table',
    item_id int unsigned NOT NULL comment 'from item table',
    km_item_id int DEFAULT NULL DEFAULT '0' comment 'use id column in csv',
    sort_order int NOT NULL DEFAULT '0',
    active_status tinyint(1) NOT NULL DEFAULT '1',
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at datetime DEFAULT NULL,
    PRIMARY KEY (`variants_item_id`),
    FOREIGN KEY (`product_variant_id`) REFERENCES `variant` (`product_variant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE
)

-- 19-11-2025
CREATE TABLE `product_option_group` (
    `product_option_group_id` int(20) unsigned NOT NULL AUTO_INCREMENT,
    `product_variant_id` int unsigned NOT NULL comment 'from variant table',
    `option_group_name` varchar(191) NOT NULL,
    `sort_order` int NOT NULL DEFAULT '0',
    `active_status` tinyint(1) NOT NULL DEFAULT '1',
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`product_option_group_id`),
    UNIQUE KEY `uq_product_variant_id_option_group_name` (
        `product_variant_id`,
        `option_group_name`
    ),
    FOREIGN KEY (`product_variant_id`) REFERENCES `variant` (`product_variant_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

--
Create Product Option under Product menu
Use product option
table
and ensure following - product_variant_id (fk) product_option_group_id (fk) name (as option) Unique kye (
    product_variant_id,
    product_option_group_id,
    name
)

ALTER TABLE `product_option`
ADD COLUMN `product_variant_id` int unsigned NOT NULL comment 'from variant table' AFTER `product_id`,
ADD COLUMN `product_option_group_id` int unsigned NOT NULL comment 'from product_option_group table' AFTER `product_variant_id`,
-- ADD COLUMN `name` varchar(191) NOT NULL AFTER `product_option_group_id`,
ADD UNIQUE KEY `uq_product_variant_id_product_option_group_id_name` (
    `product_variant_id`,
    `product_option_group_id`,
    `name`
),
ADD FOREIGN KEY (`product_variant_id`) REFERENCES `variant` (`product_variant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD FOREIGN KEY (`product_option_group_id`) REFERENCES `product_option_group` (`product_option_group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 22-11-2025
ALTER TABLE `design_resource`
ADD COLUMN `sort_order` int NOT NULL DEFAULT '0' AFTER `hex_value`,
ADD COLUMN `deleted_at` DATETIME DEFAULT NULL AFTER `sort_order`,
ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `deleted_at`,
ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

ALTER TABLE `project`
ADD COLUMN `sort_order` int NOT NULL DEFAULT '0' AFTER `link_text`;

ALTER TABLE `user_group`
ADD COLUMN `code` VARCHAR(191) NOT NULL AFTER `user_group_id`,
ADD COLUMN deleted_at DATETIME DEFAULT NULL AFTER sort_order,
ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `deleted_at`,
ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`,
ADD UNIQUE KEY `uq_user_group_code` (`code`);

-- 25-11-2025
ALTER TABLE `coupon`
CHANGE `deleted_at` `deleted_at` DATETIME NULL DEFAULT NULL;

ALTER TABLE job ADD COLUMN deleted_at DATETIME DEFAULT NULL;

ALTER TABLE product_discount
ADD COLUMN deleted_at DATETIME DEFAULT NULL;

ALTER TABLE product_discount
ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE product_discount
ADD COLUMN updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 26-11-2025
ALTER TABLE `customer`
ADD COLUMN `phone` VARCHAR(50) DEFAULT NULL AFTER `gmail_Id`,
ADD COLUMN `address` VARCHAR(255) DEFAULT NULL AFTER `phone`;

ALTER TABLE `customer`
ADD COLUMN `billing_first_name` varchar(32) DEFAULT NULL,
ADD COLUMN `billing_last_name` varchar(32) DEFAULT NULL,
ADD COLUMN `billing_company` varchar(60) DEFAULT NULL,
ADD COLUMN `billing_address_1` varchar(191) DEFAULT NULL,
ADD COLUMN `billing_address_2` varchar(191) DEFAULT NULL,
ADD COLUMN `billing_city` varchar(128) DEFAULT NULL,
ADD COLUMN `billing_post_code` varchar(10) DEFAULT NULL,
ADD COLUMN `billing_country_id` int unsigned DEFAULT NULL,
ADD COLUMN `billing_region` varchar(128) DEFAULT NULL,
ADD COLUMN `billing_region_id` int unsigned DEFAULT NULL,
ADD COLUMN `payment_method` varchar(128) DEFAULT NULL,
ADD COLUMN `payment_data` text DEFAULT NULL,
ADD COLUMN `payment_status_id` int unsigned DEFAULT NULL,
ADD COLUMN `shipping_first_name` varchar(32) DEFAULT NULL,
ADD COLUMN `shipping_last_name` varchar(32) DEFAULT NULL,
ADD COLUMN `shipping_company` varchar(60) DEFAULT NULL,
ADD COLUMN `shipping_address_1` varchar(191) DEFAULT NULL,
ADD COLUMN `shipping_address_2` varchar(191) DEFAULT NULL,
ADD COLUMN `shipping_city` varchar(128) DEFAULT NULL,
ADD COLUMN `shipping_post_code` varchar(10) DEFAULT NULL,
ADD COLUMN `shipping_country` varchar(128) DEFAULT NULL,
ADD COLUMN `shipping_country_id` int unsigned DEFAULT NULL,
ADD COLUMN `shipping_region` varchar(128) DEFAULT NULL,
ADD COLUMN `shipping_region_id` int unsigned DEFAULT NULL,
ADD COLUMN `shipping_method` varchar(128) DEFAULT NULL,
ADD COLUMN `shipping_data` text DEFAULT NULL,
ADD COLUMN `shipping_status_id` int unsigned DEFAULT NULL;

UPDATE `customer`
SET
    billing_first_name = 'John',
    billing_last_name = 'Doe',
    billing_company = 'ABC Ltd',
    billing_address_1 = '123 Main Street',
    billing_address_2 = 'Suite 4B',
    billing_city = 'Sydney',
    billing_post_code = '2000',
    billing_country_id = 36,
    billing_region = 'NSW',
    billing_region_id = 101,
    payment_method = 'Credit Card',
    payment_data = 'VISA - Dummy Payment',
    payment_status_id = 1,
    shipping_first_name = 'Jane',
    shipping_last_name = 'Doe',
    shipping_company = 'XYZ Logistics',
    shipping_address_1 = '456 George Street',
    shipping_address_2 = 'Level 2',
    shipping_city = 'Melbourne',
    shipping_post_code = '3000',
    shipping_country = 'Australia',
    shipping_country_id = 36,
    shipping_region = 'VIC',
    shipping_region_id = 102,
    shipping_method = 'Standard Shipping',
    shipping_data = 'Tracking: DUMMY123456',
    shipping_status_id = 1;

WHERE customer_id = 1;

-- 30-11-2025
-- Alter attribute table to add value column
ALTER TABLE `attribute` ADD COLUMN value text DEFAULT NULL;

ALTER TABLE item ADD COLUMN is_default TINYINT(1) NOT NULL DEFAULT 0;

ALTER TABLE product_variant
ADD COLUMN is_default TINYINT(1) NOT NULL DEFAULT 0 after active_status;

ALTER TABLE product_option
add COLUMN description text NULL DEFAULT NULL;

ALTER TABLE product_option_group
add COLUMN description text NULL DEFAULT NULL;

ALTER TABLE product_variant
add COLUMN variant_description text NULL DEFAULT NULL;

-- 13-12-2025
ALTER TABLE `variant_item`
CHANGE `variants_item_id` `variant_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
-- 12-12-2025
UPDATE `component_item`
SET
    `fields` = '[{\"name\": \"title\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"Shop Archi\", \"options\": [], \"imagesData\": []}, {\"name\": \"image\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"FileUpload\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": [{\"id\": null, \"file\": {\"name\": \"contemporary-interior.jpg\", \"size\": 79590, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/php5nhnn20p0ojf91dBjxn\", \"full_path\": \"contemporary-interior.jpg\"}, \"name\": \"contemporary-interior.jpg\", \"size\": 79590, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/12/contemporary-interior.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": 2345, \"objectURL\": \"http://localhost:8089/media/uploads2025/12/contemporary-interior.jpg\", \"created_at\": \"\", \"description\": \"\"}], \"imagesData\": [{\"id\": null, \"file\": {\"name\": \"contemporary-interior.jpg\", \"size\": 79590, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/php5nhnn20p0ojf91dBjxn\", \"full_path\": \"contemporary-interior.jpg\"}, \"name\": \"contemporary-interior.jpg\", \"size\": 79590, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/12/contemporary-interior.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": 2345, \"objectURL\": \"http://localhost:8089/media/uploads2025/12/contemporary-interior.jpg\", \"created_at\": \"\", \"description\": \"\"}]}, {\"name\": \"linkText\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": \"Buy Now\", \"options\": [{\"label\": \"Buy Now\", \"value\": \"Buy Now\"}], \"imagesData\": []}, {\"name\": \"linkUrl\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": \"htt t \", \"imagesData\": []}]'
WHERE
    `component_item`.`component_item_id` = 39;

INSERT INTO
    `component_item` (
        `component_item_id`,
        `property_name`,
        `component_id`,
        `model`,
        `item_count`,
        `is_recent`,
        `is_featured`,
        `fields`,
        `model_id`,
        `related_models`,
        `title`,
        `subtitle`,
        `link_text`,
        `description`
    )
VALUES (
        NULL,
        'items',
        '25',
        NULL,
        '1',
        '1',
        '1',
        '[{\"name\": \"title\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": \"View Catalogue\", \"imagesData\": []}, {\"name\": \"image\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"FileUpload\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": [{\"id\": null, \"file\": {\"name\": \"melb.jpg\", \"size\": 268646, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/phpue5j9cq26374cTq3KjD\", \"full_path\": \"melb.jpg\"}, \"name\": \"melb.jpg\", \"size\": 268646, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/12/melb.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": 2347, \"objectURL\": \"http://localhost:8089/media/uploads2025/12/melb.jpg\", \"created_at\": \"\", \"description\": \"\"}], \"imagesData\": [{\"id\": null, \"file\": {\"name\": \"melb.jpg\", \"size\": 268646, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/phpue5j9cq26374cTq3KjD\", \"full_path\": \"melb.jpg\"}, \"name\": \"melb.jpg\", \"size\": 268646, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/12/melb.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": 2347, \"objectURL\": \"http://localhost:8089/media/uploads2025/12/melb.jpg\", \"created_at\": \"\", \"description\": \"\"}]}, {\"name\": \"linkText\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": \"Buy Now\", \"imagesData\": []}, {\"name\": \"linkUrl\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": \"#\", \"imagesData\": []}]',
        NULL,
        NULL,
        '',
        '',
        '',
        ''
    ),
    (
        NULL,
        'items',
        '25',
        NULL,
        '1',
        '1',
        '1',
        '[{\"name\": \"title\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": \"Shop Now\", \"imagesData\": []}, {\"name\": \"image\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"FileUpload\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": [{\"id\": null, \"file\": {\"name\": \"explore-1.png\", \"size\": 361567, \"type\": \"image/png\", \"error\": 0, \"tmp_name\": \"/tmp/php36emci8n3qc9beeZFM9\", \"full_path\": \"explore-1.png\"}, \"name\": \"explore-1.png\", \"size\": 361567, \"type\": \"image/png\", \"image\": \"/media/uploads2025/12/explore-1.png\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": 2348, \"objectURL\": \"http://localhost:8089/media/uploads2025/12/explore-1.png\", \"created_at\": \"\", \"description\": \"\"}], \"imagesData\": [{\"id\": null, \"file\": {\"name\": \"explore-1.png\", \"size\": 361567, \"type\": \"image/png\", \"error\": 0, \"tmp_name\": \"/tmp/php36emci8n3qc9beeZFM9\", \"full_path\": \"explore-1.png\"}, \"name\": \"explore-1.png\", \"size\": 361567, \"type\": \"image/png\", \"image\": \"/media/uploads2025/12/explore-1.png\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": 2348, \"objectURL\": \"http://localhost:8089/media/uploads2025/12/explore-1.png\", \"created_at\": \"\", \"description\": \"\"}]}, {\"name\": \"linkText\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": \"Buy Now\", \"imagesData\": []}, {\"name\": \"linkUrl\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"isNew\": true, \"value\": \"#\", \"imagesData\": []}]',
        NULL,
        NULL,
        '',
        '',
        '',
        ''
    );

-- 12-12-2025

INSERT INTO
    `component` (
        `component_id`,
        `name`,
        `section_title`,
        `section_subtitle`,
        `section_link`,
        `title`,
        `subtitle`,
        `description`,
        `image`,
        `images`,
        `links`,
        `buttons`,
        `template`,
        `active`,
        `model`
    )
VALUES (
        90,
        'productspecifications',
        '',
        '',
        '',
        '',
        '',
        '',
        '[]',
        '[]',
        '[]',
        '[]',
        '',
        '1',
        NULL
    );

INSERT INTO
    `component_item` (
        `component_item_id`,
        `property_name`,
        `component_id`,
        `model`,
        `item_count`,
        `is_recent`,
        `is_featured`,
        `fields`,
        `model_id`,
        `related_models`,
        `title`,
        `subtitle`,
        `link_text`,
        `description`
    )
VALUES (
        NULL,
        'items',
        '90',
        'product',
        '0',
        '0',
        '0',
        '[\"`product`.`product_id`\", \"`product`.`product_code`\", \"`product`.`specifications`\", \"`product`.`image`\", \"`product_resource`.`resource_type`\", \"`product_resource`.`design_resource_id`\", \"`product_resource`.`product_id`\"]',
        NULL,
        '[{\"name\": \"ProductContent\", \"type\": \"product_content\", \"class\": \"App\\\\Core\\\\Models\\\\Product\\\\ProductContent\", \"model\": \"product_content.product_id\", \"source\": \"product.product_id\", \"joinType\": \"LEFT\", \"model_id\": 1, \"joinFields\": [\"`product_content`.`product_id`\", \"`product_content`.`language_id`\", \"`product_content`.`name`\", \"`product_content`.`slug`\", \"`product_content`.`content`\", \"`product_content`.`tag`\", \"`product_content`.`meta_title`\", \"`product_content`.`meta_description`\", \"`product_content`.`meta_keywords`\", \"`product_content`.`icon`\"], \"fieldsExist\": true}, {\"name\": \"ProductResource\", \"type\": \"product_resource\", \"class\": \"App\\\\Core\\\\Models\\\\Product\\\\ProductResource\", \"model\": \"product_resource.product_id\", \"source\": \"product.product_id\", \"joinType\": \"LEFT\", \"model_id\": 18, \"joinFields\": [\"`product_resource`.`product_resource_id`\", \"`product_resource`.`product_id`\", \"`product_resource`.`design_resource_id`\", \"`product_resource`.`resource_type`\", \"`product_resource`.`sort_order`\", \"`product_resource`.`active_status`\", \"`product_resource`.`created_at`\", \"`product_resource`.`updated_at`\", \"`product_resource`.`deleted_at`\"], \"fieldsExist\": true}, {\"name\": \"DesignResource\", \"type\": \"design_resource\", \"class\": \"App\\\\Core\\\\Models\\\\Design\\\\DesignResource\", \"model\": \"design_resource.design_resource_id\", \"source\": \"product_resource.design_resource_id\", \"joinType\": \"LEFT\", \"model_id\": 19}, {\"name\": \"DesignResourceDocument\", \"type\": \"design_resource_document\", \"class\": \"App\\\\Core\\\\Models\\\\Design\\\\DesignResourceDocument\", \"model\": \"design_resource_document.design_resource_id\", \"source\": \"design_resource.design_resource_id\", \"joinType\": \"LEFT\", \"model_id\": 21}]',
        'Product Specification Test',
        '',
        NULL,
        ''
    );

ALTER TABLE `user_address`
ADD COLUMN (
    `is_billing` TINYINT(1) NOT NULL DEFAULT 0,
    `is_shipping` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL
);

ALTER TABLE `mvc`.`user_address`
ADD UNIQUE `uk_user_id_is_shipping_is_billing` (
    `user_id`,
    `is_billing`,
    `is_shipping`
);

ALTER TABLE `user_address`
CHANGE `last_name` `last_name` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL;

ALTER TABLE `user`
CHANGE `user_id` `user_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
CHANGE `first_name` `first_name` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
CHANGE `last_name` `last_name` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
CHANGE `url` `url` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
CHANGE `display_name` `display_name` VARCHAR(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
CHANGE `avatar` `avatar` VARCHAR(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
CHANGE `token` `token` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
CHANGE `created_at` `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
CHANGE `updated_at` `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;

--  test case
ALTER TABLE user_address DROP FOREIGN KEY fk_user_address_user;

-- remove variant_item_id from item_option table
ALTER TABLE `item_option` DROP COLUMN `variant_item_id`;

-- 21-12-2025
ALTER TABLE `vendor`
CHANGE `vendor_id` `vendor_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
ADD COLUMN `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
ADD COLUMN `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN `deleted_at` DATETIME DEFAULT NULL;

ALTER TABLE `vendor` CHANGE `image` `image` JSON NULL DEFAULT NULL;

ALTER TABLE `product_variant`
ADD COLUMN `image` JSON after `variant_description`;

-- 23-12-2025  same as pinboard and quote table
ALTER TABLE `order`
ADD COLUMN `customer_id` INT UNSIGNED NOT NULL AFTER `invoice_no`,
ADD CONSTRAINT `fk_order_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 29-12-2025
SELECT
    ti.taxonomy_item_id AS category_id,
    ti.parent_id AS parent_id,
    ti.name AS product_category,
    txp.name AS parent_name,
    SUM(oi.total_price) AS total_amount
FROM
    order_items oi
    JOIN product p ON p.product_id = oi.product_id
    JOIN product_to_taxonomy_item ptx ON p.product_id = ptx.product_id
    JOIN taxonomy_item ti ON ti.taxonomy_item_id = ptx.taxonomy_item_id
    LEFT JOIN taxonomy_item txp ON txp.taxonomy_item_id = ti.parent_id
    JOIN taxonomy t ON t.taxonomy_id = ti.taxonomy_id
WHERE
    t.taxonomy_id = 1
GROUP BY
    -- ti.taxonomy_item_id,
    ti.parent_id,
    txp.name
ORDER BY total_amount DESC
LIMIT 3

-- 31-12-2025
CREATE TABLE `timezone` (
    `country_code` char(3) NOT NULL,
    `timezone` varchar(125) NOT NULL DEFAULT '',
    `gmt_offset` float(10, 2) DEFAULT NULL,
    `dst_offset` float(10, 2) DEFAULT NULL,
    `raw_offset` float(10, 2) DEFAULT NULL,
    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`country_code`, `timezone`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

-- 03-01-2026
CREATE TABLE `length_type_content` (
    `length_type_id` int(10) unsigned NOT NULL,
    `language_id` int(10) unsigned NOT NULL,
    `name` varchar(30) NOT NULL,
    `unit` varchar(4) NOT NULL,
    PRIMARY KEY (
        `length_type_id`,
        `language_id`
    ),
    UNIQUE KEY `uq_length_type_content_language_id_name` (`language_id`, `name`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

ALTER TABLE `weight_type`
CHANGE `deleted_at` `deleted_at` TIMESTAMP NULL DEFAULT NULL;

ALTER TABLE `site`
ADD `other_settings` JSON NULL DEFAULT NULL AFTER `seo_settings`;

-- 05-01-2026
-- Add login_token to user table
CREATE TABLE `pinboard_item` (
    `pinboard_item_id` int unsigned NOT NULL AUTO_INCREMENT,
    `language_id` int unsigned NOT NULL,
    `uuid` char(36) NOT NULL,
    `pinboard_id` int unsigned NOT NULL,
    `product_id` int unsigned DEFAULT NULL,
    `project_id` int unsigned DEFAULT NULL,
    `media_id` int unsigned DEFAULT NULL,
    `comment_id` int unsigned DEFAULT NULL,
    `description` varchar(500) NOT NULL,
    `quantity` int NOT NULL DEFAULT '0',
    `unit_price` decimal(13, 2) NOT NULL DEFAULT '0.00',
    `total_price` decimal(13, 2) NOT NULL DEFAULT '0.00',
    `photo` varchar(255) DEFAULT NULL,
    `sort_order` int NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (
        `pinboard_item_id`,
        `language_id`
    ),
    UNIQUE KEY `uuid` (`uuid`),
    KEY `pinboard_id` (`pinboard_id`),
    KEY `product_id` (`product_id`)
) ENGINE = InnoDB AUTO_INCREMENT = 51 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci

ALTER TABLE `pinboard_item`
ADD COLUMN `model_id` int unsigned NULL AFTER `pinboard_id`,
ADD COLUMN `model_type` varchar(50) NULL AFTER `model_id`;

ALTER TABLE `pinboard_item`
ADD UNIQUE KEY `uk_pinboard_item_model_id_model_type` (`model_id`, `model_type`);

ALTER TABLE `pinboard_item`
ADD COLUMN comments text NULL DEFAULT NULL AFTER `description`;

-- 06-01-2026
ALTER TABLE pinboard
ADD COLUMN pinboard_name varchar(255) DEFAULT null AFTER job_title;

-- 06-01-2026

CREATE TABLE `visit_showroom` (
    `visit_showroom_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_id` INT UNSIGNED DEFAULT NULL,
    `showroom_id` INT UNSIGNED NOT NULL,
    `tour_type` VARCHAR(50) DEFAULT NULL,
    `date` DATE DEFAULT NULL,
    `time_zone` VARCHAR(50) DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`visit_showroom_id`),
    CONSTRAINT `fk_visit_customer` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_visit_showroom` FOREIGN KEY (`showroom_id`) REFERENCES `showrooms` (`showrooms_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    KEY `idx_visit_customer_id` (`customer_id`),
    KEY `idx_visit_showroom_id` (`showroom_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- 07-01-2026
SELECT pi.*
FROM
    pinboard_item AS pi
    INNER JOIN pinboard AS pb ON pb.pinboard_id = pi.pinboard_id
WHERE
    pb.user_id = 93
    AND pb.pinboard_status_id = 1
ORDER BY pi.sort_order ASC, pi.pinboard_item_id ASC;

-- 08-01-2026
-- dummy data for models
INSERT INTO
    product_resource (
        product_id,
        design_resource_id,
        resource_type,
        sort_order,
        active_status
    )
SELECT
    p.product_id,
    d.design_resource_id,
    'models' AS resource_type,
    (d.design_resource_id - 32) AS sort_order,
    1 AS active_status
FROM (
        SELECT product_id
        FROM product
        WHERE
            product_id BETWEEN 1 AND 100
    ) p
    JOIN (
        SELECT design_resource_id
        FROM design_resource
        WHERE
            design_resource_id BETWEEN 32 AND 130
    ) d ON p.product_id = (
        (d.design_resource_id - 32) % 100
    ) + 1;

-- dummy data for documents
INSERT INTO
    product_resource (
        product_id,
        design_resource_id,
        resource_type,
        sort_order,
        active_status
    )
SELECT
    p.product_id,
    d.design_resource_id,
    'documents' AS resource_type,
    (d.design_resource_id - 1) AS sort_order,
    1 AS active_status
FROM (
        SELECT product_id
        FROM product
        WHERE
            product_id BETWEEN 1 AND 100
    ) p
    JOIN (
        SELECT design_resource_id
        FROM design_resource
        WHERE
            design_resource_id BETWEEN 1 AND 2
    ) d ON p.product_id = (
        (d.design_resource_id - 1) % 100
    ) + 1;

-- 10-01-2026
ALTER TABLE product_image
ADD COLUMN media_id int DEFAULT NULL after image;

ALTER TABLE project_image
ADD COLUMN media_id int DEFAULT NULL after image;

ALTER TABLE post_image
ADD COLUMN media_id int DEFAULT NULL after image;

ALTER TABLE project_section_images
ADD COLUMN media_id int DEFAULT NULL after image;

-- product image dummy data

INSERT INTO
    product_image (
        product_id,
        image,
        media_id,
        sort_order,
        image_link,
        status,
        way_points
    )
SELECT
    p.product_id,
    JSON_OBJECT(
        'original',
        CONCAT(
            '/images/product_',
            p.product_id,
            '_',
            m.media_id,
            '.jpg'
        ),
        'thumb',
        CONCAT(
            '/images/product_',
            p.product_id,
            '_',
            m.media_id,
            '_thumb.jpg'
        )
    ) AS image,
    m.media_id,
    (m.media_id - 2317) AS sort_order,
    CONCAT(
        'https://example.com/product/',
        p.product_id,
        '/image/',
        m.media_id
    ) AS image_link,
    JSON_OBJECT('active', true) AS status,
    JSON_ARRAY() AS way_points
FROM (
        SELECT product_id
        FROM product
        WHERE
            product_id BETWEEN 1 AND 30
    ) p
    JOIN (
        SELECT media_id
        FROM media
        WHERE
            media_id BETWEEN 2317 AND 2347
    ) m ON p.product_id = ((m.media_id - 30) % 30) + 1
LIMIT 100;

-- make query for product image param product_id, taxonomy_item_id

-- product image
SELECT m.*
FROM
    media m
    JOIN product_image pi ON pi.media_id = m.media_id
    JOIN product_to_taxonomy_item pti ON pti.product_id = pi.product_id
WHERE
    pti.taxonomy_item_id = 1;

-- project image dummy data
INSERT INTO
    project_image (
        project_id,
        image,
        media_id,
        sort_order,
        image_link,
        status,
        way_points
    )
SELECT
    p.project_id,
    JSON_OBJECT(
        'original',
        CONCAT(
            '/images/project_',
            p.project_id,
            '_',
            m.media_id,
            '.jpg'
        ),
        'thumb',
        CONCAT(
            '/images/project_',
            p.project_id,
            '_',
            m.media_id,
            '_thumb.jpg'
        )
    ) AS image,
    m.media_id,
    (m.media_id - 2317) AS sort_order,
    CONCAT(
        'https://example.com/project/',
        p.project_id,
        '/image/',
        m.media_id
    ) AS image_link,
    JSON_OBJECT('active', true) AS status,
    JSON_ARRAY() AS way_points
FROM (
        SELECT project_id
        FROM project
        WHERE
            project_id BETWEEN 1 AND 54
    ) p
    JOIN (
        SELECT media_id
        FROM media
        WHERE
            media_id BETWEEN 2317 AND 2415
    ) m ON p.project_id = ((m.media_id - 2317) % 54) + 1
LIMIT 100;

-- dummy project to taxonomy item
INSERT INTO
    project_to_taxonomy_item (project_id, taxonomy_item_id)
SELECT p.project_id, ti.taxonomy_item_id
FROM (
        SELECT project_id
        FROM project
        WHERE
            project_id BETWEEN 1 AND 54
    ) p
    JOIN (
        SELECT taxonomy_item_id
        FROM taxonomy_item
        WHERE
            taxonomy_item_id BETWEEN 1 AND 54
    ) ti ON p.project_id = (
        (ti.taxonomy_item_id - 1) % 54
    ) + 1
LIMIT 100;
-- project image query
SELECT m.*
FROM
    media m
    JOIN project_image pi ON pi.media_id = m.media_id
    JOIN project_to_taxonomy_item pti ON pti.project_id = pi.project_id
WHERE
    pi.project_id = 1
    and pti.taxonomy_item_id = 2;

-- post dummy data
INSERT INTO
    post (
        admin_id,
        site_id,
        status,
        image,
        media_id,
        comment_status,
        parent,
        sort_order,
        type,
        comment_count,
        views,
        description,
        description_one,
        description_two,
        description_three,
        keyline_quote,
        feature_image_thumb,
        feature_image,
        image_banner,
        image_thumb,
        main_image_one,
        main_image_two,
        is_featured,
        title,
        created_at,
        updated_at
    )
SELECT
    1 AS admin_id,
    1 AS site_id,
    'publish' AS status,
    JSON_OBJECT(
        'original',
        CONCAT(
            '/uploads/posts/',
            m.media_id,
            '.jpg'
        ),
        'thumb',
        CONCAT(
            '/uploads/posts/thumb_',
            m.media_id,
            '.jpg'
        )
    ) AS image,
    m.media_id,
    'open' AS comment_status,
    0 AS parent,
    (m.media_id - 2317) AS sort_order,
    'post' AS type,
    FLOOR(RAND() * 20) AS comment_count,
    FLOOR(RAND() * 5000) AS views,
    CONCAT(
        'This is dummy description for post ',
        m.media_id
    ) AS description,
    CONCAT(
        'Description one for post ',
        m.media_id
    ) AS description_one,
    CONCAT(
        'Description two for post ',
        m.media_id
    ) AS description_two,
    CONCAT(
        'Description three for post ',
        m.media_id
    ) AS description_three,
    CONCAT(
        'Keyline quote for post ',
        m.media_id
    ) AS keyline_quote,
    JSON_OBJECT(
        'src',
        CONCAT(
            '/uploads/feature/thumb_',
            m.media_id,
            '.jpg'
        )
    ) AS feature_image_thumb,
    JSON_OBJECT(
        'src',
        CONCAT(
            '/uploads/feature/',
            m.media_id,
            '.jpg'
        )
    ) AS feature_image,
    JSON_OBJECT(
        'src',
        CONCAT(
            '/uploads/banner/',
            m.media_id,
            '.jpg'
        )
    ) AS image_banner,
    JSON_OBJECT(
        'src',
        CONCAT(
            '/uploads/thumb/',
            m.media_id,
            '.jpg'
        )
    ) AS image_thumb,
    JSON_OBJECT(
        'src',
        CONCAT(
            '/uploads/main1/',
            m.media_id,
            '.jpg'
        )
    ) AS main_image_one,
    JSON_OBJECT(
        'src',
        CONCAT(
            '/uploads/main2/',
            m.media_id,
            '.jpg'
        )
    ) AS main_image_two,
    IF(m.media_id % 10 = 0, 1, 0) AS is_featured,
    CONCAT(
        'Dummy Post Title ',
        m.media_id
    ) AS title,
    DATE_SUB(
        NOW(),
        INTERVAL(m.media_id - 2317) DAY
    ) AS created_at,
    NOW() AS updated_at
FROM (
        SELECT media_id
        FROM media
        WHERE
            media_id BETWEEN 2317 AND 2416
    ) m
LIMIT 100;

-- post to taxonomy item dummy data
INSERT INTO
    post_to_taxonomy_item (post_id, taxonomy_item_id)
SELECT p.post_id, ti.taxonomy_item_id
FROM (
        SELECT post_id
        FROM post
        WHERE
            post_id BETWEEN 198 AND 274
    ) p
    JOIN (
        SELECT taxonomy_item_id
        FROM taxonomy_item
        WHERE
            taxonomy_item_id BETWEEN 1 AND 54
    ) ti ON p.post_id = (
        (ti.taxonomy_item_id - 1) % 77
    ) + 198
LIMIT 100;

-- model query
select m.*
from
    design_resource_document drd
    join media m on m.media_id = drd.media_id
    join design_resource dr on dr.design_resource_id = drd.design_resource_id
    -- if context is product then join product_resource
    join product_resource pr on pr.design_resource_id = drd.design_resource_id
    join product_to_taxonomy_item pti on pti.product_id = pr.product_id
    -- 
where
    dr.resource_type = 'documents'
    -- if context is project 
    and pr.product_id = 1
    and pti.taxonomy_item_id = 1;

-- document same to models
-- finishes
select *
from
    design_resource_document drd
    join media m on m.media_id = drd.media_id
    join design_resource dr on dr.design_resource_id = drd.design_resource_id
where
    dr.resource_type = 'finishes';

-- textiles
select *
from
    design_resource_document drd
    join media m on m.media_id = drd.media_id

ALTER TABLE product ADD COLUMN media_id int DEFAULT NULL after image;

ALTER TABLE media ADD UNIQUE INDEX uk_media_path (`path`);

-- 17-01-2026
-- new table create comment_photo table
CREATE TABLE comment_photo (
    comment_photo_id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    comment_id INT(20) UNSIGNED NOT NULL,
    model_id INT(20) UNSIGNED DEFAULT NULL COMMENT 'product_id, project_id, post_id, pinboard_item_id, showroom_id',
    model_type VARCHAR(50) DEFAULT NULL COMMENT 'product, project, post, pinboard item, showrooms',
    media_id INT(20) UNSIGNED NOT NULL,
    image JSON NOT NULL,
    sort_order INT(10) UNSIGNED NOT NULL DEFAULT 0,
    active_status TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (comment_photo_id),
    KEY idx_comment_photo_sort_order (sort_order),
    KEY idx_comment_photo_active_status (active_status),
    CONSTRAINT fk_comment_photo_comment FOREIGN KEY (comment_id) REFERENCES comment (comment_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_comment_photo_media FOREIGN KEY (media_id) REFERENCES media (media_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 17-01-2026
ALTER TABLE `comment`
ADD COLUMN `model_id` INT(20) UNSIGNED DEFAULT NULL COMMENT 'product_id, project_id, post_id, pinboard_item_id, showroom_id' AFTER `post_id`;

ALTER TABLE `comment`
ADD COLUMN `model_type` VARCHAR(50) DEFAULT NULL COMMENT 'product, project, post, pinboard item, showrooms' AFTER `model_id`;

ALTER TABLE `comment`
CHANGE `post_id` `post_id` INT UNSIGNED NULL DEFAULT NULL;
-- 17-01-2026
ALTER TABLE `pinboard_item`
ADD COLUMN `options` JSON DEFAULT NULL AFTER `description`;
-- 17-01-2026
ALTER TABLE `product_image`
ADD `type` VARCHAR(191) NULL DEFAULT NULL AFTER `way_points`;

-- 18-01-2026 abdullah

select design_resource_document.*
from
    design_resource_document
    join design_resource on design_resource_document.design_resource_id = design_resource.design_resource_id
    join product_resource on product_resource.design_resource_id = design_resource.design_resource_id
where
    design_resource.resource_type = 'models'
    and product_resource.product_id = 1;

ALTER TABLE project
ADD COLUMN preview_text TEXT DEFAULT NULL after description;

-- order page
select *
from
    `order`
    join order_items on order_items.order_id = `order`.order_id
    join product on product.product_id = order_items.product_id
where
    order.customer_id = 16
group by
    order.order_id;

-- detail
select * from order_items where order_id = 4;

select * from quote where customer_id = 16;

-- quote details
select * from quote_item where quote_id = 1;

-- Insert 4 customers with customer_id 2, 3, 4, 5
-- 31-01-2026
INSERT INTO
    `customer` (
        `customer_id`,
        `company_id`,
        `user_id`,
        `organisation_id`,
        `uuid`,
        `org_code`,
        `name`,
        `rating`,
        `abn`,
        `segment_id`,
        `term_id`,
        `credit_limit`,
        `caution_bad_payer`,
        `is_active`,
        `date_last_invoice`,
        `website`,
        `event_group`,
        `default_price_list`,
        `deposit_percentage`,
        `gst`,
        `is_gmail_lead`,
        `gmail_Id`,
        `phone`,
        `address`,
        `bpay_ref`,
        `created_at`,
        `updated_at`,
        `billing_first_name`,
        `billing_last_name`,
        `billing_company`,
        `billing_address_1`,
        `billing_address_2`,
        `billing_city`,
        `billing_post_code`,
        `billing_country_id`,
        `billing_region`,
        `billing_region_id`,
        `payment_method`,
        `payment_data`,
        `payment_status_id`,
        `shipping_first_name`,
        `shipping_last_name`,
        `shipping_company`,
        `shipping_address_1`,
        `shipping_address_2`,
        `shipping_city`,
        `shipping_post_code`,
        `shipping_country`,
        `shipping_country_id`,
        `shipping_region`,
        `shipping_region_id`,
        `shipping_method`,
        `shipping_data`,
        `shipping_status_id`
    )
VALUES (
        2,
        1,
        1,
        1,
        UUID_TO_BIN(UUID()),
        'ORG-002',
        'Tech Solutions Australia',
        4.5,
        '12345678901',
        1,
        1,
        50000.00,
        0,
        1,
        CURDATE(),
        'https://techsolutions.com.au',
        'Enterprise',
        1,
        10.00,
        10.00,
        0,
        'contact@techsolutions.com.au',
        '+61 2 9876 5432',
        '123 Tech Street, Sydney NSW 2000',
        'BPAY002',
        NOW(),
        NOW(),
        'Sarah',
        'Johnson',
        'Tech Solutions Australia',
        '123 Tech Street',
        'Level 5',
        'Sydney',
        '2000',
        36,
        'NSW',
        101,
        'Credit Card',
        'VISA - Primary Payment Method',
        1,
        'Sarah',
        'Johnson',
        'Tech Solutions Australia',
        '123 Tech Street',
        'Level 5',
        'Sydney',
        '2000',
        'Australia',
        36,
        'NSW',
        101,
        'Standard Shipping',
        'Tracking: TECH002',
        1
    ),
    (
        3,
        1,
        1,
        2,
        UUID_TO_BIN(UUID()),
        'ORG-003',
        'Global Manufacturing Co',
        4.8,
        '23456789012',
        1,
        1,
        100000.00,
        0,
        1,
        CURDATE(),
        'https://globalmfg.com.au',
        'Manufacturing',
        1,
        15.00,
        10.00,
        0,
        'info@globalmfg.com.au',
        '+61 3 8765 4321',
        '456 Industrial Way, Melbourne VIC 3000',
        'BPAY003',
        NOW(),
        NOW(),
        'Michael',
        'Chen',
        'Global Manufacturing Co',
        '456 Industrial Way',
        'Factory 3',
        'Melbourne',
        '3000',
        36,
        'VIC',
        102,
        'Bank Transfer',
        'Account: BSB 123-456',
        1,
        'Michael',
        'Chen',
        'Global Manufacturing Co',
        '456 Industrial Way',
        'Factory 3',
        'Melbourne',
        '3000',
        'Australia',
        36,
        'VIC',
        102,
        'Express Shipping',
        'Tracking: GLOB003',
        1
    ),
    (
        4,
        1,
        1,
        3,
        UUID_TO_BIN(UUID()),
        'ORG-004',
        'Design Studio Melbourne',
        4.2,
        '34567890123',
        1,
        1,
        25000.00,
        0,
        1,
        CURDATE(),
        'https://designstudio.melbourne',
        'Creative',
        1,
        5.00,
        10.00,
        0,
        'hello@designstudio.melbourne',
        '+61 3 7654 3210',
        '789 Creative Lane, Melbourne VIC 3000',
        'BPAY004',
        NOW(),
        NOW(),
        'Emma',
        'Wilson',
        'Design Studio Melbourne',
        '789 Creative Lane',
        'Studio A',
        'Melbourne',
        '3000',
        36,
        'VIC',
        102,
        'Credit Card',
        'MASTERCARD - Business Account',
        1,
        'Emma',
        'Wilson',
        'Design Studio Melbourne',
        '789 Creative Lane',
        'Studio A',
        'Melbourne',
        '3000',
        'Australia',
        36,
        'VIC',
        102,
        'Standard Shipping',
        'Tracking: DSGN004',
        1
    ),
    (
        5,
        1,
        1,
        4,
        UUID_TO_BIN(UUID()),
        'ORG-005',
        'Retail Solutions Brisbane',
        4.6,
        '45678901234',
        1,
        1,
        75000.00,
        0,
        1,
        CURDATE(),
        'https://retailsolutions.com.au',
        'Retail',
        1,
        20.00,
        10.00,
        0,
        'sales@retailsolutions.com.au',
        '+61 7 6543 2109',
        '321 Retail Boulevard, Brisbane QLD 4000',
        'BPAY005',
        NOW(),
        NOW(),
        'James',
        'Brown',
        'Retail Solutions Brisbane',
        '321 Retail Boulevard',
        'Shop 15',
        'Brisbane',
        '4000',
        36,
        'QLD',
        103,
        'Credit Card',
        'AMEX - Corporate Card',
        1,
        'James',
        'Brown',
        'Retail Solutions Brisbane',
        '321 Retail Boulevard',
        'Shop 15',
        'Brisbane',
        '4000',
        'Australia',
        36,
        'QLD',
        103,
        'Priority Shipping',
        'Tracking: RETS005',
        1
    );

-- 20-01-2026
alter table order_items
add column (
    item_id BIGINT UNSIGNED NULL,
    km_item_id INT UNSIGNED NULL DEFAULT '0',
    options JSON NULL
);

-- add foreign key to item table
alter table order_items
add foreign key (item_id) references item (item_id);

truncate table order_items;

INSERT INTO
    `order_items` (
        `language_id`,
        `uuid`,
        `order_id`,
        `item_id`,
        `product_id`,
        `description`,
        `quantity`,
        `unit_price`,
        `total_price`,
        `photo`,
        `options`,
        `sort_order`,
        `created_at`,
        `updated_at`
    )
SELECT
    1 AS language_id,
    UUID() AS uuid,
    o.order_id,
    FLOOR(1 + RAND() * 100) AS item_id,
    FLOOR(1 + RAND() * 100) AS product_id,
    CONCAT(
        'Product description for product ',
        FLOOR(1 + RAND() * 100)
    ) AS description,
    qty.quantity,
    price.unit_price,
    ROUND(
        qty.quantity * price.unit_price,
        2
    ) AS total_price,
    CONCAT(
        'product_',
        FLOOR(1 + RAND() * 100),
        '.jpg'
    ) AS photo,
    JSON_OBJECT(
        'quantity',
        qty.quantity,
        'product_id',
        FLOOR(1 + RAND() * 100),
        'product_code',
        'kove',
        'description',
        'Kove is engineered for continuous use in demanding work environments.',
        'image',
        '/media/Products/image/Kove_Image.jpg',
        'variant',
        JSON_OBJECT(
            'variant_id',
            FLOOR(1 + RAND() * 100),
            'item',
            JSON_OBJECT(
                'item_id',
                CONCAT(
                    'KV',
                    LPAD(
                        FLOOR(1 + RAND() * 100),
                        3,
                        '0'
                    )
                ),
                'options',
                JSON_ARRAY(
                    JSON_OBJECT(
                        'product_option_group_id',
                        FLOOR(1 + RAND() * 100),
                        'product_option_id',
                        FLOOR(1 + RAND() * 100),
                        'option_name',
                        'Adjustable Arms',
                        'subOption',
                        JSON_OBJECT()
                    ),
                    JSON_OBJECT(
                        'product_option_group_id',
                        FLOOR(1 + RAND() * 100),
                        'product_option_id',
                        FLOOR(1 + RAND() * 100),
                        'option_name',
                        'Black Fabric',
                        'subOption',
                        JSON_OBJECT()
                    ),
                    JSON_OBJECT(
                        'product_option_group_id',
                        FLOOR(1 + RAND() * 100),
                        'product_option_id',
                        FLOOR(1 + RAND() * 100),
                        'option_name',
                        'Black Mesh',
                        'subOption',
                        JSON_OBJECT()
                    ),
                    JSON_OBJECT(
                        'product_option_group_id',
                        FLOOR(1 + RAND() * 100),
                        'product_option_id',
                        FLOOR(1 + RAND() * 100),
                        'option_name',
                        'Black',
                        'subOption',
                        JSON_OBJECT()
                    )
                )
            )
        )
    ) AS options,
    item_no.sort_order,
    o.created_at,
    o.updated_at
FROM `order` o
    JOIN (
        /* generate 1–3 items per order */
        SELECT 1 AS sort_order
        UNION ALL
        SELECT 2
        UNION ALL
        SELECT 3
    ) item_no
    JOIN (
        SELECT FLOOR(1 + RAND() * 5) AS quantity
    ) qty
    JOIN (
        SELECT ROUND(50 + RAND() * 950, 2) AS unit_price
    ) price
WHERE
    o.order_id BETWEEN 1 AND 100
    AND item_no.sort_order <= FLOOR(1 + RAND() * 3);

select * from item where item_id = 18;

select * from item_option where item_id = 18;

select * from product_option where product_id = 1;

ALTER TABLE `order_items`
CHANGE `order_item_id` `order_items_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;

INSERT INTO
    `logistic_statuses` (
        `language_id`,
        `name`,
        `sort_order`
    )
VALUES (1, 'Unscheduled', 1),
    (1, 'Scheduled', 2),
    (1, 'Job Completed', 3),
    (
        1,
        'Job Unable To Complete',
        4
    );

INSERT INTO
    `logistic_types` (
        `uuid`,
        `name`,
        `short`,
        `type`,
        `track_resource`,
        `forecasted_rate`,
        `is_active`,
        `created_at`,
        `updated_at`
    )
VALUES (
        '550e8400-e29b-41d4-a716-446655440100',
        'Standard Delivery',
        'STD',
        'Delivery',
        1,
        50.0000,
        1,
        NOW(),
        NOW()
    ),
    (
        '550e8400-e29b-41d4-a716-446655440101',
        'Express Delivery',
        'EXP',
        'Delivery',
        1,
        100.0000,
        1,
        NOW(),
        NOW()
    ),
    (
        '550e8400-e29b-41d4-a716-446655440102',
        'Installation Service',
        'INST',
        'Service',
        0,
        150.0000,
        1,
        NOW(),
        NOW()
    ),
    (
        '550e8400-e29b-41d4-a716-446655440103',
        'Pickup Service',
        'PICK',
        'Service',
        0,
        30.0000,
        1,
        NOW(),
        NOW()
    ),
    (
        '550e8400-e29b-41d4-a716-446655440104',
        'Heavy Goods Transport',
        'HGT',
        'Transport',
        1,
        200.0000,
        1,
        NOW(),
        NOW()
    ),
    (
        '550e8400-e29b-41d4-a716-446655440105',
        'Fragile Goods Handling',
        'FRAG',
        'Handling',
        1,
        120.0000,
        1,
        NOW(),
        NOW()
    );

INSERT INTO
    `logistic_dates` (
        `uuid`,
        `order_id`,
        `logistic_types_id`,
        `customer_id`,
        `logistic_statuses_id`,
        `date`,
        `sort_order`,
        `mins`,
        `drive_mins`,
        `drive_kms`,
        `time_pref`,
        `calc`,
        `expected_start`,
        `expected_end`,
        `actual_start`,
        `actual_end`,
        `actual_mins`,
        `customer_name`,
        `time_block`,
        `address`,
        `latitude`,
        `longitude`,
        `send_email`,
        `email_confirmed`,
        `email_alerted`,
        `load_up`,
        `actual_cost`,
        `actual_cost_updated`,
        `notes`,
        `created_at`,
        `updated_at`
    )
SELECT
    UUID() AS uuid,
    o.order_id,
    FLOOR(1 + RAND() * 6) AS logistic_types_id,
    FLOOR(1 + RAND() * 50) AS customer_id,
    FLOOR(1 + RAND() * 4) AS logistic_statuses_id,
    DATE_ADD(
        CURDATE(),
        INTERVAL FLOOR(RAND() * 30) DAY
    ) AS date,
    FLOOR(1 + RAND() * 5) AS sort_order,
    FLOOR(30 + RAND() * 240) AS mins,
    FLOOR(10 + RAND() * 120) AS drive_mins,
    FLOOR(5 + RAND() * 50) AS drive_kms,
    ELT(
        FLOOR(1 + RAND() * 3),
        'AM',
        'PM',
        'EV'
    ) AS time_pref,
    FLOOR(RAND() * 2) AS calc,
    SEC_TO_TIME(FLOOR(RAND() * 28800 + 28800)) AS expected_start, -- 8:00 AM to 4:00 PM
    SEC_TO_TIME(FLOOR(RAND() * 28800 + 32400)) AS expected_end, -- 9:00 AM to 5:00 PM
    SEC_TO_TIME(FLOOR(RAND() * 28800 + 28800)) AS actual_start,
    SEC_TO_TIME(FLOOR(RAND() * 28800 + 32400)) AS actual_end,
    FLOOR(RAND() * 480) AS actual_mins,
    CONCAT(
        'Customer ',
        FLOOR(1 + RAND() * 100)
    ) AS customer_name,
    FLOOR(RAND() * 2) AS time_block,
    CONCAT(
        FLOOR(1 + RAND() * 999),
        ' Main Street, City ',
        FLOOR(1 + RAND() * 50)
    ) AS address,
    ROUND(23 + RAND() * 5, 4) AS latitude,
    ROUND(90 + RAND() * 5, 4) AS longitude,
    FLOOR(RAND() * 2) AS send_email,
    FLOOR(RAND() * 2) AS email_confirmed,
    FLOOR(RAND() * 2) AS email_alerted,
    FLOOR(RAND() * 2) AS load_up,
    ROUND(RAND() * 1000, 4) AS actual_cost,
    FLOOR(RAND() * 2) AS actual_cost_updated,
    CONCAT(
        'Note ',
        FLOOR(1 + RAND() * 500)
    ) AS notes,
    NOW() AS created_at,
    NOW() AS updated_at
FROM `order` o
WHERE
    o.order_id BETWEEN 1 AND 100;

-- logistic dates query
SELECT
    `order`.order_id,
    `order`.invoice_no,
    `order`.customer_order_id,
    `order`.order_description,
    `order`.created_at,
    `order`.updated_at,
    logistic_types.name as logistic_type_name,
    logistic_statuses.name as logistic_status_name,
    logistic_dates.date,
    logistic_dates.expected_start,
    logistic_dates.expected_end,
    logistic_dates.actual_start,
    logistic_dates.actual_end
from
    logistic_dates
    join `order` on `order`.order_id = logistic_dates.order_id
    join logistic_types on logistic_types.logistic_types_id = logistic_dates.logistic_types_id
    join logistic_statuses on logistic_statuses.logistic_statuses_id = logistic_dates.logistic_statuses_id
where
    logistic_dates.customer_id = 16;

update quote set quote_status_id = 1;

SELECT
    product_resource.product_resource_id,
    product_resource.design_resource_id,
    product_resource.resource_type,
    product_resource.sort_order,
    product_resource.active_status,
    product.product_id AS id,
    product_content.name,
    product.image,
    product.description,
    product_content.slug,
    taxonomy_item_content.slug AS category_slug
FROM
    product
    INNER JOIN product_content ON product_content.product_id = product.product_id
    INNER JOIN product_to_taxonomy_item ON product_to_taxonomy_item.product_id = product.product_id
    INNER JOIN taxonomy_item_content ON taxonomy_item_content.taxonomy_item_id = product_to_taxonomy_item.taxonomy_item_id
    left join product_resource on product_resource.product_id = product.product_id
WHERE
    product.active = 1
    AND product_resource.resource_type = 'finishes'
    --  and product_resource.design_resource_id = 1
    AND product_content.language_id = 1;

update product set weight = '41';

SELECT product.product_id AS id, product_content.name, product.weight
FROM product
    INNER JOIN product_content ON product_content.product_id = product.product_id
WHERE
    product.active = 1
    AND product_content.language_id = 1
    AND product.weight >= 40
    AND product.weight <= 41;

update product_resource
set
    resource_type = 'finishes'
where
    resource_type = 'models';

-- 27-01-2026
alter table pinboard
add column contact_number json default null after customer_id;

SELECT * FROM `pinboard` WHERE `pinboard_id` = 37

SELECT *
FROM
    `comment_photo`
    JOIN pinboard_item on pinboard_item.pinboard_item_id = comment_photo.model_id;

select *
from
    comment_photo
    join comment on comment.comment_id = comment_photo.comment_id
    join pinboard on pinboard.pinboard_id = comment.model_id
WHERE
    model_type = 'pinboard'
    and comment.model_id = 20;

-- 27-01-2026

CREATE TABLE service_request (
    service_request_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pinboard_id INT UNSIGNED DEFAULT NULL,
    customer_id INT UNSIGNED DEFAULT NULL,
    email VARCHAR(255) DEFAULT NULL,
    content TEXT DEFAULT NULL,
    comment_attachment varchar(191) DEFAULT NULL COMMENT 'comment_photo.image',
    attachments JSON DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_pinboard_id (pinboard_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_email (email)
);

-- 27-01-2026 nazmul
CREATE TABLE comment_upvote (
    comment_upvote_id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    comment_id INT(20) UNSIGNED NOT NULL,
    user_id INT(20) UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (comment_upvote_id),
    CONSTRAINT fk_comment_upvote_comment FOREIGN KEY (comment_id) REFERENCES comment (comment_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_comment_upvote_user FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 28-01-2026
alter table visit_showroom
add column pinboard_id int unsigned default null after customer_id;

-- 28-01-2026 nazmul
CREATE TABLE IF NOT EXISTS product_accessories (
    product_accessories_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_product_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NOT NULL,
    item_id BIGINT UNSIGNED NOT NULL,
    price DECIMAL(10, 5) NOT NULL DEFAULT '0.00000',
    active_status tinyint(1) NOT NULL DEFAULT 1,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at datetime DEFAULT NULL,
    PRIMARY KEY (product_accessories_id),
    FOREIGN KEY (parent_product_id) REFERENCES product (product_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES product (product_id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (item_id) REFERENCES item (item_id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY uq_parent_product_id_product_id_item_id (
        parent_product_id,
        product_id,
        item_id
    ),
    INDEX idx_product_accessories_active_status (active_status)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- 29-01-2026
alter table product_content
add column title varchar(191) not null default '' after slug;

alter table product_content
add column tag_line varchar(500) not null default '' after title;

update pinboard
set
    contact_number = json_array(
        json_object(
            'name',
            '',
            'contact_number',
            '01849378511'
        )
    )
where
    pinboard_id = 33;

SELECT * from pinboard where pinboard_id = 59;

-- 31-01-2026 abdullah
SELECT
    pa.product_accessories_id,
    pa.parent_product_id,
    ppc.name AS parent_product_name,
    pa.product_id,
    apc.name AS product_name,
    i.item_id,
    i.item_code,
    pa.price,
    pa.created_at
FROM
    product_accessories AS pa
    -- Parent product
    INNER JOIN product AS pp ON pp.product_id = pa.parent_product_id
    INNER JOIN product_content AS ppc ON ppc.product_id = pp.product_id
    -- Accessory product
    INNER JOIN product AS ap ON ap.product_id = pa.product_id
    INNER JOIN product_content AS apc ON apc.product_id = ap.product_id
    -- Item
    INNER JOIN item AS i ON i.item_id = pa.item_id
    -- Optional filters (recommended)
WHERE
    pa.deleted_at IS NULL
    AND pa.active_status = 1
    AND pa.parent_product_id = 133;

-- 31-01-2026 abdullah
-- create a pinboard_item_accessories table {pinboard_id, pinboard_item_id, accessories_product_id, accessories_item_id} → product_accessories pinboard_item_accessories table.
CREATE TABLE `pinboard_item_accessories` (
    `pinboard_item_accessories_id` int unsigned NOT NULL AUTO_INCREMENT,
    `pinboard_id` int unsigned NOT NULL,
    `pinboard_item_id` int unsigned NOT NULL,
    `accessories_product_id` int unsigned NOT NULL,
    `accessories_item_id` int unsigned NOT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (
        `pinboard_item_accessories_id`
    ),
    KEY `idx_pinboard_item_id` (`pinboard_item_id`),
    KEY `idx_accessories_product_id` (`accessories_product_id`),
    KEY `idx_accessories_item_id` (`accessories_item_id`),
    KEY `pinboard_id` (`pinboard_id`),
    CONSTRAINT `pinboard_item_accessories_ibfk_1` FOREIGN KEY (`pinboard_id`) REFERENCES `pinboard` (`pinboard_id`),
    CONSTRAINT `pinboard_item_accessories_ibfk_4` FOREIGN KEY (`accessories_product_id`) REFERENCES `product` (`product_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci

-- 01-02-2026
ALTER TABLE `project`
ADD `banner_way_points` JSON NULL DEFAULT NULL AFTER `image`;

ALTER TABLE `product`
ADD `banner_way_points` JSON NULL DEFAULT NULL AFTER `banner_image`;

ALTER TABLE `post`
ADD `banner_way_points` JSON NULL DEFAULT NULL AFTER `image`;

ALTER TABLE `project`
ADD COLUMN is_featured tinyint(1) NOT NULL DEFAULT 0;

-- admin component
update heroproduct
set
    banner_way_points
update heroproject
set
    banner_way_points
update blogdetailshero
set
    banner_way_points

-- alter otp_code and otp_created_at and otp_expiry_time in user table
alter table user
add column otp_code varchar(10) null default null after email;

alter table user
add column otp_created_at datetime null default null after otp_code;

alter table user
add column otp_expiry_time datetime null default null after otp_created_at;

ALTER TABLE `pinboard_item`
CHANGE `comments` `comments` JSON DEFAULT NULL;

ALTER TABLE `customer`
ADD COLUMN `company_name` VARCHAR(255) DEFAULT NULL;
-- 04-02-2026 nazmul
ALTER TABLE `component`
ADD `banner_way_points` JSON NULL DEFAULT NULL AFTER `model`;

ALTER TABLE `showrooms`
ADD `banner_way_points` JSON NULL DEFAULT NULL AFTER `overview_image`;

ALTER TABLE `taxonomy_item`
ADD `banner_way_points` JSON NULL DEFAULT NULL AFTER `name`;

-- 07-02-2026
UPDATE `component_item`
SET
    `title` = 'Product Specification'
WHERE
    `component_item`.`component_item_id` = 212;

-- 08-02-2026
ALTER TABLE `product`
ADD `dimension_image` JSON NULL DEFAULT NULL AFTER `feature_image_three`;

ALTER TABLE `product`
ADD `ocean_plastic_used` TINYINT(1) NOT NULL DEFAULT 0;

SELECT *
FROM `product`
WHERE
    `ocean_plastic_used` = 1
ORDER BY `product`.`ocean_plastic_used` DESC;

-- 09-02-2026
ALTER TABLE `user`
ADD `is_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `email`;

ALTER TABLE `customer`
ADD `is_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `company_name`;

ALTER TABLE `comment`
DROP FOREIGN KEY `fk_comment_post`,
DROP FOREIGN KEY `fk_comment_user`;

ALTER TABLE `comment`
DROP INDEX `fk_comment_user`,
DROP INDEX `post_id`;

-- please update:-
UPDATE `component_item`
SET
    `fields` = '[\"`product`.`description`\", \"`product`.`specifications`\", \"`product`.`product_code`\", \"`product`.`image`\", \"`product_content`.`name`\", \"`product_content`.`slug`\", \"`product_content`.`content`\", \"`product_content`.`meta_title`\", \"`product_content`.`meta_description`\", \"`product`.`banner_image`\", \"`product`.`banner_way_points`\", \"`product`.`product_id`\", \"`product_content`.`tag_line`\"]\r\n'
WHERE
    `component_item`.`component_item_id` = 205;

UPDATE `component_item`
SET
    `fields` = '[\"`post`.`image_banner`\", \"`post_content`.`slug`\", \"`post`.`title`\", \"`post`.`created_at`\", \"`post`.`banner_way_points`\", \"`post`.`post_id`\", \"`post`.`description`\"]\r\n'
WHERE
    `component_item`.`component_item_id` = 207;

-- design resources section
UPDATE `component_item`
SET
    `fields` = '[{\"name\": \"img\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"FileUpload\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": [{\"id\": null, \"file\": {\"name\": \"home_dr-models.jpg\", \"size\": 53819, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/phpd45h4bs1ltke9fBamIa\", \"full_path\": \"home_dr-models.jpg\"}, \"name\": \"home_dr-models.jpg\", \"size\": 53819, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/08/home_dr-models.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"http://localhost:8089/media/uploads2025/08/home_dr-models.jpg\", \"created_at\": \"\", \"description\": \"\"}], \"options\": [], \"imagesData\": [{\"id\": null, \"file\": {\"name\": \"home_dr-models.jpg\", \"size\": 53819, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/phpd45h4bs1ltke9fBamIa\", \"full_path\": \"home_dr-models.jpg\"}, \"name\": \"home_dr-models.jpg\", \"size\": 53819, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/08/home_dr-models.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"http://localhost:8089/media/uploads2025/08/home_dr-models.jpg\", \"created_at\": \"\", \"description\": \"\"}]}, {\"name\": \"title\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"Model Library\", \"options\": [], \"imagesData\": []}, {\"name\": \"description\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \" Download 2D/3D CAD and Revit files to streamline your floor plans and specifications.\", \"options\": [], \"imagesData\": []}, {\"name\": \"link\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"/account/resources/models\", \"options\": [], \"imagesData\": []}]\r\n'
WHERE
    `component_item`.`component_item_id` = 184;

UPDATE `component_item`
SET
    `fields` = '[{\"name\": \"img\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"FileUpload\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": [{\"id\": null, \"file\": {\"name\": \"home_dr-image.jpg\", \"size\": 40851, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/phpm8v7046opgev4TX5yiq\", \"full_path\": \"home_dr-image.jpg\"}, \"name\": \"home_dr-image.jpg\", \"size\": 40851, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/08/home_dr-image.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"http://localhost:8089/media/uploads2025/08/home_dr-image.jpg\", \"created_at\": \"\", \"description\": \"\"}], \"options\": [], \"imagesData\": [{\"id\": null, \"file\": {\"name\": \"home_dr-image.jpg\", \"size\": 40851, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/phpm8v7046opgev4TX5yiq\", \"full_path\": \"home_dr-image.jpg\"}, \"name\": \"home_dr-image.jpg\", \"size\": 40851, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/08/home_dr-image.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"http://localhost:8089/media/uploads2025/08/home_dr-image.jpg\", \"created_at\": \"\", \"description\": \"\"}]}, {\"name\": \"title\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"Image Gallery\", \"options\": [], \"imagesData\": []}, {\"name\": \"description\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"Access our full library of high-resolution project photography and detailed product imagery.\", \"options\": [], \"imagesData\": []}, {\"name\": \"link\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"/account/resources/images\", \"options\": [], \"imagesData\": []}]\r\n'
WHERE
    `component_item`.`component_item_id` = 185;

UPDATE `component_item`
SET
    `fields` = '[{\"name\": \"img\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"FileUpload\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": [{\"id\": null, \"file\": {\"name\": \"home_dr-fabrics.jpg\", \"size\": 134857, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/phpg3g4iru1k6fcbnf1huG\", \"full_path\": \"home_dr-fabrics.jpg\"}, \"name\": \"home_dr-fabrics.jpg\", \"size\": 134857, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/08/home_dr-fabrics.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"http://localhost:8089/media/uploads2025/08/home_dr-fabrics.jpg\", \"created_at\": \"\", \"description\": \"\"}], \"options\": [], \"imagesData\": [{\"id\": null, \"file\": {\"name\": \"home_dr-fabrics.jpg\", \"size\": 134857, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/phpg3g4iru1k6fcbnf1huG\", \"full_path\": \"home_dr-fabrics.jpg\"}, \"name\": \"home_dr-fabrics.jpg\", \"size\": 134857, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/08/home_dr-fabrics.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"http://localhost:8089/media/uploads2025/08/home_dr-fabrics.jpg\", \"created_at\": \"\", \"description\": \"\"}]}, {\"name\": \"title\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"Finishes\", \"options\": [], \"imagesData\": []}, {\"name\": \"description\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"Explore our extensive range of upholstery options, featuring high-performance and sustainable fabrics.\", \"options\": [], \"imagesData\": []}, {\"name\": \"link\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"/account/resources/finishes\", \"options\": [], \"imagesData\": []}\r\n]'
WHERE
    `component_item`.`component_item_id` = 186;

UPDATE `component_item`
SET
    `fields` = '[{\"name\": \"img\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"FileUpload\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": [{\"id\": null, \"file\": {\"name\": \"home_dr-finish.jpg\", \"size\": 128302, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/php1bc6vpc6kl10bjr45I2\", \"full_path\": \"home_dr-finish.jpg\"}, \"name\": \"home_dr-finish.jpg\", \"size\": 128302, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/08/home_dr-finish.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"http://localhost:8089/media/uploads2025/08/home_dr-finish.jpg\", \"created_at\": \"\", \"description\": \"\"}], \"options\": [], \"imagesData\": [{\"id\": null, \"file\": {\"name\": \"home_dr-finish.jpg\", \"size\": 128302, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/tmp/php1bc6vpc6kl10bjr45I2\", \"full_path\": \"home_dr-finish.jpg\"}, \"name\": \"home_dr-finish.jpg\", \"size\": 128302, \"type\": \"image/jpeg\", \"image\": \"/media/uploads2025/08/home_dr-finish.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"http://localhost:8089/media/uploads2025/08/home_dr-finish.jpg\", \"created_at\": \"\", \"description\": \"\"}]}, {\"name\": \"title\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"Textiles\", \"options\": [], \"imagesData\": []}, {\"name\": \"description\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"View our full collection of premium finishes, including our complete range of melamines and laminates.\", \"options\": [], \"imagesData\": []}, {\"name\": \"link\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"/account/resources/textiles\", \"options\": [], \"imagesData\": []}\r\n]'
WHERE
    `component_item`.`component_item_id` = 187;

SELECT *
FROM `component_item`
WHERE
    fields like "%http://localhost%";

UPDATE `component_item`
SET
    `fields` =
REPLACE (
        `fields`,
        'http://localhost:8089',
        ''
    )
WHERE
    `fields` LIKE '%http://localhost:8089%';

SELECT *
FROM `product`
WHERE
    banner_image like "%http://localhost:8089%";

UPDATE `product`
SET
    `banner_image` =
REPLACE (
        `banner_image`,
        'http://localhost:8089',
        ''
    )
WHERE
    `banner_image` LIKE '%http://localhost:8089%';

SELECT *
FROM `product_variant`
WHERE
    image like "%http://localhost:8089%";

UPDATE `product_variant`
SET
    `image` =
REPLACE (
        `image`,
        'http://localhost:8089',
        ''
    )
WHERE
    `image` LIKE '%http://localhost:8089%';

INSERT INTO
    taxonomy_item (
        taxonomy_item_id,
        taxonomy_item_code,
        taxonomy_id,
        image,
        template,
        parent_id,
        item_id,
        sort_order,
        status,
        color,
        name,
        banner_way_points
    )
VALUES (
        1,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Workstations_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Workstations_Product-Category.jpg\", \"full_path\": \"Workstations_Product-Category.jpg\"}, \"name\": \"Workstations_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Workstations_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Workstations_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        NULL,
        NULL,
        42,
        1,
        NULL,
        'Workstations',
        '[{\"id\": 1, \"href\": \"http://54.252.147.60:8089/categories/workstations\", \"label\": \"Workstations\", \"topPx\": 257.515625, \"leftPx\": 1547.5, \"imageWidth\": 1868, \"linkActive\": true, \"topPercent\": \"37.4295967\", \"imageHeight\": 688, \"leftPercent\": \"82.8426124\"}]'
    ),
    (
        2,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Workstations_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Workstations_Product-Category.jpg\", \"full_path\": \"Workstations_Product-Category.jpg\"}, \"name\": \"Workstations_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Workstations_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Workstations_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        1,
        NULL,
        2,
        2,
        NULL,
        'Fixed Height Workstations',
        NULL
    ),
    (
        3,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Workstations_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Workstations_Product-Category.jpg\", \"full_path\": \"Workstations_Product-Category.jpg\"}, \"name\": \"Workstations_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Workstations_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Workstations_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        1,
        NULL,
        1,
        3,
        NULL,
        'Height Adjustable Workstations',
        NULL
    ),
    (
        4,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Space_Divisions_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"full_path\": \"Space_Divisions_Product-Category.jpg\"}, \"name\": \"Space_Divisions_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        NULL,
        NULL,
        32,
        4,
        NULL,
        'Space Divisions',
        NULL
    ),
    (
        5,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Space_Divisions_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"full_path\": \"Space_Divisions_Product-Category.jpg\"}, \"name\": \"Space_Divisions_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        4,
        NULL,
        1,
        5,
        NULL,
        'Privacy Screens',
        NULL
    ),
    (
        6,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Space_Divisions_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"full_path\": \"Space_Divisions_Product-Category.jpg\"}, \"name\": \"Space_Divisions_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        4,
        NULL,
        2,
        6,
        NULL,
        'Dividers and Screens',
        NULL
    ),
    (
        7,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Space_Divisions_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"full_path\": \"Space_Divisions_Product-Category.jpg\"}, \"name\": \"Space_Divisions_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Space_Divisions_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        4,
        NULL,
        3,
        7,
        NULL,
        'Work Pods',
        NULL
    ),
    (
        8,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Desks_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"full_path\": \"Desks_Product-Category.jpg\"}, \"name\": \"Desks_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        NULL,
        NULL,
        8,
        8,
        NULL,
        'Desks',
        NULL
    ),
    (
        9,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Desks_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"full_path\": \"Desks_Product-Category.jpg\"}, \"name\": \"Desks_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        8,
        NULL,
        2,
        2,
        NULL,
        'Fixed Height Desks',
        NULL
    ),
    (
        10,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Desks_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"full_path\": \"Desks_Product-Category.jpg\"}, \"name\": \"Desks_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        8,
        NULL,
        1,
        1,
        NULL,
        'Height Adjustable Desks',
        NULL
    ),
    (
        11,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Desks_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"full_path\": \"Desks_Product-Category.jpg\"}, \"name\": \"Desks_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Desks_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        8,
        NULL,
        3,
        3,
        NULL,
        'Modesty Panels',
        NULL
    ),
    (
        12,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"full_path\": \"Tables_Product-Category.jpg\"}, \"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        NULL,
        NULL,
        36,
        12,
        NULL,
        'Tables',
        '[{\"id\": 1, \"href\": \"http://54.252.147.60:8089/categories/tables\", \"label\": \"Tables\", \"topPx\": 476.515625, \"leftPx\": 1067.5, \"imageWidth\": 1868, \"linkActive\": true, \"topPercent\": \"69.2609920\", \"imageHeight\": 688, \"leftPercent\": \"57.1466809\"}]'
    ),
    (
        13,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"full_path\": \"Tables_Product-Category.jpg\"}, \"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        12,
        NULL,
        1,
        13,
        NULL,
        'Meeting Tables',
        NULL
    ),
    (
        14,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"full_path\": \"Tables_Product-Category.jpg\"}, \"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        12,
        NULL,
        2,
        14,
        NULL,
        'Height Adjustable Tables',
        NULL
    ),
    (
        15,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"full_path\": \"Tables_Product-Category.jpg\"}, \"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        12,
        NULL,
        3,
        15,
        NULL,
        'Counter Tables',
        NULL
    ),
    (
        16,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"full_path\": \"Tables_Product-Category.jpg\"}, \"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        12,
        NULL,
        4,
        16,
        NULL,
        'Training Tables',
        NULL
    ),
    (
        17,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"full_path\": \"Tables_Product-Category.jpg\"}, \"name\": \"Tables_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Tables_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        12,
        NULL,
        5,
        17,
        NULL,
        'Coffee Tables',
        NULL
    ),
    (
        18,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"full_path\": \"Seating_Product-Category.jpg\"}, \"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        NULL,
        NULL,
        31,
        18,
        NULL,
        'Seating',
        '[{\"id\": 1, \"href\": \"http://54.252.147.60:8089/categories/seating\", \"label\": \"Seating\", \"topPx\": 381.515625, \"leftPx\": 1130.5, \"imageWidth\": 1868, \"linkActive\": true, \"topPercent\": \"55.4528525\", \"imageHeight\": 688, \"leftPercent\": \"60.5192719\"}]'
    ),
    (
        19,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"full_path\": \"Seating_Product-Category.jpg\"}, \"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        18,
        NULL,
        1,
        19,
        NULL,
        'Task Seating',
        NULL
    ),
    (
        20,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"full_path\": \"Seating_Product-Category.jpg\"}, \"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        18,
        NULL,
        2,
        20,
        NULL,
        'Executive Seating',
        NULL
    ),
    (
        21,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"full_path\": \"Seating_Product-Category.jpg\"}, \"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        18,
        NULL,
        3,
        21,
        NULL,
        'Training Seating',
        NULL
    ),
    (
        22,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"full_path\": \"Seating_Product-Category.jpg\"}, \"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        18,
        NULL,
        4,
        22,
        NULL,
        'Occasional Seating',
        NULL
    ),
    (
        23,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"full_path\": \"Seating_Product-Category.jpg\"}, \"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        18,
        NULL,
        6,
        23,
        NULL,
        'Benches',
        NULL
    ),
    (
        24,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"full_path\": \"Seating_Product-Category.jpg\"}, \"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        18,
        NULL,
        5,
        24,
        NULL,
        'Stools',
        NULL
    ),
    (
        25,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"full_path\": \"Seating_Product-Category.jpg\"}, \"name\": \"Seating_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Seating_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        18,
        NULL,
        7,
        25,
        NULL,
        'Lounges',
        NULL
    ),
    (
        26,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Reception_Counters_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Reception_Counters_Product-Category.jpg\", \"full_path\": \"Reception_Counters_Product-Category.jpg\"}, \"name\": \"Reception_Counters_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Reception_Counters_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Reception_Counters_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        NULL,
        NULL,
        30,
        26,
        NULL,
        'Reception Counters',
        NULL
    ),
    (
        27,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Storage_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"full_path\": \"Storage_Product-Category.jpg\"}, \"name\": \"Storage_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        NULL,
        NULL,
        35,
        27,
        NULL,
        'Storage',
        NULL
    ),
    (
        28,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Storage_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"full_path\": \"Storage_Product-Category.jpg\"}, \"name\": \"Storage_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        27,
        NULL,
        2,
        28,
        NULL,
        'Melamine Storage',
        NULL
    ),
    (
        29,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Storage_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"full_path\": \"Storage_Product-Category.jpg\"}, \"name\": \"Storage_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        27,
        NULL,
        1,
        29,
        NULL,
        'Steel Storage',
        NULL
    ),
    (
        30,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Storage_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"full_path\": \"Storage_Product-Category.jpg\"}, \"name\": \"Storage_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Storage_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        27,
        NULL,
        3,
        30,
        NULL,
        'Lockers',
        NULL
    ),
    (
        31,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Joinery_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Joinery_Product-Category.jpg\", \"full_path\": \"Joinery_Product-Category.jpg\"}, \"name\": \"Joinery_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Joinery_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Joinery_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        NULL,
        NULL,
        18,
        31,
        NULL,
        'Joinery',
        NULL
    ),
    (
        32,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Joinery_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Joinery_Product-Category.jpg\", \"full_path\": \"Joinery_Product-Category.jpg\"}, \"name\": \"Joinery_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Joinery_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Joinery_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        31,
        NULL,
        14,
        32,
        NULL,
        'Freestanding Joinery',
        NULL
    ),
    (
        33,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Joinery_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Joinery_Product-Category.jpg\", \"full_path\": \"Joinery_Product-Category.jpg\"}, \"name\": \"Joinery_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Joinery_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Joinery_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        31,
        NULL,
        13,
        33,
        NULL,
        'Fixed Joinery',
        NULL
    ),
    (
        34,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Training_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Training_Product-Category.jpg\", \"full_path\": \"Training_Product-Category.jpg\"}, \"name\": \"Training_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Training_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Training_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        NULL,
        NULL,
        38,
        34,
        NULL,
        'Training',
        NULL
    ),
    (
        35,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Training_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Training_Product-Category.jpg\", \"full_path\": \"Training_Product-Category.jpg\"}, \"name\": \"Training_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Training_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Training_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        34,
        NULL,
        4,
        35,
        NULL,
        'Communication Boards',
        NULL
    ),
    (
        36,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Training_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Training_Product-Category.jpg\", \"full_path\": \"Training_Product-Category.jpg\"}, \"name\": \"Training_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Training_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Training_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        34,
        NULL,
        24,
        36,
        NULL,
        'Mobile Boards',
        NULL
    ),
    (
        37,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"full_path\": \"Accessories_Product-Category.jpg\"}, \"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        NULL,
        NULL,
        1,
        37,
        NULL,
        'Accessories',
        NULL
    ),
    (
        38,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"full_path\": \"Accessories_Product-Category.jpg\"}, \"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        37,
        NULL,
        2,
        38,
        NULL,
        'Computer Accessories',
        NULL
    ),
    (
        39,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"full_path\": \"Accessories_Product-Category.jpg\"}, \"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        37,
        NULL,
        3,
        39,
        NULL,
        'Desk Accessories',
        NULL
    ),
    (
        40,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"full_path\": \"Accessories_Product-Category.jpg\"}, \"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        37,
        NULL,
        1,
        40,
        NULL,
        'Power Access',
        NULL
    ),
    (
        41,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"full_path\": \"Accessories_Product-Category.jpg\"}, \"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        37,
        NULL,
        5,
        41,
        NULL,
        'Office Accessories',
        NULL
    ),
    (
        42,
        NULL,
        1,
        '[{\"id\": null, \"file\": {\"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"full_path\": \"Accessories_Product-Category.jpg\"}, \"name\": \"Accessories_Product-Category.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/categories/banner/Accessories_Product-Category.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        37,
        NULL,
        4,
        42,
        NULL,
        'Locker Accessories',
        NULL
    );

-- 15-02-2026

alter table service_request
add column company varchar(191) null default null after service_request_id;

alter table service_request
add column full_name varchar(191) null default null after company;

alter table service_request
add column request_type varchar(191) null default null after full_name;

alter table customer
add column `is_verified` tinyint NOT NULL DEFAULT 0;

ALTER TABLE `user`
ADD `is_verified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `email`;

CREATE TABLE `showroom_contact` (
    `showroom_contact_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
    `showroom_id` int UNSIGNED NOT NULL,
    `name` varchar(191) DEFAULT NULL,
    `image` json DEFAULT NULL,
    `email` varchar(191) DEFAULT NULL,
    `phone` varchar(191) DEFAULT NULL,
    `designation` varchar(191) DEFAULT NULL,
    `message` text DEFAULT NULL,
    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` datetime DEFAULT NULL,
    PRIMARY KEY (`showroom_contact_id`),
    KEY `idx_showroom_id` (`showroom_id`),
    CONSTRAINT `fk_showroom_contact_showroom` FOREIGN KEY (`showroom_id`) REFERENCES `showrooms` (`showrooms_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci;

-- 17-02-2026 dummy data showroom contact

INSERT INTO
    `showroom_contact` (
        `showroom_id`,
        `name`,
        `image`,
        `email`,
        `phone`,
        `designation`,
        `message`,
        `created_at`,
        `updated_at`,
        `deleted_at`
    )
VALUES (
        1,
        'Sarah Mitchell',
        '[{"id":null,"file":{"name":"sarah-mitchell.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/sarah-mitchell.jpg","full_path":"sarah-mitchell.jpg"},"name":"sarah-mitchell.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/sarah-mitchell.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/sarah-mitchell.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'sarah.mitchell@example.com',
        '0412000001',
        'Sales Executive',
        'Interested in bulk purchase pricing.',
        '2026-02-17 10:05:00',
        '2026-02-17 10:05:00',
        NULL
    ),
    (
        1,
        'Michael Brown',
        '[{"id":null,"file":{"name":"michael-brown.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/michael-brown.jpg","full_path":"michael-brown.jpg"},"name":"michael-brown.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/michael-brown.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/michael-brown.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'michael.brown@example.com',
        '0412000002',
        'Consultant',
        'Please schedule a product demo.',
        '2026-02-17 10:10:00',
        '2026-02-17 10:10:00',
        NULL
    ),
    (
        1,
        'Emily Carter',
        '[{"id":null,"file":{"name":"emily-carter.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/emily-carter.jpg","full_path":"emily-carter.jpg"},"name":"emily-carter.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/emily-carter.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/emily-carter.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'emily.carter@example.com',
        '0412000003',
        'Interior Designer',
        'Looking for modern office solutions.',
        '2026-02-17 10:15:00',
        '2026-02-17 10:15:00',
        NULL
    ),
    (
        1,
        'David Wilson',
        '[{"id":null,"file":{"name":"david-wilson.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/david-wilson.jpg","full_path":"david-wilson.jpg"},"name":"david-wilson.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/david-wilson.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/david-wilson.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'david.wilson@example.com',
        '0412000004',
        'Project Manager',
        'Need quotation for corporate office.',
        '2026-02-17 10:20:00',
        '2026-02-17 10:20:00',
        NULL
    ),
    (
        1,
        'Olivia Taylor',
        '[{"id":null,"file":{"name":"olivia-taylor.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/olivia-taylor.jpg","full_path":"olivia-taylor.jpg"},"name":"olivia-taylor.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/olivia-taylor.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/olivia-taylor.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'olivia.taylor@example.com',
        '0412000005',
        'Architect',
        'Interested in sustainable materials.',
        '2026-02-17 10:25:00',
        '2026-02-17 10:25:00',
        NULL
    ),
    (
        1,
        'Daniel Anderson',
        '[{"id":null,"file":{"name":"daniel-anderson.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/daniel-anderson.jpg","full_path":"daniel-anderson.jpg"},"name":"daniel-anderson.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/daniel-anderson.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/daniel-anderson.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'daniel.anderson@example.com',
        '0412000006',
        'Procurement Officer',
        'Please share catalog and pricing.',
        '2026-02-17 10:30:00',
        '2026-02-17 10:30:00',
        NULL
    );

INSERT INTO
    `showroom_contact` (
        `showroom_id`,
        `name`,
        `image`,
        `email`,
        `phone`,
        `designation`,
        `message`,
        `created_at`,
        `updated_at`,
        `deleted_at`
    )
VALUES (
        2,
        'Sophia Thomas',
        '[{"id":null,"file":{"name":"sophia-thomas.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/sophia-thomas.jpg","full_path":"sophia-thomas.jpg"},"name":"sophia-thomas.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/sophia-thomas.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/sophia-thomas.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'sophia.thomas@example.com',
        '0422000001',
        'Showroom Manager',
        'Interested in partnership opportunities.',
        '2026-02-17 11:00:00',
        '2026-02-17 11:00:00',
        NULL
    ),
    (
        2,
        'James White',
        '[{"id":null,"file":{"name":"james-white.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/james-white.jpg","full_path":"james-white.jpg"},"name":"james-white.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/james-white.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/james-white.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'james.white@example.com',
        '0422000002',
        'Interior Designer',
        'Need customization options for office setup.',
        '2026-02-17 11:05:00',
        '2026-02-17 11:05:00',
        NULL
    ),
    (
        2,
        'Isabella Martin',
        '[{"id":null,"file":{"name":"isabella-martin.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/isabella-martin.jpg","full_path":"isabella-martin.jpg"},"name":"isabella-martin.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/isabella-martin.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/isabella-martin.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'isabella.martin@example.com',
        '0422000003',
        'Architect',
        'Requesting a showroom visit appointment.',
        '2026-02-17 11:10:00',
        '2026-02-17 11:10:00',
        NULL
    ),
    (
        2,
        'William Harris',
        '[{"id":null,"file":{"name":"william-harris.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/william-harris.jpg","full_path":"william-harris.jpg"},"name":"william-harris.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/william-harris.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/william-harris.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'william.harris@example.com',
        '0422000004',
        'Business Owner',
        'Looking for premium executive desks.',
        '2026-02-17 11:15:00',
        '2026-02-17 11:15:00',
        NULL
    ),
    (
        2,
        'Mia Clark',
        '[{"id":null,"file":{"name":"mia-clark.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/mia-clark.jpg","full_path":"mia-clark.jpg"},"name":"mia-clark.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/mia-clark.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/mia-clark.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'mia.clark@example.com',
        '0422000005',
        'Procurement Manager',
        'Please send product specifications and pricing.',
        '2026-02-17 11:20:00',
        '2026-02-17 11:20:00',
        NULL
    ),
    (
        2,
        'Ethan Lewis',
        '[{"id":null,"file":{"name":"ethan-lewis.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/ethan-lewis.jpg","full_path":"ethan-lewis.jpg"},"name":"ethan-lewis.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/ethan-lewis.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/ethan-lewis.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'ethan.lewis@example.com',
        '0422000006',
        'Consultant',
        'Require delivery timeline and warranty details.',
        '2026-02-17 11:25:00',
        '2026-02-17 11:25:00',
        NULL
    );

INSERT INTO
    `showroom_contact` (
        `showroom_id`,
        `name`,
        `image`,
        `email`,
        `phone`,
        `designation`,
        `message`,
        `created_at`,
        `updated_at`,
        `deleted_at`
    )
VALUES (
        3,
        'Charlotte Hall',
        '[{"id":null,"file":{"name":"charlotte-hall.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/charlotte-hall.jpg","full_path":"charlotte-hall.jpg"},"name":"charlotte-hall.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/charlotte-hall.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/charlotte-hall.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'charlotte.hall@example.com',
        '0433000001',
        'Showroom Manager',
        'Interested in new arrivals and catalog updates.',
        '2026-02-17 12:00:00',
        '2026-02-17 12:00:00',
        NULL
    ),
    (
        3,
        'Benjamin Young',
        '[{"id":null,"file":{"name":"benjamin-young.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/benjamin-young.jpg","full_path":"benjamin-young.jpg"},"name":"benjamin-young.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/benjamin-young.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/benjamin-young.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'benjamin.young@example.com',
        '0433000002',
        'Sales Head',
        'Need corporate discount structure details.',
        '2026-02-17 12:05:00',
        '2026-02-17 12:05:00',
        NULL
    ),
    (
        3,
        'Amelia King',
        '[{"id":null,"file":{"name":"amelia-king.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/amelia-king.jpg","full_path":"amelia-king.jpg"},"name":"amelia-king.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/amelia-king.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/amelia-king.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'amelia.king@example.com',
        '0433000003',
        'Interior Consultant',
        'Book consultation session for workspace planning.',
        '2026-02-17 12:10:00',
        '2026-02-17 12:10:00',
        NULL
    ),
    (
        3,
        'Lucas Scott',
        '[{"id":null,"file":{"name":"lucas-scott.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/lucas-scott.jpg","full_path":"lucas-scott.jpg"},"name":"lucas-scott.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/lucas-scott.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/lucas-scott.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'lucas.scott@example.com',
        '0433000004',
        'Operations Manager',
        'Interested in warehouse and storage solutions.',
        '2026-02-17 12:15:00',
        '2026-02-17 12:15:00',
        NULL
    ),
    (
        3,
        'Ava Walker',
        '[{"id":null,"file":{"name":"ava-walker.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/ava-walker.jpg","full_path":"ava-walker.jpg"},"name":"ava-walker.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/ava-walker.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/ava-walker.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'ava.walker@example.com',
        '0433000005',
        'CEO',
        'Looking for executive office setup solutions.',
        '2026-02-17 12:20:00',
        '2026-02-17 12:20:00',
        NULL
    ),
    (
        3,
        'Henry Adams',
        '[{"id":null,"file":{"name":"henry-adams.jpg","size":0,"type":"image/jpeg","error":0,"tmp_name":"/media/showroom-contact/henry-adams.jpg","full_path":"henry-adams.jpg"},"name":"henry-adams.jpg","size":0,"type":"image/jpeg","image":"/media/showroom-contact/henry-adams.jpg","status":{"name":"Uploaded","severity":"success"},"media_id":null,"objectURL":"/media/showroom-contact/henry-adams.jpg","created_at":"","description":"","post_image_id":null,"project_image_id":null}]',
        'henry.adams@example.com',
        '0433000006',
        'Project Director',
        'Need custom workspace planning and layout assistance.',
        '2026-02-17 12:25:00',
        '2026-02-17 12:25:00',
        NULL
    );

-- truncate showroom_contact

truncate table showroom_contact;

select *
from
    product_content
    join product_to_taxonomy_item on product_content.product_id = product_to_taxonomy_item.product_id
    join taxonomy_item_content on product_to_taxonomy_item.taxonomy_item_id = taxonomy_item_content.taxonomy_item_id
where
    product_content.title like '%fgr%';

alter table product
add column product_family_code varchar(191) DEFAULT NULL after product_code;

INSERT INTO
    `taxonomy_item` (
        `taxonomy_item_id`,
        `taxonomy_item_code`,
        `taxonomy_id`,
        `image`,
        `template`,
        `parent_id`,
        `item_id`,
        `sort_order`,
        `status`,
        `color`,
        `name`,
        `banner_way_points`
    )
VALUES (
        NULL,
        'breakroom-tables',
        '1',
        NULL,
        '',
        '12',
        NULL,
        '0',
        '17',
        NULL,
        'Breakroom Tables',
        NULL
    );

INSERT INTO
    `taxonomy_item_content` (
        `taxonomy_item_id`,
        `language_id`,
        `name`,
        `slug`,
        `content`,
        `meta_title`,
        `meta_description`,
        `meta_keywords`,
        `link`,
        `products_link`
    )
VALUES (
        124,
        '1',
        'Breakroom Tables',
        'breakroom-tables',
        'Stylish, low-profile tables for break-out and reception areas.',
        '',
        '',
        '',
        '/categories/tables/breakroom',
        '/products/breakroom-tables'
    );

-- 21-02-2026 = nazmul
ALTER TABLE `subscription`
ADD `email` VARCHAR(191) NULL DEFAULT NULL AFTER `order_id`;

ALTER TABLE `visit_showroom`
ADD `meeting_time` VARCHAR(191) NULL DEFAULT NULL AFTER `date`;
-- 19-02-2026
alter table product_certificate
add column certificate_type varchar(191) null default null after certificate_provider;

alter table product_certificate
add column file_format varchar(191) null default null after certificate_type;

INSERT INTO
    `product` (
        `product_id`,
        `km_item_id`,
        `product_type_id`,
        `class_id`,
        `company_id`,
        `admin_id`,
        `parent_id`,
        `model`,
        `description`,
        `specifications`,
        `warranty_period`,
        `product_code`,
        `product_family_code`,
        `factory_code`,
        `sku`,
        `isbn`,
        `barcode`,
        `track_stock`,
        `stock_quantity`,
        `stock_status_id`,
        `lead_days`,
        `melbourne_lead_days`,
        `safety_stock`,
        `qty_alert`,
        `image`,
        `media_id`,
        `manufacturer_id`,
        `vendor_id`,
        `import_vendor_id`,
        `factory_vendor_id`,
        `product_range_id`,
        `product_category_id`,
        `edgetape_colour_id`,
        `requires_shipping`,
        `tax_type_id`,
        `material`,
        `weight`,
        `weight_type_id`,
        `length`,
        `length_type_id`,
        `width`,
        `height`,
        `depth`,
        `price`,
        `old_price`,
        `min_order_quantity`,
        `out_of_stock_status`,
        `carton_qm`,
        `size`,
        `carton_width`,
        `carton_depth`,
        `carton_height`,
        `gross_weight`,
        `date_available`,
        `template`,
        `views`,
        `subtract_stock`,
        `status`,
        `is_featured`,
        `sort_order`,
        `project_price_qty`,
        `project_price_discount`,
        `active`,
        `archive`,
        `specifications_image`,
        `banner_image`,
        `video_link`,
        `image_thumb`,
        `main_image_one`,
        `main_image_one_title`,
        `main_image_one_description`,
        `main_image_two`,
        `main_image_two_title`,
        `main_image_two_description`,
        `feature_description`,
        `feature_image_one`,
        `feature_image_one_title`,
        `feature_image_one_description`,
        `feature_image_two`,
        `feature_image_two_title`,
        `feature_image_two_description`,
        `feature_image_three`,
        `dimension_image`,
        `feature_image_three_title`,
        `feature_image_three_description`,
        `created_at`,
        `updated_at`,
        `banner_way_points`,
        `ocean_plastic_used`
    )
VALUES (
        NULL,
        '0',
        '1',
        '1',
        '1',
        '1',
        NULL,
        '',
        'Jive S Reception Counter provides a solid and professional first impression for any corporate environment. Its substantial, 50mm thick construction gives it a robust and architectural presence.',
        '50mm thick panel construction for top and sides\r\nFeatures durable 1mm ABS edging\r\nHigh pressure laminate 0.7mm edging \r\nOptional 18mm thick hob or 15mm thick premium compact laminate hob\r\nOptional fitted return available to extend the workspace\r\nLevelling adjustment in feet\r\nMade in Australia\r\n10 Year Warranty',
        '',
        'jive-s-reception-counter',
        'jive s',
        '',
        'jive-s-reception-counter',
        '',
        '',
        '0',
        '0',
        '1',
        '0',
        '0',
        '0',
        '0',
        '[{\"id\": null, \"file\": {\"name\": \"JiveSReception_Image.jpg\", \"size\": 145269, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/var/www/html/public/media/Products/image/JiveSReception_Image.jpg\", \"full_path\": \"JiveSReception_Image.jpg\"}, \"name\": \"JiveSReception_Image.jpg\", \"size\": 145269, \"type\": \"image/jpeg\", \"image\": \"/media/Products/image/JiveSReception_Image.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/Products/image/JiveSReception_Image.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '8463',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        '1',
        NULL,
        '1',
        NULL,
        '',
        '0.00000000',
        NULL,
        '0.00000000',
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        NULL,
        '1',
        NULL,
        NULL,
        NULL,
        '0.00000',
        '0.00000',
        '0.00000',
        NULL,
        NULL,
        '',
        '0',
        '1',
        '0',
        '0',
        '0',
        NULL,
        '0.00000',
        '1',
        '0',
        '[{\"id\": null, \"file\": {\"name\": \"JiveSReception_SpecImage.jpg\", \"size\": 31442, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/var/www/html/public/media/Products/specification/JiveSReception_SpecImage.jpg\", \"full_path\": \"JiveSReception_SpecImage.jpg\"}, \"name\": \"JiveSReception_SpecImage.jpg\", \"size\": 31442, \"type\": \"image/jpeg\", \"image\": \"/media/Products/specification/JiveSReception_SpecImage.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/Products/specification/JiveSReception_SpecImage.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '[{\"id\": null, \"file\": {\"name\": \"JiveSReception_BannerImage.jpg\", \"size\": 302448, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/var/www/html/public/media/Products/banner/JiveSReception_BannerImage.jpg\", \"full_path\": \"JiveSReception_BannerImage.jpg\"}, \"name\": \"JiveSReception_BannerImage.jpg\", \"size\": 302448, \"type\": \"image/jpeg\", \"image\": \"/media/Products/banner/JiveSReception_BannerImage.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/Products/banner/JiveSReception_BannerImage.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '',
        '[{\"id\": null, \"file\": {\"name\": \"JiveSReception_ImageThumb.jpg\", \"size\": 65641, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/var/www/html/public/media/Products/thumbnails/JiveSReception_ImageThumb.jpg\", \"full_path\": \"JiveSReception_ImageThumb.jpg\"}, \"name\": \"JiveSReception_ImageThumb.jpg\", \"size\": 65641, \"type\": \"image/jpeg\", \"image\": \"/media/Products/thumbnails/JiveSReception_ImageThumb.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/Products/thumbnails/JiveSReception_ImageThumb.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        '[{\"id\": null, \"file\": {\"name\": \"HaloBreak_B.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/media/Products/main-image-one/HaloBreak_B.jpg\", \"full_path\": \"HaloBreak_B.jpg\"}, \"name\": \"HaloBreak_B.jpg\", \"size\": 0, \"type\": \"image/jpeg\", \"image\": \"/media/Products/main-image-one/HaloBreak_B.jpg\", \"status\": {\"name\": \"Expected\", \"severity\": \"info\"}, \"media_id\": null, \"objectURL\": \"/media/Products/main-image-one/HaloBreak_B.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        'A Professional Welcome for Your Reception',
        'Jive S Reception Counter provides a solid and professional first impression for any corporate environment. Its substantial, 50mm thick construction gives it a robust and architectural presence.',
        '[{\"id\": null, \"file\": {\"name\": \"HaloBreak_B.jpg\", \"size\": 90276, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/var/www/html/public/media/Products/main-image-two/HaloBreak_B.jpg\", \"full_path\": \"HaloBreak_B.jpg\"}, \"name\": \"HaloBreak_B.jpg\", \"size\": 90276, \"type\": \"image/jpeg\", \"image\": \"/media/Products/main-image-two/HaloBreak_B.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/Products/main-image-two/HaloBreak_B.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        'Quality Construction and Finishes',
        'The counter is built with a durable 50mm thick panel construction and finished with 1mm ABS edging. An optional hob can be added in a matching finish or a premium compact laminate for a refined detail.',
        'The Australian-made Jive S Reception Counter is engineered for a solid presence and lasting durability. Its defining 50mm thick panel construction creates a robust and modern centrepiece for any reception area.',
        '[{\"id\": null, \"file\": {\"name\": \"JiveSReception_FeatureOne.jpg\", \"size\": 73666, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/var/www/html/public/media/Products/feature/JiveSReception_FeatureOne.jpg\", \"full_path\": \"JiveSReception_FeatureOne.jpg\"}, \"name\": \"JiveSReception_FeatureOne.jpg\", \"size\": 73666, \"type\": \"image/jpeg\", \"image\": \"/media/Products/feature/JiveSReception_FeatureOne.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/Products/feature/JiveSReception_FeatureOne.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        'Robust 50mm Construction',
        'Jive S is constructed with 50mm thick panels for the body and transaction top. This substantial construction provides superior strength and a distinctive, solid profile.',
        '[{\"id\": null, \"file\": {\"name\": \"JiveSReception_FeatureTwo.jpg\", \"size\": 75171, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/var/www/html/public/media/Products/feature/JiveSReception_FeatureTwo.jpg\", \"full_path\": \"JiveSReception_FeatureTwo.jpg\"}, \"name\": \"JiveSReception_FeatureTwo.jpg\", \"size\": 75171, \"type\": \"image/jpeg\", \"image\": \"/media/Products/feature/JiveSReception_FeatureTwo.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/Products/feature/JiveSReception_FeatureTwo.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        'Optional Counter Hob',
        'An optional hob can be specified to sit on the transaction top for added privacy and functionality. It is available in a wide range of finishes, including a premium 15mm thick compact laminate.',
        '[{\"id\": null, \"file\": {\"name\": \"JiveSReception_FeatureThree.jpg\", \"size\": 52299, \"type\": \"image/jpeg\", \"error\": 0, \"tmp_name\": \"/var/www/html/public/media/Products/feature/JiveSReception_FeatureThree.jpg\", \"full_path\": \"JiveSReception_FeatureThree.jpg\"}, \"name\": \"JiveSReception_FeatureThree.jpg\", \"size\": 52299, \"type\": \"image/jpeg\", \"image\": \"/media/Products/feature/JiveSReception_FeatureThree.jpg\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": null, \"objectURL\": \"/media/Products/feature/JiveSReception_FeatureThree.jpg\", \"created_at\": \"\", \"description\": \"\", \"post_image_id\": null, \"project_image_id\": null}]',
        NULL,
        'Designed for Easy Installation',
        'The counter is designed in two separate pieces, the main body and the top, which allows for easier transport and access into buildings. The pieces are assembled on-site for a secure and complete installation.',
        '2026-02-19 08:07:53',
        '2026-02-19 08:07:53',
        NULL,
        '0'
    );

INSERT INTO
    `product_content` (
        `product_id`,
        `language_id`,
        `name`,
        `slug`,
        `title`,
        `tag_line`,
        `content`,
        `tag`,
        `meta_title`,
        `meta_description`,
        `meta_keywords`,
        `icon`
    )
VALUES (
        261,
        1,
        'Jive S Reception Counter',
        'jive-s-reception-counter',
        'Jive S Reception Counter',
        'A Professional Welcome for Your Reception',
        'The Australian-made Jive S Reception Counter is engineered for a solid presence and lasting durability. Its defining 50mm thick panel construction creates a robust and modern centrepiece for any reception area.

Jive S Reception Counter provides a solid and professional first impression for any corporate environment. Its substantial, 50mm thick construction gives it a robust and architectural presence.

Features:
- 50mm thick panel construction for top and sides
- Durable 1mm ABS edging
- High pressure laminate 0.7mm edging
- Optional 18mm thick hob or 15mm premium compact laminate hob
- Optional fitted return to extend the workspace
- Levelling adjustment feet
- Made in Australia
- 10 Year Warranty',
        'reception counter, jive reception desk, office reception desk, australian made counter',
        'Jive S Reception Counter | 50mm Thick Reception Desk',
        'Jive S Reception Counter features robust 50mm thick construction, premium finishes, optional hob and fitted return. Australian made with 10 year warranty.',
        'jive reception counter, reception desk australia, 50mm reception counter, office reception furniture',
        NULL
    );

INSERT INTO
    `product_to_taxonomy_item` (
        `product_id`,
        `taxonomy_item_id`
    )
VALUES ('261', '26');

ALTER TABLE `item`
ADD COLUMN `display_width` VARCHAR(255) NULL DEFAULT NULL AFTER `depth`;

ALTER TABLE `item`
ADD COLUMN `display_height` VARCHAR(255) NULL DEFAULT NULL AFTER `display_width`;

ALTER TABLE `item`
ADD COLUMN `display_depth` VARCHAR(255) NULL DEFAULT NULL AFTER `display_height`;

-- 23-02-2026 = Shofiul

ALTER TABLE `pinboard_temp_item`
ADD COLUMN `title` VARCHAR(255) NULL DEFAULT NULL AFTER `language_id`;

ALTER TABLE `visit_showroom`
ADD COLUMN `showroom_contact_id` INT UNSIGNED DEFAULT NULL AFTER `customer_id`;

-- 25-02-2026 abdullah
SELECT
    product.product_id AS id,
    product_content.name,
    product_content.title,
    product_content.tag_line,
    product.image,
    product.description,
    product_content.slug,
    taxonomy_item_content.slug AS category_slug,
    taxonomy_item_content.name AS category_name,
    tags.taxonomy_item_id AS tag_id,
    tags.name AS tag_name,
    product_certificate.product_certificate_id,
    product_certificate.title AS certificate_title,
    product_certificate.description AS certificate_description,
    design_resource.title AS finish_name,
    design_resource.img AS finish_image,
    design_resource.hex_value AS finish_color,
    product.product_id
FROM
    `product` AS `product`
LEFT JOIN `product_content` `product_content` ON
    product_content.product_id = product.product_id
LEFT JOIN `product_to_taxonomy_item` `product_to_taxonomy_item` ON
    product_to_taxonomy_item.product_id = product.product_id
LEFT JOIN `taxonomy` `taxonomy` ON
    taxonomy.taxonomy_id = product_to_taxonomy_item.taxonomy_id
LEFT JOIN `taxonomy_item_content` `taxonomy_item_content` ON
    taxonomy_item_content.taxonomy_item_id = product_to_taxonomy_item.taxonomy_item_id
LEFT JOIN `product_certificate` `product_certificate` ON
    product_certificate.product_id = product.product_id
LEFT JOIN `taxonomy_item_content` `tags` ON
    tags.taxonomy_item_id = product_to_taxonomy_item.taxonomy_item_id AND taxonomy.taxonomy_id = 2
LEFT JOIN `product_resource` `product_resource` ON
    product_resource.product_id = product.product_id AND product_resource.resource_type = "finishes"
LEFT JOIN `design_resource` `design_resource` ON
    design_resource.design_resource_id = product_resource.design_resource_id
WHERE
    product.active = :product_active AND product.product_family_code = :product_product_family_code
GROUP BY
    `product`.product_id
LIMIT 100;

-- tags
select *
from
    taxonomy_item as ti
    join taxonomy_item_content on taxonomy_item_content.taxonomy_item_id = ti.taxonomy_item_id
    join taxonomy on taxonomy.taxonomy_id = ti.taxonomy_id
where
    taxonomy.type = 'tags';
-- product to taxonomy item
select product.product_code, product.product_id, taxonomy_item.taxonomy_item_code, taxonomy_item.name
from
    product_to_taxonomy_item as pti
    join product on product.product_id = pti.product_id
    join taxonomy_item on taxonomy_item.taxonomy_item_id = pti.taxonomy_item_id
    join taxonomy on taxonomy.taxonomy_id = taxonomy_item.taxonomy_id
where
    taxonomy.taxonomy_id = 2
    and product.product_id = 243
order by product.product_id desc;
-- group by pti.product_id;

-- abdullah
ALTER TABLE product_to_taxonomy_item
ADD COLUMN created_at datetime DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE product_to_taxonomy_item
ADD COLUMN updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

SELECT
    product.product_id AS id,
    product_content.name,
    product_content.title,
    product_content.tag_line,
    product.image,
    product_content.tag_line AS description,
    product_content.slug,
    product_content.slug AS product_slug,
    product_content.meta_keywords AS tags,
    product_content.meta_description AS meta_description,
    taxonomy_item_content.slug AS category_slug,
    taxonomy_item_content.name AS category_name,
    taxonomy_item.sort_order AS category_sort_order,
    taxonomy_item_content.name AS category,
    taxonomy_item_content.link AS category_link,
    taxonomy_item_content.products_link,
    taxonomy_item_content.content AS content,
    tags.taxonomy_item_id AS tag_id,
    tags.name AS tag_name,
    product_certificate.product_certificate_id,
    product_certificate.title AS certificate_title,
    product_certificate.description AS certificate_description,
    design_resource.title AS finish_name,
    design_resource.img AS finish_image,
    design_resource.hex_value AS finish_color
FROM
    `product` AS `product`
    LEFT JOIN `product_content` `product_content` ON product_content.product_id = product.product_id
    LEFT JOIN `product_to_taxonomy_item` `product_to_taxonomy_item` ON product_to_taxonomy_item.product_id = product.product_id
    LEFT JOIN `taxonomy_item` `taxonomy_item` ON taxonomy_item.taxonomy_item_id = product_to_taxonomy_item.taxonomy_item_id
    LEFT JOIN `taxonomy_item_content` `taxonomy_item_content` ON taxonomy_item_content.taxonomy_item_id = product_to_taxonomy_item.taxonomy_item_id
    LEFT JOIN `taxonomy` `taxonomy` ON taxonomy.taxonomy_id = taxonomy_item_content.taxonomy_item_id
    LEFT JOIN `product_certificate` `product_certificate` ON product_certificate.product_id = product.product_id
    LEFT JOIN `taxonomy_item_content` `tags` ON tags.taxonomy_item_id = product_to_taxonomy_item.taxonomy_item_id
    AND taxonomy.taxonomy_id = 2
    LEFT JOIN `product_resource` `product_resource` ON product_resource.product_id = product.product_id
    AND product_resource.resource_type = "finishes"
    LEFT JOIN `design_resource` `design_resource` ON design_resource.design_resource_id = product_resource.design_resource_id
WHERE
    product.active = 1
    AND taxonomy_item.parent_id = 27
    AND product.product_id = 243
GROUP BY
    `product`.product_id
ORDER BY taxonomy_item.sort_order ASC
LIMIT 100;

store_link catalogue_link

alter table product
add column store_link varchar(255) null default null after ocean_plastic_used;

alter table product
add column catalogue_link varchar(255) null default null after store_link;

-- 26 Feb
alter table item
add column dimensions_image json null default null after quote_image;

-- 26-02-2026 = nazmul
ALTER TABLE `visit_showroom`
ADD `duration` VARCHAR(191) NULL DEFAULT NULL AFTER `meeting_time`;
--

alter table showroom_contact
add column sort_order int unsigned not null default 0 after message;

alter table showroom_contact
add column status tinyint(1) not null default 1 after sort_order;

SELECT
    `product`.product_id as id,
    product_content.title as title,
    product_content.name as name,
    `product`.image,
    product_content.tag_line as description,
    product_content.slug as slug,
    CONCAT(
        "products reference: ",
        product.product_id
    ) as reference,
    CONCAT(
        "products/",
        taxonomy_item_content.slug,
        "/",
        product_content.slug
    ) as href,
    CONCAT(
        "Product-",
        product.product_id
    ) as model_type
FROM
    `product` AS `product`
    LEFT JOIN `product_content` `product_content` ON product_content.product_id = product.product_id
    LEFT JOIN `product_to_taxonomy_item` `product_to_taxonomy_item` ON product_to_taxonomy_item.product_id = product.product_id
    LEFT JOIN `taxonomy_item` `taxonomy_item` ON taxonomy_item.taxonomy_item_id = product_to_taxonomy_item.taxonomy_item_id
    LEFT JOIN `taxonomy_item_content` `taxonomy_item_content` ON taxonomy_item_content.taxonomy_item_id = taxonomy_item.taxonomy_item_id
WHERE
    product.product_code LIKE '%counter%'
    OR product_content.slug LIKE '%counter%'
    OR product_content.name LIKE '%counter%'
    OR product_content.title LIKE '%counter%'
    OR product_content.tag_line LIKE '%counter%'
GROUP BY
    product.product_id
LIMIT 50;

-- 28 Feb 2026
alter table product_to_taxonomy_item
add column delete_tags varchar(191) default null;

-- 28-02-2026 nazmul
ALTER TABLE `type`
ADD `deleted_at` DATE NULL DEFAULT NULL AFTER `sort_order`;

-- 2 March
ALTER TABLE `component`
CHANGE `description` `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL;

-- 03 March
INSERT INTO
    `component` (
        `component_id`,
        `name`,
        `section_title`,
        `section_subtitle`,
        `section_link`,
        `title`,
        `subtitle`,
        `description`,
        `image`,
        `images`,
        `links`,
        `buttons`,
        `template`,
        `active`,
        `model`,
        `banner_way_points`
    )
VALUES (
        NULL,
        'herocontactsales',
        'Contact Sales',
        '',
        '',
        '',
        '',
        '',
        '[{\"id\": null, \"file\": {\"name\": \"about-hero.png\", \"size\": 522214, \"type\": \"image/png\", \"error\": 0, \"tmp_name\": \"/tmp/phpbdp6s97fsm7mbLi0DiZ\", \"full_path\": \"about-hero.png\"}, \"name\": \"about-hero.png\", \"size\": 522214, \"type\": \"image/png\", \"image\": \"/media/Components//about-hero.png\", \"status\": {\"name\": \"Uploaded\", \"severity\": \"success\"}, \"media_id\": 10064, \"objectURL\": \"http://localhost:8089/media/Components//about-hero.png\", \"created_at\": \"\", \"description\": \"\"}]',
        '[]',
        '[]',
        '[{\"id\": 0, \"url\": \"http://localhost:8089/contact-sales#book-now\", \"icon\": \"th-btn text-capitalize\", \"type\": \"\", \"title\": \"Book a Visit\", \"target\": \"\"}]',
        '',
        '1',
        NULL,
        NULL
    );

-- 03 March 2026
select product.product_id, product.product_code, product_content.slug
from product
    LEFT join product_content on product_content.product_id = product.product_id
where
    product.product_code != product_content.slug;
-- total result 18
-- 268	clic-	clic
-- 269	co-op-	co-op
-- 262	cosmopolitan-	cosmopolitan
-- 287	curve-	curve
-- 270	elki-	elki
-- 271	halo-	halo
-- 272	jive-s-	jive-s
-- 273	keywork-	keywork
-- 140	lunar-coat-&-hat-stand	lunar-coat-hat-stand
-- 274	merge-	merge
-- 275	monolite-	monolite
-- 276	oslo-	oslo
-- 277	remi-	remi
-- 284	sparki-in-desk-module-	sparki-in-desk-module
-- 285	sparki-power-rail-	sparki-power-rail
-- 279	swish-	swish
-- 227	trak-clamps-&-brackets	trak-clamps-brackets
-- 233	universal-drawer-units-&-filing-cabinets	universal-drawer-units-filing-cabinets

ALTER TABLE `item_option`
ADD COLUMN `hex_color` varchar(50) DEFAULT NULL AFTER `required`;

ALTER TABLE `item_option`
ADD COLUMN `option_image` json DEFAULT NULL AFTER `hex_color`;

ALTER TABLE `mvc`.`design_resource`
ADD UNIQUE `uk_design_resource_title_type` (`title`, `resource_type`);

ALTER TABLE `mvc`.`design_resource_document`
ADD UNIQUE `uk_design_resource_id_url` (`design_resource_id`, `url`);

ALTER TABLE `mvc`.`product_resource`
ADD UNIQUE `uk_product_id_design_resource_id` (
    `product_id`,
    `design_resource_id`
);

ALTER TABLE `product_option`
ADD COLUMN `hex_color` varchar(50) DEFAULT NULL AFTER `active_status`;

ALTER TABLE `product_option`
ADD COLUMN `option_image` json DEFAULT NULL AFTER `hex_color`;

ALTER TABLE `service_request`
ADD COLUMN `first_name` varchar(191) DEFAULT NULL AFTER `full_name`;

ALTER TABLE `service_request`
ADD COLUMN `last_name` varchar(191) DEFAULT NULL AFTER `first_name`;

-- 05 March 2026 (media data)
INSERT INTO
    media (
        media_id,
        file,
        type,
        meta,
        parent_id,
        folder_id,
        name,
        path
    )
VALUES (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'about',
        NULL,
        NULL,
        'about',
        '/media/about'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'admins',
        NULL,
        NULL,
        'admins',
        '/media/admins'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Blogs',
        NULL,
        NULL,
        'Blogs',
        '/media/Blogs'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'catalogue',
        NULL,
        NULL,
        'catalogue',
        '/media/catalogue'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'categories',
        NULL,
        NULL,
        'categories',
        '/media/categories'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Certificates',
        NULL,
        NULL,
        'Certificates',
        '/media/Certificates'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Comments',
        NULL,
        NULL,
        'Comments',
        '/media/Comments'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Components',
        NULL,
        NULL,
        'Components',
        '/media/Components'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'design-resource',
        NULL,
        NULL,
        'design-resource',
        '/media/design-resource'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'item-options',
        NULL,
        NULL,
        'item-options',
        '/media/item-options'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Logo',
        NULL,
        NULL,
        'Logo',
        '/media/Logo'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'manufacturers',
        NULL,
        NULL,
        'manufacturers',
        '/media/manufacturers'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Others',
        NULL,
        NULL,
        'Others',
        '/media/Others'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Pages',
        NULL,
        NULL,
        'Pages',
        '/media/Pages'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Pinboard',
        NULL,
        NULL,
        'Pinboard',
        '/media/Pinboard'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'post-tag',
        NULL,
        NULL,
        'post-tag',
        '/media/post-tag'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'post-types',
        NULL,
        NULL,
        'post-types',
        '/media/post-types'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'product',
        NULL,
        NULL,
        'product',
        '/media/product'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Products',
        NULL,
        NULL,
        'Products',
        '/media/Products'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Products-Backup',
        NULL,
        NULL,
        'Products-Backup',
        '/media/Products-Backup'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'product-types',
        NULL,
        NULL,
        'product-types',
        '/media/product-types'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Projects',
        NULL,
        NULL,
        'Projects',
        '/media/Projects'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Services',
        NULL,
        NULL,
        'Services',
        '/media/Services'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'showroom',
        NULL,
        NULL,
        'showroom',
        '/media/showroom'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'Showroom-contacts',
        NULL,
        NULL,
        'Showroom-contacts',
        '/media/Showroom-contacts'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'sites',
        NULL,
        NULL,
        'sites',
        '/media/sites'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'uploads',
        NULL,
        NULL,
        'uploads',
        '/media/uploads'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'uploads2025',
        NULL,
        NULL,
        'uploads2025',
        '/media/uploads2025'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'uploads2026',
        NULL,
        NULL,
        'uploads2026',
        '/media/uploads2026'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'users',
        NULL,
        NULL,
        'users',
        '/media/users'
    ),
    (
        NULL,
        '[{"name":"/media/folder.png","size":123904,"type":"image/jpeg","tmp_name":"/media/folder.png","full_path":"AceWS_Image.jpg","objectURL":"/media/folder.png"}]',
        'folder',
        'vendors',
        NULL,
        NULL,
        'vendors',
        '/media/vendors'
    );

-- 10 March 2026
ALTER TABLE `customer` CHANGE `uuid` `uuid` varchar(255) NOT NULL;

-- 11 March 2026 nazmul
ALTER TABLE `service_request`
ADD `uuid` VARCHAR(255) NOT NULL AFTER `service_request_id`;

ALTER TABLE `service_request` ADD INDEX (`uuid`);

-- 12 March 2026 nazmul
ALTER TABLE `service_request`
ADD `form_type` VARCHAR(191) NULL AFTER `request_type`;

ALTER TABLE `service_request`
ADD `catalogue_format` VARCHAR(191) NULL DEFAULT NULL AFTER `last_name`;

ALTER TABLE `service_request`
ADD `phone_number` VARCHAR(191) NULL DEFAULT NULL AFTER `content`,
ADD `mailing_address` VARCHAR(191) NULL DEFAULT NULL AFTER `phone_number`;

-- 17 March 2026 abdullah
ALTER TABLE `visit_showroom`
ADD `note` TEXT DEFAULT NULL AFTER `time_zone`;

-- 23 March 2026 Shofiul
ALTER TABLE `taxonomy_item` ADD UNIQUE (`name`);

ALTER TABLE `taxonomy_item`
ADD `slider_image` JSON NULL DEFAULT NULL AFTER `image`;

ALTER TABLE `product_to_taxonomy_item`
ADD `sort_order` INT NOT NULL DEFAULT '0' AFTER `taxonomy_item_id`;

-- 28 March 2026 abdullah
ALTER TABLE `manufacturer`
CHANGE `image` `image` VARCHAR(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL;

-- 30 March 2026 abdullah
ALTER TABLE `showrooms`
ADD `is_section_active` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;

-- 30 March 2026 nazmul
ALTER TABLE `product`
ADD `video_url` JSON NULL DEFAULT NULL AFTER `video_link`;

-- 01 April 2026 abdullah
ALTER TABLE visit_showroom DROP FOREIGN KEY fk_visit_customer;

ALTER TABLE visit_showroom DROP INDEX idx_visit_customer_id;

ALTER TABLE visit_showroom DROP FOREIGN KEY fk_visit_showroom;

ALTER TABLE visit_showroom DROP INDEX idx_visit_showroom_id;

-- start_date, end_date, start_time, end_time, label

alter table visit_showroom
add column label varchar(191) null default null after tour_type;

alter table visit_showroom
add column meeting_link varchar(191) null default null after tour_type;

-- 07 April 2026 abdullah
alter table showrooms
add column google_map_link text null default null after status;

-- 08 April 2026 abdullah
alter table product
add column show_configurator tinyint(1) not null default 0 after ocean_plastic_used;

UPDATE product set `show_configurator` = 1;

alter table taxonomy_item
add column label_name varchar(191) null default null after slider_image;

alter table showroom_contact
add column sales_team_contact tinyint(1) not null default 0 after status;

INSERT INTO
    `showroom_contact` (
        `showroom_contact_id`,
        `showroom_id`,
        `name`,
        `image`,
        `email`,
        `phone`,
        `designation`,
        `message`,
        `sort_order`,
        `status`,
        `sales_team_contact`,
        `created_at`,
        `updated_at`,
        `deleted_at`
    )
VALUES (
        NULL,
        '1',
        'Krost Sales',
        '[]',
        'sales@krost.com.au',
        '0404 208 071',
        'Sales',
        'KROST internal sales team',
        '1',
        '1',
        '1',
        '2026-02-26 09:02:20',
        '2026-03-30 09:53:55',
        NULL
    );

-- 09 April 2026 abdullah
ALTER TABLE `visit_showroom`
CHANGE `meeting_link` `meeting_link` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;

-- 10 April 2026
ALTER TABLE `product_variant`
ADD `is_accessories` TINYINT NOT NULL DEFAULT '0' AFTER `is_default`;

-- 14 April 2026 abdullah
UPDATE `order_status`
SET
    `name` = 'Cancelled'
WHERE
    `order_status`.`order_status_id` = 5
    AND `order_status`.`language_id` = 1;

-- 15 April 2026 abdullah
SELECT `banner_way_points` FROM `project` WHERE 1;
-- UPDATE project set banner_way_points = null;

-- 16 April 2026 abdullah
ALTER TABLE `service_request`
ADD `source_of_enquiry` VARCHAR(191) NULL DEFAULT NULL AFTER `mailing_address`;

-- DELIVERY INSTALLATION QUERY
SELECT 
    `order`.order_id, 
    CONCAT("#", `order`.invoice_no) AS order_number, 
    `order`.customer_order_id, 
    `order`.order_description, 
    `order`.created_at, 
    `order`.updated_at, 
    logistic_types.name AS title, 
    logistic_statuses.name AS subtitle, 
    logistic_dates.date, 
    logistic_dates.expected_start AS time, 
    logistic_dates.expected_end, 
    logistic_dates.actual_start, 
    logistic_dates.actual_end 
FROM `logistic_dates` AS `logistic_dates`
LEFT JOIN `order` 
    ON `order`.order_id = logistic_dates.order_id
LEFT JOIN `logistic_types` 
    ON logistic_types.logistic_types_id = logistic_dates.logistic_types_id
LEFT JOIN `logistic_statuses` 
    ON logistic_statuses.logistic_statuses_id = logistic_dates.logistic_statuses_id
WHERE logistic_dates.customer_id = 16

-- aler
ALTER TABLE `pinboard`
ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 0;

-- UUID 
ALTER TABLE your_table_name 
ADD COLUMN customer_uuid BINARY(16) NOT NULL;

-- Save - To bynary 
-- Retrive - to string

function uuidToBin($uuid) {
    // Remove hyphens and pack into 16-byte binary string
    return pack("H*", str_replace('-', '', $uuid));
}

function binToUuid($binary) {
    // Unpack from binary to hex string
    $hex = unpack("H*", $binary)[1];
    
    // Add hyphens back in the correct positions
    return preg_replace(
        '/([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})/',
        '$1-$2-$3-$4-$5',
        $hex
    );
}


function generateUuidV4() {
    // Generate 16 bytes of random data
    $data = random_bytes(16);

    // Set version to 0100 (4)
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10 (variant)
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Format as 8-4-4-4-12 hex string
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

// Usage
$uuidString =  generateUuidV4(); 

// Convert to binary (16 bytes)
$binaryValue = uuidToBin($uuidString);

// Convert back to string
$backToString = binToUuid($binaryValue);

echo $backToString; // Output: 550e8400-e29b-41d4-a716-446655440000

ALTER TABLE `order` ADD COLUMN `uuid` BINARY(16) NOT NULL AFTER `invoice_no`;


-- 20 April 2026 Shofiul


ALTER TABLE `access_tokens`
MODIFY COLUMN `id` VARCHAR(128) NOT NULL;

ALTER TABLE `refresh_tokens`
MODIFY COLUMN `id` VARCHAR(128) NOT NULL;


-- 21 April 2026 abdullah
ALTER TABLE `user`
ADD COLUMN `uuid` BINARY(16) NOT NULL AFTER `user_id`;
ALTER TABLE `user`
ADD COLUMN `deleted_at` DATETIME NULL AFTER `updated_at`;


-- OAuth clients: secret column is bcrypt hash; plaintext must match .env (client 1) or ERP config (client 2).
-- client_id 1 plaintext: 7d7aa8f60ba2180d661a701b41549e2f62f134e6040ffe7c87a55e9cb0a88a1c  → OAUTH_CLIENT_SECRET
-- client_id 2 plaintext: 55effdf37b205ad8b76766a42fda22d62a03ef1cfd3bb7830d47b87b474a0fd3  → ERP M2M only
INSERT INTO `clients` (`id`, `secret`, `name`, `scopes`, `redirect_uri`, `revoked`, `is_confidential`, `created_at`) VALUES
(1, '$2y$12$Foa1q84Y/3i/NYzKnF2P4uZbdhHcPbkOp3.8.8tR27l7O9E4CRYCi', 'First-party web application', '[\"basic\",\"email\",\"profile\"]', 'https://krost.com.au/', 0, 1, '2026-04-20 06:16:17'),
(2, '$2y$12$XmQiMObBKpmQ6N0TCdlX3.XHNu4KQHj3ZvhsZ1buvgxDCvrgqshG.', 'ERP machine-to-machine', '[\"basic\",\"email\",\"profile\"]', 'urn:ietf:oauth:2.0:oob', 0, 1, '2026-04-20 06:16:17');

-- SELECT * FROM `product_resource` WHERE `product_id` = 138; // logic monitor arms
-- SELECT * FROM `product_resource` WHERE `product_id` = 139; // lua monitor arms
-- INSERT INTO `product_resource` (`product_resource_id`, `product_id`, `design_resource_id`, `resource_type`, `created_at`, `updated_at`, `deleted_at`, `sort_order`, `active_status`) VALUES (NULL, '139', '45', 'models', '2026-03-04 08:19:48', '2026-03-04 08:19:48', '2026-03-04 08:19:48', '0', '1');

-- 23 April

ALTER TABLE `users_auth_scopes` CHANGE `id` `users_auth_scopes_id` INT UNSIGNED NOT NULL AUTO_INCREMENT;
-- INSERT INTO `design_resource` (`design_resource_id`, `img`, `title`, `description`, `media_id`, `type`, `img2`, `is_featured`, `grade`, `slug`, `link_text`, `brand`, `resource_type`, `hex_value`, `sort_order`) VALUES ('418', NULL, 'lua Monitor Arms', NULL, NULL, NULL, NULL,  null, NULL, 'lua-monitor-arms', NULL, NULL, 'models', NULL, NULL);

-- UPDATE `design_resource_document` SET `design_resource_id` = 418 WHERE `design_resource_document_id` IN(470,471, 472, 473);


-- 24 April 2026 Shofiul

ALTER TABLE `user` ADD `is_admin` BOOLEAN NOT NULL DEFAULT FALSE AFTER `is_verified`;
ALTER TABLE `admin` ADD `user_id` INT UNSIGNED NOT NULL AFTER `admin_id`;


-- Upsert user first, then upsert admin for shofiul@krost.com.au
INSERT INTO `user`
(`uuid`, `user_group_id`, `site_id`, `username`, `first_name`, `last_name`, `password`, `email`, `is_verified`, `is_admin`, `phone_number`, `url`, `status`, `display_name`, `avatar`, `bio`, `token`, `subscribe`, `created_at`, `updated_at`)
VALUES
(UUID_TO_BIN(UUID()), 1, 1, 'shofiulalam', 'Shofiul', 'Alam', '', 'shofiul@krost.com.au', 1, 1, '0423170288', 'www.google.com', 1, 'Shofiul', '/demo/images/avatar/amyelsner.png', 'Biography details', NULL, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE
`username` = VALUES(`username`),
`first_name` = VALUES(`first_name`),
`last_name` = VALUES(`last_name`),
`phone_number` = VALUES(`phone_number`),
`url` = VALUES(`url`),
`display_name` = VALUES(`display_name`),
`avatar` = VALUES(`avatar`),
`bio` = VALUES(`bio`),
`is_verified` = 1,
`is_admin` = 1,
`status` = 1,
`updated_at` = NOW();

INSERT INTO `admin`
(`user_id`, `username`, `first_name`, `last_name`, `password`, `email`, `phone_number`, `url`, `display_name`, `avatar`, `bio`, `role_id`, `site_access`, `status`, `token`, `created_at`, `updated_at`)
SELECT
u.`user_id`, 'shofiulalam', 'Shofiul', 'Alam', '', 'shofiul@krost.com.au', '0423170288', 'www.google.com', 'Shofiul', '/demo/images/avatar/amyelsner.png', 'Biography details', 1, 'default', 1, 'testtoken', NOW(), NOW()
FROM `user` u
WHERE u.`email` = 'shofiul@krost.com.au'
ON DUPLICATE KEY UPDATE
`username` = VALUES(`username`),
`first_name` = VALUES(`first_name`),
`last_name` = VALUES(`last_name`),
`email` = VALUES(`email`),
`phone_number` = VALUES(`phone_number`),
`url` = VALUES(`url`),
`display_name` = VALUES(`display_name`),
`avatar` = VALUES(`avatar`),
`bio` = VALUES(`bio`),
`role_id` = VALUES(`role_id`),
`site_access` = VALUES(`site_access`),
`status` = VALUES(`status`),
`token` = VALUES(`token`),
`updated_at` = NOW();

INSERT INTO `admin` (`admin_id`, `user_id`, `username`, `first_name`, `last_name`, `password`, `email`, `phone_number`, `url`, `display_name`, `avatar`, `bio`, `role_id`, `site_access`, `status`, `token`, `created_at`, `updated_at`) VALUES
(2, 3, 'tyronkrost', 'Tyron', 'Krost', '', 'tyron@krost.com.au', '', '', 'Tyron', '/demo/images/avatar/amyelsner.png', 'Biography details', 1, 'default', 1, 'testtoken', '2025-04-04 04:10:05', '2025-04-04 04:10:05');

INSERT INTO `admin` (`admin_id`, `user_id`, `username`, `first_name`, `last_name`, `password`, `email`, `phone_number`, `url`, `display_name`, `avatar`, `bio`, `role_id`, `site_access`, `status`, `token`, `created_at`, `updated_at`) VALUES
(3, 5, 'ryansank', 'Ryan', 'Sank', '', 'ryan@krost.com.au', '', '', 'Ryan', '/demo/images/avatar/amyelsner.png', 'Biography details', 1, 'default', 1, 'testtoken', '2025-04-04 04:10:05', '2025-04-04 04:10:05');

-- Upsert user first, then upsert admin for jane@krost.com.au
INSERT INTO `user`
(`uuid`, `user_group_id`, `site_id`, `username`, `first_name`, `last_name`, `password`, `email`, `is_verified`, `is_admin`, `phone_number`, `url`, `status`, `display_name`, `avatar`, `bio`, `token`, `subscribe`, `created_at`, `updated_at`)
VALUES
(UUID_TO_BIN(UUID()), 1, 1, 'janenguyen', 'Jane', 'Nguyen', '', 'jane@krost.com.au', 1, 1, '', '', 1, 'Jane', '/demo/images/avatar/amyelsner.png', 'Biography details', NULL, 0, NOW(), NOW())
ON DUPLICATE KEY UPDATE
`username` = VALUES(`username`),
`first_name` = VALUES(`first_name`),
`last_name` = VALUES(`last_name`),
`phone_number` = VALUES(`phone_number`),
`display_name` = VALUES(`display_name`),
`avatar` = VALUES(`avatar`),
`bio` = VALUES(`bio`),
`is_verified` = 1,
`is_admin` = 1,
`status` = 1,
`updated_at` = NOW();

INSERT INTO `admin`
(`user_id`, `username`, `first_name`, `last_name`, `password`, `email`, `phone_number`, `url`, `display_name`, `avatar`, `bio`, `role_id`, `site_access`, `status`, `token`, `created_at`, `updated_at`)
SELECT
u.`user_id`, 'janenguyen', 'Jane', 'Nguyen', '', 'jane@krost.com.au', '', '', 'Jane', '/demo/images/avatar/amyelsner.png', 'Biography details', 1, 'default', 1, 'testtoken', NOW(), NOW()
FROM `user` u
WHERE u.`email` = 'jane@krost.com.au'
ON DUPLICATE KEY UPDATE
`username` = VALUES(`username`),
`first_name` = VALUES(`first_name`),
`last_name` = VALUES(`last_name`),
`email` = VALUES(`email`),
`phone_number` = VALUES(`phone_number`),
`url` = VALUES(`url`),
`display_name` = VALUES(`display_name`),
`avatar` = VALUES(`avatar`),
`bio` = VALUES(`bio`),
`role_id` = VALUES(`role_id`),
`site_access` = VALUES(`site_access`),
`status` = VALUES(`status`),
`token` = VALUES(`token`),
`updated_at` = NOW();

CREATE TABLE IF NOT EXISTS `admin_login_code` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code_hash` CHAR(64) NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `source` VARCHAR(32) NOT NULL DEFAULT 'otp',
  `ip_address` VARCHAR(45) NOT NULL DEFAULT '',
  `user_agent` VARCHAR(255) NOT NULL DEFAULT '',
  `expires_at` DATETIME NOT NULL,
  `consumed_at` DATETIME NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_admin_login_code_hash` (`code_hash`),
  KEY `idx_admin_login_code_user` (`user_id`),
  KEY `idx_admin_login_code_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin` (`admin_id`, `user_id`, `username`, `first_name`, `last_name`, `password`, `email`, `phone_number`, `url`, `display_name`, `avatar`, `bio`, `role_id`, `site_access`, `status`, `token`, `created_at`, `updated_at`) VALUES
(Null, 5, 'abdullah', 'Ali', 'Abdullah', '', 'abdullah@satechnology.com.au', '', '', 'Abdullah', '/demo/images/avatar/amyelsner.png', 'Biography details', 1, 'default', 1, 'testtoken', '2025-04-04 04:10:05', '2025-04-04 04:10:05');
INSERT INTO `admin` (`admin_id`, `user_id`, `username`, `first_name`, `last_name`, `password`, `email`, `phone_number`, `url`, `display_name`, `avatar`, `bio`, `role_id`, `site_access`, `status`, `token`, `created_at`, `updated_at`) VALUES
(null, 9, 'Nazmul Hossen', 'Nazmul', 'Hossen', '', 'nazmul@satechnology.com.au', '', '', 'Jane', '/demo/images/avatar/amyelsner.png', 'Biography details', 1, 'default', 1, 'testtoken', '2025-04-04 04:10:05', '2025-04-04 04:10:05');

-- 25 April 2026 abdullah
ALTER TABLE `comment` ADD COLUMN `is_reply` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`;


UPDATE product
SET
    specifications_image = CASE
        WHEN specifications_image IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(specifications_image AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    banner_image = CASE
        WHEN banner_image IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(banner_image AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    banner_way_points = CASE
        WHEN banner_way_points IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(banner_way_points AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    video_url = CASE
        WHEN video_url IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(video_url AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    image_thumb = CASE
        WHEN image_thumb IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(image_thumb AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    main_image_one = CASE
        WHEN main_image_one IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(main_image_one AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    main_image_two = CASE
        WHEN main_image_two IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(main_image_two AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    feature_image_one = CASE
        WHEN feature_image_one IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(feature_image_one AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    feature_image_two = CASE
        WHEN feature_image_two IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(feature_image_two AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    feature_image_three = CASE
        WHEN feature_image_three IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(feature_image_three AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    dimension_image = CASE
        WHEN dimension_image IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(dimension_image AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END
WHERE
    CAST(specifications_image AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(banner_image AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(banner_way_points AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(video_url AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(image_thumb AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(main_image_one AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(main_image_two AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(feature_image_one AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(feature_image_two AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(feature_image_three AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(dimension_image AS CHAR) LIKE '%http://54.252.147.60:8089%';


 UPDATE post
SET
    image = CASE
        WHEN image IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(image AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    banner_way_points = CASE
        WHEN banner_way_points IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(banner_way_points AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    feature_image_thumb = CASE
        WHEN feature_image_thumb IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(feature_image_thumb AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    feature_image = CASE
        WHEN feature_image IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(feature_image AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    image_banner = CASE
        WHEN image_banner IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(image_banner AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    image_thumb = CASE
        WHEN image_thumb IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(image_thumb AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    main_image_one = CASE
        WHEN main_image_one IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(main_image_one AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    main_image_two = CASE
        WHEN main_image_two IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(main_image_two AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END
WHERE
    CAST(image AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(banner_way_points AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(feature_image_thumb AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(feature_image AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(image_banner AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(image_thumb AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(main_image_one AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(main_image_two AS CHAR) LIKE '%http://54.252.147.60:8089%';

 UPDATE post_image
SET
    image_link = CASE
        WHEN image_link IS NULL THEN NULL
        ELSE REPLACE(image_link, 'http://54.252.147.60:8089', '')
    END,
    image = CASE
        WHEN image IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(image AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    way_points = CASE
        WHEN way_points IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(way_points AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END
WHERE
    image_link LIKE '%http://54.252.147.60:8089%'
 OR CAST(image AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(way_points AS CHAR) LIKE '%http://54.252.147.60:8089%';

 SELECT COUNT(*) AS rows_to_update
FROM project
WHERE
    CAST(image AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(image_thumb AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(main_image_one AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(main_image_two AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(banner_way_points AS CHAR) LIKE '%http://54.252.147.60:8089%';
UPDATE project
SET
    image = CASE
        WHEN image IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(image AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    image_thumb = CASE
        WHEN image_thumb IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(image_thumb AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    main_image_one = CASE
        WHEN main_image_one IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(main_image_one AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    main_image_two = CASE
        WHEN main_image_two IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(main_image_two AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    banner_way_points = CASE
        WHEN banner_way_points IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(banner_way_points AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END
WHERE
    CAST(image AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(image_thumb AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(main_image_one AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(main_image_two AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(banner_way_points AS CHAR) LIKE '%http://54.252.147.60:8089%';


SELECT COUNT(*) AS rows_to_update
FROM project_image
WHERE
    image_link LIKE '%http://54.252.147.60:8089%'
 OR CAST(image AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(way_points AS CHAR) LIKE '%http://54.252.147.60:8089%';
UPDATE project_image
SET
    image_link = CASE
        WHEN image_link IS NULL THEN NULL
        ELSE REPLACE(image_link, 'http://54.252.147.60:8089', '')
    END,
    image = CASE
        WHEN image IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(image AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    way_points = CASE
        WHEN way_points IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(way_points AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END
WHERE
    image_link LIKE '%http://54.252.147.60:8089%'
 OR CAST(image AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(way_points AS CHAR) LIKE '%http://54.252.147.60:8089%';

UPDATE media SET file = CASE WHEN file IS NULL THEN NULL ELSE CAST(REPLACE(CAST(file AS CHAR), 'http://54.252.147.60:8089', '') AS JSON) END, path = CASE WHEN path IS NULL THEN NULL ELSE REPLACE(path, 'http://54.252.147.60:8089', '') END WHERE CAST(file AS CHAR) LIKE '%http://54.252.147.60:8089%' OR path LIKE '%http://54.252.147.60:8089%';


UPDATE design_resource
SET
    img = CASE
        WHEN img IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(img AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END,
    img2 = CASE
        WHEN img2 IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(img2 AS CHAR), 'http://54.252.147.60:8089', '') AS JSON)
    END
WHERE
    CAST(img AS CHAR) LIKE '%http://54.252.147.60:8089%'
 OR CAST(img2 AS CHAR) LIKE '%http://54.252.147.60:8089%';

 UPDATE design_resource_document
SET url = REPLACE(url, 'http://54.252.147.60:8089', '')
WHERE url LIKE '%http://54.252.147.60:8089%';


-- 28 April 2026 abdullah
UPDATE `component` SET `section_link` = '/catalogue' WHERE `component`.`component_id` = 42;

-- add pinobaed note column
ALTER TABLE `pinboard` ADD COLUMN `note` TEXT NULL AFTER `is_active`;

-- 30 April 2026 abdullah
SELECT 
    `product_id`,
    specifications,
    CASE
        WHEN specifications IS NULL OR TRIM(specifications) = '' THEN 'empty_or_null'
        WHEN specifications REGEXP '<br[[:space:]]*/?>' THEN 'has_br_tag'
        WHEN specifications REGEXP '\r|\n' THEN 'has_newline'
        ELSE 'plain_text'
    END AS spec_type
FROM product
WHERE specifications IS NOT NULL
  AND TRIM(specifications) <> '';

-- 30 April 2026 abdullah
-- testing product resource and design resource document
-- SELECT * FROM `product_resource` WHERE `product_id` = 117;
-- INSERT INTO `product_resource`
-- (`product_resource_id`, `product_id`, `design_resource_id`, `resource_type`, `created_at`, `updated_at`, `deleted_at`, `sort_order`, `active_status`)
-- VALUES
-- (6, 117, 6, 'documents', '2026-03-05 04:14:15', '2026-03-05 04:14:15', '2026-03-05 04:14:15', 0, 1);
DELETE FROM `product_resource` WHERE `product_resource_id`=6;
select * from design_resource_document where design_resource_id = 6;
DELETE FROM `design_resource_document` WHERE `design_resource_document_id`=6;
-- INSERT INTO `design_resource_document`
-- (`design_resource_document_id`, `design_resource_id`, `media_id`, `name`, `url`, `description`, `format`)
-- VALUES
-- (6, 6, 12268, 'Krost User Guide - JJ Swivel.pdf', '/media/design-resource/documents/Krost User Guide - JJ Swivel.pdf', '', 'PDF');

-- 30 April 2026 abdullah
-- testing Alex 4 
SELECT * FROM `product_resource` WHERE `product_id` = 4;
-- 50
SELECT * FROM `design_resource_document` WHERE `design_resource_id` = 50;

[
    {
        "url": "/media/design-resource/models/Alex Chair DWG-Krost.zip",
        "name": "Alex Chair DWG-Krost.zip",
        "format": "DWG",
        "media_id": 12346,
        "description": null,
        "design_resource_id": 50,
        "design_resource_document_id": 83
    },
    {
        "url": "/media/design-resource/models/Alex Chair GSM-Krost.zip",
        "name": "Alex Chair GSM-Krost.zip",
        "format": "GSM",
        "media_id": 30475,
        "description": null,
        "design_resource_id": 50,
        "design_resource_document_id": 858
    },
    {
        "url": "/media/design-resource/models/Alex Chair Max-Krost.max",
        "name": "Alex Chair Max-Krost.max",
        "format": "MAX",
        "media_id": 12347,
        "description": null,
        "design_resource_id": 50,
        "design_resource_document_id": 84
    },
    {
        "url": "/media/design-resource/models/Alex Chair Revit18-Krost.rfa",
        "name": "Alex Chair Revit18-Krost.rfa",
        "format": "RFA",
        "media_id": 12348,
        "description": null,
        "design_resource_id": 50,
        "design_resource_document_id": 85
    },
    {
        "url": "/media/design-resource/models/Alex Chair SKP-Krost.skp",
        "name": "Alex Chair SKP-Krost.skp",
        "format": "SKP",
        "media_id": 12349,
        "description": null,
        "design_resource_id": 50,
        "design_resource_document_id": 86
    },
    {
        "url": "/media/design-resource/models/Alex Chair DWG-Krost.zip",
        "name": "Alex Chair DWG-Krost.zip",
        "format": "DWG",
        "media_id": 12346,
        "description": null,
        "design_resource_id": 50,
        "design_resource_document_id": 83
    },
    {
        "url": "/media/design-resource/models/Alex Chair GSM-Krost.zip",
        "name": "Alex Chair GSM-Krost.zip",
        "format": "GSM",
        "media_id": 30475,
        "description": null,
        "design_resource_id": 50,
        "design_resource_document_id": 858
    },
    {
        "url": "/media/design-resource/models/Alex Chair Max-Krost.max",
        "name": "Alex Chair Max-Krost.max",
        "format": "MAX",
        "media_id": 12347,
        "description": null,
        "design_resource_id": 50,
        "design_resource_document_id": 84
    },
    {
        "url": "/media/design-resource/models/Alex Chair Revit18-Krost.rfa",
        "name": "Alex Chair Revit18-Krost.rfa",
        "format": "RFA",
        "media_id": 12348,
        "description": null,
        "design_resource_id": 50,
        "design_resource_document_id": 85
    },
    {
        "url": "/media/design-resource/models/Alex Chair SKP-Krost.skp",
        "name": "Alex Chair SKP-Krost.skp",
        "format": "SKP",
        "media_id": 12349,
        "description": null,
        "design_resource_id": 50,
        "design_resource_document_id": 86
    }
]

-- showing duplicate design_resource_document_id
SELECT * FROM `design_resource_document` WHERE design_resource_document_id IN (83, 858, 84, 85, 86, 83,  858, 84, 85, 86);

-- 02 May 2026 abdullah
SELECT * FROM `design_resource_document` WHERE `design_resource_id` = 50;
SELECT * FROM `product_resource` WHERE resource_type = 'documents';

-- 05 May 2026 Nazmul
ALTER TABLE `pinboard` ADD `is_visible` TINYINT(1) NOT NULL DEFAULT '1' AFTER `is_active`;

-- 05 May 2026 abdullah

DELETE FROM `product_resource` WHERE `product_resource_id`=6;
DELETE FROM `design_resource_document` WHERE `design_resource_document_id`=6;
UPDATE `customer` SET `company_name` = '' WHERE `customer`.`gmail_Id` = 'jane@krost.com.au';
select * from design_resource_document where design_resource_id = 6;

SELECT
    dd.*
FROM
    `design_resource` d
JOIN design_resource_document dd ON
    dd.design_resource_id = d.design_resource_id
WHERE
    d.`design_resource_id` = 86 and dd.design_resource_document_id in (946, 947, 948)
ORDER BY
    dd.design_resource_document_id;

DELETE FROM `design_resource_document` WHERE `design_resource_id` = 86 and `design_resource_document_id` IN (946, 947, 948);

-- delete user by email 
-- DELETE FROM `user` WHERE `email` = 'abdullah@satechnology.com.au';
-- DELETE FROM `customer` WHERE `gmail_Id` = 'abdullah@satechnology.com.au';


-- 06 May 2026 abdullah
ALTER TABLE `pinboard_item` ADD COLUMN `product_url` varchar(255) DEFAULT NULL AFTER `photo`;
ALTER TABLE `pinboard_temp_item` ADD COLUMN `product_url` varchar(255) DEFAULT NULL AFTER `photo`;

UPDATE `order` SET uuid = UUID() WHERE uuid IS NULL OR uuid = '';
UPDATE `quote` SET uuid = UUID() WHERE uuid IS NULL OR uuid = '';
UPDATE `comment` SET uuid = UUID() WHERE uuid IS NULL OR uuid = '';

ALTER TABLE `design_resource` ADD `image_url` VARCHAR(500) NULL DEFAULT NULL AFTER `img2`, 
ADD `image_thumb_url` VARCHAR(500) NULL DEFAULT NULL AFTER `image_url`;

-- The error indicates the `title` column is not numeric,
-- but rather contains string values like 'Beige'.
-- Titles should be quoted as strings.
delete from design_resource where resource_type = "textiles" and title in (
"Hydro",
"Scoot",
"Spring",
"Haste",
"Race",
"Whiz",
"Prompt",
"Waterlily",
"Tapestry",
"Windfall",
"Lyrical",
"Maze",
"Kindle",
"Yonder",
"Brewer",
"Roast",
"Blizzard",
"Heaven",
"Potter",
"Hemingway",
"Shakespeare"
);


-- 16 May 2026 abdullah
UPDATE `project_section_products` SET `product_id` = '276' WHERE `project_section_products`.`project_section_products_id` = 167;

-- delete project_section_images
DELETE FROM `project_section_images` WHERE `project_section_images_id` = 2514;
UPDATE `order_status` SET `name` = 'In-discussion ' WHERE `order_status`.`order_status_id` = 8 AND `order_status`.`language_id` = 1;


ALTER TABLE `design_resource` DROP INDEX `uniq_title_resource_type` ON `design_resource`;

ALTER TABLE `design_resource`
ADD UNIQUE KEY `uq_design_resource_title_type_brand` (`title`, `resource_type`, `brand`);
-- 18 May 2026 abdullah
delete from product where product_id = 301;
delete from product_content where product_id = 301;

-- 19 May 2026 Shoful
Alter table `pinboard` add column `lead_id` int unsigned null after `pinboard_id`;

-- 19 May 2026 abdullah -- issue not update media file ace product id 1
UPDATE `media` SET `file` = '{\"name\":\"AceWS_Image.jpg\",\"size\":123904,\"type\":\"image/jpeg\",\"tmp_name\":\"/var/www/html/public/media/Products/image/AceWS_Image.webp\",\"full_path\":\"AceWS_Image.jpg\",\"objectURL\":\"/media/Products/image/AceWS_Image.webp\"}' WHERE `media`.`media_id` = 1;

-- 23 May 2026 abdullah
UPDATE `component_item` SET `fields` = '[{\"name\":\"icon\",\"type\":{\"type\":{\"max\":100,\"min\":0,\"mask\":\"\",\"name\":\"InputText\",\"step\":1,\"type\":\"InputText\",\"value\":\"\",\"length\":0,\"options\":[],\"required\":false,\"keyfilter\":\"\",\"placeholder\":\"InputText\",\"suggestions\":[],\"treeOptions\":[],\"editorConfig\":[],\"cascadeOptions\":[]}},\"value\":\"fa-solid fa-phone\",\"options\":[],\"imagesData\":[],\"value_editor\":\"\"},{\"name\":\"title\",\"type\":{\"type\":{\"max\":100,\"min\":0,\"mask\":\"\",\"name\":\"InputText\",\"step\":1,\"type\":\"InputText\",\"value\":\"\",\"length\":0,\"options\":[],\"required\":false,\"keyfilter\":\"\",\"placeholder\":\"InputText\",\"suggestions\":[],\"treeOptions\":[],\"editorConfig\":[],\"cascadeOptions\":[]}},\"value\":\"Talk To an Expert\",\"options\":[],\"imagesData\":[],\"value_editor\":\"\"},{\"name\":\"description\",\"type\":{\"type\":{\"max\":100,\"min\":0,\"mask\":\"\",\"name\":\"InputText\",\"step\":1,\"type\":\"InputText\",\"value\":\"\",\"length\":0,\"options\":[],\"required\":false,\"keyfilter\":\"\",\"placeholder\":\"InputText\",\"suggestions\":[],\"treeOptions\":[],\"editorConfig\":[],\"cascadeOptions\":[]}},\"value\":\"Get expert advice for your next project.\",\"options\":[],\"imagesData\":[],\"value_editor\":\"\"},{\"name\":\"link_text\",\"type\":{\"type\":{\"max\":100,\"min\":0,\"mask\":\"\",\"name\":\"InputText\",\"step\":1,\"type\":\"InputText\",\"value\":\"\",\"length\":0,\"options\":[],\"required\":false,\"keyfilter\":\"\",\"placeholder\":\"InputText\",\"suggestions\":[],\"treeOptions\":[],\"editorConfig\":[],\"cascadeOptions\":[]}},\"value\":\"Contact Us\",\"options\":[],\"imagesData\":[],\"value_editor\":\"\"},{\"name\":\"link\",\"type\":{\"type\":{\"max\":100,\"min\":0,\"mask\":\"\",\"name\":\"InputText\",\"step\":1,\"type\":\"InputText\",\"value\":\"\",\"length\":0,\"options\":[],\"required\":false,\"keyfilter\":\"\",\"placeholder\":\"InputText\",\"suggestions\":[],\"treeOptions\":[],\"editorConfig\":[],\"cascadeOptions\":[]}},\"value\":\"/contact-sales#th-contact-members\",\"options\":[],\"imagesData\":[],\"value_editor\":\" \"}]' WHERE `component_item`.`component_item_id` = 114;

-- Remove http/https domain from component JSON image columns (objectURL etc. are embedded in JSON, not plain URLs).
UPDATE `component`
SET
    `image` = CASE
        WHEN `image` IS NULL THEN NULL
        ELSE CAST(REGEXP_REPLACE(CAST(`image` AS CHAR), 'https?://[^/"]+', '') AS JSON)
    END,
    `images` = CASE
        WHEN `images` IS NULL THEN NULL
        ELSE CAST(REGEXP_REPLACE(CAST(`images` AS CHAR), 'https?://[^/"]+', '') AS JSON)
    END
WHERE
    `image` LIKE '%http%'
    OR `images` LIKE '%http%';

-- Remove http/https domain from component_item JSON fields (image/img FileUpload objectURL etc.).
UPDATE `component_item`
SET
    `fields` = CASE
        WHEN `fields` IS NULL THEN NULL
        ELSE CAST(REGEXP_REPLACE(CAST(`fields` AS CHAR), 'https?://[^/"]+', '') AS JSON)
    END,
    `related_models` = CASE
        WHEN `related_models` IS NULL THEN NULL
        ELSE CAST(REGEXP_REPLACE(CAST(`related_models` AS CHAR), 'https?://[^/"]+', '') AS JSON)
    END
WHERE
    `fields` LIKE '%http%'
    OR `related_models` LIKE '%http%';

-- Remove http/https domain from media table (2025_03_14_create_media_table: file JSON, path varchar).
UPDATE `media`
SET
    `file` = CASE
        WHEN `file` IS NULL THEN NULL
        ELSE CAST(REGEXP_REPLACE(CAST(`file` AS CHAR), 'https?://[^/"]+', '') AS JSON)
    END
WHERE
    CAST(`file` AS CHAR) LIKE '%http%';

UPDATE `media` AS `m1`
INNER JOIN (
    SELECT
        `media_id`,
        REGEXP_REPLACE(`path`, 'https?://[^/"]+', '') AS `new_path`
    FROM `media`
    WHERE
        `path` LIKE '%http%'
) AS `clean` ON `clean`.`media_id` = `m1`.`media_id`
LEFT JOIN `media` AS `m2` ON `m2`.`path` = `clean`.`new_path` AND `m2`.`media_id` <> `m1`.`media_id`
SET
    `m1`.`path` = `clean`.`new_path`
WHERE
    `m2`.`media_id` IS NULL;

-- Drop http-path duplicates when a row with the relative path already exists (uk_media_path).
DELETE `m1`
FROM `media` AS `m1`
INNER JOIN `media` AS `m2`
    ON `m2`.`path` = REGEXP_REPLACE(`m1`.`path`, 'https?://[^/"]+', '')
    AND `m2`.`media_id` <> `m1`.`media_id`
WHERE
    `m1`.`path` LIKE '%http%';

-- Strip production. subdomain from product image_thumb URLs (e.g. production.krost.com.au -> krost.com.au).
UPDATE `product`
SET
    `image_thumb` = CASE
        WHEN `image_thumb` IS NULL THEN NULL
        ELSE CAST(REPLACE(CAST(`image_thumb` AS CHAR), 'production.', '') AS JSON)
    END
WHERE
    CAST(`image_thumb` AS CHAR) LIKE '%production.%';


select * from design_resource where resource_type = "models";

delete from design_resource_document where design_resource_id in (select design_resource_id from design_resource where resource_type = "models");
delete from design_resource where resource_type = "models";


UPDATE media

 SET `file` = REPLACE(cast(`file` as char), ".jpg", ".webp") 
 
 WHERE `file` like "%Products/image%";

-- 23 May 2026 abdullah
UPDATE `component_item` SET `fields` = '[{\"name\":\"icon\",\"type\":{\"type\":{\"max\":100,\"min\":0,\"mask\":\"\",\"name\":\"InputText\",\"step\":1,\"type\":\"InputText\",\"value\":\"\",\"length\":0,\"options\":[],\"required\":false,\"keyfilter\":\"\",\"placeholder\":\"InputText\",\"suggestions\":[],\"treeOptions\":[],\"editorConfig\":[],\"cascadeOptions\":[]}},\"value\":\"fa-solid fa-phone\",\"options\":[],\"imagesData\":[],\"value_editor\":\"\"},{\"name\":\"title\",\"type\":{\"type\":{\"max\":100,\"min\":0,\"mask\":\"\",\"name\":\"InputText\",\"step\":1,\"type\":\"InputText\",\"value\":\"\",\"length\":0,\"options\":[],\"required\":false,\"keyfilter\":\"\",\"placeholder\":\"InputText\",\"suggestions\":[],\"treeOptions\":[],\"editorConfig\":[],\"cascadeOptions\":[]}},\"value\":\"Talk To an Expert\",\"options\":[],\"imagesData\":[],\"value_editor\":\"\"},{\"name\":\"description\",\"type\":{\"type\":{\"max\":100,\"min\":0,\"mask\":\"\",\"name\":\"InputText\",\"step\":1,\"type\":\"InputText\",\"value\":\"\",\"length\":0,\"options\":[],\"required\":false,\"keyfilter\":\"\",\"placeholder\":\"InputText\",\"suggestions\":[],\"treeOptions\":[],\"editorConfig\":[],\"cascadeOptions\":[]}},\"value\":\"Get expert advice for your next project.\",\"options\":[],\"imagesData\":[],\"value_editor\":\"\"},{\"name\":\"link_text\",\"type\":{\"type\":{\"max\":100,\"min\":0,\"mask\":\"\",\"name\":\"InputText\",\"step\":1,\"type\":\"InputText\",\"value\":\"\",\"length\":0,\"options\":[],\"required\":false,\"keyfilter\":\"\",\"placeholder\":\"InputText\",\"suggestions\":[],\"treeOptions\":[],\"editorConfig\":[],\"cascadeOptions\":[]}},\"value\":\"Contact Us\",\"options\":[],\"imagesData\":[],\"value_editor\":\"\"},{\"name\":\"link\",\"type\":{\"type\":{\"max\":100,\"min\":0,\"mask\":\"\",\"name\":\"InputText\",\"step\":1,\"type\":\"InputText\",\"value\":\"\",\"length\":0,\"options\":[],\"required\":false,\"keyfilter\":\"\",\"placeholder\":\"InputText\",\"suggestions\":[],\"treeOptions\":[],\"editorConfig\":[],\"cascadeOptions\":[]}},\"value\":\"/contact-sales#th-contact-members\",\"options\":[],\"imagesData\":[],\"value_editor\":\" \"}]' WHERE `component_item`.`component_item_id` = 114;

ALTER TABLE `visit_showroom` ADD `cancelled_at` TIMESTAMP NULL AFTER `updated_at`;
CREATE INDEX idx_visit_showroom_cancelled_at ON `visit_showroom`(`cancelled_at`);

-- 1-6-2026 abdullah
ALTER TABLE `service_request` ADD   `state` varchar(191) null default null;
ALTER TABLE `visit_showroom` ADD  `enquiry_type` VARCHAR(100) DEFAULT NULL;
ALTER TABLE `visit_showroom` ADD `source` VARCHAR(255) NOT NULL AFTER `note`;

TRUNCATE TABLE `post_status`;

INSERT INTO `post_status` (`post_status_id`, `language_id`, `name`) VALUES
(1, 1, 'Draft'),
(2, 1, 'Future'),
(3, 1, 'Pending'),
(4, 1, 'Published'),
(5, 1, 'Private'),
(6, 1, 'Trash');

ALTER TABLE `post` ADD  `status_id` int(20) default null AFTER `status`;

-- 2-6-2026
ALTER TABLE `service_request` ADD `project_details` varchar(191) default null AFTER `state`;

ALTER TABLE `user` 
ADD `notify_orders` TINYINT(1) NOT NULL DEFAULT '0' AFTER `subscribe`, 
ADD `notify_quotes` TINYINT(1) NOT NULL DEFAULT '0' AFTER `notify_orders`;


-- 05-06-2026
UPDATE `design_resource_document`
SET `format` = 'RFA'
WHERE `design_resource_document_id` IN (1316, 1317, 1319);

-- 06-06-2026 nazmul 
TRUNCATE TABLE `project_status`;

INSERT INTO `project_status` (`project_status_id`, `language_id`, `name`) VALUES
(1, 1, 'Draft'),
(2, 1, 'Future'),
(3, 1, 'Pending'),
(4, 1, 'Published'),
(5, 1, 'Private'),
(6, 1, 'Trash');

UPDATE project set status_id = 4, status = "Published" where project_id is not null;

SELECT * FROM `design_resource_document` WHERE name like "%AFRDI%";
-- design_resource_document 
ALTER TABLE `design_resource_document`
ADD `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
ADD `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;


-- 13-06-2026 (abdullah)


-- 17-06-2026


UPDATE `component_item` SET `fields` = '{
    "0": {
        "name": "contact_email",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "sales@krost.com.au",
        "imagesData": []
    },
    "1": {
        "name": "contact_phone",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "1800 1KROST",
        "imagesData": []
    },
    "2": {
        "name": "sydney_office_name",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Sydney Office",
        "imagesData": []
    },
    "3": {
        "name": "sydney_office_address",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "33 Ricketty Street, Mascot NSW, 2020",
        "imagesData": []
    },
    "social_media": {
        "name": "social_media",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": [
            {
                "url": "https://www.linkedin.com/company/krost-business-furniture/",
                "icon": "fa-brands fa-linkedin-in",
                "platform": "LinkedIn"
            },
            {
                "url": "https://www.facebook.com/krostfurniture",
                "icon": "fa-brands fa-facebook-f",
                "platform": "Facebook"
            },
            {
                "url": "https://www.instagram.com/krostfurniture/",
                "icon": "fa-brands fa-instagram",
                "platform": "Instagram"
            },
            {
                "url": "https://www.pinterest.com/krostfurniture/",
                "icon": "fa-brands fa-pinterest-p",
                "platform": "Pinterest"
            },
            {
                "url": "https://www.youtube.com/@KrostAu",
                "icon": "fa-brands fa-youtube",
                "platform": "Youtube"
            }
        ],
        "imagesData": []
    },
    "copyright_year": {
        "name": "copyright_year",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "2025",
        "imagesData": []
    },
    "subscription_title": {
        "name": "subscription_title",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Get krost product updates",
        "imagesData": []
    },
    "copyright_terms_url": {
        "name": "copyright_terms_url",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "content/page.html",
        "imagesData": []
    },
    "sydney_office_hours": {
        "name": "sydney_office_hours",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Open weekdays, 8am to 5pm",
        "imagesData": []
    },
    "sydney_office_phone": {
        "name": "sydney_office_phone",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "02 9557 3055",
        "imagesData": []
    },
    "brisbane_office_hours": {
        "name": "brisbane_office_hours",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Open weekdays, 8am to 5pm",
        "imagesData": []
    },
    "brisbane_office_phone": {
        "name": "sydney_office_phone",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "02 9557 3055",
        "imagesData": []
    },
    "copyright_privacy_url": {
        "name": "copyright_privacy_url",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "content/page.html",
        "imagesData": []
    },
    "melbourne_office_name": {
        "name": "melbourne_office_name",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Melbourne Office",
        "imagesData": []
    },
    "brisbane_office_name": {
        "name": "brisbane_office_name",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "brisbane Office",
        "imagesData": []
    },
    "copyright_company_name": {
        "name": "copyright_company_name",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "KROST",
        "imagesData": []
    },
    "melbourne_office_hours": {
        "name": "melbourne_office_hours",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "open weekdays, 9am to 5pm",
        "imagesData": []
    },
    "melbourne_office_phone": {
        "name": "melbourne_office_phone",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "03 9682 8280",
        "imagesData": []
    },
    "copyright_powered_by_url": {
        "name": "copyright_powered_by_url",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "",
        "imagesData": []
    },
    "melbourne_office_address": {
        "name": "melbourne_office_address",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "17-643 spencer st, West Melbourne VIC, 3003",
        "imagesData": []
    },
    "subscription_button_text": {
        "name": "subscription_button_text",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Subscribe Now",
        "imagesData": []
    },
    "subscription_description": {
        "name": "subscription_description",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Receive the latest news and updates from Krost",
        "imagesData": []
    },
    "subscription_placeholder": {
        "name": "subscription_placeholder",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Your Email Address Please",
        "imagesData": []
    },
    "copyright_powered_by_text": {
        "name": "copyright_powered_by_text",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Powered by Krost",
        "imagesData": []
    },
    "footer_navigation_visit_us": {
        "name": "footer_navigation_visit_us",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Visit Us",
        "imagesData": []
    },
    "footer_navigation_our_store": {
        "name": "footer_navigation_our_store",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Our Store",
        "imagesData": []
    },
    "footer_navigation_contact_us": {
        "name": "footer_navigation_contact_us",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "Contact Us",
        "imagesData": []
    },
    "footer_navigation_contact_us_url": {
        "name": "footer_navigation_contact_us_url",
        "type": {
            "type": {
                "max": 100,
                "min": 0,
                "mask": "",
                "name": "InputText",
                "step": 1,
                "type": "InputText",
                "value": "",
                "length": 0,
                "options": [],
                "required": false,
                "keyfilter": "",
                "placeholder": "InputText",
                "suggestions": [],
                "treeOptions": [],
                "editorConfig": [],
                "cascadeOptions": []
            }
        },
        "isNew": true,
        "value": "#",
        "imagesData": []
    }
}' WHERE `component_item`.`component_item_id` = 74;

ALTER TABLE `product_image`
ADD `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
ADD `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- 14-06-2026 (abdullah)
ALTER TABLE `product` CHANGE `created_at` `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP, CHANGE `updated_at` `updated_at` TIMESTAMP on update CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;

update project set status_id = 4, status = 'Published';

-- 15-06-2026 (abdullah)
ALTER TABLE `product_content` ADD `rules` TEXT NULL DEFAULT NULL AFTER `icon`;

-- 16-06-2026 
DELETE FROM `product_certificate` WHERE `product_certificate`.`product_certificate_id` = 74;
DELETE FROM `product_certificate` WHERE `product_certificate`.`product_certificate_id` = 76;
DELETE FROM `product_certificate` WHERE `product_certificate`.`product_certificate_id` = 22;
DELETE FROM `product_certificate` WHERE `product_certificate`.`product_certificate_id` = 23;
DELETE FROM `product_certificate` WHERE `product_certificate`.`product_certificate_id` = 70;
DELETE FROM `product_certificate` WHERE `product_certificate`.`product_certificate_id` = 72;

ALTER TABLE `product_certificate` ADD UNIQUE `uniq_product_id_type` (`product_id`, `title`);

ALTER TABLE `product_certificate`
ADD `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
ADD `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE post_image
ADD created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
ADD updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

UPDATE `product` SET `ocean_plastic_used` = '0';

ALTER TABLE `pinboard_temp_item` CHANGE `description` `description` VARCHAR(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL;

-- 13-6-2026
ALTER TABLE `user`
ADD `designation` VARCHAR(50) DEFAULT NULL AFTER `bio`;

-- 23-6-2026
ALTER TABLE `project`
ADD COLUMN `credit_label` VARCHAR(100) DEFAULT 'Designed by';

social_media
{
    "url": "https://www.youtube.com/@KrostAu",
    "icon": "fa-brands fa-youtube-p",
    "platform": "Youtube"
}

ALTER TABLE `post_content`
  MODIFY COLUMN `meta_keywords` VARCHAR(750) NULL,
  MODIFY COLUMN `meta_description` VARCHAR(750) NULL,
  MODIFY COLUMN `meta_title` VARCHAR(750) NULL;

  -- 24-6-2026
ALTER TABLE `design_resource`
ADD COLUMN `tag` VARCHAR(100) DEFAULT null;

-- 25-6-2026
ALTER TABLE `product`
CHANGE `width` `width` VARCHAR(191) NULL,
CHANGE `height` `height` VARCHAR(191) NULL,
CHANGE `depth` `depth` VARCHAR(191) NULL;

-- 25-6-2026 nazmul 
INSERT INTO `component` (`component_id`, `name`, `section_title`, `section_subtitle`, `section_link`, `title`, `subtitle`, `description`, `image`, `images`, `links`, `buttons`, `template`, `active`, `model`, `banner_way_points`) VALUES (NULL, 'catalogueconfirmation', '', '', '', '', '', '', '[]', '[]', '[]', '[]', '', '1', NULL, NULL)
-- 26-06-2026
INSERT INTO `component` (`component_id`, `name`, `section_title`, `section_subtitle`, `section_link`, `title`, `subtitle`, `description`, `image`, `images`, `links`, `buttons`, `template`, `active`, `model`, `banner_way_points`) VALUES (NULL, 'blogrelatedarticleslider', 'Related Articles', 'Discover more articles.', '', '', '', '', '[]', '[]', '[]', '[]', 'feature', '1', NULL, NULL);

-- Add created_at and updated_at to project_image (matches 2025_05_06_create_project_image_table.php)

ALTER TABLE project_image
ADD COLUMN created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER way_points,
ADD COLUMN updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- 29-06-2026
ALTER TABLE `project_section_products` ADD `finish_material` TEXT NULL DEFAULT NULL AFTER `product_id`;

UPDATE `site` SET `site_id` = '1' WHERE `site`.`host` = "https://krost.com.au";

-- 03-07-2026 nazmul
ALTER TABLE `component` ADD `mobile_banner` JSON NULL DEFAULT NULL AFTER `image`;
-- 5-07-2026 header
UPDATE `component_item` SET `fields` = '[{\"name\": \"top_header_middle_text\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"The 2026 Workspace Catalogue is Here\", \"options\": [], \"imagesData\": [], \"value_editor\": \"\"}, {\"name\": \"top_header_middle_text_link\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"/catalogue\", \"options\": [], \"imagesData\": [], \"value_editor\": \"\"}, {\"name\": \"top_header_right_text\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"Catalogue Copy\", \"options\": [], \"imagesData\": [], \"value_editor\": \"\"}, {\"name\": \"Top_header_right_text_link\", \"type\": {\"type\": {\"max\": 100, \"min\": 0, \"mask\": \"\", \"name\": \"InputText\", \"step\": 1, \"type\": \"InputText\", \"value\": \"\", \"length\": 0, \"options\": [], \"required\": false, \"keyfilter\": \"\", \"placeholder\": \"InputText\", \"suggestions\": [], \"treeOptions\": [], \"editorConfig\": [], \"cascadeOptions\": []}}, \"value\": \"/catalogue\", \"options\": [], \"imagesData\": [], \"value_editor\": \"\"}]\r\n' 
WHERE `component_item`.`component_item_id` = 227;

ALTER Table `comment` ADD COLUMN `is_checked` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_reply`;
