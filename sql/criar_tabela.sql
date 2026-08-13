-- =============================================================================
-- PlayZone — script de criação do banco
-- Execute no phpMyAdmin (aba SQL) ou no MySQL Workbench / linha de comando.
-- =============================================================================

CREATE DATABASE IF NOT EXISTS catalogo_jogos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE catalogo_jogos;

CREATE TABLE IF NOT EXISTS jogos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    titulo          VARCHAR(150) NOT NULL,
    plataforma      VARCHAR(50)  NOT NULL,
    genero          VARCHAR(50)  NOT NULL,
    desenvolvedora  VARCHAR(100) NULL,
    ano_lancamento  INT          NULL,
    status          ENUM('Quero Jogar', 'Jogando', 'Zerado', 'Abandonado')
                    NOT NULL DEFAULT 'Quero Jogar',
    nota            DECIMAL(3,1) NULL,
    horas_jogadas   INT          NOT NULL DEFAULT 0,
    data_cadastro   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

