# PlayZone

**Sua zona de jogos** — CRUD em PHP com Programação Orientada a Objetos.

| | |
|---|---|
| **Autor** | Gustavo Romão |
| **Projeto** | PlayZone |
| **Logo** | `public/img/logo.svg` |
| **Tipo** | Aplicação web acadêmica (PHP puro, sem frameworks) |
| **Objetivo** | Catalogar jogos de PC, consoles e outras plataformas, com as quatro operações de CRUD |

A **PlayZone** é uma zona pessoal de jogos feito pelo Gustavo Romão: cada título tem plataforma, gênero, status (Quero Jogar, Jogando, Zerado, Abandonado), nota, horas jogadas e demais dados. A interface é um painel escuro com visual gamer; o código é organizado em camadas para deixar claro o que é regra de negócio, o que é banco e o que é tela.

A marca aparece no cabeçalho (logo + nome), no favicon da aba do navegador e no rodapé de todas as páginas.

---

## O que o sistema faz

| Operação | Onde acontece | O que o usuário vê |
|---|---|---|
| **Create** | `public/criar.php` | Formulário “Novo Jogo” |
| **Read** | `public/index.php` | Grade de cards com busca em tempo real |
| **Update** | `public/editar.php` | Mesmo formulário, já preenchido |
| **Delete** | `public/deletar.php` | Modal de confirmação → exclusão |

Mensagens de sucesso ou erro (cadastro, edição, exclusão) aparecem como **toast** no canto da tela e somem sozinhas.

---

## Tecnologias

Nenhuma biblioteca pesada e nenhum framework (nada de Laravel, jQuery, React, etc.). Tudo o que aparece abaixo faz parte da entrega.

### Backend

| Tecnologia | Papel na PlayZone |
|---|---|
| **PHP 8+** (orientado a objetos) | Linguagem da aplicação: classes, encapsulamento, getters/setters |
| **PDO** | API nativa do PHP para falar com o banco |
| **Prepared statements** | Toda query usa placeholders (`:titulo`, `:id`). Variável **nunca** é concatenada no SQL — isso evita SQL Injection |
| **Sessão PHP** (`$_SESSION`) | Flash messages: grava o aviso, redireciona, mostra o toast e apaga a sessão |

### Banco de dados

| Tecnologia | Papel na PlayZone |
|---|---|
| **MySQL / MariaDB** | SGBD (o que o XAMPP sobe junto com o Apache) |
| **SQL** | Script `sql/criar_tabela.sql` cria o banco `catalogo_jogos` e a tabela `jogos` |
| **utf8mb4** | Charset da conexão e da tabela (acentos, ç, etc.) |

### Frontend

| Tecnologia | Papel na PlayZone |
|---|---|
| **HTML5** | Estrutura das páginas, formulário e logo |
| **CSS3** | Tema escuro (preto, branco, cinza e vermelho), grid responsivo, cores por status, animações de hover |
| **JavaScript (vanilla)** | Animação de entrada dos cards, filtro ao vivo, modal de exclusão, toasts |
| **SVG** | Logo da PlayZone (`public/img/logo.svg`) — o botão play dentro de um “zona” (anéis) |
| **Google Fonts** | *Oxanium* (títulos / wordmark) e *Outfit* (texto) |

### Ambiente de execução

| Tecnologia | Papel |
|---|---|
| **Apache** (XAMPP / Laragon / WAMP) | Servidor HTTP que interpreta os arquivos `.php` |
| **XAMPP** (recomendado) | Pacote Apache + MySQL + PHP no Windows |

---

## Identidade visual

O logo da PlayZone é um quadrado arredondado em degradê vermelho, com dois anéis (a “zona”) e o triângulo de **play** branco no centro. Ele é usado em três lugares:

1. **Cabeçalho** — ícone + wordmark “PlayZone”
2. **Favicon** — o mesmo SVG na aba do navegador
3. **Rodapé** — “PlayZone · Criado por Gustavo Romão”

Arquivo: `public/img/logo.svg`

---

## Arquitetura (camadas)

