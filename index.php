<?php

// count this month's transactions
$lines = file('./counter.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
  $thismonth = date('Ym');

  $line_array = explode(",", $line);

  if($line_array[0] == $thismonth){
    $count = $line_array[1];
    $percentage = ($count / 5000) *100;
  }

}
if(empty($count)){
  $count = 0;
  $percentage = 0;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="This is an application using the Bing Search API to search images and download them collectively. Bing Search APIを利用して画像をダウンロードするツールです。">
<meta name="keywords" content="image,download,一括ダウンロード">
<meta name="author" content="Akira Mukai">
<title>Image Search & Download - 画像一括ダウンローダー</title>
  <link rel="shortcut icon" href="./favicon.ico">
  <link rel="stylesheet" type="text/css" href="./css/bootstrap.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <style type="text/css">
  body { padding-top: 80px; }
  @media ( min-width: 768px ) {
    #banner {
      min-height: 300px;
      border-bottom: none;
    }
    .bs-docs-section {
      margin-top: 8em;
    }
    .bs-component {
      position: relative;
    }
    .bs-component .modal {
      position: relative;
      top: auto;
      right: auto;
      left: auto;
      bottom: auto;
      z-index: 1;
      display: block;
    }
    .bs-component .modal-dialog {
      width: 90%;
    }
    .bs-component .popover {
      position: relative;
      display: inline-block;
      width: 220px;
      margin: 20px;
    }
    .nav-tabs {
      margin-bottom: 15px;
    }
  }
  </style>

  <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

</head>
<body>

<div class="container">

  <div class="row">

    <!-- Blog Entries Column -->
    <div class="col-lg-12">

    <h1 class="text-center">
    <i class="fa fa-windows"></i> Bing Image Search
    </h1>

    <br><br><br>

    <p class="text-center">Please <a href="https://github.com/s0323861/image_search">report bugs and send feedback</a> on GitHub.</p>

    </div>

  </div>

  <!-- Forms
  ================================================== -->
  <div class="row">

    <div class="col-xs-8 col-xs-offset-2">

    <form method="POST" action="search.php">

      <div class="input-group">
        <input type="text" name="query" class="form-control" placeholder="Search for...">
        <span class="input-group-btn">
          <button class="btn btn-default" type="submit"><i class="fa fa-search"></i> Go!</button>
        </span>
      </div><!-- /input-group -->

    </form>

    </div>

  </div>

  <div class="row">
    <div class="col-lg-12">

      <br><br><br>

      <h3 class="text-center">今月のトランザクション数 (monthly transaction)</h3>
      <div class="bs-component">
        <div class="progress progress-striped">
          <div class="progress-bar progress-bar-success" aria-valuenow="<?php echo round($percentage); ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo round($percentage); ?>%"><?php echo round($percentage); ?>%</div>
        </div>
      </div>

    </div>
  </div>

  <hr>

  <!-- Footer -->
  <footer>
  <div class="row">
    <div class="col-lg-12">
    <p>Developed by Akira Mukai 2015</p>
    </div>
    <!-- /.col-lg-12 -->
  </div>
  <!-- /.row -->
  </footer>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="./js/bootstrap.min.js"></script>

</body>
</html>
