<!-- Summary mini-cards -->
<div class="cards-grid" style="margin-bottom:20px">
    <div class="card">
        <div class="card-label">Receitas do mês</div>
        <div class="card-value verde">€ <?= formatMoney((int)($totais['receitas'] ?? 0)) ?></div>
    </div>
    <div class="card">
        <div class="card-label">Despesas do mês</div>
        <div class="card-value vermelho">€ <?= formatMoney((int)($totais['despesas'] ?? 0)) ?></div>
    </div>
    <div class="card">
        <div class="card-label">Saldo do mês</div>
        <?php $saldo = (int)($totais['receitas'] ?? 0) - (int)($totais['despesas'] ?? 0); ?>
        <div class="card-value <?= $saldo >= 0 ? 'verde' : 'vermelho' ?>">
            € <?= formatMoney(abs($saldo)) ?>
            <?= $saldo < 0 ? ' <small style="font-size:0.7em">(negativo)</small>' : '' ?>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filters-bar">
    <form method="GET" action="/lancamentos" style="display:contents">
        <div class="form-group">
            <label>Mês</label>
            <input type="month" name="mes" value="<?= h($mes) ?>">
        </div>
        <div class="form-group">
            <label>Tipo</label>
            <select name="tipo">
                <option value="">Todos</option>
                <option value="receita" <?= $tipo === 'receita' ? 'selected' : '' ?>>Receitas</option>
                <option value="despesa" <?= $tipo === 'despesa' ? 'selected' : '' ?>>Despesas</option>
            </select>
        </div>
        <div class="form-group">
            <label>Categoria</label>
            <select name="cat">
                <option value="">Todas</option>
                <?php foreach ($categorias as $cat): ?>
                <option value="<?= h($cat['id']) ?>" <?= (string)$catId === (string)$cat['id'] ? 'selected' : '' ?>>
                    <?= h($cat['nome']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary btn-sm" style="align-self:flex-end">Filtrar</button>
        <a href="/lancamentos" class="btn btn-secondary btn-sm" style="align-self:flex-end">Limpar</a>
    </form>
</div>

<div class="page-header">
    <h2>💰 Lançamentos — <?= h(monthName($mes)) ?></h2>
    <a href="/lancamentos/novo" class="btn btn-success">+ Novo Lançamento</a>
</div>

<?php if (empty($lancamentos)): ?>
<div class="table-wrapper">
    <div class="empty-state">
        <div class="icon">💸</div>
        <p>Não há lançamentos para o período selecionado.</p>
        <a href="/lancamentos/novo" class="btn btn-success">Registar Lançamento</a>
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
                <th>Tipo</th>
                <th class="td-right">Valor</th>
                <th style="width:130px">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($lancamentos as $l): ?>
            <tr>
                <td style="white-space:nowrap"><?= h(formatDate($l['data'])) ?></td>
                <td>
                    <?= h($l['descricao']) ?>
                    <?php if ($l['observacoes']): ?>
                    <small class="text-light" style="display:block"><?= h($l['observacoes']) ?></small>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($l['categoria_nome']): ?>
                    <span class="cat-dot" style="background:<?= h($l['categoria_cor']) ?>"></span>
                    <?= h($l['categoria_nome']) ?>
                    <?php else: ?>
                    <span class="text-light">—</span>
                    <?php endif; ?>
                </td>
                <td><span class="badge badge-<?= h($l['tipo']) ?>"><?= h($l['tipo']) ?></span></td>
                <td class="td-right fw-bold <?= $l['tipo'] === 'receita' ? 'text-verde' : 'text-vermelho' ?>">
                    € <?= formatMoney($l['valor']) ?>
                </td>
                <td class="td-actions">
                    <a href="/lancamentos/editar/<?= h($l['id']) ?>" class="btn btn-outline btn-sm">Editar</a>
                    <form method="POST" action="/lancamentos/excluir/<?= h($l['id']) ?>"
                          class="form-delete" style="display:inline">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-danger btn-sm">✕</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
