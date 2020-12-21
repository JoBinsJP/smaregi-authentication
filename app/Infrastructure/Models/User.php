<?php

namespace App\Infrastructure\Models;

use App\Constants\DBTable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * @package App\Infrastructure\Models
 *
 * @property int             $id
 * @property string          $smaregi_id
 * @property string          $email
 * @property int             $contract_id
 * @property string          $smaregi_access_token
 * @property string          $smaregi_refresh_token
 * @property Carbon          $logged_in_at
 * @property Carbon          $created_at
 * @property Carbon          $updated_at
 *
 * @property SmaregiContract $smaregi_contract
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * @var string
     */
    protected $table = DBTable::AUTH_USERS;

    /**
     * @var string[]
     */
    protected $fillable = [
        'email',
        'smaregi_id',
        'contract_id',
        'logged_in_at',
        'smaregi_access_token',
        'smaregi_refresh_token',
    ];

    /**
     * @var string[]
     */
    protected $dates = [
        'logged_in_at',
    ];


    /** @noinspection PhpMethodNamingConventionInspection */
    public function smaregi_contract(): BelongsTo
    {
        return $this->belongsTo(SmaregiContract::class, 'contract_id', 'id');
    }
}
