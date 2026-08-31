<?php
/**
 * Jogo — Model (a "entidade" do domínio). $
 *
 * Por que os atributos são privados (encapsulamento)?
 * Para que ninguém de fora altere $nota = 99 ou $status = "xyz" direto.
 * Toda mudança passa por um setter, onde as regras de negócio vivem.
 * O Model representa UM jogo; ele não fala com o banco — isso é papel do DAO.
 */
class Jogo
{
    private const STATUS_PERMITIDOS = ['Quero Jogar', 'Jogando', 'Zerado', 'Abandonado'];

    private ?int $id = null;
    private string $titulo = '';
    private string $plataforma = '';
    private string $genero = '';
    private ?string $desenvolvedora = null;
    private ?int $anoLancamento = null;
    private string $status = 'Quero Jogar';
    private ?float $nota = null;
    private int $horasJogadas = 0;
    private ?string $dataCadastro = null;

    public function __construct()
    {
        // Construtor vazio de propósito: o objeto nasce "em branco"
        // e é preenchido pelos setters (na view) ou pelo DAO (ao ler o banco).
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void
    {
        $titulo = trim($titulo);
        if ($titulo === '') {
            throw new InvalidArgumentException('O título é obrigatório.');
        }
        $this->titulo = $titulo;
    }

    public function getPlataforma(): string
    {
        return $this->plataforma;
    }

    public function setPlataforma(string $plataforma): void
    {
        $plataforma = trim($plataforma);
        if ($plataforma === '') {
            throw new InvalidArgumentException('A plataforma é obrigatória.');
        }
        $this->plataforma = $plataforma;
    }

    public function getGenero(): string
    {
        return $this->genero;
    }

    public function setGenero(string $genero): void
    {
        $genero = trim($genero);
        if ($genero === '') {
            throw new InvalidArgumentException('O gênero é obrigatório.');
        }
        $this->genero = $genero;
    }

    public function getDesenvolvedora(): ?string
    {
        return $this->desenvolvedora;
    }

    public function setDesenvolvedora(?string $desenvolvedora): void
    {
        $desenvolvedora = $desenvolvedora !== null ? trim($desenvolvedora) : '';
        $this->desenvolvedora = $desenvolvedora === '' ? null : $desenvolvedora;
    }

    public function getAnoLancamento(): ?int
    {
        return $this->anoLancamento;
    }

    public function setAnoLancamento(?int $anoLancamento): void
    {
        $this->anoLancamento = $anoLancamento;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        if (!in_array($status, self::STATUS_PERMITIDOS, true)) {
            throw new InvalidArgumentException('Status inválido.');
        }
        $this->status = $status;
    }

    public function getNota(): ?float
    {
        return $this->nota;
    }

    /**
     * Regra de negócio: nota só existe entre 0 e 10 (ou fica vazia).
     * Se alguém tentar 11 ou -1, o objeto recusa — o banco nunca recebe lixo.
     */
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

    public function getHorasJogadas(): int
    {
        return $this->horasJogadas;
    }

    public function setHorasJogadas(int $horasJogadas): void
    {
        if ($horasJogadas < 0) {
            throw new InvalidArgumentException('Horas jogadas não pode ser negativo.');
        }
        $this->horasJogadas = $horasJogadas;
    }

    public function getDataCadastro(): ?string
    {
        return $this->dataCadastro;
    }

    public function setDataCadastro(?string $dataCadastro): void
    {
        $this->dataCadastro = $dataCadastro;
    }

    /** Lista os status válidos — a view usa isso para montar o <select>. */
    public static function statusPermitidos(): array
    {
        return self::STATUS_PERMITIDOS;
    }
}
