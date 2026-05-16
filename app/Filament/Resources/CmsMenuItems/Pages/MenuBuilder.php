<?php

declare(strict_types=1);

namespace App\Filament\Resources\CmsMenuItems\Pages;

use App\Enums\MenuContext;
use App\Filament\Resources\CmsMenuItems\CmsMenuItemResource;
use App\Filament\Resources\CmsMenus\CmsMenuResource;
use App\Models\CmsMenu;
use App\Models\CmsMenuItem;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\View as SchemaView;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;

class MenuBuilder extends ListRecords
{
    protected static string $resource = CmsMenuItemResource::class;

    protected static ?string $title = null;

    protected static ?string $navigationLabel = 'Menu builder';

    protected static ?string $breadcrumb = 'Menu builder';

    protected static ?int $navigationSort = 18;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquares2x2;

    #[Url]
    public string $context = 'frontend';

    #[Url]
    public ?int $menuId = null;

    #[Url]
    public ?int $parentId = null;

    public function mount(): void
    {
        parent::mount();

        $this->ensureMenuId();
    }

    public function updatedContext(): void
    {
        $this->parentId = null;
        $this->menuId = null;
        $this->ensureMenuId();
        $this->resetTable();
    }

    public function updatedMenuId(): void
    {
        $this->parentId = null;
        $this->resetTable();
    }

    public function goUpParent(): void
    {
        if ($this->parentId === null) {
            return;
        }

        $parent = CmsMenuItem::query()->find($this->parentId);
        $this->parentId = $parent?->parent_id;
        $this->resetTable();
    }

    public function getTitle(): string
    {
        return __('Menu builder');
    }

    protected function menuContext(): MenuContext
    {
        return MenuContext::tryFrom($this->context) ?? MenuContext::Frontend;
    }

    protected function ensureMenuId(): void
    {
        if ($this->menuId !== null) {
            $exists = CmsMenu::query()
                ->whereKey($this->menuId)
                ->forContext($this->menuContext())
                ->exists();

            if ($exists) {
                return;
            }
        }

        $this->menuId = CmsMenu::query()
            ->forContext($this->menuContext())
            ->orderBy('name')
            ->value('id');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageMenus')
                ->label(__('Manage menus'))
                ->icon(Heroicon::OutlinedBars3)
                ->url(fn (): string => CmsMenuResource::getUrl())
                ->color('gray'),
            CreateAction::make()
                ->mutateFormDataUsing(function (array $data): array {
                    $data['cms_menu_id'] = $this->menuId;
                    $data['parent_id'] = $this->parentId;

                    return $data;
                }),
        ];
    }

    protected function getTableQuery(): Builder
    {
        if ($this->menuId === null) {
            return CmsMenuItem::query()->whereRaw('0 = 1');
        }

        $query = CmsMenuItem::query()->where('cms_menu_id', $this->menuId);

        if ($this->parentId === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $this->parentId);
        }

        return $query->orderBy('sort_order');
    }

    protected function getTableReorderColumn(): ?string
    {
        return 'sort_order';
    }

    public function table(Table $table): Table
    {
        $isAdmin = $this->menuContext() === MenuContext::Admin;

        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable(),
                TextColumn::make('link_type')
                    ->label(__('Link type'))
                    ->badge(),
                TextColumn::make('url')
                    ->label(__('URL'))
                    ->limit(32)
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('route_name')
                    ->label(__('Route'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('resource_class')
                    ->label(__('Resource'))
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '—')
                    ->toggleable($isAdmin),
                TextColumn::make('navigation_group')
                    ->label(__('Nav group'))
                    ->toggleable($isAdmin)
                    ->placeholder('—'),
                TextColumn::make('sort_order')
                    ->label(__('Order'))
                    ->numeric()
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
                IconColumn::make('open_new_tab')
                    ->label(__('New tab'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('subitems')
                    ->label(__('Sub-items'))
                    ->icon(Heroicon::OutlinedFolderOpen)
                    ->url(fn (CmsMenuItem $record): string => static::getUrl([
                        'context' => $this->context,
                        'menuId' => $this->menuId,
                        'parentId' => $record->id,
                    ])),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaView::make('filament.cms.menu-builder-header')
                    ->viewData(fn ($livewire): array => [
                        'menuId' => $livewire->menuId,
                        'parentId' => $livewire->parentId,
                        'context' => $livewire->context,
                    ]),
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }
}
