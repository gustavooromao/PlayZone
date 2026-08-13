<?php
/**
 * Create — monta um objeto Jogo a partir do formulário e pede ao DAO para persistir.
 * A view valida o que o usuário digitou; o Model reforça as regras; o DAO grava.
 */
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Jogo.php';
require_once __DIR__ . '/../dao/JogoDAO.php';

function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

$erros = [];
$dados = [
    'titulo'         => '',
    'plataforma'     => '',
    'genero'         => '',
    'desenvolvedora' => '',
    'ano_lancamento' => '',
    'status'         => 'Quero Jogar',
    'nota'           => '',
    'horas_jogadas'  => '0',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($dados as $campo => $padrao) {
        $dados[$campo] = trim((string) ($_POST[$campo] ?? $padrao));
    }

    if ($dados['titulo'] === '') {
        $erros[] = 'O título é obrigatório.';
    }
    if ($dados['plataforma'] === '') {
        $erros[] = 'A plataforma é obrigatória.';
    }
    if ($dados['genero'] === '') {
        $erros[] = 'O gênero é obrigatório.';
    }

    if ($erros === []) {
        try {
            $jogo = new Jogo();
            $jogo->setTitulo($dados['titulo']);
            $jogo->setPlataforma($dados['plataforma']);
            $jogo->setGenero($dados['genero']);
            $jogo->setDesenvolvedora($dados['desenvolvedora'] === '' ? null : $dados['desenvolvedora']);
            $jogo->setAnoLancamento($dados['ano_lancamento'] === '' ? null : (int) $dados['ano_lancamento']);
            $jogo->setStatus($dados['status']);
            $jogo->setNota($dados['nota'] === '' ? null : (float) str_replace(',', '.', $dados['nota']));
            $jogo->setHorasJogadas($dados['horas_jogadas'] === '' ? 0 : (int) $dados['horas_jogadas']);

            $dao = new JogoDAO(new Database());
            $dao->inserir($jogo);

            $_SESSION['mensagem'] = [
                'tipo'  => 'sucesso',
                'texto' => 'Jogo cadastrado com sucesso',
            ];
            header('Location: index.php');
            exit;
        } catch (Throwable $e) {
            $erros[] = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo jogo — PlayZone</title>
    <link rel="icon" href="img/logo.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oxanium:wght@500;700;800&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="bg-grid" aria-hidden="true"></div>

    <header class="topbar">
        <a class="logo" href="index.php">
            <img class="logo__mark" src="img/logo.svg" alt="Logo PlayZone">
            <span class="logo__text">
                <span class="logo__kicker">Novo registro</span>
                <span class="logo__name">PlayZone</span>
            </span>
        </a>
        <a class="btn btn--ghost" href="index.php">Voltar à PlayZone</a>
    </header>

    <main class="container container--narrow">
        <h1 class="page-title">Cadastrar jogo</h1>
        <?php if ($erros): ?>
            <ul class="form-errors">
                <?php foreach ($erros as $erro): ?>
                    <li><?= e($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form class="form" method="post" action="criar.php" novalidate>
            <label>
                Título *
                <input type="text" name="titulo" maxlength="150" required value="<?= e($dados['titulo']) ?>">
            </label>

            <label>
                Plataforma *
                <input type="text" name="plataforma" maxlength="50" list="plataformas" required value="<?= e($dados['plataforma']) ?>">
            </label>

            <label>
                Gênero *
                <input type="text" name="genero" maxlength="50" list="generos" required value="<?= e($dados['genero']) ?>">
            </label>

            <label>
                Desenvolvedora
                <input type="text" name="desenvolvedora" maxlength="100" value="<?= e($dados['desenvolvedora']) ?>">
            </label>

            <label>
                Ano de lançamento
                <input type="number" name="ano_lancamento" min="1970" max="2030" value="<?= e($dados['ano_lancamento']) ?>">
            </label>

            <label>
                Status
                <select name="status">
                    <?php foreach (Jogo::statusPermitidos() as $status): ?>
                        <option value="<?= e($status) ?>" <?= $dados['status'] === $status ? 'selected' : '' ?>>
                            <?= e($status) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Nota (0 a 10)
                <input type="number" name="nota" min="0" max="10" step="0.1" value="<?= e($dados['nota']) ?>">
            </label>

            <label>
                Horas jogadas
                <input type="number" name="horas_jogadas" min="0" step="1" value="<?= e($dados['horas_jogadas']) ?>">
            </label>

            <div class="form__actions">
                <a class="btn btn--ghost" href="index.php">Cancelar</a>
                <button type="submit" class="btn btn--primary btn--action">Salvar</button>
            </div>
        </form>
    </main>

    <datalist id="plataformas">
        <option value="PC">
        <option value="PlayStation 5">
        <option value="PlayStation 4">
        <option value="Xbox Series X">
        <option value="Xbox Series S">
        <option value="Xbox One">
        <option value="Nintendo Switch">
        <option value="Steam Deck">
        <option value="Mobile">
    </datalist>
    <datalist id="generos">
        <option value="Ação">
        <option value="Aventura">
        <option value="RPG">
        <option value="FPS">
        <option value="Estratégia">
        <option value="Esporte">
        <option value="Corrida">
        <option value="Terror">
        <option value="Puzzle">
        <option value="Simulação">
        <option value="Indie">
        <option value="Luta">
        <option value="Plataforma">
        <option value="Mundo Aberto">
    </datalist>

    <footer class="site-footer">
        <img src="img/logo.svg" alt="">
        PlayZone · Criado por Gustavo Romão
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
