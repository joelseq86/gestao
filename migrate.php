<?php
/**
 * Migration script — adds IVA columns to lancamentos table.
 * Run once on existing databases.
 * Access via: http://yourdomain/migrate.php
 * DELETE this file after running it.
 */

define('DB_PATH', __DIR__ . '/storage/gestao.db');

if (!extension_loaded('pdo_sqlite')) {
    die('❌ PDO SQLite extension is not available.');
}

if (!file_exists(DB_PATH)) {
    die('❌ Base de dados não encontrada em: ' . DB_PATH);
}

try {
    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $added = [];

    // Add taxa_iva column if it doesn't exist
    try {
        $pdo->exec('ALTER TABLE lancamentos ADD COLUMN taxa_iva INTEGER NOT NULL DEFAULT 0');
        $added[] = 'taxa_iva';
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'duplicate column')) {
            echo "ℹ️ Coluna <code>taxa_iva</code> já existe — ignorado.<br>\n";
        } else {
            throw $e;
        }
    }

    // Add valor_iva column if it doesn't exist
    try {
        $pdo->exec('ALTER TABLE lancamentos ADD COLUMN valor_iva INTEGER NOT NULL DEFAULT 0');
        $added[] = 'valor_iva';
    } catch (PDOException $e) {
        if (str_contains($e->getMessage(), 'duplicate column')) {
            echo "ℹ️ Coluna <code>valor_iva</code> já existe — ignorado.<br>\n";
        } else {
            throw $e;
        }
    }

    if (!empty($added)) {
        echo "✅ Colunas adicionadas com sucesso: " . implode(', ', $added) . "<br>\n";
    }

    echo "<br>✅ Migração concluída.<br>\n";
    echo "<strong>⚠️ Apague este ficheiro (migrate.php) após a execução!</strong>\n";

} catch (Exception $e) {
    echo "❌ Erro: " . htmlspecialchars($e->getMessage());
}
