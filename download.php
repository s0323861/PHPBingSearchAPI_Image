<?php

$zipfile = "./image.zip";

$urls = $_POST["url"];

foreach($urls as $url){

  // confirm whether the links exist
  $header = @get_headers($url);
  if(preg_match('/^HTTP¥/.*¥s+200¥s/i', $header[0])){

    // extract the extension
    $extension = pathinfo($filename, PATHINFO_EXTENSION);

    if($extension == "jpg" or $extension == "png" or $extension == "gif"){
      $data = file_get_contents($url);

      // ファイル名を抽出
      $filename = basename($url);

      // 新規にファイルを作成
      file_put_contents($filename, $data);

      // 圧縮するファイルの配列
      $files[] = $filename;

    }

  }

}

$zip = new ZipArchive();
$res = $zip->open($zipfile, ZipArchive::CREATE);

if($res === true){
    foreach($files as $file){
        $zip->addFile($file);
    }
    $zip->close();
} else {
    echo 'Error Code: ' . $res;
    exit();
}

// 作成したzipファイルをダウンロード
header('Content-Type: application/octet-stream'); 
header(sprintf('Content-Disposition: attachment; filename="%s"', basename($zipfile)) ); 
header(sprintf('Content-Length: %d', filesize($zipfile)) );
readfile($zipfile);

// zipファイルを削除
unlink($zipfile);

// 画像を削除
foreach($files as $imagefile){
  unlink($imagefile);
}

exit();
?>
