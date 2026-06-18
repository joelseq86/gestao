<?php

class IvaController
{
    public function index(array $params): void
    {
        Auth::requireAuth();

        $ano = (int)($_GET['ano'] ?? date('Y'));
        if ($ano < 2000 || $ano > 2099) {
            $ano = (int)date('Y');
        }

        $mesesNomes = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
            '04' => 'Abril',   '05' => 'Maio',      '06' => 'Junho',
            '07' => 'Julho',   '08' => 'Agosto',    '09' => 'Setembro',
            '10' => 'Outubro', '11' => 'Novembro',  '12' => 'Dezembro',
        ];

        $meses = [];
        for ($m = 1; $m <= 12; $m++) {
            $chave = sprintf('%04d-%02d', $ano, $m);
            $row = Database::fetchOne(
                'SELECT
                    SUM(CASE WHEN tipo = \'receita\' THEN valor_iva ELSE 0 END) AS liquidado,
                    SUM(CASE WHEN tipo = \'despesa\' THEN valor_iva ELSE 0 END) AS dedutivel
                 FROM lancamentos
                 WHERE strftime(\'%Y-%m\', data) = ?',
                [$chave]
            );
            $liquidado = (int)($row['liquidado'] ?? 0);
            $dedutivel = (int)($row['dedutivel'] ?? 0);
            $meses[] = [
                'mes'       => $chave,
                'nome'      => $mesesNomes[sprintf('%02d', $m)],
                'liquidado' => $liquidado,
                'dedutivel' => $dedutivel,
                'a_pagar'   => $liquidado - $dedutivel,
            ];
        }

        // Quarterly totals
        $trimestres = [];
        for ($t = 0; $t < 4; $t++) {
            $slice = array_slice($meses, $t * 3, 3);
            $trimestres[$t + 1] = [
                'liquidado' => array_sum(array_column($slice, 'liquidado')),
                'dedutivel' => array_sum(array_column($slice, 'dedutivel')),
                'a_pagar'   => array_sum(array_column($slice, 'a_pagar')),
            ];
        }

        // Annual total
        $anual = [
            'liquidado' => array_sum(array_column($meses, 'liquidado')),
            'dedutivel' => array_sum(array_column($meses, 'dedutivel')),
            'a_pagar'   => array_sum(array_column($meses, 'a_pagar')),
        ];

        // Breakdown by rate for the year
        $porTaxa = Database::fetchAll(
            'SELECT taxa_iva,
                SUM(CASE WHEN tipo = \'receita\' THEN valor_iva ELSE 0 END) AS liquidado,
                SUM(CASE WHEN tipo = \'despesa\' THEN valor_iva ELSE 0 END) AS dedutivel,
                COUNT(*) AS total_lancamentos
             FROM lancamentos
             WHERE strftime(\'%Y\', data) = ?
               AND taxa_iva > 0
             GROUP BY taxa_iva
             ORDER BY taxa_iva DESC',
            [(string)$ano]
        );

        $pageTitle = 'Relatório de IVA';
        $activePage = 'iva';
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . '/../views/iva/index.php';
        require __DIR__ . '/../views/layout/footer.php';
    }
}
