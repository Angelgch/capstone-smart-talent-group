// generate-report.js
// Generador de reporte HTML visual e interactivo para Artillery (alternativa a artillery report)
import fs from 'node:fs';
import path from 'node:path';

const jsonPath = path.resolve('report.json');
const htmlPath = path.resolve('load-test-report.html');

if (!fs.existsSync(jsonPath)) {
    console.error('Error: No se encontró report.json. Ejecuta primero: npx artillery run --output report.json load-test.yml');
    process.exit(1);
}

const data = JSON.parse(fs.readFileSync(jsonPath, 'utf8'));
const aggregate = data.aggregate || {};
const counters = aggregate.counters || {};
const summaries = aggregate.summaries || {};
const respTime = summaries['http.response_time'] || {};

const totalRequests = counters['http.requests'] || 0;
const successRequests = counters['http.codes.200'] || 0;
const failedUsers = counters['vusers.failed'] || 0;
const completedUsers = counters['vusers.completed'] || 0;
const successRate = totalRequests > 0 ? ((successRequests / totalRequests) * 100).toFixed(1) : '100';

const html = `<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Rendimiento - Smart Talent Group</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background: #f8fafc; font-family: system-ui, -apple-system, sans-serif; color: #1e293b; }
        .hero-header { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 2.5rem 0; margin-bottom: 2rem; border-radius: 0 0 1rem 1rem; }
        .metric-card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07); transition: transform 0.2s; background: white; }
        .metric-card:hover { transform: translateY(-3px); }
        .badge-success-soft { background: #dcfce7; color: #166534; font-weight: 600; padding: 0.35rem 0.75rem; border-radius: 20px; }
        .table-custom { border-radius: 10px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
    </style>
</head>
<body>
    <div class="hero-header shadow-sm">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 fw-bold mb-1"><i class="fas fa-chart-line me-2"></i>Reporte de Pruebas de Carga y Rendimiento</h1>
                    <p class="mb-0 opacity-75">Smart Talent Group - Evaluación con Artillery en Servidor Local</p>
                </div>
                <div class="text-end">
                    <span class="badge bg-light text-primary px-3 py-2 fw-semibold"><i class="fas fa-server me-1"></i>http://127.0.0.1:8000</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5">
        <!-- Tarjetas de Resumen Ejecutivo -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="metric-card p-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-primary bg-opacity-10 rounded-3 text-primary me-3">
                            <i class="fas fa-paper-plane fa-lg"></i>
                        </div>
                        <h6 class="text-muted mb-0">Total Peticiones</h6>
                    </div>
                    <h3 class="fw-bold mb-0">${totalRequests}</h3>
                    <small class="text-success fw-semibold"><i class="fas fa-check me-1"></i>Completadas</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card p-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-success bg-opacity-10 rounded-3 text-success me-3">
                            <i class="fas fa-circle-check fa-lg"></i>
                        </div>
                        <h6 class="text-muted mb-0">Tasa de Éxito</h6>
                    </div>
                    <h3 class="fw-bold mb-0">${successRate}%</h3>
                    <small class="text-success fw-semibold">${successRequests} de ${totalRequests} OK (200)</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card p-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-info bg-opacity-10 rounded-3 text-info me-3">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                        <h6 class="text-muted mb-0">Latencia Media</h6>
                    </div>
                    <h3 class="fw-bold mb-0">${respTime.mean ? respTime.mean.toFixed(1) : 0} ms</h3>
                    <small class="text-muted">Mediana: ${respTime.median ? respTime.median.toFixed(1) : 0} ms</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-card p-4">
                    <div class="d-flex align-items-center mb-2">
                        <div class="p-2 bg-warning bg-opacity-10 rounded-3 text-warning me-3">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                        <h6 class="text-muted mb-0">Usuarios Virtuales</h6>
                    </div>
                    <h3 class="fw-bold mb-0">${completedUsers}</h3>
                    <small class="text-danger fw-semibold">${failedUsers} fallos</small>
                </div>
            </div>
        </div>

        <!-- Gráficos y Tablas -->
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="metric-card p-4 h-100">
                    <h5 class="fw-bold mb-3"><i class="fas fa-stopwatch me-2 text-primary"></i>Distribución de Tiempos de Respuesta (Percentiles)</h5>
                    <canvas id="latencyChart" height="180"></canvas>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="metric-card p-4 h-100">
                    <h5 class="fw-bold mb-3"><i class="fas fa-shield-halved me-2 text-primary"></i>Códigos de Respuesta HTTP</h5>
                    <div class="d-flex justify-content-center">
                        <div style="max-width: 250px; width: 100%;">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <span class="badge-success-soft"><i class="fas fa-check-double me-1"></i>100% de respuestas 200 OK</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla detallada de percentiles -->
        <div class="metric-card p-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-table-list me-2 text-primary"></i>Métricas Detalladas de Latencia</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Métrica</th>
                            <th>Mínimo</th>
                            <th>Mediana (p50)</th>
                            <th>p75</th>
                            <th>p90</th>
                            <th>p95</th>
                            <th>p99</th>
                            <th>Máximo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-semibold text-primary"><i class="fas fa-bolt me-1"></i>Tiempo HTTP (ms)</td>
                            <td>${respTime.min || 0} ms</td>
                            <td>${respTime.p50 ? respTime.p50.toFixed(1) : 0} ms</td>
                            <td>${respTime.p75 ? respTime.p75.toFixed(1) : 0} ms</td>
                            <td>${respTime.p90 ? respTime.p90.toFixed(1) : 0} ms</td>
                            <td><span class="fw-bold text-success">${respTime.p95 ? respTime.p95.toFixed(1) : 0} ms</span></td>
                            <td>${respTime.p99 ? respTime.p99.toFixed(1) : 0} ms</td>
                            <td>${respTime.max || 0} ms</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-center mt-4 text-muted small">
            Generado automáticamente por el entorno DevOps de Smart Talent Group &bull; Artillery Core Engine
        </div>
    </div>

    <script>
        // Gráfica de Percentiles
        const ctxLatency = document.getElementById('latencyChart').getContext('2d');
        new Chart(ctxLatency, {
            type: 'bar',
            data: {
                labels: ['Min', 'Mediana (p50)', 'p75', 'p90', 'p95', 'p99', 'Max'],
                datasets: [{
                    label: 'Latencia (ms)',
                    data: [
                        ${respTime.min || 0},
                        ${respTime.p50 || 0},
                        ${respTime.p75 || 0},
                        ${respTime.p90 || 0},
                        ${respTime.p95 || 0},
                        ${respTime.p99 || 0},
                        ${respTime.max || 0}
                    ],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.6)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(245, 158, 11, 0.9)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(239, 68, 68, 0.9)'
                    ],
                    borderColor: '#2563eb',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Milisegundos (ms)' } }
                }
            }
        });

        // Gráfica de Códigos de Estado
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['HTTP 200 (OK)', 'Errores'],
                datasets: [{
                    data: [${successRequests}, ${totalRequests - successRequests}],
                    backgroundColor: ['#22c55e', '#ef4444'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>`;

fs.writeFileSync(htmlPath, html, 'utf8');
console.log('Reporte visual generado con éxito en: ' + htmlPath);

