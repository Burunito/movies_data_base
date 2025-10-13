<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Genre;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('movies', function (Blueprint $table) {
            $table->foreignId('genre_id')
                ->nullable()
                ->constrained('genres') 
                ->nullOnDelete()
                ->after('title');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->foreignId('genre_id')
                ->nullable()
                ->constrained('genres') 
                ->nullOnDelete()
                ->after('url');
        });

        Schema::table('actors', function (Blueprint $table) {
            $table->foreignId('genre_id')
                ->nullable()
                ->constrained('genres') 
                ->nullOnDelete()
                ->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('genre_id');
        });

        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('genre_id');
        });

        Schema::table('actors', function (Blueprint $table) {
            $table->dropColumn('genre_id');
        });
    }
};
