<?php
/**
 * Setup script — run once to create the database and seed data.
 * Access via: http://yourdomain/setup.php  (file is in project root, NOT inside public/)
 * DELETE this file after running it.
 */

// Adjust this path if needed
define('DB_PATH', __DIR__ . '/storage/gestao.db');

if (!extension_loaded('pdo_sqlite')) {
    die('❌ PDO SQLite extension is not available.');
}

$storageDir = dirname(DB_PATH);
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA foreign_keys = ON');

    // Schema
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categorias (
            id        INTEGER PRIMARY KEY AUTOINCREMENT,
            nome      TEXT    NOT NULL,
            tipo      TEXT    NOT NULL CHECK(tipo IN ('receita','despesa')),
            cor       TEXT    NOT NULL DEFAULT '#6c757d',
            criado_em TEXT    NOT NULL DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS lancamentos (
            id           INTEGER PRIMARY KEY AUTOINCREMENT,
            tipo         TEXT    NOT NULL CHECK(tipo IN ('receita','despesa')),
            descricao    TEXT    NOT NULL,
            valor        INTEGER NOT NULL,
            categoria_id INTEGER REFERENCES categorias(id) ON DELETE SET NULL,
            data         TEXT    NOT NULL,
            observacoes  TEXT,
            taxa_iva     INTEGER NOT NULL DEFAULT 0,
            valor_iva    INTEGER NOT NULL DEFAULT 0,
            criado_em    TEXT    NOT NULL DEFAULT (datetime('now'))
        );

        CREATE INDEX IF NOT EXISTS idx_lancamentos_data ON lancamentos(data);
        CREATE INDEX IF NOT EXISTS idx_lancamentos_tipo ON lancamentos(tipo);
    ");

    // Seed categories
    $cats = [
        // Receitas
        ['Vendas / Serviços',   'receita', '#27ae60'],
        ['Consultoria',         'receita', '#2ecc71'],
        ['Investimentos',       'receita', '#1abc9c'],
        ['Outras Receitas',     'receita', '#16a085'],
        // Despesas
        ['Fornecedores',        'despesa', '#e74c3c'],
        ['Salários',            'despesa', '#c0392b'],
        ['Rendas / Instalações','despesa', '#e67e22'],
        ['Marketing',           'despesa', '#d35400'],
        ['Tecnologia / Software','despesa','#9b59b6'],
        ['Transportes',         'despesa', '#8e44ad'],
        ['Alimentação',         'despesa', '#f39c12'],
        ['Impostos / Taxas',    'despesa', '#7f8c8d'],
        ['Serviços Externos',   'despesa', '#2980b9'],
        ['Outras Despesas',     'despesa', '#95a5a6'],
    ];

    $stmt = $pdo->prepare('INSERT OR IGNORE INTO categorias (nome, tipo, cor) VALUES (?, ?, ?)');
    foreach ($cats as $c) {
        $stmt->execute($c);
    }

    echo "✅ Base de dados criada com sucesso em: " . DB_PATH . "\n\n";
    echo "Categorias criadas: " . count($cats) . "\n\n";
    echo "<strong>⚠️ Apague este ficheiro (setup.php) após a execução!</strong>\n";
    echo "\nPode agora aceder à aplicação: <a href='/'>Iniciar</a>\n";

} catch (Exception $e) {
    echo "❌ Erro: " . htmlspecialchars($e->getMessage());
}
