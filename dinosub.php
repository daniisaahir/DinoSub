<!DOCTYPE html>
<html>
<head>
  <title>DinoSub (PHP)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background-color: #222;
      color: #fff;
      font-family: 'Poppins', sans-serif;
      padding: 20px;
      margin: 0;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
    }

    h1 {
      color: #ff4d4d;
      text-align: center;
    }

    h2 {
      color: #ff4d4d;
      margin-top: 20px;
    }

    form {
      margin-top: 20px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    label {
      display: block;
      margin-bottom: 10px;
    }

    input[type="text"] {
      padding: 10px;
      font-size: 16px;
      border: none;
      background-color: #444;
      color: #fff;
      border-radius: 4px;
      margin-bottom: 10px;
      width: 100%;
    }

    input[type="submit"] {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #ff4d4d;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      width: 100%;
    }

    input[type="submit"]:hover {
      background-color: #e63946;
    }

    pre {
      background-color: #444;
      padding: 10px;
      white-space: pre-wrap;
    }

    .copy-button {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #ff4d4d;
      color: #fff;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      display: block;
      margin-top: 10px;
      width: 100%;
      text-align: center;
    }

    .copy-button:hover {
      background-color: #e63946;
    }

    .author-link {
      color: #ff4d4d;
      text-decoration: none;
    }

    @media (min-width: 768px) {
      body {
        padding: 40px;
      }

      h1 {
        font-size: 32px;
        margin-bottom: 30px;
      }

      .container {
        margin-top: 50px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>DinoSub (PHP)</h1>
    <p style="text-align: center;">
      Author: <a href="https://github.com/daniisaahir" class="author-link">https://github.com/daniisaahir</a>
    </p>
    <form method="POST" action="">
      <label for="domain">URL:</label>
      <input type="text" id="domain" name="domain" placeholder="example.com" required>
      <input type="submit" value="Submit">
    </form>

    <?php
    function getSubdomains($domain) {
      $url = 'https://crt.sh/?q=%.' . $domain . '&output=json';
      $response = file_get_contents($url);
      if ($response !== false) {
        $data = json_decode($response, true);
        $subdomains = array_column($data, 'name_value');
        return array_unique($subdomains);
      }
      return [];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (isset($_POST['domain'])) {
        $domain = $_POST['domain'];
        $subdomains = getSubdomains($domain);
        if (!empty($subdomains)) {
          echo "<h2>Results:</h2>";
          echo "<pre>";
          foreach ($subdomains as $subdomain) {
            echo htmlspecialchars($subdomain) . "\n";
          }
          echo "</pre>";
          echo '<button class="copy-button" onclick="copyResults()">Copy Results</button>';
        }
      }
    }
    ?>

    <script>
      function copyResults() {
        var results = document.querySelector('pre');
        var textarea = document.createElement('textarea');
        textarea.value = results.textContent;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Results copied to clipboard!');
      }
    </script>
  </div>

  <script>
    WebFontConfig = {
      google: { families: [ 'Poppins:400,600' ] }
    };
    (function(d) {
      var wf = d.createElement('script'), s = d.scripts[0];
      wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
      wf.async = true;
      s.parentNode.insertBefore(wf, s);
    })(document);
  </script>
</body>
</html>
