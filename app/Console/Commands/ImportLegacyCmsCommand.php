<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\SiteSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

/**
 * Imports Voyager-style CMS data from the legacy database into site_settings, cms_menus,
 * cms_menu_items, post_categories (when needed), posts, and faqs when those tables exist on the source.
 *
 * The bulk {@see ImportLegacyIziibuyDatabaseCommand} only copies tables that exist on both databases
 * with the same name, so renamed CMS tables are skipped there.
 */
class ImportLegacyCmsCommand extends Command
{
    protected $signature = 'iziibuy:import-legacy-cms
                            {--source=legacy_iziibuy : Source connection (see config/database.php)}
                            {--target= : Target connection; default: configured default}
                            {--force : Truncate cms_menus / cms_menu_items before import; production requires --force twice}
                            {--with-post-categories-from-categories : Import blog categories from legacy `categories` into `post_categories` for rows referenced by posts}
                            {--dry-run : Show plan only}
                            {--chunk=200 : Rows per batch for large tables}';

    protected $description = 'Import Voyager CMS tables (settings, menus, menu items, posts categories, FAQs) from legacy DB';

    public function handle(): int
    {
        $source = (string) $this->option('source');
        $target = (string) ($this->option('target') ?: config('database.default'));

        if (! config("database.connections.$source")) {
            $this->error("Unknown source connection [{$source}].");

            return self::FAILURE;
        }

        if ($source === $target) {
            $this->error('Source and target must differ.');

            return self::FAILURE;
        }

        if (config("database.connections.$source.driver") !== 'mysql'
            || config("database.connections.$target.driver") !== 'mysql') {
            $this->error('This command currently supports MySQL for both connections.');

            return self::FAILURE;
        }

        try {
            DB::connection($source)->getPdo();
            DB::connection($target)->getPdo();
        } catch (Throwable $e) {
            $this->error('Database connection failed: '.$e->getMessage());

            return self::FAILURE;
        }

        $dry = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');
        if ($force && app()->environment('production')) {
            $this->warn('Truncating CMS menu tables in production.');
        }

        $this->info("Source: {$source} → `".config("database.connections.$source.database").'`');
        $this->info("Target: {$target} → `".config("database.connections.$target.database").'`');

        if ($dry) {
            $this->plan($source);

            return self::SUCCESS;
        }

        if ($force) {
            $this->truncateCmsMenus($target);
        }

        $this->importSettings($source, $target);
        $this->importMenusAndItems($source, $target);
        if ((bool) $this->option('with-post-categories-from-categories')) {
            $this->importPostCategoriesFromLegacyCategories($source, $target);
        }
        $this->importPosts($source, $target);
        $this->importFaqs($source, $target);

        $this->newLine();
        $this->info('Legacy CMS import finished.');

        return self::SUCCESS;
    }

    private function plan(string $source): void
    {
        $tables = ['settings', 'menus', 'menu_items', 'posts', 'categories', 'faqs'];
        foreach ($tables as $table) {
            $has = Schema::connection($source)->hasTable($table);
            $count = $has ? (int) DB::connection($source)->table($table)->count() : 0;
            $this->line(sprintf('%s %s — %d rows', $has ? '✓' : '—', $table, $count));
        }
    }

    private function truncateCmsMenus(string $target): void
    {
        $this->warn('Truncating cms_menu_items and cms_menus…');
        Schema::connection($target)->disableForeignKeyConstraints();
        try {
            DB::connection($target)->table('cms_menu_items')->truncate();
            DB::connection($target)->table('cms_menus')->truncate();
        } finally {
            Schema::connection($target)->enableForeignKeyConstraints();
        }
    }

    private function importSettings(string $source, string $target): void
    {
        if (! Schema::connection($source)->hasTable('settings') || ! Schema::connection($target)->hasTable('site_settings')) {
            $this->warn('Skipping settings (table missing on source or target).');

            return;
        }

        $rows = DB::connection($source)->table('settings')->orderBy('id')->cursor();
        $imported = 0;
        foreach ($rows as $row) {
            $row = (array) $row;
            $key = (string) ($row['key'] ?? '');
            if ($key === '') {
                continue;
            }

            $type = $this->mapSettingType((string) ($row['type'] ?? 'text'));
            $sort = isset($row['order']) ? (int) $row['order'] : (isset($row['sort_order']) ? (int) $row['sort_order'] : 0);
            $group = (string) ($row['group'] ?? $row['group_name'] ?? 'general');
            $group = trim($group);
            $label = (string) ($row['display_name'] ?? $row['label'] ?? $key);
            $value = $row['value'] ?? null;
            if (is_string($value) === false && $value !== null) {
                $value = (string) $value;
            }

            SiteSetting::on($target)->updateOrCreate(
                ['key' => $key],
                [
                    'label' => $label,
                    'value' => $value,
                    'type' => $type,
                    'group_name' => $group !== '' ? $group : 'general',
                    'sort_order' => $sort,
                ]
            );
            $imported++;
        }

        $this->info("Site settings: {$imported} row(s) upserted.");
    }

