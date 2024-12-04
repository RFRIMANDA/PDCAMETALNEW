<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Error 404 - Page Not Found</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #4facfe, #00f2fe);
      color: #fff;
      font-family: 'Arial', sans-serif;
      height: 100vh;
      margin: 0;
      overflow: hidden;
    }

    h1, h2, .btn {
    position: relative;
    z-index: 1; /* Berada di atas error-animation */
    }

    .error-section {
    position: relative; /* Pastikan elemen ini menjadi konteks untuk z-index */
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100vh;
    text-align: center;
    }

    .error-section h1 {
      font-size: 10rem;
      font-weight: bold;
      margin: 0;
      text-shadow: 0 4px 6px rgba(0, 0, 0, 0.4);
      animation: float 3s ease-in-out infinite;
    }

    .error-section h2 {
      font-size: 2rem;
      margin-top: 1rem;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .btn-custom {
      margin-top: 2rem;
      padding: 10px 30px;
      font-size: 1rem;
      border-radius: 30px;
      background: #fff;
      color: #4facfe;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s ease-in-out;
    }

    .btn-custom:hover {
      background: #4facfe;
      color: #fff;
      transform: scale(1.1);
    }

    .error-animation {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('https://media.giphy.com/media/l1J3preURPiwjRPvK/giphy.gif') center center no-repeat;
    background-size: cover;
    opacity: 0.2;
    z-index: 0; /* Berada di belakang elemen lain */
    }

    @keyframes float {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-10px);
      }
    }
  </style>
</head>

<body>
  <div class="error-section">
    <div class="error-animation"></div>
    <h1>404</h1>
    <h2>Oops! The page you're looking for can't be found.</h2>

    <a class="btn btn-primary" href="javascript:history.back()">Go Back</a>
  </div>
</body>

</html>
