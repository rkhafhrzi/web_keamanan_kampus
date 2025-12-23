<!DOCTYPE html>
<html lang="en">

<head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <script>
        
            tailwind.config = {

                  content: [
                        "./*.{html,js,php}",
                        "./**/*.{html,js,php}",
                  ],

                  theme: {
                        extend: {
                              transitionProperty: {
                                    'transform': 'transform',
                              },
                        }
                  }
            };
      </script>

      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
      <script src="js/report-logic.js"></script>
</body>

</html>