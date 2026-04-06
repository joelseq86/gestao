<div class="page-header">
    <h2>📊 Dashboard — <?= h(monthName($mes)) ?></h2>
    <a href="/lancamentos/novo" class="btn btn-success">+ Novo Lançamento</a>
</div>

<!-- Summary cards -->
<div class="cards-grid">
    <div class="card">
        <div class="card-label">💚 Receitas do Mês</div>
        <div class="card-value verde">€ <?= formatMoney($receitas) ?></div>
        <div class="card-sub"><?= h(monthName($mes)) ?></div>
    </div>
    <div class="card">
        <div class="card-label">🔴 Despesas do Mês</div>
        <div class="card-value vermelho">€ <?= formatMoney($despesas) ?></div>
        <div class="card-sub"><?= h(monthName($mes)) ?></div>
    </div>
    <div class="card">
        <div class="card-label">💙 Saldo do Mês</div>
        <div class="card-value <?= $saldo >= 0 ? 'verde' : 'vermelho' ?>">
            <?= $saldo < 0 ? '−' : '' ?>€ <?= formatMoney(abs($saldo)) ?>
        </div>
        <div class="card-sub"><?= $saldo >= 0 ? 'Positivo' : 'Negativo' ?></div>
    </div>
    <div class="card">
        <div class="card-label">🏦 Saldo Total Acumulado</div>
        <?php $st = (int)($totalGeral['saldo'] ?? 0); ?>
        <div class="card-value <?= $st >= 0 ? 'azul' : 'vermelho' ?>">
            <?= $st < 0 ? '−' : '' ?>€ <?= formatMoney(abs($st)) ?>
        </div>
        <div class="card-sub">Todos os registos</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:28px" class="dashboard-grid">

    <!-- Recent transactions -->
    <div>
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
            <h3 style="font-size:1rem;font-weight:600">Últimos Lançamentos</h3>
            <a href="/lancamentos" class="btn btn-outline btn-sm">Ver todos</a>
        </div>

        <?php if (empty($recentes)): ?>
        <div class="table-wrapper">
            <div class="empty-state" style="padding:40px">
                <div class="icon">💸</div>
                <p>Ainda não há lançamentos.</p>
                <a href="/lancamentos/novo" class="btn btn-success btn-sm">Registar o primeiro</a>
            </div>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th class="td-right">Valor</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recentes as $l): ?>
                    <tr>
                        <td style="white-space:nowrap;font-size:0.82rem"><?= h(formatDate($l['data'])) ?></td>
                        <td>
                            <span class="badge badge-<?= h($l['tipo']) ?>" style="margin-right:6px"><?= h($l['tipo']) ?></span>
                            <?= h($l['descricao']) ?>
                        </td>
                        <td>
                            <?php if ($l['categoria_nome']): ?>
                            <span class="cat-dot" style="background:<?= h($l['categoria_cor']) ?>"></span>
                            <span style="font-size:0.85rem"><?= h($l['categoria_nome']) ?></span>
                            <?php else: ?>
                            <span class="text-light">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="td-right fw-bold <?= $l['tipo'] === 'receita' ? 'text-verde' : 'text-vermelho' ?>">
                            <?= $l['tipo'] === 'despesa' ? '−' : '+' ?>€ <?= formatMoney($l['valor']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Top categories -->
    <div>
        <div style="margin-bottom:12px">
            <h3 style="font-size:1rem;font-weight:600">Top Despesas por Categoria</h3>
        </div>

        <?php if (empty($topCategorias)): ?>
        <div class="table-wrapper">
            <div class="empty-state" style="padding:40px 20px">
                <p class="text-light" style="font-size:0.875rem">Sem dados para este mês.</p>
            </div>
        </div>
        <?php else: ?>
        <div class="table-wrapper" style="padding:0">
            <table class="summary-table">
                <tbody>
                <?php foreach ($topCategorias as $tc): ?>
                    <tr>
                        <td>
                            <span class="cat-dot" style="background:<?= h($tc['cor']) ?>"></span>
                            <?= h($tc['nome']) ?>
                        </td>
                        <td class="text-vermelho">€ <?= formatMoney($tc['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div style="margin-top:20px">
            <a href="/relatorios" class="btn btn-outline" style="width:100%;justify-content:center;">
                📈 Ver Relatórios
            </a>
        </div>
    </div>
</div>

<style>
@media(max-width:768px){
    .dashboard-grid { grid-template-columns: 1fr !important; }
}
</style>
