<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $data['user_id'] = $user->id;

        return $data;
    }
}
