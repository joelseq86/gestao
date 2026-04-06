<?php

function h(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function formatMoney(int $cents): string
{
    return number_format($cents / 100, 2, ',', '.');
}

function parseMoney(string $input): int
{
    $clean = preg_replace('/[^\d,.]/', '', $input);
    // Handle Portuguese format "1.234,56"
    if (str_contains($clean, ',')) {
        $clean = str_replace('.', '', $clean);
        $clean = str_replace(',', '.', $clean);
    }
    return (int) round((float)$clean * 100);
}

function formatDate(string $date): string
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d ? $d->format('d/m/Y') : $date;
}

function parseDate(string $input): string
{
    // Accept dd/mm/yyyy or yyyy-mm-dd
    if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $input)) {
        $d = DateTime::createFromFormat('d/m/Y', $input);
        return $d ? $d->format('Y-m-d') : $input;
    }
    return $input;
}

function csrfToken(): string
{
    Auth::start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfVerify(): void
{
    Auth::start();
    $token = $_POST['_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Token CSRF inválido.');
    }
}

function csrfField(): string
{
    return '<input type="hidden" name="_token" value="' . h(csrfToken()) . '">';
}

function flashSet(string $type, string $message): void
{
    Auth::start();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flashGet(): ?array
{
    Auth::start();
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function currentMonth(): string
{
    return date('Y-m');
}

function monthName(string $yearMonth): string
{
    $months = [
        '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
        '04' => 'Abril',   '05' => 'Maio',       '06' => 'Junho',
        '07' => 'Julho',   '08' => 'Agosto',     '09' => 'Setembro',
        '10' => 'Outubro', '11' => 'Novembro',   '12' => 'Dezembro',
    ];
    [$year, $month] = explode('-', $yearMonth);
    return ($months[$month] ?? $month) . ' ' . $year;
}
