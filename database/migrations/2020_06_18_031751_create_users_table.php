<?php

use App\Constants\DBTable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateUsersTable
 */
class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            DBTable::AUTH_USERS,
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('smaregi_id')->unique();
                $table->string('email');
                $table->unsignedBigInteger('contract_id');
                $table->text('smaregi_access_token')->nullable();
                $table->text('smaregi_refresh_token')->nullable();
                $table->timestamp('logged_in_at')->nullable();
                $table->rememberToken();
                $table->timestamps();

                $table->foreign('contract_id')->references('id')->on(DBTable::SMAREGI_CONTRACTS)->onDelete('cascade');
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
        Schema::dropIfExists(DBTable::AUTH_USERS);
    }
}
