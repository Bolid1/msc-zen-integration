<?php

declare(strict_types=1);

use App\Models\ZenToken;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddZenTokenStateColumn extends Migration
{
    public function up(): void
    {
        Schema::table('zen_tokens', function (Blueprint $table) {
            $table->string('status', 10)->default(ZenToken::STATUS_ACTIVE);
            $table->text('last_error')->nullable(true);
            $table->dropIndex('zen_tokens_group_id_index');
            $table->index(['group_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('zen_tokens', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('last_error');
            $table->dropIndex('zen_tokens_group_id_status_index');
            $table->index('group_id');
        });
    }
}
