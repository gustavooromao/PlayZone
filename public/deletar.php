<?php
/**
 * Delete — não renderiza HTML: só executa a exclusão e devolve o usuário à listagem.
 * A confirmação visual acontece no modal de index.php (JavaScript), antes de chegar aqui.
 */
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Jogo.php';
require_once __DIR__ . '/../dao/JogoDAO.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'ID inválido para exclusão.'];
    header('Location: index.php');
    exit;
}

try {
    $dao = new JogoDAO(new Database());
    $jogo = $dao->buscarPorId($id);

    if ($jogo === null) {
        $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => 'Jogo não encontrado.'];
    } else {
        $dao->deletar($id);
        $_SESSION['mensagem'] = [
            'tipo'  => 'sucesso',
            'texto' => 'Jogo excluído com sucesso',
        ];
    }
} catch (Throwable $e) {
    $_SESSION['mensagem'] = ['tipo' => 'erro', 'texto' => $e->getMessage()];
}

header('Location: index.php');
exit;
