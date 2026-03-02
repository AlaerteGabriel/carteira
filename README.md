# 💰 Carteira Financeira - Laravel 12

Sistema de Carteira Digital com arquitetura baseada em **Ledger** (Livro-razão), focado em integridade financeira, segurança de transações e padrões de projeto sólidos.

---

## 🛠️ Regras de Negócio e Robustez

* **Transações ACID:** Uso de `DB::beginTransaction` em operações financeiras e cadastros.
* **Processamento Assíncrono:** Envio de e-mails de boas-vindas via *Jobs* com `afterCommit()`, garantindo o disparo apenas após a confirmação no banco.
* **Estornos (Imutabilidade):** Transações não são deletadas. O estorno gera uma nova entrada de contrapartida para auditoria.
* **Segurança:** Validação rigorosa de saldo em tempo real antes de qualquer débito.

---

## 🏗️ Padrões de Projeto e Arquitetura

O sistema utiliza princípios de **Clean Code** e **SOLID**:

### 1. Repository Pattern (com Interfaces)
O gerenciamento de usuários é desacoplado através da `UsersInterface`.
* **Localização:** `app/Repositories/UsersRepository.php`
* **Destaque:** Centraliza a persistência, sanitização de CPF e tratamento de exceções.

### 2. Service Pattern
Toda a lógica financeira reside no `TransacoesService`.
* **Localização:** `app/Services/TransacoesService.php`
* **Destaque:** Isola as regras de negócio de depósitos e transferências dos Controllers.

### 3. Observer Pattern (Sistema de Ledger)
O saldo (`us_balanco`) é sincronizado automaticamente pelo `TransacoesObserver`.
* **Localização:** `app/Observers/TransacoesObserver.php`
* **Destaque:** O saldo é um dado derivado; qualquer inserção na tabela de transações dispara o recálculo automático.

### 4. Policy Pattern (Segurança)
Implementação da `TransacoesPolicy` para controle de acesso.
* **Destaque:** Proteção contra **IDOR**, permitindo ações apenas aos envolvidos na transação.

---

## 🧪 Estratégia de Testes
O projeto conta com uma suíte de **Feature Tests** que validam:
* Cadastro de usuários via Repository.
* Depósitos e transferências com validação de saldo.
* Estorno de transações com recálculo via Observer.
* Bloqueio de acesso indevido via Policies.

---

## 🚀 Guia de Instalação e Execução

### Pré-requisitos:
* **PHP 8.2 ou 8.3**
* **Composer**
* **MySQL / MariaDB**

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
php artisan test
```

## 👥 Usuários de Teste
O banco de dados é populado com usuários aleatórios via Seed. Você pode utilizar o usuário principal para acesso:

* **Login:** `contato@alaertegabriel.com.br`
* **Senha:** `admin`

Obs: A senha padrão para todos os usuários gerados pelo Seed é admin.

Desenvolvido por Alaerte Gabriel.
