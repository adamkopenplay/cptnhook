<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use CptnHook\Integration\Laravel\Model\Hook;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('cptnhook.tableName', Hook::DEFAULT_TABLE_NAME);
        Schema::create($tableName, function (Blueprint $table) use($tableName) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('group')->index($tableName . '_groups_idx');
            $table->timestamps();
            $table->unique(['name', 'group'], $tableName . '_group_name_comp_uniq');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('cptnhook.tableName', Hook::DEFAULT_TABLE_NAME));
    }
};
