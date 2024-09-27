## Brazilians Inputs for Filament Forms

## Installation

Require this package in your composer.json and update composer. This will download the package.

    composer require michaeld555/filament-fields
  
## Using the fields

To create a money input use:

```php
    use Michaeld555\MoneyInput;

    MoneyInput::make('value')
    ->prefix('R$')
```
To create a cep input use:

```php
    use Michaeld555\CepInput;

    Cep::make('postal_code')
    ->viaCep(
        mode: 'suffix', // Determines whether the action should be appended to (suffix) or prepended to (prefix) the cep field, or not included at all (none).
        errorMessage: 'CEP inválido.', // Error message to display if the CEP is invalid.

        /**
         * Other form fields that can be filled by ViaCep.
         * The key is the name of the Filament input, and the value is the ViaCep attribute that corresponds to it.
         * More information: https://viacep.com.br/
         */
        setFields: [
            'street' => 'logradouro',
            'number' => 'numero',
            'complement' => 'complemento',
            'district' => 'bairro',
            'city' => 'localidade',
            'state' => 'uf'
        ],
        citiesTable: 'cities', // if you want to set a field with information from your cities table in the database
        ibgeColumn: 'igbe_code',  // the name of your column in the cities database that receives the IBGE code
    ),

```