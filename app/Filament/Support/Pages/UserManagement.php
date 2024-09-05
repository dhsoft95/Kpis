<?php

namespace App\Filament\Support\Pages;

use App\Filament\Support\Widgets\UserInfo;
use App\Models\AppUser;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Query\Builder;

class UserManagement extends Page
{

//    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected int | string | array $columnSpan = 'full';

    protected static ?string $navigationGroup = 'User Management';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';


    protected static string $view = 'filament.support.pages.user-management';

    protected function getHeaderWidgets(): array
    {
        return [
            UserInfo::class,
        ];
    }



    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
}
