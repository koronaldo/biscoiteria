<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Models\UserGroup;
use App\Services\UserGroupAssignmentService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;

class ManageUserGroups extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Gerenciar Grupos de Usuários';

    public array $data = [];

    public function getView(): string
    {
        return 'filament.resources.users.pages.manage-user-groups';
    }

    protected function resolveRecord(string|int $key): Model
    {
        return User::findOrFail($key);
    }

    protected function getFormStatePath(): string
    {
        return 'data';
    }

    public function mount(string|int $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->data['groups'] = $this->record
            ->groups()
            ->pluck('user_groups.id')
            ->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            CheckboxList::make('groups')
                ->label('Grupos do usuário')
                ->options(
                    UserGroup::query()
                        ->orderBy('descricao')
                        ->pluck('descricao', 'id')
                        ->toArray()
                )
                ->columns(2),
        ];
    }

    public function save(UserGroupAssignmentService $service): void
    {
        $groups = $this->data['groups'] ?? [];

        $service->syncGroups($this->record, $groups);

        Notification::make()
            ->title('Grupos atualizados com sucesso')
            ->success()
            ->send();
    }
}
