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

            <!-- Taxa IVA -->
            <div class="form-group">
                <label for="taxa_iva">Taxa IVA</label>
                <select id="taxa_iva" name="taxa_iva">
                    <option value="0"  <?= (int)($lancamento['taxa_iva'] ?? 0) === 0  ? 'selected' : '' ?>>0% — Isento</option>
                    <option value="6"  <?= (int)($lancamento['taxa_iva'] ?? 0) === 6  ? 'selected' : '' ?>>6% — Reduzida</option>
                    <option value="13" <?= (int)($lancamento['taxa_iva'] ?? 0) === 13 ? 'selected' : '' ?>>13% — Intermédia</option>
                    <option value="23" <?= (int)($lancamento['taxa_iva'] ?? 0) === 23 ? 'selected' : '' ?>>23% — Normal</option>
                </select>
            </div>

            <!-- Valor IVA -->
            <div class="form-group">
                <label for="valor_iva">Valor IVA (€)</label>
                <input type="text" id="valor_iva" name="valor_iva"
                       value="<?= h($lancamento['valor_iva'] ?? '0,00') ?>"
                       placeholder="0,00"
                       inputmode="decimal">
            </div>

            <!-- Total com IVA (read-only) -->
            <div class="form-group">
                <label>Total c/ IVA (€)</label>
                <input type="text" id="total_com_iva" readonly
                       placeholder="0,00"
                       style="background:#f8f9fa;cursor:default;">
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

        <script>
        (function () {
            function ptToFloat(str) {
                var s = str.replace(/[^\d,.]/g, '');
                if (s.indexOf(',') !== -1) {
                    s = s.replace(/\./g, '').replace(',', '.');
                }
                return parseFloat(s) || 0;
            }
            function floatToPt(num) {
                return num.toFixed(2).replace('.', ',');
            }
            function recalc() {
                var valor    = ptToFloat(document.getElementById('valor').value);
                var taxa     = parseInt(document.getElementById('taxa_iva').value, 10) || 0;
                var ivaVal   = Math.round(valor * taxa) / 100;
                var total    = valor + ivaVal;
                document.getElementById('valor_iva').value    = floatToPt(ivaVal);
                document.getElementById('total_com_iva').value = floatToPt(total);
            }
            document.getElementById('valor').addEventListener('input', recalc);
            document.getElementById('taxa_iva').addEventListener('change', recalc);
            // Initialise on load
            recalc();
        })();
        </script>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">
                <?= $editing ? '💾 Guardar Alterações' : '✅ Registar Lançamento' ?>
            </button>
            <a href="/lancamentos" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>
