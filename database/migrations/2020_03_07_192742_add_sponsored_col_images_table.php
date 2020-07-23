<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSponsoredColImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('images')) {
            if (!Schema::hasColumn('images', 'sponsored')) {
                    Schema::table('images', function (Blueprint $table) {
                    $table->enum('sponsored', ['yes', 'no'])->default('no');
                });
            }
        }
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('images')) {
            if (Schema::hasColumn('images', 'sponsored')) {
                Schema::table('images', function (Blueprint $table) {
                    $table->dropColumn(['sponsored']);
                });
            }
            
        }
        
    }
}
