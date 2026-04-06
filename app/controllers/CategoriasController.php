<?php

class CategoriasController
{
    public function index(array $params): void
    {
        Auth::requireAuth();
        $categorias = Database::fetchAll(
            'SELECT * FROM categorias ORDER BY tipo, nome'
        );
        $pageTitle = 'Categorias';
        $activePage = 'categorias';
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/categorias/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public function nova(array $params): void
    {
        Auth::requireAuth();
        $pageTitle = 'Nova Categoria';
        $activePage = 'categorias';
        $categoria = ['id' => null, 'nome' => '', 'tipo' => 'despesa', 'cor' => '#e74c3c'];
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/categorias/form.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public function salvar(array $params): void
    {
        Auth::requireAuth();
        csrfVerify();

        $nome = trim($_POST['nome'] ?? '');
        $tipo = $_POST['tipo'] ?? 'despesa';
        $cor  = $_POST['cor']  ?? '#6c757d';

        if (!$nome || !in_array($tipo, ['receita', 'despesa'])) {
            flashSet('danger', 'Preencha todos os campos corretamente.');
            redirect('/categorias/nova');
        }

        Database::query(
            'INSERT INTO categorias (nome, tipo, cor) VALUES (?, ?, ?)',
            [$nome, $tipo, $cor]
        );

        flashSet('success', 'Categoria criada com sucesso.');
        redirect('/categorias');
    }

    public function editar(array $params): void
    {
        Auth::requireAuth();
        $categoria = Database::fetchOne('SELECT * FROM categorias WHERE id = ?', [$params['id']]);
        if (!$categoria) {
            flashSet('danger', 'Categoria não encontrada.');
            redirect('/categorias');
        }
        $pageTitle = 'Editar Categoria';
        $activePage = 'categorias';
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/categorias/form.php';
        require __DIR__ . '/../views/layout/footer.php';
    }

    public function atualizar(array $params): void
    {
        Auth::requireAuth();
        csrfVerify();

        $nome = trim($_POST['nome'] ?? '');
        $tipo = $_POST['tipo'] ?? 'despesa';
        $cor  = $_POST['cor']  ?? '#6c757d';

        if (!$nome || !in_array($tipo, ['receita', 'despesa'])) {
            flashSet('danger', 'Preencha todos os campos corretamente.');
            redirect('/categorias/editar/' . $params['id']);
        }

        Database::query(
            'UPDATE categorias SET nome = ?, tipo = ?, cor = ? WHERE id = ?',
            [$nome, $tipo, $cor, $params['id']]
        );

        flashSet('success', 'Categoria atualizada com sucesso.');
        redirect('/categorias');
    }

    public function excluir(array $params): void
    {
        Auth::requireAuth();
        csrfVerify();

        // Check if in use
        $count = Database::fetchOne(
            'SELECT COUNT(*) as n FROM lancamentos WHERE categoria_id = ?',
            [$params['id']]
        );
        if ($count && $count['n'] > 0) {
            flashSet('danger', 'Não é possível excluir: existem lançamentos associados a esta categoria.');
            redirect('/categorias');
        }

        Database::query('DELETE FROM categorias WHERE id = ?', [$params['id']]);
        flashSet('success', 'Categoria excluída com sucesso.');
        redirect('/categorias');
    }
}
