<?php

use App\Constants\DBTable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateSmaregiContractsTable
 */
class CreateSmaregiContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            DBTable::SMAREGI_CONTRACTS,
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('smaregi_contract_id')->unique();
                $table->text('smaregi_system_access_token');
                $table->timestamps();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(DBTable::SMAREGI_CONTRACTS);
    }
}
