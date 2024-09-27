<?php

namespace Michaeld555;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Livewire\Component as Livewire;

class CepInput extends TextInput
{

    public function viaCep(string $mode = 'suffix', string $errorMessage = 'CEP inválido.', array $setFields = [], string $citiesTable = null, string $ibgeColumn = null): static
    {
        $viaCepRequest = function ($state, $livewire, $set, $component, $errorMessage, array $setFields, $citiesTable, $ibgeColumn) {

            $livewire->validateOnly($component->getKey());

            $request = Http::get("viacep.com.br/ws/$state/json/")->json();

            if (!is_null($citiesTable) && !is_null($ibgeColumn) && !blank($request) && !Arr::has($request, 'erro')) {

                $city = DB::table($citiesTable)->where('ibge_code', $ibgeColumn)->first();

                $request['city_id'] = $city->id;

                $request['state_id'] = $city->state_id;

            }

            foreach ($setFields as $key => $value) {

                $set($key, $request[$value] ?? null);

            }

            if (blank($request) || Arr::has($request, 'erro')) {
                throw ValidationException::withMessages([
                    $component->getKey() => $errorMessage,
                ]);
            }
        };

        $this->minLength(9)
            ->mask('99999-999')
            ->afterStateUpdated(function ($state, Livewire $livewire, Set $set, Component $component) use ($errorMessage, $setFields, $viaCepRequest, $citiesTable, $ibgeColumn) {
                $viaCepRequest($state, $livewire, $set, $component, $errorMessage, $setFields, $citiesTable, $ibgeColumn);
            })
            ->suffixAction(function () use ($mode, $errorMessage, $setFields, $viaCepRequest, $citiesTable, $ibgeColumn) {
                if ($mode === 'suffix') {
                    return Action::make('search-action')
                        ->label('Buscar CEP')
                        ->icon('heroicon-o-magnifying-glass')
                        ->action(function ($state, Livewire $livewire, Set $set, Component $component) use ($errorMessage, $setFields, $viaCepRequest, $citiesTable, $ibgeColumn) {
                            $viaCepRequest($state, $livewire, $set, $component, $errorMessage, $setFields, $citiesTable, $ibgeColumn);
                        })
                        ->cancelParentActions();
                }
            })
            ->prefixAction(function () use ($mode, $errorMessage, $setFields, $viaCepRequest, $citiesTable, $ibgeColumn) {
                if ($mode === 'prefix') {
                    return Action::make('search-action')
                        ->label('Buscar CEP')
                        ->icon('heroicon-o-magnifying-glass')
                        ->action(function ($state, Livewire $livewire, Set $set, Component $component) use ($errorMessage, $setFields, $viaCepRequest, $citiesTable, $ibgeColumn) {
                            $viaCepRequest($state, $livewire, $set, $component, $errorMessage, $setFields, $citiesTable, $ibgeColumn);
                        })
                        ->cancelParentActions();
                }
            });

        return $this;
    }

}