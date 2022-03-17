<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\PublicAPI\Domain\Application\Repositories\EloquentApplicationsRepository;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(EloquentApplicationsRepository::TABLE_NAME, function(Blueprint $table){
            $table->string('key', 16);
            $table->string('name', 255);
            $table->string('status', 255);
            $table->integer('status_code');
            $table->dateTime('status_date');
            $table->string('secret', 32);
            $table->string('version', 32);
            $table->timestamps();

            $table->primary('key');
            $table->index(['key', 'secret']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop(EloquentApplicationsRepository::TABLE_NAME);
    }
}
