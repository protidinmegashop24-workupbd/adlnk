<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lets the admin choose "Plain Text" vs "HTML code" per post/page
     * instead of that being auto-detected from the content. body_raw
     * stores exactly what the admin typed (so re-opening the editor shows
     * their original text, not the rendered HTML); body stays the
     * rendered HTML used for public display, unchanged.
     */
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->string('body_mode', 10)->default('html')->after('body');
            $table->text('body_raw')->nullable()->after('body_mode');
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->string('body_mode', 10)->default('html')->after('body');
            $table->text('body_raw')->nullable()->after('body_mode');
        });

        // Every row that already exists was saved before this feature
        // existed, so its "body" column is already rendered HTML — treat
        // it as the raw input too, so editing it keeps behaving exactly
        // as before (nothing is re-formatted or changed on screen).
        DB::table('blog_posts')->update(['body_raw' => DB::raw('body')]);
        DB::table('pages')->update(['body_raw' => DB::raw('body')]);
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['body_mode', 'body_raw']);
        });

        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn(['body_mode', 'body_raw']);
        });
    }
};
