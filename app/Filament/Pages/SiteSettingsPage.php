<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class SiteSettingsPage extends Page
{
    use WithFileUploads;

    protected static ?string $slug = 'settings';

    protected static ?string $navigationLabel = 'Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'site';

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $title = 'Settings';

    protected string $view = 'filament.pages.site-settings';

    public string $activeGroup = '';

    /** @var list<string> */
    public array $groups = [];

    /** @var array<int, array<string, mixed>> */
    public array $settings = [];

    /** @var array<int, mixed> */
    public array $values = [];

    /** @var array<int, TemporaryUploadedFile|null> */
    public array $uploads = [];

    public bool $showAddForm = false;

    public string $newKey = '';

    public string $newLabel = '';

    public string $newType = 'text';

    public string $newGroup = 'general';

    public int $newSortOrder = 0;

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function switchGroup(string $group): void
    {
        $this->activeGroup = $group;
    }

    public function save(): void
    {
        $activeSettings = collect($this->settings)
            ->filter(fn (array $s): bool => $s['group_name'] === $this->activeGroup);

        foreach ($activeSettings as $id => $setting) {
            $model = SiteSetting::find($id);
            if (! $model) {
                continue;
            }

            if (in_array($setting['type'], ['image', 'file']) && isset($this->uploads[$id]) && $this->uploads[$id] !== null) {
                $file = $this->uploads[$id];
                $path = $file->store('site-settings', 'public');

                if ($model->value && Storage::disk('public')->exists($model->value)) {
                    Storage::disk('public')->delete($model->value);
                }

                $model->update(['value' => $path]);
                $this->values[$id] = $path;
                unset($this->uploads[$id]);
            } elseif (! in_array($setting['type'], ['image', 'file'])) {
                $value = $this->values[$id] ?? null;

                if ($setting['type'] === 'checkbox') {
                    $value = $value ? '1' : '0';
                }

                $model->update(['value' => $value]);
            }
        }

        Notification::make()
            ->title(__('Settings saved successfully!'))
            ->success()
            ->send();
    }

    public function removeFile(int $id): void
    {
        $setting = SiteSetting::find($id);

        if ($setting?->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting?->update(['value' => null]);
        $this->values[$id] = null;
        unset($this->uploads[$id]);
    }

    public function addSetting(): void
    {
        $this->validate([
            'newKey' => ['required', 'string', 'max:255', 'unique:site_settings,key'],
            'newLabel' => ['required', 'string', 'max:255'],
            'newType' => ['required', 'in:text,textarea,number,checkbox,file,image,select_dropdown'],
            'newGroup' => ['required', 'string', 'max:255'],
            'newSortOrder' => ['integer'],
        ]);

        SiteSetting::create([
            'key' => $this->newKey,
            'label' => $this->newLabel,
            'type' => $this->newType,
            'group_name' => $this->newGroup,
            'sort_order' => $this->newSortOrder,
            'value' => null,
        ]);

        $this->reset(['newKey', 'newLabel', 'newSortOrder', 'showAddForm']);
        $this->newType = 'text';
        $this->newGroup = 'general';

        $this->loadSettings();

        Notification::make()
            ->title(__('Setting added successfully!'))
            ->success()
            ->send();
    }

    public function deleteSetting(int $id): void
    {
        $setting = SiteSetting::find($id);

        if ($setting) {
            if (in_array($setting->type, ['image', 'file']) && $setting->value && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }

            $setting->delete();
        }

        $this->loadSettings();

        Notification::make()
            ->title(__('Setting deleted.'))
            ->success()
            ->send();
    }

    protected function loadSettings(): void
    {
        $allSettings = SiteSetting::orderBy('sort_order')->get();

        $this->groups = $allSettings->pluck('group_name')->unique()->values()->all();

        if ($this->activeGroup === '' || ! in_array($this->activeGroup, $this->groups)) {
            $this->activeGroup = $this->groups[0] ?? '';
        }

        $this->settings = [];
        $this->values = [];

        foreach ($allSettings as $setting) {
            $this->settings[$setting->id] = $setting->toArray();
            $this->values[$setting->id] = $setting->value;
        }
    }
}
