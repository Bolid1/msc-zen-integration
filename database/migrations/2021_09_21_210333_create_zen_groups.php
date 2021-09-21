<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see ZenGroup
 */
class CreateZenGroups extends Migration
{
    public function up(): void
    {
        Schema::create('zen_groups', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zen_groups');
    }
}
