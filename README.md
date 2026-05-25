# SAFE - Sistema de Autorização e Fluxo Escolar

SAFE é uma aplicação Laravel com painel administrativo em Filament para digitalizar autorizações, ocorrências, notificações e comunicação interna entre AQV, professores e portaria.

O projeto substitui fluxos manuais em papel por registros digitais rastreáveis, com histórico de ações, permissões por setor e escopo de acesso por turma.

## Sobre o projeto

O SAFE resolve o controle operacional de entrada tardia e saída antecipada de alunos. A AQV registra a autorização, o professor da turma valida a movimentação e a portaria acompanha as liberações necessárias para finalizar o fluxo.

Principais objetivos:

- centralizar autorizações de entrada e saída;
- controlar quais turmas e alunos cada professor pode acessar;
- manter histórico auditável de leituras, aprovações, recusas e finalizações;
- notificar automaticamente os setores envolvidos;
- oferecer um painel administrativo pronto para demonstração acadêmica.

## Stack utilizada

- PHP 8.3
- Laravel 13
- Filament 5
- Livewire 4
- MySQL em desenvolvimento local
- SQLite em memória para testes automatizados
- Spatie Laravel Permission
- Tailwind CSS 4
- Vite
- PHPUnit
- Laravel Pint

## Arquitetura

A aplicação segue a estrutura padrão do Laravel, com separação por domínio:

- `app/Models`: entidades principais, como `User`, `Course`, `Classroom`, `Student`, `Authorization`, `Occurrence`, `InternalMessage` e modelos de auditoria.
- `app/Filament/Resources`: telas administrativas do Filament para CRUDs e listagens operacionais.
- `app/Policies`: regras de autorização por perfil e por escopo de turma.
- `app/Services/SafeNotifier.php`: serviço central de notificações internas.
- `app/Notifications`: notificações persistidas no banco.
- `database/migrations`: estrutura do banco, relações, histórico e mensagens.
- `database/seeders`: dados de demonstração, usuários de teste, cursos, turmas e alunos.
- `tests/Feature`: testes de autenticação, navegação, seeders, permissões e fluxos SAFE.

## Funcionalidades

- Autenticação no painel administrativo.
- Perfis por setor: Admin, AQV, Professor e Portaria.
- CRUD de funcionários com vínculo de professores a múltiplas turmas.
- Cadastro de cursos, turmas e alunos.
- Seeders com cursos Desenvolvimento de Sistemas e Eletroeletrônica.
- Seeders com 3 turmas por curso e 5 alunos por turma.
- Professores seedados: Eduardo, Samuel e Bruno.
- Eduardo acessa DS e Eletroeletrônica.
- Samuel acessa apenas Eletroeletrônica.
- Bruno acessa apenas Desenvolvimento de Sistemas.
- Fluxo de autorização de entrada.
- Fluxo de autorização de saída antecipada.
- Confirmação de leitura pelo professor.
- Aprovação, recusa e finalização com histórico.
- Notificações internas persistidas no banco.
- Badges de mensagens e notificações não lidas.
- Mensagens internas por usuário ou setor.
- Ocorrências escolares com status, observações e auditoria.
- Históricos de autorizações e ocorrências disponíveis no painel.

## Fluxo AQV, Professor e Portaria

1. A AQV ou a portaria registra uma autorização digital.
2. O sistema notifica os professores vinculados à turma do aluno.
3. O professor confirma leitura e aprova ou recusa a solicitação.
4. Em saída antecipada, a portaria recebe a liberação do professor.
5. A portaria confirma a saída e encerra o fluxo.
6. Cada etapa gera registros em histórico e notificações internas.

## Como executar

Clone o projeto e instale as dependências:

```bash
composer install
npm install
```

Configure o ambiente:

```bash
cp .env.example .env
php artisan key:generate
```

Configure o banco no `.env`. Exemplo para MySQL local:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3308
DB_DATABASE=safe
DB_USERNAME=root
DB_PASSWORD=
```

Crie e popule o banco:

```bash
php artisan migrate:fresh --seed
```

Compile os assets:

```bash
npm run build
```

Suba o servidor local:

```bash
php artisan serve
```

Acesse:

```text
http://127.0.0.1:8000/admin
```

## Usuários de teste

Todos os usuários abaixo usam a senha `12345678`, exceto `test@example.com`, que usa `password`.

| Perfil | E-mail | Permissão |
| --- | --- | --- |
| Admin | `admin@safe.com` | Acesso total, incluindo funcionários |
| AQV | `aqv@safe.com` | Autorizações, ocorrências, alunos e turmas |
| Portaria | `portaria@safe.com` | Autorizações, mensagens e confirmações |
| Professor Eduardo | `eduardo@safe.com` | DS e Eletroeletrônica |
| Professor Samuel | `samuel@safe.com` | Eletroeletrônica |
| Professor Bruno | `bruno@safe.com` | Desenvolvimento de Sistemas |
| Test User | `test@example.com` | Admin técnico para testes |

## Testes e qualidade

Execute a suíte automatizada:

```bash
php artisan test
```

Verifique o padrão de código:

```bash
vendor/bin/pint --test
```

Compile os assets antes de publicar:

```bash
npm run build
```

## Melhorias futuras

- Broadcasting com WebSockets para notificações instantâneas sem polling.
- Assinatura digital ou confirmação por QR Code na portaria.
- Relatórios por período, curso, turma e motivo.
- Exportação de autorizações e ocorrências em PDF.
- Dashboard específico por setor.
- Controle de responsáveis legais do aluno.
- Integração com sistemas acadêmicos externos.
- Auditoria avançada com trilhas por IP e dispositivo.

## Licença

Projeto acadêmico desenvolvido sobre Laravel e bibliotecas open source.
