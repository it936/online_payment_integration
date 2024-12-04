<!DOCTYPE html>
<html>
<head>
    <title>CCAvenue Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
    <center>
        <h2>Redirecting to CCAvenue Payment Gateway</h2>
        
        <div class="spinner-grow text-primary" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-secondary" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-success" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-danger" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-warning" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-info" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-light" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
<div class="spinner-grow text-dark" role="status">
  <span class="visually-hidden">Loading...</span>
</div>
        
        <form id="ccavenuePaymentForm" method="POST" action="{{ $production_url }}">
            
            <!-- Add any required hidden fields here -->
            <input type="hidden" name="encRequest" value="{{ $encrypted_data }}">
            <input type="hidden" name="access_code" value="{{ $access_code }}">
            </form>
            <noscript>
                <p>
                    JavaScript is disabled in your browser. Please click the button below to proceed.
                </p>
                <button type="submit">Proceed to Payment</button>
        
            </noscript>    
            
    </center>


    <script type="text/javascript">
         
        
          
  //  await sleep(3000); 
    document.getElementById('ccavenuePaymentForm').submit();
                   
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    </script>
</body>
</html>
