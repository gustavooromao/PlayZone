<?php
/**
 * JogoDAO — Data Access Object.
 *
 * Decisão de design: o Model (Jogo) não executa SQL. Quem conhece a tabela,
 * os nomes das colunas e o PDO é só esta classe. Se amanhã a tabela mudar,
 * o restante do sistema continua falando em "inserir um Jogo".
 *
 * Todas as queries usam prepared statements (:nome) — nunca concatenação
 * de variável na string SQL, para evitar SQL Injection.
 */
class JogoDAO
{
    private PDO $pdo;

    /**
     * Recebe Database (não o PDO cru) para depender da nossa abstração de conexão.
     */
    public function __construct(Database $database)
    {
        $this->pdo = $database->getConnection();
    }

    /** Devolve todos os jogos cadastrados, do mais recente para o mais antigo. */
    public function listarTodos(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM jogos ORDER BY data_cadastro DESC'
        );
        $stmt->execute();

        $jogos = [];
        foreach ($stmt->fetchAll() as $linha) {
            $jogos[] = $this->hidratar($linha);
        }

        return $jogos;
    }

    /** Busca um jogo pela chave primária. Retorna null se não existir. */
    public function buscarPorId(int $id): ?Jogo
    {
        $stmt = $this->pdo->prepare('SELECT * FROM jogos WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $linha = $stmt->fetch();

        return $linha ? $this->hidratar($linha) : null;
    }

    /** Insere um novo jogo e grava o id gerado pelo AUTO_INCREMENT no objeto. */
    public function inserir(Jogo $jogo): bool
    {
        $sql = 'INSERT INTO jogos
                    (titulo, plataforma, genero, desenvolvedora, ano_lancamento, status, nota, horas_jogadas)
                VALUES
                    (:titulo, :plataforma, :genero, :desenvolvedora, :ano_lancamento, :status, :nota, :horas_jogadas)';

        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute($this->parametros($jogo));

        if ($ok) {
            $jogo->setId((int) $this->pdo->lastInsertId());
        }

        return $ok;
    }

    /** Atualiza os dados de um jogo já existente, identificado pelo id. $*/
    public function atualizar(Jogo $jogo): bool
    {
        $sql = 'UPDATE jogos SET
                    titulo = :titulo,
                    plataforma = :plataforma,
                    genero = :genero,
                    desenvolvedora = :desenvolvedora,
                    ano_lancamento = :ano_lancamento,
                    status = :status,
                    nota = :nota,
                    horas_jogadas = :horas_jogadas
                WHERE id = :id';

        $stmt = $this->pdo->prepare($sql);
        $params = $this->parametros($jogo);
        $params[':id'] = $jogo->getId();

        return $stmt->execute($params);
    }

    /** Remove o jogo cujo id foi informado. */
    public function deletar(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM jogos WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Converte uma linha associativa do PDO em um objeto Jogo.
     * Isolado aqui para listarTodos e buscarPorId não repetirem o mapeamento.
     */
    private function hidratar(array $linha): Jogo
    {
        $jogo = new Jogo();
        $jogo->setId((int) $linha['id']);
        $jogo->setTitulo($linha['titulo']);
        $jogo->setPlataforma($linha['plataforma']);
        $jogo->setGenero($linha['genero']);
        $jogo->setDesenvolvedora($linha['desenvolvedora']);
        $jogo->setAnoLancamento($linha['ano_lancamento'] !== null ? (int) $linha['ano_lancamento'] : null);
        $jogo->setStatus($linha['status']);
        $jogo->setNota($linha['nota'] !== null ? (float) $linha['nota'] : null);
        $jogo->setHorasJogadas((int) $linha['horas_jogadas']);
        $jogo->setDataCadastro($linha['data_cadastro']);

        return $jogo;
    }

    /** Monta o array de placeholders usado no INSERT e no UPDATE. */
    private function parametros(Jogo $jogo): array
    {
        return [
            ':titulo'          => $jogo->getTitulo(),
            ':plataforma'      => $jogo->getPlataforma(),
            ':genero'          => $jogo->getGenero(),
            ':desenvolvedora'  => $jogo->getDesenvolvedora(),
            ':ano_lancamento'  => $jogo->getAnoLancamento(),
            ':status'          => $jogo->getStatus(),
            ':nota'            => $jogo->getNota(),
            ':horas_jogadas'   => $jogo->getHorasJogadas(),
        ];
    }
}
