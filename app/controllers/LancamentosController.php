<?php

class LancamentosController
{
    public function index(array $params): void
    {
        Auth::requireAuth();

        // Filters
        $mes    = $_GET['mes']    ?? currentMonth();
        $tipo   = $_GET['tipo']   ?? '';
        $catId  = $_GET['cat']    ?? '';

        $where  = ['strftime(\'%Y-%m\', data) = ?'];
        $binds  = [$mes];

        if (in_array($tipo, ['receita', 'despesa'])) {
            $where[] = 'tipo = ?';
            $binds[] = $tipo;
        }
        if ($catId !== '') {
            $where[] = 'categoria_id = ?';
            $binds[] = $catId;
        }

        $sql = 'SELECT l.*, c.nome AS categoria_nome, c.cor AS categoria_cor
                FROM lancamentos l
                LEFT JOIN categorias c ON c.id = l.categoria_id
                WHERE ' . implode(' AND ', $where) . '
                ORDER BY data DESC, l.id DESC';

        $lancamentos = Database::fetchAll($sql, $binds);
        $categorias  = Database::fetchAll('SELECT * FROM categorias ORDER BY tipo, nome');

        $totais = Database::fetchOne(
            'SELECT
                SUM(CASE WHEN tipo = \'receita\' THEN valor ELSE 0 END) AS receitas,
                SUM(CASE WHEN tipo = \'despesa\' THEN valor ELSE 0 END) AS despesas,
                SUM(CASE WHEN tipo = \'receita\' THEN valor_iva ELSE 0 END) AS iva_receitas,
                SUM(CASE WHEN tipo = \'despesa\' THEN valor_iva ELSE 0 END) AS iva_despesas
             FROM lancamentos WHERE strftime(\'%Y-%m\', data) = ?',
            [$mes]
        );

        $pageTitle = 'Lançamentos';
        $activePage = 'lancamentos';
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/lancamentos/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public function novo(array $params): void
    {
        Auth::requireAuth();
        $categorias = Database::fetchAll('SELECT * FROM categorias ORDER BY tipo, nome');
        $lancamento = [
            'id' => null, 'tipo' => 'despesa', 'descricao' => '',
            'valor' => '', 'categoria_id' => '', 'data' => date('Y-m-d'), 'observacoes' => '',
            'taxa_iva' => 0, 'valor_iva' => 0,
        ];
        $pageTitle = 'Novo Lançamento';
        $activePage = 'lancamentos';
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/lancamentos/form.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public function salvar(array $params): void
    {
        Auth::requireAuth();
        csrfVerify();

        $data = $this->validate();
        if ($data === null) {
            redirect('/lancamentos/novo');
        }

        Database::query(
            'INSERT INTO lancamentos (tipo, descricao, valor, categoria_id, data, observacoes, taxa_iva, valor_iva)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            $data
        );

        flashSet('success', 'Lançamento registado com sucesso.');
        redirect('/lancamentos');
    }

    public function editar(array $params): void
    {
        Auth::requireAuth();
        $lancamento = Database::fetchOne('SELECT * FROM lancamentos WHERE id = ?', [$params['id']]);
        if (!$lancamento) {
            flashSet('danger', 'Lançamento não encontrado.');
            redirect('/lancamentos');
        }
        // Convert cents to decimal for display in form
        $lancamento['valor']     = number_format($lancamento['valor'] / 100, 2, ',', '.');
        $lancamento['valor_iva'] = number_format(($lancamento['valor_iva'] ?? 0) / 100, 2, ',', '.');
        $lancamento['taxa_iva']  = (int)($lancamento['taxa_iva'] ?? 0);
        $categorias = Database::fetchAll('SELECT * FROM categorias ORDER BY tipo, nome');
        $pageTitle = 'Editar Lançamento';
        $activePage = 'lancamentos';
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/lancamentos/form.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public function atualizar(array $params): void
    {
        Auth::requireAuth();
        csrfVerify();

        $data = $this->validate();
        if ($data === null) {
            redirect('/lancamentos/editar/' . $params['id']);
        }

        $data[] = $params['id'];
        Database::query(
            'UPDATE lancamentos SET tipo=?, descricao=?, valor=?, categoria_id=?, data=?, observacoes=?, taxa_iva=?, valor_iva=?
             WHERE id=?',
            $data
        );

        flashSet('success', 'Lançamento atualizado com sucesso.');
        redirect('/lancamentos');
    }

    public function excluir(array $params): void
    {
        Auth::requireAuth();
        csrfVerify();
        Database::query('DELETE FROM lancamentos WHERE id = ?', [$params['id']]);
        flashSet('success', 'Lançamento excluído com sucesso.');
        redirect('/lancamentos');
    }

    private function validate(): ?array
    {
        $tipo        = $_POST['tipo']         ?? '';
        $descricao   = trim($_POST['descricao']  ?? '');
        $valorStr    = trim($_POST['valor']      ?? '');
        $catId       = $_POST['categoria_id']    ?? null;
        $data        = trim($_POST['data']       ?? '');
        $obs         = trim($_POST['observacoes'] ?? '');
        $taxaIva     = (int)($_POST['taxa_iva']  ?? 0);
        $valorIvaStr = trim($_POST['valor_iva']  ?? '0');

        if (!in_array($tipo, ['receita', 'despesa']) || !$descricao || !$valorStr || !$data) {
            flashSet('danger', 'Preencha todos os campos obrigatórios.');
            return null;
        }

        $valor = parseMoney($valorStr);
        if ($valor <= 0) {
            flashSet('danger', 'O valor deve ser maior que zero.');
            return null;
        }

        if (!in_array($taxaIva, [0, 6, 13, 23])) {
            $taxaIva = 0;
        }
        $valorIva = parseMoney($valorIvaStr);

        $catId = $catId !== '' ? (int)$catId : null;
        $data  = parseDate($data);

        return [$tipo, $descricao, $valor, $catId, $data, $obs ?: null, $taxaIva, $valorIva];
    }
}