O projeto segue uma separação clássica de responsabilidades. Cada camada tem **uma** função; as outras não invadem o trabalho dela.

```
┌─────────────────────────────────────────┐
│  Views (public/)                        │  Interface: HTML, formulários, cards
│  index · criar · editar · deletar       │
└─────────────────┬───────────────────────┘
                  │ cria / lê objetos Jogo
                  ▼
┌─────────────────────────────────────────┐
│  Model (models/Jogo.php)                │  Dados + regras (nota 0–10, status válido)
│  atributos privados, getters/setters    │
└─────────────────┬───────────────────────┘
                  │ o DAO persiste o objeto
                  ▼
┌─────────────────────────────────────────┐
│  DAO (dao/JogoDAO.php)                  │  Único lugar com SQL
│  listar · buscar · inserir · atualizar  │
│  deletar                                │
└─────────────────┬───────────────────────┘
                  │ pede a conexão pronta
                  ▼
┌─────────────────────────────────────────┐
│  Database (config/Database.php)         │  Só PDO: host, banco, usuário, senha
└─────────────────────────────────────────┘
```

### Por que essa divisão?

- **Database** não sabe o que é um jogo. Só entrega uma conexão PDO.
- **Jogo (Model)** não executa SQL. Representa *um* jogo e protege as regras (encapsulamento).
- **JogoDAO** não desenha HTML. Conhece a tabela `jogos` e traduz linha ↔ objeto.
- **Views** não montam `INSERT`/`UPDATE`. Montam a tela, validam o que o usuário digitou e chamam o DAO.

Se a senha do MySQL mudar, só `Database.php` muda. Se a tabela ganhar uma coluna, o ajuste começa no Model e no DAO — as telas continuam falando em “um Jogo”.

---

## Estrutura de pastas

```
PlayZone/
├── README.md                 ← este arquivo
├── index.php                 ← redireciona para public/index.php
├── config/
│   └── Database.php          ← conexão PDO
├── models/
│   └── Jogo.php              ← entidade + regras de negócio
├── dao/
│   └── JogoDAO.php           ← CRUD com prepared statements
├── sql/
│   └── criar_tabela.sql      ← CREATE DATABASE / CREATE TABLE / dados de exemplo
└── public/                   ← o que o navegador acessa
    ├── index.php             ← listagem (Read)
    ├── criar.php             ← cadastro (Create)
    ├── editar.php            ← edição (Update)
    ├── deletar.php           ← exclusão (Delete)
    ├── img/
    │   └── logo.svg          ← logo da PlayZone
    ├── css/
    │   └── style.css
    └── js/
        └── script.js
```

O `index.php` da **raiz** só faz `header('Location: public/index.php')`. CSS, JS, logo e páginas ficam em `public/` para os caminhos relativos (`css/style.css`, `img/logo.svg`) funcionarem.

---

## Conceitos de POO aplicados

| Conceito | Onde aparece no código |
|---|---|
| **Classe** | `Database`, `Jogo`, `JogoDAO` |
| **Encapsulamento** | Atributos `private` em `Jogo`; acesso só por getters/setters |
| **Construtor** | `Jogo` nasce vazio e é preenchido pelos setters; `JogoDAO` recebe `Database` no construtor |
| **Tipagem** | `private string $titulo`, `public function inserir(Jogo $jogo): bool` |
| **Regra de negócio no Model** | `setNota()` recusa valores fora de 0–10; `setStatus()` só aceita os quatro status do ENUM |
| **Separação de responsabilidades** | Conexão ≠ dados ≠ SQL ≠ tela |
| **Injeção de dependência (simples)** | `new JogoDAO(new Database())` — o DAO não cria a conexão sozinho “escondido”; ela é passada de fora |

### Encapsulamento

Os atributos de `Jogo` são privados de propósito. Sem isso, qualquer página poderia fazer `$jogo->nota = 99` e gravar lixo no banco. Com o setter, a regra fica **num único lugar**:

