# 💰 Wallet System - Laravel 12

Sistema de Carteira Digital com arquitetura baseada em **Ledger** (Livro-razão), focado em integridade financeira, segurança de transações e padrões de projeto sólidos.

---

## 🏗️ Arquitetura e Padrões de Projeto

O projeto foi construído utilizando padrões que garantem a escalabilidade e a testabilidade do código:

### 1. Repository Pattern (com Interfaces)
O gerenciamento de usuários é desacoplado através da `UsersInterface`.
* **Localização:** `app/Repositories/UsersRepository.php`
* **Destaque:** Uso de `DB::beginTransaction` e `DB::commit` para garantir atomicidade em cadastros e atualizações.
* **Segurança:** Sanitização de dados (CPF) e validação de hash de senha antes da persistência.

### 2. Service Pattern
Toda a lógica de negócio financeira está centralizada no `TransacoesService`.
* **Localização:** `app/Services/TransacoesService.php`
* **Destaque:** Centraliza regras de depósito, transferência e validação de saldo em tempo real, evitando "Fat Controllers".

### 3. Observer Pattern (Sincronização de Saldo)
O campo `us_balanco` é um dado derivado e sincronizado automaticamente pelo `TransacoesObserver`.
* **Localização:** `app/Observers/TransacoesObserver.php`
* **Destaque:** O saldo nunca é editado manualmente; ele é o resultado da soma de entradas e saídas processadas a cada evento de model.

### 4. Policy Pattern (Segurança de Acesso)
Camada de autorização para proteção contra ataques IDOR.
* **Localização:** `app/Policies/TransacoesPolicy.php`
* **Destaque:** Garante que apenas os usuários envolvidos (Pagador, Beneficiário ou Criador) tenham acesso aos detalhes ou solicitem estorno.

---

## 🛠️ Regras de Negócio Implementadas

- **Estornabilidade Reversa:** Transações não são deletadas. O estorno gera uma nova transação de anulação (débito/crédito reverso) para manter a trilha de auditoria.
- **Processamento Assíncrono:** Envio de e-mails de boas-vindas via `SendCadastroUserJob` com o método `afterCommit()`, garantindo o disparo apenas após o sucesso da transação no banco.
- **Imutabilidade:** O histórico financeiro é preservado para fins de auditoria.

---

## 🚀 Guia de Instalação e Execução

Siga os comandos abaixo para configurar o ambiente:

```bash
# 1. Instalar dependências
composer install

# 2. Configurar ambiente
cp .env.example .env
php artisan key:generate

# 3. Preparar Banco de Dados e Seed
# O Seeder utiliza os Services e Repositories para gerar dados íntegros
php artisan migrate:fresh --seed

# 4. Executar Testes Automatizados (Feature Tests)
# Valida segurança, saldo, transferências e estornos
php artisan test
