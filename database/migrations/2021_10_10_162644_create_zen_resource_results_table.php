<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @see \App\Models\ZenResourceResult
 */
class CreateZenResourceResultsTable extends Migration
{
    public function up(): void
    {
        Schema::create('zen_resource_results', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('msc_firm_id');
            $table->foreignId('zen_raw_item_id');
            $table->string('msc_resource_id', 200)->nullable(true)->default(null);
            $table->timestamp('last_try_at')->nullable(true)->default(null);
            $table->string('last_try_error')->nullable(true)->default(null);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zen_resource_results');
    }
}
