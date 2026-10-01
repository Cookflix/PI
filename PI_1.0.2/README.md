# Portal de Receitas — refatoração MVCS

Esta versão é uma base de refatoração do PI em PHP puro.

## Estrutura

- `app/Models`: entidades/modelos.
- `app/Repositories`: acesso ao banco e SQL.
- `app/Services`: regras de negócio.
- `app/Controllers`: fluxo HTTP.
- `app/Database`: conexão PDO.
- `resources/views`: HTML/PHP de apresentação.
- `routes/web.php`: rotas.
- `public/index.php`: front controller.
- `config/database.php`: configuração do banco.
- `composer.json`: autoload PSR-4.

## Instalação

1. Copie `.env.example` para `.env` e configure o banco.
2. Rode `composer dump-autoload`.
3. Aponte o servidor web para `public/`.
4. Garanta que o schema atual do PI contenha, no mínimo:
   - `usuario`
   - `cliente`
   - `culinarista`
   - `receita`
5. Ajuste `ReceitaRepository` quando seu schema real de receitas/estatísticas for confirmado.

## Observações

Esta base preserva a lógica principal fornecida no código original, mas algumas decisões ainda precisam ser alinhadas ao schema real do banco e às telas completas do PI.

Não há autenticação/autorização completa de produção, CSRF e middleware de rota nesta primeira etapa.
