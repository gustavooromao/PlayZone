<?php
/**
 * Listagem (Read) — única responsabilidade desta página: mostrar a PlayZone.
 * Create/Update/Delete acontecem em criar.php, editar.php e deletar.php.
 */
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Jogo.php';
require_once __DIR__ . '/../dao/JogoDAO.php';

function e(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function statusClasse(string $status): string
{
    $mapa = [
        'Quero Jogar' => 'quero-jogar',
        'Jogando'     => 'jogando',
        'Zerado'      => 'zerado',
        'Abandonado'  => 'abandonado',
    ];
    return $mapa[$status] ?? 'quero-jogar';
}

$erroConexao = null;
$jogos = [];

try {
    $dao = new JogoDAO(new Database());
    $jogos = $dao->listarTodos();
} catch (Throwable $e) {
    $erroConexao = $e->getMessage();
}

// Toast: lê a mensagem da sessão e apaga na hora (flash message).
$flash = $_SESSION['mensagem'] ?? null;
unset($_SESSION['mensagem']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlayZone</title>
    <link rel="icon" href="img/logo.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Oxanium:wght@500;700;800&family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script>document.documentElement.classList.add("js");</script>
</head>
<body>
    <div class="bg-grid" aria-hidden="true"></div>

    <header class="topbar">
        <a class="logo" href="index.php">
            <img class="logo__mark" src="img/logo.svg" alt="Logo PlayZone">
            <span class="logo__text">
                <span class="logo__kicker">Sua zona de jogos</span>
                <h1 class="logo__name">PlayZone</h1>
            </span>
        </a>

        <div class="topbar__actions">
            <label class="search">
                <span class="visually-hidden">Buscar jogos</span>
                <input
                    type="search"
                    id="busca"
                    placeholder="Filtrar por título, plataforma ou status..."
                    autocomplete="off"
                >
            </label>
            <a class="btn btn--primary" href="criar.php">Novo Jogo</a>
        </div>
    </header>

    <main class="container">
        <?php if ($erroConexao): ?>
            <p class="empty-state"><?= e($erroConexao) ?></p>
        <?php elseif (count($jogos) === 0): ?>
            <p class="empty-state">Nenhum jogo cadastrado ainda. Clique em <strong>Novo Jogo</strong> para começar.</p>
        <?php else: ?>
            <p class="result-count" id="contador"><?= count($jogos) ?> jogo(s) na PlayZone</p>
            <section class="grid" id="grade-jogos">
                <?php foreach ($jogos as $jogo): ?>
                    <?php
                    $classeStatus = statusClasse($jogo->getStatus());
                    $textoBusca = $jogo->getTitulo() . ' ' . $jogo->getPlataforma() . ' ' . $jogo->getStatus();
                    $busca = function_exists('mb_strtolower')
                        ? mb_strtolower($textoBusca, 'UTF-8')
                        : strtolower($textoBusca);
                    $notaTexto = $jogo->getNota() === null ? '—' : number_format($jogo->getNota(), 1, ',', '');
                    ?>
                    <article
                        class="card card--<?= e($classeStatus) ?>"
                        data-search="<?= e($busca) ?>"
                    >
                        <div class="card__glow" aria-hidden="true"></div>
                        <header class="card__head">
                            <h2 class="card__title"><?= e($jogo->getTitulo()) ?></h2>
                            <span class="badge badge--<?= e($classeStatus) ?>"><?= e($jogo->getStatus()) ?></span>
                        </header>
                        <p class="card__platform"><?= e($jogo->getPlataforma()) ?></p>
                        <dl class="card__stats">
                            <div>
                                <dt>Nota</dt>
                                <dd><?= e($notaTexto) ?></dd>
                            </div>
                            <div>
                                <dt>Horas</dt>
                                <dd><?= (int) $jogo->getHorasJogadas() ?>h</dd>
                            </div>
                        </dl>
                        <footer class="card__actions">
                            <a class="btn btn--ghost btn--action" href="editar.php?id=<?= (int) $jogo->getId() ?>">Editar</a>
                            <a
                                class="btn btn--danger btn--action"
                                href="deletar.php?id=<?= (int) $jogo->getId() ?>"
                                data-confirm-delete
                                data-titulo="<?= e($jogo->getTitulo()) ?>"
                            >Excluir</a>
                        </footer>
                    </article>
                <?php endforeach; ?>
            </section>
            <p class="empty-state is-hidden" id="sem-resultado">Nenhum jogo corresponde à busca.</p>
        <?php endif; ?>
    </main>

    <div class="modal" id="modal-excluir" role="dialog" aria-modal="true" aria-labelledby="modal-titulo">
        <div class="modal__backdrop" data-modal-close></div>
        <div class="modal__box">
            <h2 id="modal-titulo">Tem certeza?</h2>
            <p id="modal-texto">Essa exclusão não pode ser desfeita.</p>
            <div class="modal__actions">
                <button type="button" class="btn btn--ghost" data-modal-close>Cancelar</button>
                <a class="btn btn--danger" id="modal-confirmar" href="#">Confirmar</a>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toast-container">
        <?php if ($flash): ?>
            <div class="toast toast--<?= e($flash['tipo'] ?? 'sucesso') ?> is-active" data-autohide>
                <?= e($flash['texto'] ?? '') ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="site-footer">
        <img src="img/logo.svg" alt="">
        PlayZone · Criado por Gustavo Romão
    </footer>

    <script src="js/script.js"></script>
</body>
</html>
