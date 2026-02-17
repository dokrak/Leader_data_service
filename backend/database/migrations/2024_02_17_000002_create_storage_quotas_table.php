<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('storage_quotas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('total_quota')->default(10737418240); // 10GB default
            $table->bigInteger('used_space')->default(0);
            $table->timestamps();
        });
        
        // Insert default quota
        DB::table('storage_quotas')->insert([
            'total_quota' => 10737418240, // 10GB in bytes
            'used_space' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_quotas');
    }
};
