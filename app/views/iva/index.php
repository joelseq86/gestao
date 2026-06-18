<?php
$mesesNomes = [
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
    '04' => 'Abril',   '05' => 'Maio',      '06' => 'Junho',
    '07' => 'Julho',   '08' => 'Agosto',    '09' => 'Setembro',
    '10' => 'Outubro', '11' => 'Novembro',  '12' => 'Dezembro',
];
?>

<div class="page-header">
    <h2>🧾 Relatório de IVA — <?= h($ano) ?></h2>
</div>

<!-- Year selector -->
<div class="filters-bar" style="margin-bottom:20px">
    <form method="GET" action="/iva" style="display:contents">
        <div class="form-group">
            <label>Ano</label>
            <select name="ano" onchange="this.form.submit()">
                <?php for ($y = $ano - 2; $y <= $ano + 2; $y++): ?>
                <option value="<?= h($y) ?>" <?= $y === $ano ? 'selected' : '' ?>><?= h($y) ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </form>
</div>

<!-- Monthly table -->
<div class="table-wrapper" style="margin-bottom:30px">
    <table>
        <thead>
            <tr>
                <th>Mês</th>
                <th class="td-right">IVA Liquidado<br><small style="font-weight:normal">(Receitas)</small></th>
                <th class="td-right">IVA Dedutível<br><small style="font-weight:normal">(Despesas)</small></th>
                <th class="td-right">IVA a Pagar / Recuperar</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($meses as $i => $m):
            $mesNum = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
            $isEndOfQuarter = in_array($i + 1, [3, 6, 9, 12]);
            $quarterNum = (int)ceil(($i + 1) / 3);
        ?>
            <tr>
                <td><?= h($m['nome']) ?></td>
                <td class="td-right">
                    <?= $m['liquidado'] > 0 ? '€ ' . formatMoney($m['liquidado']) : '<span class="text-light">—</span>' ?>
                </td>
                <td class="td-right">
                    <?= $m['dedutivel'] > 0 ? '€ ' . formatMoney($m['dedutivel']) : '<span class="text-light">—</span>' ?>
                </td>
                <td class="td-right fw-bold <?= $m['a_pagar'] > 0 ? 'text-vermelho' : ($m['a_pagar'] < 0 ? 'text-verde' : '') ?>">
                    <?php if ($m['a_pagar'] > 0): ?>
                        € <?= formatMoney($m['a_pagar']) ?>
                    <?php elseif ($m['a_pagar'] < 0): ?>
                        ▲ € <?= formatMoney(abs($m['a_pagar'])) ?> <small style="font-size:0.75em">(recuperar)</small>
                    <?php else: ?>
                        <span class="text-light">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if ($isEndOfQuarter):
                $q = $trimestres[$quarterNum];
            ?>
            <tr style="background:#f0f4f8;font-weight:600;">
                <td style="padding-left:1.5rem">Total T<?= $quarterNum ?></td>
                <td class="td-right">€ <?= formatMoney($q['liquidado']) ?></td>
                <td class="td-right">€ <?= formatMoney($q['dedutivel']) ?></td>
                <td class="td-right <?= $q['a_pagar'] > 0 ? 'text-vermelho' : ($q['a_pagar'] < 0 ? 'text-verde' : '') ?>">
                    <?php if ($q['a_pagar'] > 0): ?>
                        € <?= formatMoney($q['a_pagar']) ?>
                    <?php elseif ($q['a_pagar'] < 0): ?>
                        ▲ € <?= formatMoney(abs($q['a_pagar'])) ?> <small style="font-size:0.75em">(recuperar)</small>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background:#e8ecf0;font-weight:700;font-size:1.05em;">
                <td>Total Anual</td>
                <td class="td-right">€ <?= formatMoney($anual['liquidado']) ?></td>
                <td class="td-right">€ <?= formatMoney($anual['dedutivel']) ?></td>
                <td class="td-right <?= $anual['a_pagar'] > 0 ? 'text-vermelho' : ($anual['a_pagar'] < 0 ? 'text-verde' : '') ?>">
                    <?php if ($anual['a_pagar'] > 0): ?>
                        € <?= formatMoney($anual['a_pagar']) ?>
                    <?php elseif ($anual['a_pagar'] < 0): ?>
                        ▲ € <?= formatMoney(abs($anual['a_pagar'])) ?> <small style="font-size:0.75em">(recuperar)</small>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Breakdown by rate -->
<?php if (!empty($porTaxa)): ?>
<h3 style="margin-bottom:12px">Desagregação por Taxa — <?= h($ano) ?></h3>
<div class="table-wrapper">
    <table>
        <thead>
            <tr>
                <th>Taxa IVA</th>
                <th class="td-right">IVA Liquidado</th>
                <th class="td-right">IVA Dedutível</th>
                <th class="td-right">Saldo</th>
                <th class="td-right">Lançamentos</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($porTaxa as $t): ?>
            <tr>
                <td><strong><?= h($t['taxa_iva']) ?>%</strong></td>
                <td class="td-right">€ <?= formatMoney((int)$t['liquidado']) ?></td>
                <td class="td-right">€ <?= formatMoney((int)$t['dedutivel']) ?></td>
                <?php $saldoTaxa = (int)$t['liquidado'] - (int)$t['dedutivel']; ?>
                <td class="td-right fw-bold <?= $saldoTaxa > 0 ? 'text-vermelho' : ($saldoTaxa < 0 ? 'text-verde' : '') ?>">
                    <?php if ($saldoTaxa > 0): ?>
                        € <?= formatMoney($saldoTaxa) ?>
                    <?php elseif ($saldoTaxa < 0): ?>
                        ▲ € <?= formatMoney(abs($saldoTaxa)) ?>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td class="td-right"><?= h($t['total_lancamentos']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="table-wrapper">
    <div class="empty-state">
        <div class="icon">🧾</div>
        <p>Não há lançamentos com IVA para <?= h($ano) ?>.</p>
    </div>
</div>
<?php endif; ?>
