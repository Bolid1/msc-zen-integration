<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see Integration
 */
class CreateIntegrationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('msc_user_id');
            $table->string('msc_firm_id');
            $table->foreignId('group_id');
            $table->unique([
                'msc_user_id',
                'msc_firm_id',
                'group_id',
            ]);
            $table->index('group_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
}
