<?php

namespace App\Infrastructure\Models;

use App\Constants\DBTable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

/**
 * Class Store
 * @package App\Infrastructure\Models
 *
 * @property int             $id
 * @property int             $smaregi_contract_id
 * @property string          $smaregi_store_id
 * @property string          $smaregi_store_name
 * @property Carbon          $created_at
 * @property Carbon          $updated_at
 *
 * @property SmaregiContract $smaregi_contract
 * @property Collection      $smaregi_orders
 * @property Collection      $incomplete_orders
 */
class Store extends Model
{
    /**
     * @var string
     */
    protected $table = DBTable::STORES;
    /**
     * @var string[]
     */
    protected $fillable = [
        'smaregi_contract_id',
        'smaregi_store_id',
        'smaregi_store_name',
    ];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();
    }

    /** @noinspection PhpMethodNamingConventionInspection */
    public function smaregi_contract(): BelongsTo
    {
        return $this->belongsTo(SmaregiContract::class, 'smaregi_contract_id', 'id');
    }
}
