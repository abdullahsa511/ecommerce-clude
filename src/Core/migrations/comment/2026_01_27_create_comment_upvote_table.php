<?php

declare(strict_types=1);

class CreateCommentUpvoteTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the comment_upvote table.
     */
    public function up(): void
    {
        $query = "CREATE TABLE comment_upvote (
            comment_upvote_id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            comment_id INT(20) UNSIGNED NOT NULL,
            user_id INT(20) UNSIGNED NOT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL DEFAULT NULL,
            PRIMARY KEY (comment_upvote_id),
            CONSTRAINT fk_comment_upvote_comment FOREIGN KEY (comment_id) REFERENCES comment (comment_id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_comment_upvote_user FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8mb4;";

        try {
            $this->pdo->exec($query);
            echo "Table 'comment_upvote' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'comment_upvote': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the comment_upvote table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `comment_upvote`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'comment_upvote' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'comment_upvote': " . $e->getMessage() . "\n";
        }
    }
} 