<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see ZenToken
 */
class CreateZenTokens extends Migration
{
    public function up(): void
    {
        Schema::create('zen_tokens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('group_id');
            $table->string('type', 20);
            $table->dateTime('expires_at');
            $table->text('access');
            $table->text('refresh');
            $table->index('group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zen_tokens');
    }
}
