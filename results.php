<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Electronic Voting System</title>

    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>
    <nav class="navbar navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.html">Electronic Voting System</a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="index.html">Vote</a></li>
                <li class="active"><a href="results.php">Results</a></li>
            </ul>
        </div>
      </div>
    </nav>
    <div id="content" class="container">
        <h1>Results</h1>

        <?php
        include('db-config.php');

        $dsn = "mysql:host=" . $dbhost . ";dbname=" . $dbname . ";charset=utf8";
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $pdo = new PDO($dsn, $dbuser, $dbpass, $opt);

        $query = $pdo->prepare("SELECT * FROM parties");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        $parties = array();
        foreach ($result as $row) {
            $parties[$row['hash_id']] = $row['name'];
        }

        $query = $pdo->prepare("SELECT * FROM votes");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($query->rowCount() > 0): ?>
            <table class="table">
                <thead>
                    <th>
                        Entry id
                    </th>
                    <th>
                        Vote id
                    </th>
                    <th>
                        Party
                    </th>
                </thead>

            <?php foreach ($result as $row): ?>
                <tr>
                    <td>
                        <?= $row['id']; ?>
                    </td>
                    <td>
                        <?= $row['vote_id']; ?>
                    </td>
                    <td>
                        <?= $parties[$row['party_hash']]; ?>
                    </td>
                </tr>
            <?php endforeach;
            echo '</table>';
        else:
            echo "No votes yet...";
        endif;
        ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
  </body>

</html>