```php
public function setNota(?float $nota): void
{
    if ($nota === null) {
        $this->nota = null;
        return;
    }
    if ($nota < 0 || $nota > 10) {
        throw new InvalidArgumentException('A nota deve estar entre 0 e 10.');
    }
    $this->nota = $nota;
}
```

A view também valida campos obrigatórios (título, plataforma, gênero) para mostrar erro amigável no formulário. São duas camadas: UX na tela, invariante no objeto.

---

## Banco de dados

Banco: `catalogo_jogos`  
Tabela: `jogos`

| Campo | Tipo | Observação |
|---|---|---|
| `id` | INT, PK, AUTO_INCREMENT | Identificador |
| `titulo` | VARCHAR(150) | Obrigatório |
| `plataforma` | VARCHAR(50) | Obrigatório (PC, PS5, Xbox Series X, Switch, …) |
| `genero` | VARCHAR(50) | Obrigatório |
| `desenvolvedora` | VARCHAR(100) | Opcional |
| `ano_lancamento` | INT | Opcional |
| `status` | ENUM | `Quero Jogar` (padrão), `Jogando`, `Zerado`, `Abandonado` |
| `nota` | DECIMAL(3,1) | Opcional, 0 a 10 |
| `horas_jogadas` | INT | Padrão 0 |
| `data_cadastro` | TIMESTAMP | Padrão `CURRENT_TIMESTAMP` |

O script `sql/criar_tabela.sql` cria o banco e a tabela vazia. Os jogos entram depois pelo cadastro da PlayZone.

---

## Frontend

Arquivo: `public/js/script.js` — JavaScript puro.

1. **Entrada dos cards** — cada card recebe a classe `.is-visible` com atraso de 60 ms em relação ao anterior (fade + slide-up).
2. **Busca em tempo real** — o campo do topo filtra por título, plataforma ou status **sem recarregar** a página (`data-search` em cada card).
3. **Modal de exclusão** — o clique em Excluir **não** chama `confirm()` do navegador. Abre um modal (“Tem certeza?”) e só então vai para `deletar.php?id=`.
4. **Toasts** — avisos da sessão deslizam na tela e desaparecem após 4 segundos.

O CSS (`public/css/style.css`) define o tema gamer em **preto, branco, cinza e vermelho** e as cores de status:

- **Quero Jogar** — vermelho
- **Jogando** — branco
- **Zerado** — vermelho escuro
- **Abandonado** — cinza

Layout em grid, com quebra para celular (`@media` abaixo de 720 px).

---

## Como executar

1. Instale o **XAMPP** (Apache + MySQL + PHP).
2. Copie esta pasta para o `htdocs`, por exemplo:
   `C:\xampp\htdocs\playzone`
3. Inicie **Apache** e **MySQL** no painel do XAMPP.
4. Abra o phpMyAdmin (`http://localhost/phpmyadmin`) e execute o conteúdo de `sql/criar_tabela.sql`.
5. Se o usuário do MySQL não for `root` com senha vazia, edite as constantes no topo de `config/Database.php`:

```php
private const HOST    = 'localhost';
private const DB_NAME = 'catalogo_jogos';
private const USER    = 'root';
private const PASS    = '';
```

6. No navegador: `http://localhost/playzone/`

---

## Fluxo de uma operação (exemplo: cadastrar)

1. O usuário preenche `criar.php` e envia o POST.
2. A view valida título, plataforma e gênero.
3. É criado um `new Jogo()`; os setters aplicam as regras (nota, status, etc.).
4. `JogoDAO::inserir($jogo)` dispara um `INSERT` com placeholders.
5. A página grava `"Jogo cadastrado com sucesso"` na sessão e redireciona para a listagem.
6. `index.php` lê a sessão, mostra o toast e **apaga** a mensagem para ela não repetir no F5.

Editar e excluir seguem o mesmo desenho: a view orquestra, o Model garante os dados, o DAO fala com o MySQL.

---

## Autor

**Gustavo Romão**  
Projeto acadêmico de Linguagem e Técnicas de Programação

**PlayZone** — Sua zona de jogos, no código.
