# EloquentTrainer

Plataforma fullstack em Laravel para aprender e praticar **Eloquent ORM** com:
- desafios (50+)
- feedback instantaneo
- *SQL explainer* (SQL + bindings)
- pontuacao por usuario (persistida no banco)

---

## Funcionalidades

- **Desafios**: niveis `easy` / `medium` / `hard`, com pontuacao.
- **Universo de dados**: schema “Cinema & Streaming” (Users, Profiles, Movies, Directors, Actors, Genres, Subscriptions, Reviews).
- **Eloquent Judge**: valida a entrada, executa em sandbox e compara com o gabarito.
- **Login/Cadastro**: progresso e pontos salvos por usuario.

---

## Como rodar (Windows / PowerShell)

1) Dependencias PHP
```powershell
composer install
copy .env.example .env
php artisan key:generate
```

2) Banco de dados
```powershell
# Configure DB_* no .env
php artisan migrate:fresh --seed
```

3) Frontend (Tailwind/Vite)
```powershell
npm install
npm run dev
```

4) Servidor
```powershell
php artisan serve
```

URLs:
- Landing: `http://127.0.0.1:8000/`
- Login: `http://127.0.0.1:8000/login`
- Desafios (requer login): `http://127.0.0.1:8000/challenges`

---

## Login

- Criar conta: `/register`
- Usuarios do seed (CinemaSeeder): senha padrao da factory = `password`

---

## Estrutura (arquivos importantes)

```text
app/
  Challenges/
    ChallengeRepository.php      # 50+ desafios (gabaritos e metadados)
    SchemaCatalog.php            # colunas + tipos (dica no enunciado)
  Http/Controllers/
    AuthController.php           # login/cadastro/logout
    ChallengeController.php      # listagem, pagina do desafio, submissao
  Models/
    ChallengeProgress.php        # progresso por usuario
  Services/
    EloquentJudgeService.php     # core: sandbox + comparacao + SQL

database/
  migrations/
    2026_02_02_000001_create_challenge_progress_table.php
  seeders/
    CinemaSeeder.php

resources/views/
  landing.blade.php
  challenges/
    index.blade.php
    show.blade.php
  auth/
    login.blade.php
    register.blade.php
```

---

## Progresso / Pontuacao

Tabela `challenge_progress`:
- `user_id` (FK)
- `challenge_id` (string)
- `completed`, `points_awarded`, `attempts`
- `last_code`, `completed_at`

---

## Notas de seguranca (importante)

O Judge usa `eval()` para executar a expressao Eloquent do usuario. Ha filtragem por regex e rollback via transaction, mas isso **nao** e um sandbox seguro para producao.

Se for publicar:
- isole a execucao (processo/container separado)
- aplique timeouts e limites de memoria
- use parser/AST no lugar de regex
- registre auditoria e bloqueie funcoes perigosas por config/ini