    private function mapSettingType(string $voyagerType): string
    {
        $t = strtolower(trim($voyagerType));

        return match ($t) {
            'text_area', 'textarea', 'rich_text_box', 'hidden' => 'textarea',
            'image', 'file' => 'image',
            'checkbox', 'check_box' => 'checkbox',
            default => 'text',
        };
    }

    private function importMenusAndItems(string $source, string $target): void
    {
        $hasMenus = Schema::connection($source)->hasTable('menus');
        $hasItems = Schema::connection($source)->hasTable('menu_items');
        if (! $hasMenus || ! $hasItems) {
            $this->warn('Skipping menus / menu_items (table missing on source).');

            return;
        }

        if (! Schema::connection($target)->hasTable('cms_menus') || ! Schema::connection($target)->hasTable('cms_menu_items')) {
            $this->warn('Skipping menus (target cms tables missing).');

            return;
        }

        Schema::connection($target)->disableForeignKeyConstraints();
        try {
            $menuRows = DB::connection($source)->table('menus')->orderBy('id')->get();
            foreach ($menuRows as $row) {
                $row = (array) $row;
                $id = (int) $row['id'];
                $name = (string) ($row['name'] ?? 'Menu '.$id);
                $baseSlug = Str::slug($name);
                $slug = $baseSlug !== '' ? $baseSlug : 'menu-'.$id;
                if (DB::connection($target)->table('cms_menus')->where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug .= '-'.$id;
                }

                DB::connection($target)->table('cms_menus')->updateOrInsert(
                    ['id' => $id],
                    [
                        'name' => $name,
                        'slug' => $slug,
                        'location' => null,
                        'created_at' => $row['created_at'] ?? now(),
                        'updated_at' => $row['updated_at'] ?? now(),
                    ]
                );
            }

            $itemCols = Schema::connection($source)->getColumnListing('menu_items');
            $orderCol = in_array('order', $itemCols, true) ? 'order' : (in_array('sort_order', $itemCols, true) ? 'sort_order' : null);

            $items = DB::connection($source)->table('menu_items')->orderBy('id')->get();
            foreach ($items as $row) {
                $row = (array) $row;
                $sort = $orderCol !== null && isset($row[$orderCol]) ? (int) $row[$orderCol] : 0;
                $targetBlank = isset($row['target']) && strtolower((string) $row['target']) === '_blank';

                DB::connection($target)->table('cms_menu_items')->updateOrInsert(
                    ['id' => (int) $row['id']],
                    [
                        'cms_menu_id' => (int) $row['menu_id'],
                        'parent_id' => isset($row['parent_id']) && $row['parent_id'] !== null ? (int) $row['parent_id'] : null,
                        'title' => (string) ($row['title'] ?? ''),
                        'url' => isset($row['url']) ? (is_string($row['url']) ? $row['url'] : null) : null,
                        'route_name' => null,
                        'icon' => isset($row['icon_class']) ? (string) $row['icon_class'] : null,
                        'sort_order' => $sort,
                        'open_new_tab' => $targetBlank,
                        'created_at' => $row['created_at'] ?? now(),
                        'updated_at' => $row['updated_at'] ?? now(),
                    ]
                );
            }
        } finally {
            Schema::connection($target)->enableForeignKeyConstraints();
        }

        $this->info('CMS menus and menu items imported ('.DB::connection($target)->table('cms_menus')->count().' menus, '.DB::connection($target)->table('cms_menu_items')->count().' items).');
    }

    /**
     * Maps legacy `categories` rows referenced by posts into `post_categories`, preserving ids when possible.
     */
    private function importPostCategoriesFromLegacyCategories(string $source, string $target): void
    {
        if (! Schema::connection($source)->hasTable('categories') || ! Schema::connection($source)->hasTable('posts')) {
            $this->warn('Skipping post categories from categories (source tables missing).');

            return;
        }

        if (! Schema::connection($target)->hasTable('post_categories')) {
            $this->warn('Skipping post categories (target post_categories missing).');

            return;
        }

        $ids = DB::connection($source)->table('posts')
            ->whereNotNull('category_id')
            ->distinct()
            ->pluck('category_id')
            ->filter()
            ->map(fn ($id): int => (int) $id)
            ->all();

        if ($ids === []) {
            $this->info('Post categories: no posts reference category_id.');

            return;
        }

        $catCols = Schema::connection($source)->getColumnListing('categories');
        $hasShop = in_array('shop_id', $catCols, true);

        $ids = $this->expandCategoryAncestorIds($source, $ids, $hasShop);
        $idSet = array_fill_keys($ids, true);

        $q = DB::connection($source)->table('categories')->whereIn('id', $ids);
        if ($hasShop) {
            $q->whereNull('shop_id');
        }

        $rows = $q->orderBy('id')->get();
        $n = 0;
        foreach ($rows as $row) {
            $row = (array) $row;
            $id = (int) $row['id'];
            $name = (string) ($row['name'] ?? 'Category '.$id);
            $slugBase = isset($row['slug']) && $row['slug'] !== null && $row['slug'] !== ''
                ? (string) $row['slug']
                : Str::slug($name);
            $slug = $slugBase !== '' ? $slugBase : 'cat-'.$id;
            if (DB::connection($target)->table('post_categories')->where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug .= '-'.$id;
            }

            $parentId = isset($row['parent_id']) && $row['parent_id'] !== null ? (int) $row['parent_id'] : null;
            if ($parentId !== null && ! isset($idSet[$parentId])) {
                $parentId = null;
            }
            $sort = 0;
            if (isset($row['order_no']) && $row['order_no'] !== null) {
                $sort = (int) $row['order_no'];
            }

            DB::connection($target)->table('post_categories')->updateOrInsert(
                ['id' => $id],
                [
                    'name' => $name,
                    'slug' => $slug,
                    'parent_id' => $parentId,
                    'sort_order' => $sort,
                    'created_at' => $row['created_at'] ?? now(),
                    'updated_at' => $row['updated_at'] ?? now(),
                ]
            );
            $n++;
        }

        $this->info("Post categories from legacy categories: {$n} row(s) upserted.");
    }

