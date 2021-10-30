<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNeedResultColumn extends Migration
{
    public function up(): void
    {
        Schema::table('zen_raw_items', function (Blueprint $table) {
            $table->boolean('need_result')->nullable(false)->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('zen_raw_items', function (Blueprint $table) {
            $table->dropColumn('need_result');
        });
    }
}
