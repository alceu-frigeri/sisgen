<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <!--meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" /-->
    <title>SYROCO 2015</title>
</head>
  <body>

<?php

    echo getcwd() . "\n"; 

    foreach ($_FILES["docs"]["error"] as $key => $error)
    {
        if ($error == UPLOAD_ERR_OK)
        {
            $tmp_name = $_FILES["docs"]["tmp_name"][$key];
            $name = $_FILES["docs"]["name"][$key];
            $timestamp=time();
            if(move_uploaded_file($tmp_name, '/export/var/www/sbai17/uploads/'.$timestamp."-".$name))
            {
                    echo "File $name was successfully uploaded.<br/>";
            }
            else
            {
                echo "File $name WAS NOT sucessfully uploaded!<br/>";
            }
        }
    }
?>
    <p>
    <strong>If all three files were successfully uploaded, your registration is concluded.</strong><br/>
    </p>
    
    <p>If you have papers accepted for SYROCO 2015, you will receive the vouchers for submission of the final version by e-mail as soon as we process you credit card data.</p>

</body>
</html>
                                
