<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageQuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_quota',
        'used_space',
    ];

    protected $casts = [
        'total_quota' => 'integer',
        'used_space' => 'integer',
    ];

    /**
     * Get available space in bytes
     */
    public function getAvailableSpaceAttribute(): int
    {
        return $this->total_quota - $this->used_space;
    }

    /**
     * Get percentage used
     */
    public function getPercentageUsedAttribute(): float
    {
        if ($this->total_quota == 0) {
            return 0;
        }
        return ($this->used_space / $this->total_quota) * 100;
    }

    /**
     * Check if has enough space
     */
    public function hasEnoughSpace(int $requiredBytes): bool
    {
        return $this->available_space >= $requiredBytes;
    }
}
