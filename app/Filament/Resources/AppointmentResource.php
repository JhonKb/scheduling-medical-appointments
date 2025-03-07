<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Specialization;
use Filament\Forms;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('specialization_id')
                    ->label('Specialization')
                    ->options(Specialization::all()
                        ->pluck('name', 'id')
                        ->toArray())
                    ->reactive()
                    ->required()
                    ->dehydrated(fn(?string $state): bool => false),
                Forms\Components\Select::make('doctor_id')
                    ->options(function (callable $get) {
                        $specializationId = $get('specialization_id');
                        if ($specializationId) {
                            return Doctor::where('specialization_id', $specializationId)
                                ->join('users', 'users.id', '=', 'doctors.user_id')
                                ->pluck('users.name', 'doctors.id')->toArray();
                        }
                        return [];
                    })
                    ->disabled(fn(callable $get) => !$get('specialization_id'))
                    ->reactive()
                    ->required(),
                Forms\Components\Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->reactive()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\DatePicker::make('birth_date')
                            ->required()
                            ->maxDate(now()->subYear()),
                        Forms\Components\Select::make('gender')
                            ->options(['M' => 'Male', 'F' => 'Female', 'O' => 'Other'])
                            ->required(),
                        Forms\Components\TextInput::make('cpf')
                            ->mask('999.999.999-99')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->mask('(99) 99999-9999')
                            ->required(),
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->minDate(now()->toDateString())
                    ->disabled(fn(callable $get) => !$get('doctor_id'))
                    ->required(),
                TimePicker::make('time')
                    ->disabled(fn(callable $get) => !$get('doctor_id'))
                    ->required(),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options(['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'])
                    ->default('pending')
                    ->disabled(fn(string $operation): bool => $operation === 'create'),
                Forms\Components\Textarea::make('reason')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->label('Doctor')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('patient.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppointments::route('/'),
        ];
    }
}
