document.addEventListener('DOMContentLoaded', function() {
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

    // 2. Doughnut Chart: Orang Aktif
    const doughEl = document.getElementById('doughnutChartActive');
    if (doughEl) {
        new Chart(doughEl, {
            type: 'doughnut',
            data: {
                labels: JSON.parse(doughEl.dataset.labels),
                datasets: [{
                    data: JSON.parse(doughEl.dataset.aktif),
                    backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899']
                }]
            },
            options: commonOptions(doughEl.dataset.title)
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
});