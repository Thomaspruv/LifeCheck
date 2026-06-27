<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckInItem extends Model
{
    protected $fillable = ['check_in_id', 'template_item_id', 'value'];

    public function checkIn(): BelongsTo
    {
        return $this->belongsTo(CheckIn::class);
    }

    public function templateItem(): BelongsTo
    {
        return $this->belongsTo(TemplateItem::class);
    }
}
