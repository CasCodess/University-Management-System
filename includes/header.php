<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>University Management System</title>

    <!-- 1. Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- 2. FontAwesome for Icons (Required for Dashboard icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- 3. Google Fonts (Inter - Modern & Clean) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- 4. Your Custom Global Stylesheet -->
    <!-- Note: Path adjusted to point to the css folder you mentioned -->
    <link rel="stylesheet" href="../css/style.css">

    <!-- 5. Chart.js for Analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        /* Optional: Smooth loading transition */
        body {
            opacity: 0;
            transition: opacity 0.4s ease-in-out;
        }
        body.loaded {
            opacity: 1;
        }
    </style>
</head>
<body onload="document.body.classList.add('loaded')">