<?php $editing = !empty($lancamento['id']); ?>

<div class="page-header">
    <h2><?= $editing ? '✏️ Editar Lançamento' : '➕ Novo Lançamento' ?></h2>
    <a href="/lancamentos" class="btn btn-secondary">← Voltar</a>
</div>

<div class="form-card">
    <form method="POST" action="<?= $editing ? '/lancamentos/atualizar/' . h($lancamento['id']) : '/lancamentos/salvar' ?>">
        <?= csrfField() ?>

        <div class="form-grid">
            <!-- Tipo -->
            <div class="form-group span-2">
                <label>Tipo *</label>
                <div class="radio-tabs">
                    <input type="radio" id="tipo_despesa" name="tipo" value="despesa"
                           <?= ($lancamento['tipo'] ?? 'despesa') === 'despesa' ? 'checked' : '' ?>>
                    <label for="tipo_despesa">📤 Despesa</label>

                    <input type="radio" id="tipo_receita" name="tipo" value="receita"
                           <?= ($lancamento['tipo'] ?? '') === 'receita' ? 'checked' : '' ?>>
                    <label for="tipo_receita">📥 Receita</label>
                </div>
            </div>

            <!-- Descrição -->
            <div class="form-group span-2">
                <label for="descricao">Descrição *</label>
                <input type="text" id="descricao" name="descricao"
                       value="<?= h($lancamento['descricao']) ?>"
                       placeholder="Ex: Pagamento de fornecedor, Venda de serviço..."
                       required maxlength="255">
            </div>

            <!-- Valor -->
            <div class="form-group">
                <label for="valor">Valor (€) *</label>
                <input type="text" id="valor" name="valor"
                       value="<?= h($lancamento['valor']) ?>"
                       placeholder="0,00" required
                       inputmode="decimal">
            </div>

            <!-- Data -->
            <div class="form-group">
                <label for="data">Data *</label>
                <input type="date" id="data" name="data"
                       value="<?= h($lancamento['data']) ?>" required>
            </div>

            <!-- Categoria -->
            <div class="form-group">
                <label for="categoria_id">Categoria</label>
                <select id="categoria_id" name="categoria_id">
                    <option value="">Sem categoria</option>
                    <?php
                    $grupos = ['receita' => '📥 Receitas', 'despesa' => '📤 Despesas'];
                    foreach ($grupos as $tipo => $label):
                        $cats = array_filter($categorias, fn($c) => $c['tipo'] === $tipo);
                        if (!$cats) continue;
                    ?>
                    <optgroup label="<?= h($label) ?>">
                        <?php foreach ($cats as $cat): ?>
                        <option value="<?= h($cat['id']) ?>"
                                <?= (string)($lancamento['categoria_id'] ?? '') === (string)$cat['id'] ? 'selected' : '' ?>>
                            <?= h($cat['nome']) ?>
                        </option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Observações -->
            <div class="form-group">
                <label for="observacoes">Observações</label>
                <input type="text" id="observacoes" name="observacoes"
                       value="<?= h($lancamento['observacoes'] ?? '') ?>"
                       placeholder="Notas adicionais (opcional)" maxlength="500">
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <?= $editing ? '💾 Guardar Alterações' : '✅ Registar Lançamento' ?>
            </button>
            <a href="/lancamentos" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
