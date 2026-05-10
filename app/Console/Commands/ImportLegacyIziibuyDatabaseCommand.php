<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use Throwable;

/**
 * Copy data from the legacy iziibuy MySQL database into this application's default (or target) database.
 *
 * Tables present on both connections are copied in foreign-key dependency order. Uses INSERT IGNORE so
 * re-runs skip duplicate primary keys. Pair with --truncate for a clean target (destructive).
 *
 * Each row is projected onto the target table's columns only (case-insensitive), so legacy-only fields
 * (e.g. Voyager columns on permissions) are not sent to the new schema.
 */
class ImportLegacyIziibuyDatabaseCommand extends Command
{
    protected $signature = 'iziibuy:import-legacy-database
                            {--source=legacy_iziibuy : Source database connection name (config/database.php)}
                            {--target= : Target connection; default: your configured default database}
                            {--tables= : Comma-separated tables to copy (default: intersection of source and target)}
                            {--exclude= : Comma-separated extra tables to skip}
                            {--with-migrations : Copy the migrations table (normally skipped)}
                            {--with-runtime-tables : Include sessions, cache, jobs, failed_jobs, password resets}
                            {--truncate : TRUNCATE each target table before import (requires --force in production)}
                            {--chunk=500 : Rows per lazy read chunk}
                            {--insert-chunk=100 : Rows per insert batch}
                            {--force : Allow destructive operations in production}
                            {--dry-run : Show plan and row counts only}';

    protected $description = 'Copy data from legacy iziibuy DB (second connection) into the target database';

    /**
     * @var list<string>
     */
    private array $runtimeExcludeTables = [
        'sessions',
        'cache',
        'cache_locks',
        'jobs',
        'job_batches',
        'failed_jobs',
        'password_reset_tokens',
        'password_resets',
    ];

