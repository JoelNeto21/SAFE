# SAFE - Sistema de Autorizacao e Fluxo Escolar

SAFE e uma aplicacao Laravel com painel administrativo em Filament para controlar autorizacoes de entrada tardia e saida antecipada de alunos. O objetivo e substituir registros manuais por um fluxo digital rastreavel entre AQV, professores e portaria.

## Visao geral

A aplicacao organiza o processo operacional da escola em um painel unico:

- AQV e portaria cadastram autorizacoes para alunos.
- O professor responsavel pela turma avalia a solicitacao.
- Entradas sao encerradas automaticamente apos aprovacao do professor.
- Saidas seguem para a portaria, que libera e finaliza o fluxo.
- Autorizacoes finalizadas saem da tela operacional e permanecem no historico.
- Notificacoes internas registram os passos relevantes do fluxo.

## Stack

| Camada | Tecnologia |
| --- | --- |
| Backend | PHP 8.3, Laravel 13 |
| Admin | Filament 5, Livewire 4 |
| Banco local | MySQL |
| Testes | PHPUnit, SQLite em memoria |
| Permissoes | Spatie Laravel Permission |
| Frontend | Tailwind CSS 4, Vite, Chart.js |
| Qualidade | Laravel Pint |

## Perfis de acesso

| Perfil | Responsabilidade |
| --- | --- |
| Admin | Gerencia usuarios, cadastros, permissoes e toda a operacao. |
| AQV | Cadastra e acompanha autorizacoes, alunos, turmas e comunicacoes internas. |
| Professor | Visualiza apenas turmas e autorizacoes sob sua responsabilidade. |
| Portaria | Acompanha autorizacoes de saida e confirma a liberacao final do aluno. |

## Funcionalidades

- Autenticacao no painel `/admin`.
- CRUD de funcionarios, cursos, turmas e alunos.
- Vinculo de professores a uma ou mais turmas.
- Cadastro de autorizacoes de entrada e saida.
- Selecao obrigatoria do professor responsavel conforme a turma do aluno.
- Notificacao enviada somente ao professor responsavel pela autorizacao.
- Notificacao para a portaria quando houver fluxo de saida.
- Marcacao automatica de notificacao como lida ao abrir.
- Badge de notificacoes baseado apenas em itens nao lidos.
- Historico auditavel de leitura, aprovacao, recusa e finalizacao.
- Grafico de pizza para distribuicao dos status das autorizacoes.
- Centralizacao visual das telas de criacao e edicao.

## Regras de autorizacao

### Entrada

1. AQV ou portaria cria a autorizacao de entrada.
2. O sistema envia a notificacao ao professor responsavel.
3. O professor aprova ou recusa.
4. Ao aprovar, a autorizacao e finalizada automaticamente.
5. O registro deixa a lista operacional e fica no historico.

### Saida

1. AQV ou portaria cria a autorizacao de saida.
2. O professor responsavel recebe a notificacao e avalia.
3. Se aprovada, a portaria e notificada.
4. A portaria confirma a liberacao do aluno.
5. A autorizacao e finalizada e mantida no historico.

## Aulas perdidas

Ao criar ou editar uma autorizacao, existe uma secao opcional para indicar aulas ou periodos perdidos no dia. Sao 5 checkboxes:

- 1a aula / periodo
- 2a aula / periodo
- 3a aula / periodo
- 4a aula / periodo
- 5a aula / periodo

Esses campos nao sao obrigatorios e devem ser usados apenas quando fizer sentido para o horario de entrada ou saida do aluno.

## Validacoes e mascaras

- O horario da autorizacao deve ficar entre `07:30` e `23:00`.
- A data da autorizacao e sempre a data atual.
- A hora pode ser editada no formulario.
- Matriculas de alunos possuem mascara e validacao visual.
- Nomes de turmas possuem mascara e padronizacao.
- Campos nativos de e-mail, senha e horario usam os tipos apropriados do navegador.
- As validacoes tambem sao aplicadas no backend para evitar dados fora do padrao.

## Estrutura do projeto

| Caminho | Descricao |
| --- | --- |
| `app/Models` | Entidades principais do dominio, como usuarios, alunos, turmas e autorizacoes. |
| `app/Filament/Resources` | Telas administrativas, formularios, tabelas e acoes do painel. |
| `app/Policies` | Regras de acesso por perfil e escopo de turma. |
| `app/Services/SafeNotifier.php` | Servico central de notificacoes internas. |
| `database/migrations` | Estrutura do banco de dados. |
| `database/seeders` | Dados iniciais e usuarios de demonstracao. |
| `tests/Feature` | Testes automatizados dos fluxos principais. |
| `resources/css/filament` | Ajustes visuais do painel Filament. |

## Configuracao local

Instale as dependencias:

```bash
composer install
npm install
```

Crie o arquivo de ambiente:

```bash
cp .env.example .env
php artisan key:generate
```

Configure o MySQL local no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=safe
DB_USERNAME=root
DB_PASSWORD=senaisp
```

Crie o banco `safe` no MySQL antes de rodar as migracoes, caso ele ainda nao exista.

## Banco e seeders

Para recriar o banco do zero e popular os dados de demonstracao:

```bash
php artisan migrate:fresh --seed
```

Os seeders criam perfis, permissoes, usuarios de teste, cursos, turmas, alunos e tipos de autorizacao.

## Assets

Compile os assets de producao:

```bash
npm run build
```

Durante desenvolvimento, use:

```bash
npm run dev
```

## Execucao

Suba o servidor local:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Acesse o painel:

```text
http://127.0.0.1:8000/admin
```

## Usuarios de teste

Todos os usuarios abaixo usam a senha `12345678`, exceto `test@example.com`, que usa `password`.

| Perfil | E-mail | Senha | Escopo |
| --- | --- | --- | --- |
| Admin | `admin@safe.com` | `12345678` | Acesso total |
| AQV | `aqv@safe.com` | `12345678` | Operacao de autorizacoes e cadastros escolares |
| Portaria | `portaria@safe.com` | `12345678` | Confirmacao de saidas |
| Professor Eduardo | `eduardo@safe.com` | `12345678` | DS e Eletroeletronica |
| Professor Samuel | `samuel@safe.com` | `12345678` | Eletroeletronica |
| Professor Bruno | `bruno@safe.com` | `12345678` | Desenvolvimento de Sistemas |
| Test User | `test@example.com` | `password` | Usuario tecnico para testes |

## Testes e qualidade

Execute a suite automatizada:

```bash
php artisan test
```

Verifique o padrao de codigo:

```bash
vendor/bin/pint --test
```

Limpe caches da aplicacao:

```bash
php artisan optimize:clear
```

## Checklist de demonstracao

1. Entrar como AQV e criar uma autorizacao de entrada para um aluno.
2. Selecionar o professor responsavel exibido conforme a turma.
3. Entrar como professor e abrir a notificacao recebida.
4. Aprovar a entrada e confirmar que ela foi finalizada.
5. Criar uma autorizacao de saida.
6. Aprovar como professor.
7. Entrar como portaria, abrir a notificacao e finalizar a saida.
8. Conferir o registro em Historico de Autorizacoes.

## Licenca

Projeto academico desenvolvido com Laravel, Filament e bibliotecas open source.
