<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

class Login extends BaseLogin
{
    public function getTitle(): string|Htmlable
    {
        return 'Entrar no SAFE';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Acesse o SAFE';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Portal de autorizações escolares do SENAI-SP.';
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label('E-mail institucional')
            ->placeholder('nome@safe.com')
            ->email()
            ->required()
            ->autocomplete()
            ->autofocus();
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label('Senha')
            ->placeholder('Digite sua senha')
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->autocomplete('current-password')
            ->required();
    }

    protected function getRememberFormComponent(): Component
    {
        return Checkbox::make('remember')
            ->label('Manter conectado');
    }

    protected function getAuthenticateFormAction(): Action
    {
        return Action::make('authenticate')
            ->label('Entrar')
            ->submit('authenticate');
    }
}
