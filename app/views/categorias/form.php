<?php
$editing = !empty($categoria['id']);
$colors = ['#e74c3c','#e67e22','#f1c40f','#2ecc71','#1abc9c','#3498db','#9b59b6','#e91e63','#795548','#607d8b','#27ae60','#2980b9'];
?>
<div class="page-header">
    <h2><?= $editing ? '✏️ Editar Categoria' : '➕ Nova Categoria' ?></h2>
    <a href="/categorias" class="btn btn-secondary">← Voltar</a>
</div>

<div class="form-card">
    <form method="POST" action="<?= $editing ? '/categorias/atualizar/' . h($categoria['id']) : '/categorias/salvar' ?>">
        <?= csrfField() ?>

        <div class="form-grid">
            <div class="form-group span-2">
                <label for="nome">Nome da Categoria *</label>
                <input type="text" id="nome" name="nome"
                       value="<?= h($categoria['nome']) ?>"
                       placeholder="Ex: Fornecedores, Salários..." required>
            </div>

            <div class="form-group">
                <label>Tipo *</label>
                <div class="radio-tabs">
                    <input type="radio" id="tipo_despesa" name="tipo" value="despesa"
                           <?= ($categoria['tipo'] ?? 'despesa') === 'despesa' ? 'checked' : '' ?>>
                    <label for="tipo_despesa">📤 Despesa</label>

                    <input type="radio" id="tipo_receita" name="tipo" value="receita"
                           <?= ($categoria['tipo'] ?? '') === 'receita' ? 'checked' : '' ?>>
                    <label for="tipo_receita">📥 Receita</label>
                </div>
            </div>

            <div class="form-group">
                <label>Cor</label>
                <div class="color-options">
                    <?php foreach ($colors as $c): ?>
                    <div class="color-option <?= ($categoria['cor'] ?? '') === $c ? 'selected' : '' ?>"
                         style="background:<?= h($c) ?>"
                         data-color="<?= h($c) ?>"
                         title="<?= h($c) ?>"></div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="cor" name="cor" value="<?= h($categoria['cor'] ?? '#e74c3c') ?>">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <?= $editing ? '💾 Guardar Alterações' : '✅ Criar Categoria' ?>
            </button>
            <a href="/categorias" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
