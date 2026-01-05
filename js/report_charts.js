document.addEventListener('DOMContentLoaded', function () {
    // Pengaturan default untuk semua grafik agar ukurannya seragam
    const commonOptions = (title) => ({
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: title,
                font: { size: 14, weight: 'bold' },
                color: '#1e293b'
            },
            legend: {
                position: 'bottom',
                labels: { boxWidth: 10, font: { size: 11 } }
            }
        }
    });

    // 1. Bar Chart: Arus Masuk/Keluar
    const barEl = document.getElementById('barChartStats');
    if (barEl) {
        new Chart(barEl, {
            type: 'bar',
            data: {
                labels: JSON.parse(barEl.dataset.labels),
                datasets: [
                    { label: 'Masuk', data: JSON.parse(barEl.dataset.masuk), backgroundColor: '#10b981' },
                    { label: 'Keluar', data: JSON.parse(barEl.dataset.keluar), backgroundColor: '#ef4444' }
                ]
            },
            options: commonOptions(barEl.dataset.title)
        });
    }

    // 2. Ubah dari Doughnut/Bar ke Line Chart
    const doughEl = document.getElementById('doughnutChartActive');
    if (doughEl) {
        new Chart(doughEl, {
            type: 'line', // <--- UBAH ke 'line'
            data: {
                labels: JSON.parse(doughEl.dataset.labels),
                datasets: [{
                    label: 'Jumlah Orang Aktif',
                    data: JSON.parse(doughEl.dataset.aktif),
                    fill: true, // Memberikan efek area di bawah garis
                    backgroundColor: 'rgba(99, 102, 241, 0.2)', // Warna bayangan (transparan)
                    borderColor: '#6366f1', // Warna garis utama
                    borderWidth: 3,
                    tension: 0.4, // Membuat garis menjadi melengkung (smooth)
                    pointBackgroundColor: '#6366f1',
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                ...commonOptions(doughEl.dataset.title),
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        },
                        grid: {
                            display: true,
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // 3. Pie Chart: Kendaraan
    const pieVEl = document.getElementById('pieChartVehicle');
    if (pieVEl) {
        new Chart(pieVEl, {
            type: 'pie',
            data: {
                labels: JSON.parse(pieVEl.dataset.labels),
                datasets: [{
                    data: JSON.parse(pieVEl.dataset.values),
                    backgroundColor: ['#3b82f6', '#f59e0b', '#64748b']
                }]
            },
            options: commonOptions(pieVEl.dataset.title)
        });
    }

    // 4. Pie Chart: Civitas (Mhs vs Dosen)
    const pieCEl = document.getElementById('pieChartCivitas');
    if (pieCEl) {
        new Chart(pieCEl, {
            type: 'pie',
            data: {
                labels: JSON.parse(pieCEl.dataset.labels),
                datasets: [{
                    data: JSON.parse(pieCEl.dataset.values),
                    backgroundColor: ['#4f46e5', '#f43f5e']
                }]
            },
            options: commonOptions(pieCEl.dataset.title)
        });
    }


    // 5. Line Chart: Laporan Barang Hilang & Ditemukan
    const barangEl = document.getElementById('lineChartBarang');
    if (barangEl) {
        new Chart(barangEl, {
            type: 'line',
            data: {
                labels: JSON.parse(barangEl.dataset.labels),
                datasets: [
                    {
                        label: 'Hilang',
                        data: JSON.parse(barangEl.dataset.hilang),
                        borderColor: '#ef4444', // Merah
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4
                    },
                    {
                        label: 'Ditemukan',
                        data: JSON.parse(barangEl.dataset.ditemukan),
                        borderColor: '#3b82f6', // Biru
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                ...commonOptions(barangEl.dataset.title),
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
});