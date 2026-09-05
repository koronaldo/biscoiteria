<?php

namespace App\Filament\Resources\UserGroups\Pages;

use App\Filament\Resources\UserGroups\UserGroupResource;
use App\Models\Menu;
use App\Models\UserGroup;
use App\Services\UserGroupMenuService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class ManageGroupMenus extends Page
{
    use InteractsWithRecord;

    protected static string $resource = UserGroupResource::class;

    public function getTitle(): string
    {
        return 'Gerenciar menus do grupo';
    }

    public array $data = [];

    public array $menuTree = [];

    public array $previousMenusState = [];

    public function getView(): string
    {
        return 'filament.resources.user-groups.pages.manage-group-menus';
    }

    protected function resolveRecord(string|int $key): Model
    {
        return UserGroup::findOrFail($key);
    }

    public function mount(string|int $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->data['menus'] = $this->record
            ->menus()
            ->pluck('menus.id')
            ->map(fn ($id) => (string) $id)
            ->toArray();

        $this->previousMenusState = $this->data['menus'];

        $this->menuTree = $this->buildMenuTree();
    }

    protected function buildMenuTree(): array
    {
        return Menu::query()
            ->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('sort')->orderBy('title')])
            ->orderBy('sort')
            ->orderBy('title')
            ->get()
            ->toArray();
    }

    protected function getParentChildMap(): array
    {
        $map = [];
        foreach ($this->menuTree as $parent) {
            $map[(string) $parent['id']] = collect($parent['children'])
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        }

        return $map;
    }

    public function updatedDataMenus(): void
    {
        $map = $this->getParentChildMap();
        $previous = $this->previousMenusState;
        $current = array_map('strval', $this->data['menus']);

        $added = array_diff($current, $previous);
        $removed = array_diff($previous, $current);

        foreach ($map as $parentId => $childrenIds) {

            if (in_array($parentId, $added)) {
                $current = array_merge($current, $childrenIds);
            }

            if (in_array($parentId, $removed)) {
                $current = array_diff($current, $childrenIds);
            }

            $childAdded = array_intersect($childrenIds, $added);
            $childRemoved = array_intersect($childrenIds, $removed);

            if (! empty($childAdded)) {
                $current[] = $parentId;
            }

            if (! empty($childRemoved)) {
                $stillChecked = array_intersect($childrenIds, $current);
                if (empty($stillChecked)) {
                    $current = array_diff($current, [$parentId]);
                }
            }
        }

        $current = array_values(array_unique($current));

        $this->previousMenusState = $current;
        $this->data['menus'] = $current;
    }

    public function save(UserGroupMenuService $service): void
    {
        $menus = array_map('intval', $this->data['menus'] ?? []);

        $service->syncMenus($this->record, $menus);

        Notification::make()
            ->title('Menus atualizados com sucesso')
            ->success()
            ->send();
    }
}
