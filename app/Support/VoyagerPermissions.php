<?php

declare(strict_types=1);

namespace App\Support;

use App\Console\Commands\ImportLegacyVoyagerPermissionsCommand;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

/**
 * Voyager BREAD permissions as Spatie permission names: {@code browse_posts}, {@code edit_users}, …
 *
 * After running {@see ImportLegacyVoyagerPermissionsCommand} (or defining the same
 * names manually), use {@see self::allows()} or the {@code voyager.permission} middleware.
 */
final class VoyagerPermissions
{
    /** @var list<string> */
    public const BREAD_ACTIONS = ['browse', 'read', 'edit', 'add', 'delete'];

    /**
     * Spatie / Voyager permission key, e.g. browse + posts → {@code browse_posts}.
     */
    public static function permissionKey(string $action, string $dataTypeSlug): string
    {
        $actionNormalized = strtolower(trim($action));
        $slug = strtolower(str_replace(['-', ' '], '_', trim($dataTypeSlug)));

        return "{$actionNormalized}_{$slug}";
    }

    /**
     * Whether the user has the Voyager-style permission via Spatie.
     *
     * Super admin (Shield) and legacy {@see User::ROLES} Admin role_id bypass checks.
     */
    public static function allows(?User $user, string $action, string $dataTypeSlug): bool
    {
        $user ??= Auth::user();
        if (! $user instanceof User) {
            return false;
        }

        $superAdminRole = (string) config('filament-shield.super_admin.name', 'super_admin');
        if ($user->hasRole($superAdminRole)) {
            return true;
        }

        if ((int) $user->role_id === User::ROLES['Admin']) {
            return true;
        }

        $key = self::permissionKey($action, $dataTypeSlug);

        return $user->can($key);
    }

    public static function authorize(?User $user, string $action, string $dataTypeSlug): void
    {
        if (! self::allows($user, $action, $dataTypeSlug)) {
            abort(403, __('Permission denied'));
        }
    }

    /**
     * Forget Spatie permission cache (call after importing permissions).
     */
    public static function forgetCachedPermissions(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
