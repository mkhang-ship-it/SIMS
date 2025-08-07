<?php
// Updated: Added prize won count tracking and improved availability checking for lucky draw system
// - Added getWonCount() method to track how many times this prize has been won
// - Improved getRemainingQuantityAttribute() calculation
// - Added isFullyWon() method to check if prize is completely exhausted

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Prize extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'image',
        'quantity',
        'lucky_draw_id',
        'order',
        'special_gift'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'order' => 'integer',
        'special_gift' => 'boolean'
    ];

    public function luckyDraw()
    {
        return $this->belongsTo(LuckyDraw::class);
    }

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('assets/luckydraw/assets/images/prize.png');
    }

    public function getWonCountAttribute()
    {
        // Tính số lần giải này đã được trúng
        $winners = $this->luckyDraw->winners ?? [];
        return collect($winners)->where('prize', $this->name)->count();
    }

    public function getRemainingQuantityAttribute()
    {
        // Tính số lượng giải còn lại bằng cách trừ đi số người đã trúng giải này
        return max(0, $this->quantity - $this->won_count);
    }

    public function isAvailable()
    {
        return $this->remaining_quantity > 0;
    }

    public function isFullyWon()
    {
        return $this->remaining_quantity <= 0;
    }
}
