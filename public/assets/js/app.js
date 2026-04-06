// Hamburger menu
const hamburger = document.getElementById('hamburger');
const sidebar   = document.getElementById('sidebar');
if (hamburger && sidebar) {
    hamburger.addEventListener('click', () => {
        sidebar.classList.toggle('open');
    });
    document.addEventListener('click', (e) => {
        if (!sidebar.contains(e.target) && !hamburger.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
}

// Confirm delete
document.querySelectorAll('.form-delete').forEach(form => {
    form.addEventListener('submit', e => {
        if (!confirm('Tem a certeza que quer excluir este registo?')) {
            e.preventDefault();
        }
    });
});

// Auto-dismiss alerts
document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }, 4000);
});

// Color picker for categories
document.querySelectorAll('.color-option').forEach(el => {
    el.addEventListener('click', () => {
        const group = el.closest('.color-options');
        group.querySelectorAll('.color-option').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        const input = document.getElementById('cor');
        if (input) input.value = el.dataset.color;
    });
});

// Initialize charts if data attributes are present
function initCharts() {
    // Monthly bar chart
    const barEl = document.getElementById('chart-mensal');
    if (barEl && typeof Chart !== 'undefined') {
        const labels   = JSON.parse(barEl.dataset.labels   || '[]');
        const receitas = JSON.parse(barEl.dataset.receitas || '[]');
        const despesas = JSON.parse(barEl.dataset.despesas || '[]');

        new Chart(barEl, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Receitas',
                        data: receitas,
                        backgroundColor: 'rgba(39, 174, 96, 0.8)',
                        borderRadius: 4,
                    },
                    {
                        label: 'Despesas',
                        data: despesas,
                        backgroundColor: 'rgba(231, 76, 60, 0.8)',
                        borderRadius: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'top' } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => '€ ' + Number(v).toLocaleString('pt-PT', { minimumFractionDigits: 2 }),
                        },
                    },
                },
            },
        });
    }

    // Doughnut chart for categories
    const doughnutEl = document.getElementById('chart-categorias');
    if (doughnutEl && typeof Chart !== 'undefined') {
        const labels = JSON.parse(doughnutEl.dataset.labels || '[]');
        const values = JSON.parse(doughnutEl.dataset.values || '[]');
        const colors = JSON.parse(doughnutEl.dataset.colors || '[]');

        new Chart(doughnutEl, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff',
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 12 } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ' € ' + Number(ctx.raw).toLocaleString('pt-PT', { minimumFractionDigits: 2 }),
                        },
                    },
                },
            },
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCharts);
} else {
    initCharts();
}
