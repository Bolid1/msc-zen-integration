<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see ZenRawItem
 */
class CreateZenRawItemsTable extends Migration
{
    public function up(): void
    {
        Schema::create('zen_raw_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('group_id');
            $table->string('type', 30);
            $table->string('zen_id', 200);
            $table->timestamp('changed_at')->nullable();
            $table->string('action');
            $table->json('data');
            // Индекс для обхода по таблице при отправке данных
            $table->index(['group_id', 'type', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zen_raw_items');
    }
}
