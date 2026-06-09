<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractTerm extends Model
{
    protected $fillable = [
        'title_km',
        'title_en',
        'content_km',
        'content_en',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Content split into individual lines (for numbered list rendering).
     */
    public function linesKm(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->content_km ?? ''))));
    }

    public function linesEn(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->content_en ?? ''))));
    }
}
