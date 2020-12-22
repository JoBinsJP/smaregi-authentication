<?php

namespace App\Infrastructure\Models;

use App\Constants\DBTable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class SmaregiContract
 * @package App\Infrastructure\Models
 *
 * @property int        $id
 * @property string     $contract_id
 * @property string     $smaregi_system_access_token
 * @property Carbon     $created_at
 * @property Carbon     $updated_at
 *
 * @property Collection $users
 * @property Collection $smaregi_stores
 */
class SmaregiContract extends Model
{
    /**
     * @var string
     */
    protected $table = DBTable::SMAREGI_CONTRACTS;
    /**
     * @var string[]
     */
    protected $fillable = [
        'contract_id',
        'smaregi_system_access_token',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'contract_id', 'contract_id');
    }

    /**
     * @param string $contractId
     * @param string $systemAccessToken
     *
     * @return SmaregiContract
     */
    public function saveContract(string $contractId, string $systemAccessToken): SmaregiContract
    {
        return SmaregiContract::updateOrCreate(
            ['smaregi_contract_id' => $contractId],
            [
                'smaregi_system_access_token' => $systemAccessToken,
            ]
        );
    }
}
