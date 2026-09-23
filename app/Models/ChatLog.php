<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    protected $fillable = ['user_id', 'question', 'intent', 'faq_id', 'answered'];

    protected function casts(): array
    {
        return [
            'answered' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function faq()
    {
        return $this->belongsTo(Faq::class);
    }
}
