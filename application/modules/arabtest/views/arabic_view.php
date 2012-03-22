<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="ar">
  <head>
      <title>test the scripts</title>
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  </head>
  <body>
          <?php
              echo 'time() function called directly on the the view: ' . time() ;
           ?>
      <br />
  
          <?php 
              echo 'time() function from the controller, passed through Khaled\'s libraries:  ' . $hdate; 
          ?>
      <br />
          <?php 
              echo 'Transliteration, passed through Khaled\'s libraries:  ' . $translit; 
          ?>
  
  </body>
</html>