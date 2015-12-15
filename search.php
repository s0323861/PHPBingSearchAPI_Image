<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="author" content="Akira Mukai">
<title>Image Search & Download - 画像一括ダウンローダー</title>
  <link rel="shortcut icon" href="./favicon.ico">
  <link rel="stylesheet" href="./css/bootstrap.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="./css/bootstrap-switch.min.css">
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

<div class="navbar navbar-default navbar-fixed-top">
  <div class="container">
    <div class="navbar-header">
    <a href="./" class="navbar-brand"><i class="fa fa-windows"></i> Bing Search</a>
    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    </div>
    <div class="navbar-collapse collapse" id="navbar-main">
    <form class="navbar-form navbar-left" role="search">
    <div class="form-group">
    <input type="text" class="form-control" placeholder="Search" value="<?php echo $_POST['query']; ?>">
    </div>
    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> GO!</button>
    </form>
    <ul class="nav navbar-nav navbar-right">
    <li><a href="#" id="btn_dl"><i class="fa fa-download"></i> Download</a></li>
    </ul>
    </div>
  </div>
</div>
</header>

<div class="container">

  <form method="post" action="download.php" id="downloadForm">

    <div class="row">

      <div class="article">

<?php

include("configs.php");

$acctKey = $CONFIGS['AccountKey'];

$adult = $CONFIGS['Adult'];

$rootUri = 'https://api.datamarket.azure.com/Bing/Search';

// ページ番号の取得
$page = $_POST['page'];
if(empty($page)){
  $page = 1;
}

// オフセットの設定
$offset = ($page - 1) * 50;

if ($_POST['query'])
{
  // Here is where you'll process the query. 
  // The rest of the code samples in this tutorial are inside this conditional block.

  // Encode the query and the single quotes that must surround it.
  $query = urlencode("'{$_POST['query']}'");

  // Get the selected service operation (Web or Image).
  //$serviceOp = $_POST['service_op'];
  $serviceOp = "Image";

  // Construct the full URI for the query.
  $requestUri = "$rootUri/$serviceOp?\$format=json&Query=$query&\$skip=$offset&Adult='{$adult}'";

  // Encode the credentials and create the stream context.
  $auth = base64_encode("$acctKey:$acctKey");
  $data = array(
    'http' => array(
      'request_fulluri' => true,
      // ignore_errors can help debug – remove for production. This option added in PHP 5.2.10
      'ignore_errors' => true,
      'header'  => "Authorization: Basic $auth")
    );
  $context = stream_context_create($data);

  // Get the response from Bing.
  $response = file_get_contents($requestUri, 0, $context);

  // Decode the response.
  $jsonObj = json_decode($response);

  $resultStr = '';

  // Parse each result according to its metadata type.
  foreach($jsonObj->d->results as $value)
  {

    $bytes = formatBytes($value->FileSize);

$resultStr .= <<< EOM
<div class="col-sm-6 col-md-4">
  <div class="thumbnail">
    <h4 class="text-center"><span class="label label-primary">{$value->Width}x{$value->Height}</span></h4>
    <img src="{$value->Thumbnail->MediaUrl}" alt="{$value->Title}" class="img-responsive">
    <div class="caption">
      <div class="text-center"><input type="checkbox" name="url[]" id="switch" value="{$value->MediaUrl}"></div>
      <div class="row">
        <div class="col-md-6 col-xs-6">
          <h3>{$value->Title}</h3>
        </div>
        <div class="col-md-6 col-xs-6">
          <h3><label>{$bytes}</label></h3>
        </div>
      </div>
      <p>{$tag}</p>
      <div class="row">
        <div class="col-md-6">
          <a class="btn btn-primary btn-product" href="{$value->SourceUrl}" target="_blank"><i class="fa fa-external-link"></i> Link</a>
        </div>
        <div class="col-md-6">
          <a class="btn btn-success btn-product" href="{$value->MediaUrl}" target="_blank"><i class="fa fa-search-plus"></i> Enlarge</a>
        </div>
      </div>
    </div>
  </div>
</div>
EOM;

  }

  /**
   * count this month's transactions
   */
  // read the file
  $lines = file('./counter.csv', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  $flg = 1;
  foreach ($lines as $line) {
    $thismonth = date('Ym');

    $line_array = explode(",", $line);

    if($line_array[0] == $thismonth){
      $added = $line_array[1] + 1;
      $line = $thismonth . "," . $added;
      $flg = 0;
    }

    $modified[] = $line . "\n";

  }
  if($flg){
    $modified[] = $thismonth . "," . "0" . "\n";
  }

  // overwrite the file
  file_put_contents( './counter.csv', $modified );

}

echo $resultStr;

/**
 * バイト数をフォーマットする
 * @param integer $bytes
 * @param integer $precision
 * @param array $units
 */
function formatBytes($bytes, $precision = 2, array $units = null)
{
  if ( abs($bytes) < 1024 )
  {
    $precision = 0;
  }

  if ( is_array($units) === false )
  {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
  }

  if ( $bytes < 0 )
  {
    $sign = '-';
    $bytes = abs($bytes);
  }
  else
  {
    $sign = '';
  }

  $exp   = floor(log($bytes) / log(1024));
  $unit  = $units[$exp];
  $bytes = $bytes / pow(1024, floor($exp));
  $bytes = sprintf('%.'.$precision.'f', $bytes);
  return $sign.$bytes.' '.$unit;
}

?>

    </div>
    <!-- /.article -->

  </div>

  <div class="row">

    <div class="navigation text-center">
      <a class="btn btn-primary btn-sm" href="search.php?page=<?php echo $page+1; ?>&query=<?php echo urlencode($_POST['query']); ?>"><span class="glyphicon glyphicon-refresh"></span> Read more</a>
    </div>

  </div>

  </form>

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
<script src="./js/bootstrap-switch.min.js"></script>
<script src="./js/jquery.infinitescroll.min.js"></script>

<script>
$(function(){

  $("[name='url[]']").bootstrapSwitch({
    size: 'mini'
  });

  $('#content').infinitescroll({
    navSelector  : ".navigation",
    nextSelector : ".navigation a",
    itemSelector : ".article"
  },function(newElements) {
      $(newElements).hide().delay(100).fadeIn(600);
      $(".navigation").appendTo("#content").delay(300).fadeIn(600);
  });

  $('#content').infinitescroll('unbind');
  $(".navigation a").click(function(){
    $('#content').infinitescroll('retrieve');
    return false;
  });

  $('#btn_dl').click(function(){
    var checkeditem = $('#switch :checked').length;
    if(checkeditem > 0) {
      $('#downloadForm').submit();
    }else{
      alert("None of items are selected.");
    }
  });

});

</script>
</body>
</html>
