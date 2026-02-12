<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bills', function (Blueprint $table) {
            // Drop foreign key constraint to customers
            $table->dropForeign(['customer_id']);
            
            // Change the column type if needed and add new constraint to users
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            // Drop foreign key constraint to users
            $table->dropForeign(['customer_id']);
            
            // Restore original constraint to customers
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }
};
