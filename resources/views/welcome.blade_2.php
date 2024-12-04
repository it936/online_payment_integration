<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centered Content</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .center-content {
            display: grid;
            place-items: center; /* Centers both horizontally and vertically */
            height: 50vh; /* Make the div take up the full height of the viewport */
            text-align: center; /* Center text inside the div */
        }
        
        .center-image {
            display: flex;
            justify-content: center; /* Align horizontally */
            align-items: center; /* Align vertically */
            height: 50vh; /* Full viewport height */
        }
        .center-image img {
            max-width: 50%; /* Ensure image doesn't overflow */
            max-height: 50%; /* Ensure image stays within the viewport */
            object-fit: contain; /* Keep aspect ratio intact */
        }
    
    </style>
</head>
<body>

    <div class="center-content">
          <div class="center-image">
        <img src="https://ssus.ac.in/images/new4.png" alt="Image from external link">
    </div>
        <div>
           
            <h1>Welcome to SSUS Online Payment Portal!</h1>
            <p>Explore our amazing features for online payments.</p>
            <a href="payform" class="btn btn-primary btn-lg">Learn More</a>
        </div>
    </div>

    <!-- Other Content -->
    
    <!-- Bootstrap JS (optional, for interactive components) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
