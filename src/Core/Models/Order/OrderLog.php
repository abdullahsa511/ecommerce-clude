<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;

class OrderLog extends Model
{
    protected string $table = 'order_log';
    protected string $tableAlias = 'ol';

    /**
     * Order Log ID
     */
    protected int $order_log_id;

    /**
     * Order ID
     */
    protected int $order_id;

    /**
     * Status
     */
    protected int $status;

    /**
     * Comment
     */
    protected string $comment;

    /**
     * Created At
     */
    protected string $created_at;

    /**
     * Note
     */
    protected string $note;

    /**
     * Get parent order relationship
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function getOrderLogId(): int
    {
        return $this->order_log_id;
    }

    public function setOrderLogId(int $order_log_id): void
    {
        $this->order_log_id = $order_log_id;
    }

    public function getOrderId(): int
    {
        return $this->order_id;
    }

    public function setOrderId(int $order_id): void
    {
        $this->order_id = $order_id;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 