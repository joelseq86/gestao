<?php

class DashboardController
{
    public function index(array $params): void
    {
        Auth::requireAuth();

        $mes = currentMonth();

        // Monthly summary
        $totais = Database::fetchOne(
            'SELECT
                SUM(CASE WHEN tipo = \'receita\' THEN valor ELSE 0 END) AS receitas,
                SUM(CASE WHEN tipo = \'despesa\' THEN valor ELSE 0 END) AS despesas
             FROM lancamentos
             WHERE strftime(\'%Y-%m\', data) = ?',
            [$mes]
        );

        $receitas = (int)($totais['receitas'] ?? 0);
        $despesas = (int)($totais['despesas'] ?? 0);
        $saldo    = $receitas - $despesas;

        // Total all time balance
        $totalGeral = Database::fetchOne(
            'SELECT
                SUM(CASE WHEN tipo = \'receita\' THEN valor ELSE 0 END) -
                SUM(CASE WHEN tipo = \'despesa\' THEN valor ELSE 0 END) AS saldo
             FROM lancamentos'
        );

        // Recent transactions (last 10)
        $recentes = Database::fetchAll(
            'SELECT l.*, c.nome AS categoria_nome, c.cor AS categoria_cor
             FROM lancamentos l
             LEFT JOIN categorias c ON c.id = l.categoria_id
             ORDER BY data DESC, l.id DESC
             LIMIT 10'
        );

        // Top 5 expense categories this month
        $topCategorias = Database::fetchAll(
            'SELECT c.nome, c.cor, SUM(l.valor) AS total
             FROM lancamentos l
             JOIN categorias c ON c.id = l.categoria_id
             WHERE l.tipo = \'despesa\'
               AND strftime(\'%Y-%m\', l.data) = ?
             GROUP BY l.categoria_id
             ORDER BY total DESC
             LIMIT 5',
            [$mes]
        );

        $pageTitle = 'Dashboard';
        $activePage = 'dashboard';
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/dashboard/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}
