<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see MscToken
 */
class CreateMscTokens extends Migration
{
    public function up(): void
    {
        Schema::create('msc_tokens', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('msc_user_id');
            $table->string('msc_firm_id');
            $table->string('type', 20);
            $table->dateTime('expires_at');
            $table->text('access');
            $table->text('refresh');
            $table->unique(['msc_user_id', 'msc_firm_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('msc_tokens');
    }
}
