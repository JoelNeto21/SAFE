@php
    $shortcuts = [
        ['key' => '0', 'label' => 'Admin', 'email' => 'admin@safe.com'],
        ['key' => '1', 'label' => 'AQV', 'email' => 'aqv@safe.com'],
        ['key' => '2', 'label' => 'Prof. Bruno (DS)', 'email' => 'bruno@safe.com'],
        ['key' => '3', 'label' => 'Prof. Samuel (Eletro)', 'email' => 'samuel@safe.com'],
        ['key' => '4', 'label' => 'Prof. Eduardo (Geral)', 'email' => 'eduardo@safe.com'],
        ['key' => '5', 'label' => 'Portaria', 'email' => 'portaria@safe.com'],
    ];
@endphp

<aside class="safe-login-shortcuts" aria-label="Atalhos de login para testes">
    <div class="safe-login-shortcuts__header">
        <span>Atalhos de teste</span>
        <button type="button" class="safe-login-shortcuts__toggle" aria-expanded="true" aria-label="Alternar atalhos">
            <span class="safe-login-shortcuts__toggle-icon">
                {!! \Filament\Support\generate_icon_html(\Filament\Support\Icons\Heroicon::ChevronUp, size: \Filament\Support\Enums\IconSize::Small)->toHtml() !!}
            </span>
        </button>
    </div>

    <dl class="safe-login-shortcuts__list">
        @foreach ($shortcuts as $shortcut)
            <div
                class="safe-login-shortcuts__item"
                data-safe-login-shortcut="{{ $shortcut['key'] }}"
                role="button"
                tabindex="0"
                aria-label="Preencher login de {{ $shortcut['label'] }}"
            >
                <dt class="safe-login-shortcuts__key">
                    <kbd class="kbd-ctrl">Ctrl</kbd>
                    <span class="safe-login-shortcuts__plus">+</span>
                    <kbd class="kbd-key">{{ $shortcut['key'] }}</kbd>
                </dt>
                <dd>
                    <strong>{{ $shortcut['label'] }}</strong>
                    <span>{{ $shortcut['email'] }}</span>
                </dd>
            </div>
        @endforeach
    </dl>
</aside>

<script>
    (() => {
        const credentials = {
            '0': { email: 'admin@safe.com', password: '12345678' },
            '1': { email: 'aqv@safe.com', password: '12345678' },
            '2': { email: 'bruno@safe.com', password: '12345678' },
            '3': { email: 'samuel@safe.com', password: '12345678' },
            '4': { email: 'eduardo@safe.com', password: '12345678' },
            '5': { email: 'portaria@safe.com', password: '12345678' },
        };

        const fillField = (selector, value) => {
            const field = document.querySelector(selector);

            if (! field) {
                return;
            }

            field.value = value;
            field.dispatchEvent(new Event('input', { bubbles: true }));
            field.dispatchEvent(new Event('change', { bubbles: true }));
        };

        const fillLogin = (shortcut) => {
            const credential = credentials[shortcut];

            if (! credential) {
                return;
            }

            fillField('[wire\\:model="data.email"]', credential.email);
            fillField('[wire\\:model="data.password"]', credential.password);

            document
                .querySelector(`[data-safe-login-shortcut="${shortcut}"]`)
                ?.animate(
                    [
                        { transform: 'translateY(0)', background: 'rgba(227, 6, 19, 0.08)' },
                        { transform: 'translateY(-2px)', background: 'rgba(227, 6, 19, 0.16)' },
                        { transform: 'translateY(0)', background: 'rgba(255, 255, 255, 0.92)' },
                    ],
                    { duration: 360, easing: 'ease-out' },
                );
        };

        document.querySelectorAll('[data-safe-login-shortcut]').forEach((shortcutItem) => {
            const shortcut = shortcutItem.dataset.safeLoginShortcut;

            shortcutItem.addEventListener('click', () => fillLogin(shortcut));

            shortcutItem.addEventListener('keydown', (event) => {
                if (! ['Enter', ' '].includes(event.key)) {
                    return;
                }

                event.preventDefault();
                fillLogin(shortcut);
            });
        });

        // toggle collapse/expand
        const aside = document.querySelector('.safe-login-shortcuts');
        const toggleBtn = document.querySelector('.safe-login-shortcuts__toggle');

        if (toggleBtn && aside) {
            toggleBtn.addEventListener('click', () => {
                const isCollapsed = aside.classList.toggle('collapsed');
                // aria-expanded reflects whether the list is visible
                toggleBtn.setAttribute('aria-expanded', String(!isCollapsed));
            });
        }

        document.addEventListener('keydown', (event) => {
            if (! event.ctrlKey || event.altKey || event.metaKey || event.shiftKey) {
                return;
            }

            if (! Object.hasOwn(credentials, event.key)) {
                return;
            }

            event.preventDefault();
            fillLogin(event.key);
        });
    })();
</script>
