<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    use HasFactory;
    protected $table = "games";

    public function gamePlayers()
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }
}
