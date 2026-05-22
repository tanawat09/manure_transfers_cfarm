<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manure_transfers', function (Blueprint $table) {
            $table->index(['status', 'out_datetime'], 'mt_status_out_datetime_idx');
            $table->index('received_datetime', 'mt_received_datetime_idx');
            $table->index('farm_id', 'mt_farm_id_idx');
            $table->index('pile_id', 'mt_pile_id_idx');
            $table->index('out_user_id', 'mt_out_user_id_idx');
            $table->index('receive_user_id', 'mt_receive_user_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('manure_transfers', function (Blueprint $table) {
            $table->dropIndex('mt_status_out_datetime_idx');
            $table->dropIndex('mt_received_datetime_idx');
            $table->dropIndex('mt_farm_id_idx');
            $table->dropIndex('mt_pile_id_idx');
            $table->dropIndex('mt_out_user_id_idx');
            $table->dropIndex('mt_receive_user_id_idx');
        });
    }
};