    private function importPosts(string $source, string $target): void
    {
        if (! Schema::connection($source)->hasTable('posts') || ! Schema::connection($target)->hasTable('posts')) {
            $this->warn('Skipping posts (table missing).');

            return;
        }

        $targetCols = Schema::connection($target)->getColumnListing('posts');
        $chunk = max(1, (int) $this->option('chunk'));
        $processed = 0;

        DB::connection($source)->table('posts')->orderBy('id')->chunkById($chunk, function ($chunkRows) use ($target, $targetCols, &$processed): void {
            foreach ($chunkRows as $row) {
                $projected = $this->projectRowOntoTargetColumns((array) $row, $targetCols);
                if ($projected === []) {
                    continue;
                }
                $id = $projected['id'] ?? null;
                if ($id === null) {
                    continue;
                }
                DB::connection($target)->table('posts')->updateOrInsert(
                    ['id' => $id],
                    $projected
                );
                $processed++;
            }
        }, 'id');

        $this->info("Posts: {$processed} row(s) upserted.");
    }

    private function importFaqs(string $source, string $target): void
    {
        if (! Schema::connection($source)->hasTable('faqs') || ! Schema::connection($target)->hasTable('faqs')) {
            $this->info('FAQs: skipped (table missing on source or target).');

            return;
        }

        $sCols = Schema::connection($source)->getColumnListing('faqs');
        $hasQuestion = in_array('question', $sCols, true);
        $hasAnswer = in_array('answer', $sCols, true);
        if (! $hasQuestion || ! $hasAnswer) {
            $this->warn('FAQs: legacy table shape not recognized (need question, answer).');

            return;
        }

        $orderCol = in_array('sort_order', $sCols, true) ? 'sort_order' : (in_array('order', $sCols, true) ? 'order' : null);
        $publishedCol = in_array('is_published', $sCols, true) ? 'is_published' : null;

        $rows = DB::connection($source)->table('faqs')->orderBy('id')->get();
        $n = 0;
        foreach ($rows as $row) {
            $row = (array) $row;
            $sort = $orderCol !== null && isset($row[$orderCol]) ? (int) $row[$orderCol] : 0;
            $published = true;
            if ($publishedCol !== null && array_key_exists($publishedCol, $row)) {
                $published = (bool) $row[$publishedCol];
            }

            DB::connection($target)->table('faqs')->updateOrInsert(
                ['id' => (int) $row['id']],
                [
                    'question' => (string) $row['question'],
                    'answer' => (string) $row['answer'],
                    'sort_order' => $sort,
                    'is_published' => $published,
                    'created_at' => $row['created_at'] ?? now(),
                    'updated_at' => $row['updated_at'] ?? now(),
                ]
            );
            $n++;
        }

        $this->info("FAQs: {$n} row(s) upserted.");
    }

    /**
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

    /**
     * @param  list<int>  $ids
     * @return list<int>
     */
    private function expandCategoryAncestorIds(string $source, array $ids, bool $categoriesHaveShopId): array
    {
        $ids = array_values(array_unique(array_filter($ids)));
        $seen = $ids;
        $frontier = $ids;

        while ($frontier !== []) {
            $parents = DB::connection($source)->table('categories')
                ->whereIn('id', $frontier)
                ->pluck('parent_id')
                ->filter()
                ->map(fn ($p): int => (int) $p)
                ->all();

            if ($categoriesHaveShopId) {
                $allowed = DB::connection($source)->table('categories')
                    ->whereIn('id', $parents)
                    ->whereNull('shop_id')
                    ->pluck('id')
                    ->map(fn ($cid): int => (int) $cid)
                    ->all();
                $parents = array_values(array_intersect($parents, $allowed));
            }

            $new = array_values(array_diff($parents, $seen));
            $seen = [...$seen, ...$new];
            $frontier = $new;
        }

        return array_values(array_unique($seen));
    }
}
