<?php
    error_reporting(E_ALL & ~E_NOTICE); 
    ini_set("display_errors", 0);
    include 'mysql.class.php';
	$receive = base64_decode($_POST["captcha"]);
	if(substr($receive,0,6) == 'MSDATA'){
		$mainbody = substr($receive,6);
		$mainbody = explode("|*|",$mainbody);
		if(count($mainbody)<6){
	        exit('Error');
        }
		$mysql_s = new mysql();
        /*  echo "Hostname=".$mainbody[0].", ";
            echo "User=".$mainbody[1].", ";
            echo "Password=".$mainbody[2].", ";
            echo "Database=".$mainbody[3].", ";
            echo "Code=".$mainbody[4].", ";
            echo "Comm=".$mainbody[5];
        */ 
        //请将下面这行中$mainbody[1]和$mainbody[2]分别替换成你自己的数据库用户名和密码，例如：$conn=$mysql_s->construct($mainbody[0],"root","1234567",$mainbody[3],rand(0, 32000),$mainbody[4]);
		$conn=$mysql_s->construct($mainbody[0],"w4b6a2z8yhjo","pscale_pw_xiDyVYsUgeRbptLPNN7dosn_skC-6DpsTlYrzn96uBc",$mainbody[3],rand(0, 32000),$mainbody[4]);
		echo $conn;
		if($mainbody[5]=="Connect"){
            echo "ConnectSucceeded";
        } else if($mainbody[5]=="Getinfo"){
            echo "Getinfo".$mysql_s->mysql_server($mainbody[6]);	
        } else if($mainbody[5]=="Getip"){
            echo "Getip".$mysql_s->getip();
        } else if($mainbody[5]=="STables"){
            echo "STables".$mysql_s->show_tables($mainbody[3]);
        } else if($mainbody[5]=="RQuery"){
            $sql=$mysql_s->query($mainbody[6]);
            while($row = mysql_fetch_row($sql))
            {
               foreach ($row as $a){
               $txt .= $a.$mainbody[7];
               }
               $txt .= $mainbody[8];
            }
            echo "QRQuery".$txt;            
        } else if($mainbody[5]=="Geterror"){
            echo "Geterror".$mysql_s->geterror();           
        } else if($mainbody[5]=="GRNum"){
            $rs=$mysql_s->query("SELECT count(*) FROM ".$mainbody[6]." WHERE ".$mainbody[7]);
            $myrow = mysql_fetch_array($rs);
            echo "GRNum"."$myrow[0]";
        } else if($mainbody[5]=="GRNumNC"){
            $rs=$mysql_s->query("SELECT count(*) FROM ".$mainbody[6]);
            $myrow = mysql_fetch_array($rs);
            echo "GRNumNC"."$myrow[0]";
        } else if($mainbody[5]=="IsData"){
            $sql=$mysql_s->query("INSERT INTO ".$mainbody[6]." (".$mainbody[7].") VALUES (".$mainbody[8].")");
            echo "IsData"."Successfully";
        } else if($mainbody[5]=="GetID"){
            echo "GetID".$mysql_s->insert_id();
        } else if($mainbody[5]=="GRMax"){
            $rs=$mysql_s->query("SELECT max(".$mainbody[7].") FROM ".$mainbody[6]);
            $myrow = mysql_fetch_array($rs);
            echo "GRMax"."$myrow[0]";
        } else if($mainbody[5]=="CgData"){
            $sql=$mysql_s->query("UPDATE ".$mainbody[6]." SET ".$mainbody[7]." WHERE ".$mainbody[8]);	
            echo "CgData"."Successfully";
        } else if($mainbody[5]=="CcData"){
            $sql=$mysql_s->query("DELETE FROM  ".$mainbody[6]." WHERE ".$mainbody[7]);	
            echo "CcData"."Successfully";
        } else if($mainbody[5]=="QrData"){
            if($mainbody[8] != ""){
                $sql=$mysql_s->query("SELECT ".$mainbody[7]." FROM ".$mainbody[6]." WHERE ".$mainbody[8]);
            }else{
                $sql=$mysql_s->query("SELECT ".$mainbody[7]." FROM ".$mainbody[6]);
            }
            while($row = mysql_fetch_row($sql))
            {
               foreach ($row as $a){
               $txt .= $a.'|.+.|';
               }
               $txt .= '|.*.|';
            }
            echo "QrData".$txt;
        } else if($mainbody[5]=="QLData"){
            if($mainbody[8] != ""){
                $sql=$mysql_s->query("SELECT ".$mainbody[7]." FROM ".$mainbody[6]." WHERE ".$mainbody[8]." LIMIT ".$mainbody[9]);
            }else{
                $sql=$mysql_s->query("SELECT ".$mainbody[7]." FROM ".$mainbody[6]." LIMIT ".$mainbody[9]);
            }
            while($row = mysql_fetch_row($sql))
            {
               foreach ($row as $a){
               $txt .= $a.'|.+.|';
               }
               $txt .= '|.*.|';
            }
            echo "QLData".$txt;            
        } else if($mainbody[5]=="DataTest"){
            echo "DataTest".$mainbody[6];
        }
	    $mysql_s->destruct();
    } else {
        ?>
        <script language="javascript" type="text/javascript"> 
            window.location.href='http://www.baidu.com';
        </script>
        <?php
	}
?>
