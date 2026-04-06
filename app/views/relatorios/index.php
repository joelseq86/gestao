<div class="page-header">
    <h2>📈 Relatórios</h2>
    <form method="GET" action="/relatorios" style="display:flex;gap:10px;align-items:center">
        <label style="font-size:0.875rem;font-weight:600">Mês:</label>
        <input type="month" name="mes" value="<?= h($mes) ?>" style="padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:0.875rem">
        <button type="submit" class="btn btn-primary btn-sm">Aplicar</button>
    </form>
</div>

<!-- Summary cards -->
<?php
$rec = (int)($totais['receitas'] ?? 0);
$dep = (int)($totais['despesas'] ?? 0);
$sal = $rec - $dep;
?>
<div class="cards-grid" style="margin-bottom:24px">
    <div class="card">
        <div class="card-label">Receitas</div>
        <div class="card-value verde">€ <?= formatMoney($rec) ?></div>
        <div class="card-sub"><?= h(monthName($mes)) ?></div>
    </div>
    <div class="card">
        <div class="card-label">Despesas</div>
        <div class="card-value vermelho">€ <?= formatMoney($dep) ?></div>
        <div class="card-sub"><?= h(monthName($mes)) ?></div>
    </div>
    <div class="card">
        <div class="card-label">Saldo</div>
        <div class="card-value <?= $sal >= 0 ? 'verde' : 'vermelho' ?>">
            <?= $sal < 0 ? '−' : '' ?>€ <?= formatMoney(abs($sal)) ?>
        </div>
        <div class="card-sub"><?= $sal >= 0 ? 'Superávit' : 'Déficit' ?></div>
    </div>
    <?php if ($dep > 0): ?>
    <div class="card">
        <div class="card-label">Taxa de Poupança</div>
        <?php $taxa = $rec > 0 ? round(($sal / $rec) * 100, 1) : 0; ?>
        <div class="card-value <?= $taxa >= 0 ? 'azul' : 'vermelho' ?>"><?= $taxa ?>%</div>
        <div class="card-sub">das receitas</div>
    </div>
    <?php endif; ?>
</div>

<!-- Charts -->
<div class="charts-grid">
    <!-- Bar chart: last 12 months -->
    <div class="chart-card">
        <h3>Receitas vs Despesas — Últimos 12 Meses</h3>
        <div class="chart-container" style="height:280px">
            <canvas id="chart-mensal"
                data-labels='<?= json_encode($barLabels, JSON_UNESCAPED_UNICODE) ?>'
                data-receitas='<?= json_encode($barReceitas) ?>'
                data-despesas='<?= json_encode($barDespesas) ?>'
            ></canvas>
        </div>
    </div>

    <!-- Doughnut: expense breakdown -->
    <div class="chart-card">
        <h3>Despesas por Categoria — <?= h(monthName($mes)) ?></h3>
        <?php if (empty($catDespesas)): ?>
        <div class="empty-state" style="padding:40px 10px">
            <p class="text-light" style="font-size:0.875rem">Sem despesas para este mês.</p>
        </div>
        <?php else: ?>
        <div class="chart-container" style="height:280px">
            <canvas id="chart-categorias"
                data-labels='<?= json_encode($doughnutLabels, JSON_UNESCAPED_UNICODE) ?>'
                data-values='<?= json_encode($doughnutValues) ?>'
                data-colors='<?= json_encode($doughnutColors) ?>'
            ></canvas>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Category breakdown table -->
<?php if (!empty($catDespesas)): ?>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
    <div>
        <h3 style="font-size:1rem;font-weight:600;margin-bottom:12px">Detalhe de Despesas por Categoria</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th class="td-right">Valor</th>
                        <th class="td-right">% do Total</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($catDespesas as $cd):
                    $pct = $dep > 0 ? round(($cd['total'] / $dep) * 100, 1) : 0;
                ?>
                    <tr>
                        <td>
                            <span class="cat-dot" style="background:<?= h($cd['cor']) ?>"></span>
                            <?= h($cd['nome']) ?>
                        </td>
                        <td class="td-right text-vermelho fw-bold">€ <?= formatMoney($cd['total']) ?></td>
                        <td class="td-right text-light"><?= $pct ?>%</td>
                    </tr>
                <?php endforeach; ?>
                    <tr style="border-top:2px solid var(--border)">
                        <td class="fw-bold">Total</td>
                        <td class="td-right text-vermelho fw-bold">€ <?= formatMoney($dep) ?></td>
                        <td class="td-right fw-bold">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
