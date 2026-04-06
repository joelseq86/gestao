<?php

class RelatoriosController
{
    public function index(array $params): void
    {
        Auth::requireAuth();

        $mes = $_GET['mes'] ?? currentMonth();

        // Last 12 months bar chart data
        $meses12 = [];
        for ($i = 11; $i >= 0; $i--) {
            $meses12[] = date('Y-m', strtotime("-$i months"));
        }

        $barLabels   = [];
        $barReceitas = [];
        $barDespesas = [];

        foreach ($meses12 as $m) {
            [$y, $mo] = explode('-', $m);
            $months = ['01'=>'Jan','02'=>'Fev','03'=>'Mar','04'=>'Abr','05'=>'Mai','06'=>'Jun',
                       '07'=>'Jul','08'=>'Ago','09'=>'Set','10'=>'Out','11'=>'Nov','12'=>'Dez'];
            $barLabels[] = ($months[$mo] ?? $mo) . ' ' . substr($y, 2);

            $row = Database::fetchOne(
                'SELECT
                    SUM(CASE WHEN tipo = \'receita\' THEN valor ELSE 0 END) AS receitas,
                    SUM(CASE WHEN tipo = \'despesa\' THEN valor ELSE 0 END) AS despesas
                 FROM lancamentos WHERE strftime(\'%Y-%m\', data) = ?',
                [$m]
            );
            $barReceitas[] = round(((int)($row['receitas'] ?? 0)) / 100, 2);
            $barDespesas[] = round(((int)($row['despesas'] ?? 0)) / 100, 2);
        }

        // Doughnut: expense categories for selected month
        $catDespesas = Database::fetchAll(
            'SELECT c.nome, c.cor, SUM(l.valor) AS total
             FROM lancamentos l
             JOIN categorias c ON c.id = l.categoria_id
             WHERE l.tipo = \'despesa\'
               AND strftime(\'%Y-%m\', l.data) = ?
             GROUP BY l.categoria_id
             ORDER BY total DESC',
            [$mes]
        );

        // Uncategorized expenses
        $semCat = Database::fetchOne(
            'SELECT SUM(valor) AS total FROM lancamentos
             WHERE tipo = \'despesa\'
               AND categoria_id IS NULL
               AND strftime(\'%Y-%m\', data) = ?',
            [$mes]
        );
        if ($semCat && $semCat['total'] > 0) {
            $catDespesas[] = ['nome' => 'Sem categoria', 'cor' => '#95a5a6', 'total' => $semCat['total']];
        }

        $doughnutLabels = array_column($catDespesas, 'nome');
        $doughnutValues = array_map(fn($c) => round($c['total'] / 100, 2), $catDespesas);
        $doughnutColors = array_column($catDespesas, 'cor');

        // Monthly summary for selected month
        $totais = Database::fetchOne(
            'SELECT
                SUM(CASE WHEN tipo = \'receita\' THEN valor ELSE 0 END) AS receitas,
                SUM(CASE WHEN tipo = \'despesa\' THEN valor ELSE 0 END) AS despesas
             FROM lancamentos WHERE strftime(\'%Y-%m\', data) = ?',
            [$mes]
        );

        $pageTitle = 'Relatórios';
        $activePage = 'relatorios';
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/relatorios/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}
