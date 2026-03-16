<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection(): ?string
    {
        return config('passport.database.connection');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('passport.database.table', 'passport'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->comment('Title');
            $table->string('key', 128)->index('idx_key')->comment('Key');
            $table->string('secret', 128)->index('idx_secret')->comment('Secret');
            $table->longText('description')->nullable()->comment('Description');
            $table->bigInteger('sorting')->default(1)->comment('Sorting');
            // $table->datetimes();
            $table->datetime('created_at')->comment('Created Time');
            $table->datetime('updated_at')->comment('Updated Time');
            // $table->softDeletesDatetime();
            $table->datetime('deleted_at')->index('idx_deleted_at')->nullable()->comment('Deleted Time');
            $table->enum('state', ['NORMAL', 'DISABLED'])->default('DISABLED')->comment('State{NORMAL:NORMAL}{DISABLE:DISABLE}');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('passport.database.table', 'passport'));
    }
};
