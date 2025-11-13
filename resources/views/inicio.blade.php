@extends('principal')
@section('titulo', 'INICIO')
@section('contenido')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <style>
        .card {
            border: none;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            animation: fadeInUp 0.6s ease both;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-info {
            border-top: 4px solid #0d6efd;
        }

        .card-success {
            border-top: 4px solid #198754;
        }

        .card-warning {
            border-top: 4px solid #ffc107;
        }

        .card-danger {
            border-top: 4px solid #dc3545;
        }

        .icon-bg {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .icon-bg-blue {
            background: rgba(13, 110, 253, 0.1);
            color: #0d6efd;
        }

        .icon-bg-green {
            background: rgba(25, 135, 84, 0.1);
            color: #198754;
        }

        .icon-bg-yellow {
            background: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .icon-bg-red {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="row g-4 justify-content-center ">
        <!-- Usuarios -->
        <div class="col-md-6 col-lg-3">
            <div class="card card-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3">
                        <div>
                            <p class="text-dark mb-0 fw-semibold fs-14">Usuarios</p>
                            <h3 class="mt-2 mb-0 fw-bold">{{ $catntidad_usuarios ?? '15' }}</h3>
                        </div>
                        <div class="icon-bg icon-bg-blue">
                            <i class="fas fa-users h3 mb-0"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-primary" style="width: 75%;"></div>
                    </div>
                    <p class="text-muted small mt-2">Todos activos</p>
                </div>
            </div>
        </div>

        <!-- Carreras -->
        <div class="col-md-6 col-lg-3">
            <div class="card card-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3">
                        <div>
                            <p class="text-dark mb-0 fw-semibold fs-14">Carreras</p>
                            <h3 class="mt-2 mb-0 fw-bold">{{ $cantidad_carreras ?? '37' }}</h3>
                        </div>
                        <div class="icon-bg icon-bg-green">
                            <i class="fas fa-graduation-cap h3 mb-0"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-success" style="width: 60%;"></div>
                    </div>
                    <p class="text-muted small mt-2">Nuevas 3 este año</p>
                </div>
            </div>
        </div>

        <!-- Sedes -->
        <div class="col-md-6 col-lg-3">
            <div class="card card-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3">
                        <div>
                            <p class="text-dark mb-0 fw-semibold fs-14">Sedes</p>
                            <h3 class="mt-2 mb-0 fw-bold">{{ $cantidad_sedes ?? '13' }}</h3>
                        </div>
                        <div class="icon-bg icon-bg-yellow">
                            <i class="fas fa-school h3 mb-0"></i>
                        </div>
                    </div>
                    <div class="progress mt-3" style="height: 5px;">
                        <div class="progress-bar bg-warning" style="width: 85%;"></div>
                    </div>
                    <p class="text-muted small mt-2">Todas activas</p>
                </div>
            </div>
        </div>

        <!-- Última noticia -->
        <div class="col-md-6 col-lg-3">
            <div class="card card-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3">
                        <div>
                            <p class="text-dark mb-0 fw-semibold fs-14">Última Noticia</p>
                            <h6 class="mt-2 mb-0 fw-bold text-truncate" style="max-width: 160px;">
                                {{ $ultima_noticia->titulo ?? 'Sin noticias' }}
                            </h6>
                        </div>
                        <div class="icon-bg icon-bg-red">
                            <i class="fas fa-newspaper h3 mb-0"></i>
                        </div>
                    </div>
                    <p class="text-muted small mt-3">
                        
                        {{ $ultima_noticia ? $ultima_noticia->created_at : '--/--/----' }}
                    </p>
                </div>
            </div>
        </div>
    </div>



    <div class="row mt-1 justify-content-center">

        <!-- Estudiantes por carrera y sede -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 text-center">
                        <i class="fas fa-users me-2 text-primary"></i>
                        Estudiantes por Carrera y Sede
                    </h5>
                    <canvas id="estudiantesStackedChart" class="" height="110"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 text-center">
                        <i class="fas fa-award me-2 text-warning"></i>
                        Tabla comparativa de titulados por mes
                    </h5>
                    <canvas id="titulacionesChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 text-center">
                        <i class="fas fa-chart-line me-2 text-success"></i>
                        Crecimiento de Estudiantes por Año
                    </h5>
                    <canvas id="crecimientoEstudiantesChart" height="120"></canvas>
                </div>
            </div>
        </div>




    </div>



@endsection



@section('scripts')

    <script>
        const ctxCrecimiento = document.getElementById('crecimientoEstudiantesChart').getContext('2d');

        // Datos dinámicos desde Laravel
        const anios = @json($anios_crecimiento); // [2023, 2025]
        const cantidadEstudiantes = @json($totales_crecimiento); // [68236, 1398]

        const minY = Math.min(...cantidadEstudiantes) - 50; // margen más grande
        const maxY = Math.max(...cantidadEstudiantes) + 50;

        const miGrafico = new Chart(ctxCrecimiento, {
            type: 'line',
            data: {
                labels: anios,
                datasets: [{
                    label: 'Cantidad de estudiantes',
                    data: cantidadEstudiantes,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(0,0,255,0.1)',
                    tension: 0.2, // suaviza la curva
                    fill: true,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        enabled: true
                    }
                },
                scales: {
                    y: {
                        min: minY,
                        max: maxY
                        // stepSize se puede omitir para que Chart.js lo calcule automáticamente
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Años'
                        }
                    }
                }
            }
        });
    </script>


    <script>
        const meses = @json($fechas_colacion); // ["mayo", "enero", ...]
        const totales = @json($totales); // [6000, 5000, ...]

        // Generar colores dinámicos para cada barra
        const colors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(199, 199, 199, 0.7)',
            'rgba(83, 102, 255, 0.7)',
            'rgba(255, 99, 255, 0.7)',
            'rgba(99, 255, 132, 0.7)',
            'rgba(54, 162, 100, 0.7)',
            'rgba(200, 159, 64, 0.7)'
        ];

        const ctxTitulaciones = document.getElementById('titulacionesChart').getContext('2d');

        new Chart(ctxTitulaciones, {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Total titulados',
                    data: totales,
                    backgroundColor: colors.slice(0, totales.length),
                    borderColor: colors.slice(0, totales.length).map(c => c.replace('0.7', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + ' titulaciones';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        // Reduce el ancho de las barras aunque haya pocas
                        barPercentage: 0.5, // % del espacio de cada categoría
                        categoryPercentage: 0.5, // % de cada barra dentro de la categoría
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>




    <script>
        // Datos desde Laravel
        const sedeCarrera = @json($sede_carrera);
        console.log(sedeCarrera);

        const ctx = document.getElementById('estudiantesStackedChart').getContext('2d');

        // Colores por sede (puedes agregar más si tienes más sedes)
        const colores = [
            '#7FCFC0', '#F49090', '#F6D26A', '#A77ACF', '#FFA07A',
            '#20B2AA', '#FFB6C1', '#87CEFA', '#FFD700', '#8FBC8F'
        ];

        // Asignar un color a cada sede
        sedeCarrera.datasets.forEach((ds, index) => {
            ds.backgroundColor = colores[index % colores.length]; // un color por sede
        });

        // Crear gráfico stacked bar
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: sedeCarrera.labels, // Carreras
                datasets: sedeCarrera.datasets
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + ' estudiantes';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Carreras'
                        },
                        ticks: {
                            maxRotation: 90,
                            minRotation: 45
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de estudiantes'
                        }
                    }
                }
            }
        });
    </script>


@endsection
