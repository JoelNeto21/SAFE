<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-8">
        <p class="text-sm font-semibold uppercase tracking-[0.08em] text-[#e30613]">Entrar no SAFE</p>
        <h2 class="mt-2 text-2xl font-semibold tracking-normal text-zinc-950">Bem-vindo de volta</h2>
        <p class="mt-2 text-sm leading-6 text-zinc-500">Use suas credenciais para acessar o painel administrativo.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="E-mail institucional" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nome@safe.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-5">
            <x-input-label for="password" value="Senha" />

            <x-text-input id="password" class="mt-2 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="Digite sua senha" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-5 block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-zinc-300 text-[#e30613] shadow-sm focus:ring-[#e30613]" name="remember">
                <span class="ms-2 text-sm text-zinc-600">Manter conectado</span>
            </label>
        </div>

        <div class="mt-8 flex items-center justify-between gap-4">
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm font-medium text-zinc-600 hover:text-[#e30613] focus:outline-none focus:ring-2 focus:ring-[#e30613] focus:ring-offset-2" href="{{ route('password.request') }}">
                    Esqueceu a senha?
                </a>
            @endif

            <x-primary-button>
                Entrar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
