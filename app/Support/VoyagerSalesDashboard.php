<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Admin dashboard metrics and scopes ported from {@see resources/views/vendor/voyager/index.blade.php}.
 */
final class VoyagerSalesDashboard
{
    public static function moneyFormat(float|int $number): string
    {
        return number_format((float) $number, 0, ',', ' ');
    }

    public static function today(): Carbon
    {
        return Carbon::today();
    }

    public static function hasDateRange(?Request $request = null): bool
    {
        $request ??= request();

        return $request->has('start') || $request->has('end');
    }

    /**
     * @param  Builder<Order>  $query
     * @return Builder<Order>
     */
    public static function applyOrderFilters(Builder $query, ?Request $request = null): Builder
    {
        $request ??= request();

        return $query
            ->when($request->filled('short'), function (Builder $q) use ($request): void {
                $q->whereHas('shop', function (Builder $sq) use ($request): void {
                    $sq->where('user_name', $request->string('short'));
                });
            })
            ->when(self::hasDateRange($request), function (Builder $q) use ($request): void {
                $q->whereBetween('created_at', [
                    $request->input('start'),
                    $request->input('end'),
                ]);
            });
    }

    /**
     * Paid orders (status = 1) with optional shop + date filters (Voyager semantics).
     *
     * @return Builder<Order>
     */
    public static function paidOrdersBase(?Request $request = null): Builder
    {
        return self::applyOrderFilters(
            Order::query()->where('status', 1),
            $request
        );
    }

    /**
     * @return Builder<Order>
     */
    public static function paidOrdersForLatestTable(?Request $request = null): Builder
    {
        return self::paidOrdersBase($request)->latest('created_at');
    }

    /**
     * @return Builder<Product>
     */
    public static function rootProductsBase(?Request $request = null): Builder
    {
        $request ??= request();

        return Product::query()
            ->whereNull('parent_id')
            ->when($request->filled('short'), function (Builder $q) use ($request): void {
                $q->whereHas('shop', function (Builder $sq) use ($request): void {
                    $sq->where('user_name', $request->string('short'));
                });
            })
            ->when(self::hasDateRange($request), function (Builder $q) use ($request): void {
                $q->whereBetween('created_at', [
                    $request->input('start'),
                    $request->input('end'),
                ]);
            });
    }

    /**
     * @return Builder<Product>
     */
    public static function mostSoldProductsForTable(?Request $request = null): Builder
    {
        return self::rootProductsBase($request)->orderByDesc('sale_count');
    }

    public static function shopsCount(?Request $request = null): int
    {
        $request ??= request();

        return Shop::query()
            ->where('status', 1)
            ->when(self::hasDateRange($request), function (Builder $q) use ($request): void {
                $q->whereBetween('created_at', [
                    $request->input('start'),
                    $request->input('end'),
                ]);
            })
            ->count();
    }

    /**
     * User query scoped like Voyager's $shops_old (before today + role filters).
     *
     * @return Builder<User>
     */
    public static function shopsOldUserBase(?Request $request = null): Builder
    {
        $request ??= request();

        return User::query()
            ->when($request->filled('short'), function (Builder $q) use ($request): void {
                $q->whereHas('shop', function (Builder $sq) use ($request): void {
                    $sq->where('user_name', $request->string('short'));
                });
            })
            ->when(self::hasDateRange($request), function (Builder $q) use ($request): void {
                $q->whereBetween('created_at', [
                    $request->input('start'),
                    $request->input('end'),
                ]);
            });
    }

    public static function shopsTodayCount(?Request $request = null): int
    {
        return self::shopsOldUserBase($request)
            ->whereDate('created_at', self::today())
            ->where('role_id', User::ROLES['Vendor'])
            ->count();
    }

    public static function totalCustomersCount(): int
    {
        return User::query()->where('role_id', User::ROLES['User'])->count();
    }

    public static function customersRegisteredTodayCount(): int
    {
        return User::query()
            ->whereDate('created_at', self::today())
            ->where('role_id', User::ROLES['User'])
            ->count();
    }

    public static function totalSalesSum(?Request $request = null): float
    {
        return (float) self::paidOrdersBase($request)->sum('total');
    }

    public static function todaySalesSum(?Request $request = null): float
    {
        return (float) self::paidOrdersBase($request)
            ->whereDate('created_at', self::today())
            ->sum('total');
    }

    public static function averageOrderValue(?Request $request = null): float
    {
        $total = (float) self::paidOrdersBase($request)->sum('total');
        $count = (int) self::paidOrdersBase($request)->count('id');

        return $total / max($count, 1);
    }

    /**
     * Top 5 customers by lifetime order total (join matches Voyager; no status filter on orders).
     *
     * @return Collection<int, object{id: int, name: string, total_orders: float|int|string}>
     */
    public static function topCustomers(): Collection
    {
        return DB::table('users')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.id', 'users.name', DB::raw('SUM(orders.total) as total_orders'))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_orders')
            ->limit(5)
            ->get();
    }
}
