<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wykres</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- gotowa biblioteka -->

    <style>
        canvas{
            width: 600px !important;
            height: 400px !important;
        }
    </style>

</head>
<body>

    <canvas id="myChart"></canvas>

    <script>

        async function getData(){
            const response = await fetch('data.php');
            const data = await response.json();
            return data;
        }

        getData().then(data => {
            const ctx = document.getElementById('myChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)', // kolor tła
                        borderColor: 'rgba(75, 192, 192, 1)', // kolor borderu
                        borderWidth: 3,
                        fill: true // zamalowanie dolnej częsci wykresu
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        tooltip: {
                            enabled: false
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
    
</body>
</html>