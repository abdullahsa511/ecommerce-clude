<?php

declare(strict_types=1);


class CreateCommentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function up(): void
    {
        try {
            $sql = "CREATE TABLE `comment` (
                `comment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `uuid` binary(16) NOT NULL,
                `post_id` int(10) UNSIGNED NULL DEFAULT NULL,
                `model_id` INT(20) UNSIGNED DEFAULT NULL COMMENT 'product_id, project_id, post_id, pinboard_item_id, showroom_id',
                `model_type` VARCHAR(50) DEFAULT NULL COMMENT 'product, project, post, pinboard item, showrooms',
                `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
                `author` tinytext NOT NULL,
                `email` varchar(100) NOT NULL DEFAULT '',
                `url` varchar(200) NOT NULL DEFAULT '',
                `ip` varchar(100) NOT NULL DEFAULT '',
                `content` text NOT NULL,
                `status` tinyint UNSIGNED NOT NULL DEFAULT 0,
                `is_reply` tinyint(1) NOT NULL DEFAULT 0,
                `is_checked` tinyint(1) NOT NULL DEFAULT 0,
                `votes` SMALLINT(3) UNSIGNED NOT NULL DEFAULT 0,
                `type` varchar(20) NOT NULL DEFAULT '',
                `parent_id` INT UNSIGNED NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`comment_id`),
                KEY `post_id` (`post_id`, `status`),
                KEY `parent` (`parent_id`),
                KEY `email` (`email`(10)),
                KEY `model_id` (`model_id`),
                KEY `model_type` (`model_type`),
                key `comment_post_id` (`post_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4";

            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new PDOException("Error creating comment table: " . $e->getMessage());
        }
    }

    public function down(): void
    {
        try {
            $sql = "DROP TABLE IF EXISTS `comment`";
            $this->pdo->exec($sql);
        } catch (PDOException $e) {
            throw new PDOException("Error dropping comment table: " . $e->getMessage());
        }
    }
} 