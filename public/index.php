<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/config.php';
require_once dirname(__DIR__) . '/app/helpers.php';
require_once dirname(__DIR__) . '/app/Database.php';
require_once dirname(__DIR__) . '/app/Auth.php';
require_once dirname(__DIR__) . '/app/Router.php';

Auth::start();

$router = new Router();

// Auth
$router->add('GET',  '/login',   'AuthController', 'showLogin');
$router->add('POST', '/login',   'AuthController', 'login');
$router->add('GET',  '/logout',  'AuthController', 'logout');

// Dashboard
$router->add('GET', '/',          'DashboardController', 'index');
$router->add('GET', '/dashboard', 'DashboardController', 'index');

// Lançamentos
$router->add('GET',  '/lancamentos',              'LancamentosController', 'index');
$router->add('GET',  '/lancamentos/novo',         'LancamentosController', 'novo');
$router->add('POST', '/lancamentos/salvar',       'LancamentosController', 'salvar');
$router->add('GET',  '/lancamentos/editar/{id}',  'LancamentosController', 'editar');
$router->add('POST', '/lancamentos/atualizar/{id}','LancamentosController', 'atualizar');
$router->add('POST', '/lancamentos/excluir/{id}', 'LancamentosController', 'excluir');

// Categorias
$router->add('GET',  '/categorias',              'CategoriasController', 'index');
$router->add('GET',  '/categorias/nova',         'CategoriasController', 'nova');
$router->add('POST', '/categorias/salvar',       'CategoriasController', 'salvar');
$router->add('GET',  '/categorias/editar/{id}',  'CategoriasController', 'editar');
$router->add('POST', '/categorias/atualizar/{id}','CategoriasController', 'atualizar');
$router->add('POST', '/categorias/excluir/{id}', 'CategoriasController', 'excluir');

// Relatórios
$router->add('GET', '/relatorios', 'RelatoriosController', 'index');

$method = $_SERVER['REQUEST_METHOD'];
$uri    = $_SERVER['REQUEST_URI'];

$router->dispatch($method, $uri);
