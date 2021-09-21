<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see ZenUser
 */
class CreateZenUsers extends Migration
{
    public function up(): void
    {
        Schema::create('zen_users', function (Blueprint $table) {
            $table->string('zen_user_id')->primary();
            $table->timestamps();
            $table->foreignId('group_id');
            // До тех пор, пока не встретим аккаунт с
            // несколькими администраторами, будет такая структура
            // @see ZenGroupManager::findOrCreateByAdmin
            $table->unique('group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zen_users');
    }
}
