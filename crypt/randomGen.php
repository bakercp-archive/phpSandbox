<?php
/*
This bit of PHP reads the Linux server's /dev/random stream and formats
the output to hexadecimal.  It might work on *BSD servers and Mac OSX
servers too but not Windows servers.

(c) 2003 Jason Hecker

*/

  if(($fhandle = fopen('/dev/random','rb')) != FALSE)
  {
    set_magic_quotes_runtime(0);

    for($i=0;$i<32;$i++)
    {
      $val = ord(fgetc($fhandle));
      if($val <= 0x0f)
      {
        $rb = sprintf("0%X",$val);
      }
      else
      {
        $rb = sprintf("%X",$val);
      }

      if($i < 5){$rs40 .= $rb;}
      if($i < 8){$rs64 .= $rb;}
      if($i < 13){$rs104 .= $rb;}
      if($i < 16){$rs128 .= $rb;}
      if($i < 19){$rs152 .= $rb;}
      if($i < 29){$rs232 .= $rb;}
      $rs256 .= "$rb";
    }
   echo "<H3>40 Bit Key (5 bytes)</H3>";
   echo "<tt>$rs40\n</tt>";
   echo "<H3>64 Bit Key (8 bytes)</H3>";
   echo "<tt>$rs64\n</tt>";
   echo "<H3>104 Bit Key (13 bytes)</H3>";
   echo "<tt>$rs104\n</tt>";
   echo "<H3>128 Bit Key (16 bytes)</H3>";
   echo "<tt>$rs128\n</tt>";
   echo "<H3>152 Bit Key (19 bytes)</H3>";
   echo "<tt>$rs152\n</tt>";
   echo "<H3>232 Bit Key (29 bytes)</H3>";
   echo "<tt>$rs232\n</tt>";
   echo "<H3>256 Bit Key (32 bytes)</H3>";
   echo "<tt>$rs256\n</tt>";

   fclose($fhandle);
  }
?>

