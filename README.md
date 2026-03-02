# 💰 Wallet System - Laravel 12

Sistema de Carteira Digital com arquitetura baseada em **Ledger** (Livro-razão), focado em integridade financeira, segurança de transações e padrões de projeto sólidos.

---

# Regras de Negócio e Robustez
Transações ACID: Uso de DB::beginTransaction em operações financeiras e cadastros.

Processamento Assíncrono: Envio de e-mails de boas-vindas via Jobs com afterCommit(), garantindo que o e-mail só seja disparado se a transação no banco for confirmada.

Estornos (Imutabilidade): Transações não são deletadas. O estorno gera uma nova entrada de contrapartida para manter a trilha de auditoria intacta.

Segurança: Validação rigorosa de saldo em tempo real antes de qualquer débito.

# Padrões de Projeto e Arquitetura
O sistema foi desenhado seguindo princípios de Clean Code e SOLID, utilizando os seguintes padrões:

1. Repository Pattern com Interfaces
O gerenciamento de usuários é realizado através da UsersInterface e implementado no UsersRepository.

Abstração: O Controller não conhece a lógica de persistência, apenas a interface.

Atomicidade: Uso de DB::beginTransaction e DB::commit para garantir que cadastros e atualizações sejam atômicos.

Sanitização: Limpeza automática de dados sensíveis (como CPF) antes da persistência.

2. Service Pattern
Toda a inteligência financeira (regras de depósito, transferência e validação de saldo) reside no TransacoesService.

Isolamento: As regras de negócio são independentes da camada de transporte (Web/API/Console).

Reusabilidade: O mesmo serviço é utilizado tanto no sistema em execução quanto nos Seeders e Testes.

3. Observer Pattern (Sistema de Ledger)
O saldo do usuário (us_balanco) é um campo derivado, sincronizado automaticamente pelo TransacoesObserver.

Integridade Total: O saldo nunca é editado manualmente. Qualquer inserção na tabela de transações dispara o recálculo automático com base no histórico de entradas e saídas.

Confiabilidade: Garante que o extrato e o saldo exibido estejam sempre em harmonia.

4. Policy Pattern (Segurança)
Implementação da TransacoesPolicy para controle granular de acesso.

Prevenção de IDOR: Garante que um usuário só visualize ou solicite estorno de transações onde ele figura como Criador, Pagador ou Beneficiário.

5. Job Dispatching (Processamento Assíncrono)
Uso de Queues para tarefas secundárias (envio de e-mails de boas-vindas) via SendCadastroUserJob.

Performance: O usuário não aguarda o envio do e-mail para receber a confirmação de cadastro.

Segurança de Transação: O Job utiliza o método afterCommit() para garantir que o e-mail só seja enfileirado se o registro no banco de dados for de fato confirmado.

🛠️ Regras de Negócio Implementadas
Validação de Saldo: Bloqueio em tempo real de transferências que excedam o saldo disponível.

Imutabilidade Financeira: Registros de transações nunca são deletados ou alterados diretamente.

Estornabilidade Reversa: Transações podem ser revertidas. O sistema gera uma nova transação de anulação (débito/crédito reverso) para manter a trilha de auditoria intacta.

Auditoria: Cada transação armazena quem a operou, quem pagou e quem recebeu.

# Estratégia de Testes
O projeto conta com uma suíte de Feature Tests que validam:

Cadastro de usuários via Repository.

Depósitos e transferências com validação de saldo.

Estorno de transações com recálculo automático de saldo via Observer.

Bloqueio de acesso indevido via Policies.

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
