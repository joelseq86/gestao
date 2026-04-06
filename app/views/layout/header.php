<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle ?? APP_NAME) ?> — <?= h(APP_NAME) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="app-wrapper">

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <h1><?= h(APP_NAME) ?></h1>
            <span>Gestão Empresarial</span>
        </div>
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="/dashboard" class="<?= in_array($activePage ?? '', ['dashboard']) ? 'active' : '' ?>">
                        <span class="nav-icon">📊</span> Dashboard
                    </a>
                </li>
                <li>
                    <a href="/lancamentos" class="<?= ($activePage ?? '') === 'lancamentos' ? 'active' : '' ?>">
                        <span class="nav-icon">💰</span> Lançamentos
                    </a>
                </li>
                <li>
                    <a href="/categorias" class="<?= ($activePage ?? '') === 'categorias' ? 'active' : '' ?>">
                        <span class="nav-icon">🏷️</span> Categorias
                    </a>
                </li>
                <li>
                    <a href="/relatorios" class="<?= ($activePage ?? '') === 'relatorios' ? 'active' : '' ?>">
                        <span class="nav-icon">📈</span> Relatórios
                    </a>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <a href="/logout">
                <span>🚪</span> Terminar sessão
            </a>
        </div>
    </aside>

    <!-- Main -->
    <div class="main-content">
        <header class="topbar">
            <div style="display:flex;align-items:center;gap:14px;">
                <div class="hamburger" id="hamburger">
                    <span></span><span></span><span></span>
                </div>
                <span class="topbar-title"><?= h($pageTitle ?? APP_NAME) ?></span>
            </div>
            <div class="topbar-right">
                <span>👤 <?= h(Auth::username()) ?></span>
            </div>
        </header>

        <main class="page-content">
<?php
$flash = flashGet();
if ($flash): ?>
<div class="alert alert-<?= h($flash['type']) ?>">
    <?= h($flash['message']) ?>
</div>
<?php endif; ?>
