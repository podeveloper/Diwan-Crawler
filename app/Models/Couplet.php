<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Couplet extends Model
{
    use HasFactory;

    protected $fillable = ['number_of_couplet', 'first_line', 'second_line', 'poem_id'];

    public function poem()
    {
        return $this->belongsTo(Poem::class);
    }
}
