<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZenGroupLastSyncedAtColumn extends Migration
{
    public function up(): void
    {
        Schema::table('zen_groups', function (Blueprint $table) {
            $table
                ->timestamp('last_synced_at')
                ->nullable(true)
                ->default(null);
        });
    }

    public function down(): void
    {
        Schema::table('zen_groups', function (Blueprint $table) {
            $table->dropColumn('last_synced_at');
        });
    }
}
