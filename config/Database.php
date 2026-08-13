<?php
/**
 * Database — única classe responsável por abrir a conexão PDO.
 *
 * Decisão de design: a view e o Model NÃO conhecem host, usuário ou senha.
 * Quem precisa falar com o MySQL pede um PDO pronto via getConnection().
 * Assim, se o banco mudar (porta, senha, até o SGBD), só este arquivo muda.
 */

class Database
{
    // Credenciais no topo do arquivo: fácil de achar e de alterar no XAMPP.
    private const HOST    = 'localhost';
    private const DB_NAME = 'catalogo_jogos';
    private const USER    = 'root';
    private const PASS    = '';

    /** @var PDO|null Uma única conexão por instância (lazy: só conecta quando pedirem). */
    private ?PDO $connection = null;

    /**
     * Devolve a instância PDO, criando-a na primeira chamada.
     */
    public function getConnection(): PDO
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        $dsn = 'mysql:host=' . self::HOST . ';dbname=' . self::DB_NAME . ';charset=utf8mb4';

        try {
            $this->connection = new PDO($dsn, self::USER, self::PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            // Prepared statements reais no servidor, não emulação no PHP.
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            throw new RuntimeException(
                'Falha ao conectar no banco "' . self::DB_NAME . '". '
                . 'Confira se o MySQL está ligado, se o banco foi criado '
                . '(sql/criar_tabela.sql) e se usuário/senha em config/Database.php estão corretos. '
                . 'Detalhe: ' . $e->getMessage()
            );
        }

        return $this->connection;
    }
}
