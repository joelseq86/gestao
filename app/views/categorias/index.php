<div class="page-header">
    <h2>🏷️ Categorias</h2>
    <a href="/categorias/nova" class="btn btn-primary">+ Nova Categoria</a>
</div>

<?php if (empty($categorias)): ?>
<div class="table-wrapper">
    <div class="empty-state">
        <div class="icon">🏷️</div>
        <p>Ainda não tem categorias criadas.</p>
        <a href="/categorias/nova" class="btn btn-primary">Criar Categoria</a>
    </div>
</div>
<?php else: ?>

<?php
$receitas = array_filter($categorias, fn($c) => $c['tipo'] === 'receita');
$despesas = array_filter($categorias, fn($c) => $c['tipo'] === 'despesa');

function renderCategoriasTable(array $cats, string $tipo): void { ?>
<h3 style="margin-bottom:12px;font-size:1rem;font-weight:600;color:var(--text-light);">
    <?= $tipo === 'receita' ? '📥 Receitas' : '📤 Despesas' ?>
</h3>
<div class="table-wrapper mb-3">
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Cor</th>
                <th>Tipo</th>
                <th style="width:120px">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($cats as $c): ?>
            <tr>
                <td>
                    <span class="cat-dot" style="background:<?= h($c['cor']) ?>"></span>
                    <?= h($c['nome']) ?>
                </td>
                <td>
                    <span style="display:inline-flex;align-items:center;gap:6px;font-size:0.8rem;color:var(--text-light);">
                        <span style="display:inline-block;width:16px;height:16px;border-radius:3px;background:<?= h($c['cor']) ?>"></span>
                        <?= h($c['cor']) ?>
                    </span>
                </td>
                <td><span class="badge badge-<?= h($c['tipo']) ?>"><?= h($c['tipo']) ?></span></td>
                <td class="td-actions">
                    <a href="/categorias/editar/<?= h($c['id']) ?>" class="btn btn-outline btn-sm">Editar</a>
                    <form method="POST" action="/categorias/excluir/<?= h($c['id']) ?>"
                          class="form-delete" style="display:inline">
                        <?= csrfField() ?>
                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php } ?>

<?php renderCategoriasTable(array_values($receitas), 'receita'); ?>
<?php renderCategoriasTable(array_values($despesas), 'despesa'); ?>

<?php endif; ?>
