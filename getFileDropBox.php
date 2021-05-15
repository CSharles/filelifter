<?php
if (isset($_FILES['file'])) {
    if ( 0 < $_FILES['file']['error'] ) {
        echo 'Error: ' . $_FILES['file']['error'] . '<br>';
    }
    else {
    }

    //usando curl
    //subir el archivo primero
        $pathlocal = $_FILES['file']['tmp_name'];
        $name=$_FILES['file']['name'];
        $fp = fopen($pathlocal, 'rb');
        $size = filesize($pathlocal);

        $cheaders = array('Authorization: Bearer [TOKEN]',
                        'Content-Type: application/octet-stream',
                        'Dropbox-API-Arg: {"path":"/fromWeb/'.$name.'", "mode":"add","autorename":true}');

        $ch = curl_init('https://content.dropboxapi.com/2/files/upload');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cheaders);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_INFILE, $fp);
        curl_setopt($ch, CURLOPT_INFILESIZE, $size);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        
        $deco=json_decode($response, true);
        $phathDropBox=$deco['path_display'];

         //creando el enlace compartido
        $parameters = array('path' => $phathDropBox);

        $headers = array('Authorization: Bearer [TOKEN]',
                        'Content-Type: application/json');

        $curlOptions = array(
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($parameters),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_VERBOSE => true
            );

        $ch = curl_init('https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings');
        curl_setopt_array($ch, $curlOptions);

        $response = curl_exec($ch);
        curl_close($ch);
//cambiamos el 0 del final de la url por un 1 para crear el enlace directo y poderlo usar en donde lo deseemos
        $deco=json_decode($response,true);
        $imgurldl=substr($deco['url'], 0, -1);
        $imgurl=$imgurldl."1";

}
   
   
   ?>
   <!DOCTYPE html>
   <html lang="es-SV">
   <head>
       <meta charset="UTF-8">
       <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Suibr a dropbox</title>
   </head>
   <body>
       <form action="<?php echo  htmlspecialchars($_SERVER['PHP_SELF'])?>" method="post" enctype="multipart/form-data">
       la imagen: <input name="file" type="file" />
       <input type="submit" value="llevame a Dropbox">
       </form>
       <?php if($imgurl ?? false): ?>
        <p>Ya esta en drop box</p>
        <div>
            <img src="<?php echo ($imgurl) ?>" alt="">
        </div>
       <?php endif; ?>
   </body>
   </html>
