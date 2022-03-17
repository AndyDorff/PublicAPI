<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\PublicAPI\Domain\Application\Repositories\EloquentApplicationsRepository;

class CreatePersonalTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(EloquentApplicationsRepository::PERSONAL_TOKENS_TABLE_NAME, function (Blueprint $table){
            $table->string('key', 16)->index();
            $table->string('hash', 32);
            $table->timestamp('expired_at')->nullable();
            $table->boolean('revoked')->default(false);
            $table->timestamps();

            $table->unique(['key', 'hash']);
            $table->foreign('key')->references('key')->on('pa_applications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(EloquentApplicationsRepository::PERSONAL_TOKENS_TABLE_NAME);
    }
}
