<?php

use App\Support\DemoSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Every workspace needs a pipeline to put deals in, so the default sales
     * process ships with the schema rather than with the seeders.
     */
    public function up(): void
    {
        if (DB::table('pipelines')->exists()) {
            return;
        }

        $pipelineId = DB::table('pipelines')->insertGetId([
            'name' => 'Sales Pipeline',
            'position' => 0,
            'is_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (DemoSeeder::DEFAULT_STAGES as $index => $stage) {
            DB::table('stages')->insert([
                'pipeline_id' => $pipelineId,
                'name' => $stage['name'],
                'probability' => $stage['probability'],
                'position' => $index,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('pipelines')->where('name', 'Sales Pipeline')->delete();
    }
};
