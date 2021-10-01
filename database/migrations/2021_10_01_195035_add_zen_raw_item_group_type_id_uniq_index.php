<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZenRawItemGroupTypeIdUniqIndex extends Migration
{
    private const INDEX = 'zen_raw_items_group_id_type_zen_id_uniq';

    public function up(): void
    {
        Schema::table('zen_raw_items', function (Blueprint $table) {
            $table->unique(['group_id', 'type', 'zen_id'], static::INDEX);
        });
    }

    public function down(): void
    {
        Schema::table('zen_raw_items', function (Blueprint $table) {
            $table->dropUnique(static::INDEX);
        });
    }
}
