# Sistema de Carteira Digital (E-Wallet) - Laravel 12
Este projeto é uma solução robusta para gerenciamento de transações financeiras, depósitos, transferências e estornos, focada em integridade de dados, segurança e escalabilidade.

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

# Regras de Negócio Implementadas
Validação de Saldo: Bloqueio em tempo real de transferências que excedam o saldo disponível.

Imutabilidade Financeira: Registros de transações nunca são deletados ou alterados diretamente.

Estornabilidade Reversa: Transações podem ser revertidas. O sistema gera uma nova transação de anulação (débito/crédito reverso) para manter a trilha de auditoria intacta.

Auditoria: Cada transação armazena quem a operou, quem pagou e quem recebeu.

# Instalação e Configuração

# 1. Clonar e instalar dependências

git clone https://github.com/AlaerteGabriel/carteira.git
composer install

# 2. Configurar ambiente
cp .env.example .env
php artisan key:generate
# (Configure seu banco de dados no .env)

# 3. Migrações e Dados Iniciais (Seed)
# O Seeder utiliza o Service para garantir que os saldos iniciais gerem extratos reais
php artisan migrate:fresh --seed

# 4. Executar Testes Automatizados
# Testes de Feature cobrindo fluxos felizes e exceções (Segurança/Saldo/Estorno)
php artisan test
