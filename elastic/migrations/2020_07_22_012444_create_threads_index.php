<?php
declare(strict_types=1);

use ElasticAdapter\Indices\Mapping;
use ElasticAdapter\Indices\Settings;
use ElasticMigrations\Facades\Index;
use ElasticMigrations\MigrationInterface;

final class CreateThreadsIndex implements MigrationInterface
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Index::create('threads', function (Mapping $mapping, Settings $settings) {
            $mapping->text('title', [
                "term_vector" => "with_positions_offsets",
                'fields' => [
                    'chinese' => [
                        'type' => 'text',
                        "analyzer" => "ik_max_word",
                    ]
                ]
            ]);

            $mapping->text('body', [
                "term_vector" => "with_positions_offsets",
                'fields' => [
                    'chinese' => [
                        'type' => 'text',
                        "analyzer" => "ik_max_word",
                    ]
                ]
            ]);

            $mapping->text('path');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Index::dropIfExists('threads');
    }
}
