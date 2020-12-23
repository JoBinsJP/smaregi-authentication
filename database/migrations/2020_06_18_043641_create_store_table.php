<?php

use App\Constants\DBTable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateStoreTable
 */
class CreateStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            DBTable::STORES,
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('smaregi_contract_id');
                $table->string('smaregi_store_id');
                $table->string('smaregi_store_name');
                $table->timestamps();
                $table->foreign('smaregi_contract_id')->references('id')->on(DBTable::SMAREGI_CONTRACTS)->onDelete('cascade');
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
        Schema::dropIfExists(DBTable::STORES);
    }
}