    public function handle(): int
    {
        $source = (string) $this->option('source');
        $target = (string) ($this->option('target') ?: config('database.default'));

        if (! config("database.connections.$source")) {
            $this->error("Unknown source connection [{$source}]. Add it in config/database.php and .env.");

            return self::FAILURE;
        }

        if (! config("database.connections.$target")) {
            $this->error("Unknown target connection [{$target}].");

            return self::FAILURE;
        }

        if (config("database.connections.$source.driver") !== 'mysql' || config("database.connections.$target.driver") !== 'mysql') {
            $this->error('This command currently supports MySQL for both source and target connections only.');

            return self::FAILURE;
        }

        try {
            DB::connection($source)->getPdo();
            DB::connection($target)->getPdo();
        } catch (Throwable $e) {
            $this->error('Database connection failed: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($source === $target) {
            $this->error('Source and target connections must differ.');

            return self::FAILURE;
        }

        $sourceDb = (string) config("database.connections.$source.database");
        $targetDb = (string) config("database.connections.$target.database");
        $this->info("Source: {$source} → database `{$sourceDb}`");
        $this->info("Target: {$target} → database `{$targetDb}`");

        if ($this->option('truncate') && app()->environment('production') && ! $this->option('force')) {
            $this->error('Refusing to --truncate in production without --force.');

            return self::FAILURE;
        }

        $sourceTables = $this->listBaseTables($source);
        $targetTables = $this->listBaseTables($target);

        $only = $this->parseCommaList((string) $this->option('tables'));
        $exclude = $this->parseCommaList((string) $this->option('exclude'));

        if (! $this->option('with-migrations')) {
            $exclude[] = 'migrations';
        }
        if (! $this->option('with-runtime-tables')) {
            $exclude = [...$exclude, ...$this->runtimeExcludeTables];
        }

        $exclude = array_values(array_unique(array_filter($exclude)));

        $intersect = array_values(array_intersect($sourceTables, $targetTables));

        if ($only !== []) {
            $intersect = array_values(array_intersect($intersect, $only));
            $missing = array_diff($only, $intersect);
            foreach ($missing as $table) {
                $this->warn("Table [{$table}] was requested but is not present on both connections — skipped.");
            }
        }

        $toCopy = array_values(array_diff($intersect, $exclude));

        if ($toCopy === []) {
            $this->warn('No tables left to copy after filters.');

            return self::SUCCESS;
        }

        $ordered = $this->orderTablesByForeignKeys($target, $toCopy);
        $insertChunk = max(1, (int) $this->option('insert-chunk'));
        $readChunk = max(1, (int) $this->option('chunk'));
        $dry = (bool) $this->option('dry-run');

        $this->newLine();
        $this->table(
            ['#', 'Table', 'Source rows'],
            collect($ordered)
                ->values()
                ->map(function (string $table, int $i) use ($source, $dry): array {
                    $count = (int) DB::connection($source)->table($table)->count();

                    return [(string) ($i + 1), $table, (string) $count];
                })
                ->all()
        );

        if ($dry) {
            $this->info('Dry run complete — no data copied.');

            return self::SUCCESS;
        }

        if ($this->option('truncate')) {
            $this->truncateTables($target, array_reverse($ordered));
        }

        $started = microtime(true);
        /** @var array<string, list<string>> */
        $targetColumnsByTable = [];
        foreach ($ordered as $table) {
            $this->line("Copying <fg=cyan>{$table}</>…");
            $targetColumnsByTable[$table] = Schema::connection($target)->getColumnListing($table);
            $this->copyTable($source, $target, $table, $readChunk, $insertChunk, $targetColumnsByTable[$table]);
        }

        $elapsed = round(microtime(true) - $started, 2);
        $this->newLine();
        $this->info("Done in {$elapsed}s.");

        return self::SUCCESS;
    }

    /**
     * @return list<string>
     */
    private function listBaseTables(string $connection): array
    {
        $database = (string) config("database.connections.$connection.database");
        $rows = DB::connection($connection)->select(
            'SELECT TABLE_NAME as name FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_TYPE = \'BASE TABLE\' ORDER BY TABLE_NAME',
            [$database]
        );

        return array_values(array_map(fn ($r) => (string) $r->name, $rows));
    }

    /**
     * @param  list<string>  $tables
     * @return list<string>
     */
    private function orderTablesByForeignKeys(string $connection, array $tables): array
    {
        $database = (string) config("database.connections.$connection.database");
        $rows = DB::connection($connection)->select(
            'SELECT DISTINCT TABLE_NAME, REFERENCED_TABLE_NAME
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL
               AND TABLE_NAME != REFERENCED_TABLE_NAME',
            [$database]
        );

        $allowed = array_flip($tables);
        /** @var array<string, list<string>> $deps child => parent names */
        $deps = [];
        foreach ($rows as $row) {
            $child = (string) $row->TABLE_NAME;
            $parent = (string) $row->REFERENCED_TABLE_NAME;
            if (! isset($allowed[$child], $allowed[$parent])) {
                continue;
            }
            $deps[$child] ??= [];
            $deps[$child][] = $parent;
        }

        /** @var array<string, list<string>> $children parent => children */
        $children = [];
        foreach ($deps as $child => $parents) {
            foreach (array_unique($parents) as $parent) {
                $children[$parent] ??= [];
                $children[$parent][] = $child;
            }
        }

        /** @var array<string, int> $inDegree */
        $inDegree = array_fill_keys($tables, 0);
        foreach ($tables as $t) {
            $parents = array_unique($deps[$t] ?? []);
            $inDegree[$t] = count($parents);
        }

        $queue = [];
        foreach ($tables as $t) {
            if ($inDegree[$t] === 0) {
                $queue[] = $t;
            }
        }
        sort($queue);

        $sorted = [];
        while ($queue !== []) {
            $table = array_shift($queue);
            $sorted[] = $table;
            foreach ($children[$table] ?? [] as $child) {
                $inDegree[$child]--;
                if ($inDegree[$child] === 0) {
                    $queue[] = $child;
                }
            }
            sort($queue);
        }

        if (count($sorted) !== count($tables)) {
            $remaining = array_values(array_diff($tables, $sorted));
            sort($remaining);
            $this->warn('Could not fully resolve foreign-key order (cycle or metadata gap). Remaining tables appended alphabetically.');
            $sorted = [...$sorted, ...$remaining];
        }

        return $sorted;
    }

    /**
     * @param  list<string>  $tables
     */
    private function truncateTables(string $target, array $tables): void
    {
        $this->warn('Truncating target tables…');
        Schema::connection($target)->disableForeignKeyConstraints();
        try {
            foreach ($tables as $table) {
                DB::connection($target)->table($table)->truncate();
                $this->line("Truncated <fg=yellow>{$table}</>");
            }
        } finally {
            Schema::connection($target)->enableForeignKeyConstraints();
        }
    }

    private function copyTable(string $source, string $target, string $table, int $readChunk, int $insertChunk, array $targetColumns): void
    {
        if (! $this->isSafeIdentifier($table)) {
            throw new InvalidArgumentException("Invalid table name: {$table}");
        }

        $pk = $this->getPrimaryKeyColumn($source, $table);
        $query = DB::connection($source)->table($table);

        if ($pk !== null) {
            $query = $query->orderBy($pk);
        }

        $buffer = [];
        $processed = 0;

        if ($pk === null) {
            $this->copyTableWithOffsetPagination($source, $target, $table, $readChunk, $insertChunk, $targetColumns);

            return;
        }

        foreach ($query->lazy($readChunk) as $row) {
            $processed++;
            $buffer[] = $this->projectRowOntoTargetColumns((array) $row, $targetColumns);
            if (count($buffer) >= $insertChunk) {
                DB::connection($target)->table($table)->insertOrIgnore($buffer);
                $buffer = [];
            }
        }

        if ($buffer !== []) {
            DB::connection($target)->table($table)->insertOrIgnore($buffer);
        }

        $this->line("  <fg=gray>{$processed} source rows processed</>");
    }

    /**
     * @param  list<string>  $targetColumns
     */
    private function copyTableWithOffsetPagination(string $source, string $target, string $table, int $readChunk, int $insertChunk, array $targetColumns): void
    {
        $offset = 0;
        $processed = 0;
        while (true) {
            $rows = DB::connection($source)->table($table)->offset($offset)->limit($readChunk)->get();
            if ($rows->isEmpty()) {
                break;
            }
            $buffer = [];
            foreach ($rows as $row) {
                $processed++;
                $buffer[] = $this->projectRowOntoTargetColumns((array) $row, $targetColumns);
                if (count($buffer) >= $insertChunk) {
                    DB::connection($target)->table($table)->insertOrIgnore($buffer);
                    $buffer = [];
                }
            }
            if ($buffer !== []) {
                DB::connection($target)->table($table)->insertOrIgnore($buffer);
            }
            $offset += $readChunk;
        }

        $this->line("  <fg=gray>{$processed} source rows processed (no primary key — offset pagination)</>");
    }

    /**
     * Keep only columns that exist on the target table (case-insensitive match). Drops legacy-only fields
     * (e.g. Voyager `key` on `permissions`) so inserts match Spatie / current schema.
     *
     * @param  array<string|int, mixed>  $row
     * @param  list<string>  $targetColumns
     * @return array<string, mixed>
     */
    private function projectRowOntoTargetColumns(array $row, array $targetColumns): array
    {
        $canonical = [];
        foreach ($targetColumns as $col) {
            $canonical[strtolower($col)] = $col;
        }

        $out = [];
        foreach ($row as $key => $value) {
            $lk = strtolower((string) $key);
            if (isset($canonical[$lk])) {
                $out[$canonical[$lk]] = $value;
            }
        }

        return $out;
    }

    private function isSafeIdentifier(string $name): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_]+$/', $name);
    }

    private function getPrimaryKeyColumn(string $connection, string $table): ?string
    {
        if (! $this->isSafeIdentifier($table)) {
            return null;
        }

        $database = (string) config("database.connections.$connection.database");
        $rows = DB::connection($connection)->select(
            'SELECT COLUMN_NAME AS name FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_KEY = \'PRI\' ORDER BY ORDINAL_POSITION LIMIT 1',
            [$database, $table]
        );

        if ($rows === []) {
            return null;
        }

        $col = (string) $rows[0]->name;

        return $this->isSafeIdentifier($col) ? $col : null;
    }

    /**
     * @return list<string>
     */
    private function parseCommaList(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        return array_values(array_filter(array_map(
            fn (string $s) => trim($s),
            explode(',', $value)
        )));
    }
}
