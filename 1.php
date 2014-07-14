<? session_start(); ?>
<? if (file_exists('pass.php')) include 'pass.php'; ?>
<?
/***************************************************************************
                             Unzipper, v1.3
 ***************************************************************************
    file:                index.php
    functionality:       Provides a shell wrapper for Vincent Blavet's PclZip module.

                         This application is helpful when there is a need to upload a
                         many files with complicated directory structure to web server,
                         for example, forum systems (like phpBB) or other applications
                         (like phpMyAdmin) which consists of many files arranged in complicated
                         directory structure. All you need to do is to upload the archive file
                         and PHP Unzipper will take care of creating the correct directory layot
                         and file extraction. This program is especially helpful when you don't
                         have FTP access to webserver but generally it will be helpful in all cases
                         when there is a need to upload many small files to webserver.
                         
    begin                14.07.2003
    last edited          12.03.2006
    copyright            (C) 2006 SagaTec
    email                market@cmsware.com

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU Lesser General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/
?>

<html>
<head><title>CMSware.com_���߽�ѹ</title>
</head>

<style type="text/css">
 A {text-decoration:none;}
 A:hover {text-decoration:underline;}
 body {
        background-color: #00CCDD;
      }
 
 .dirlist {
        display:table-cell;
        padding-right:40px;
        margin-top:10px;
        width:40%;
        }

 .filelist {
        display:table-cell;
        width:40%;
        text-align:left;
        }

 .filedirtitle {
        background-color: #7CFFCC;
        border: 1px #006699 solid;
        text-align:center;
        }

 .bigblock {
        display:table-cell;
        padding:5px;
        }

 .contents, .unzip {
        border: #FFFFFF 1px solid;
        padding:5px;
        height:auto;
        width:100%;
        position:relative;
        font-family:Tahoma;
        font-size:13px;
        text-align:left;
        }
        
 .unzip {
        text-align:left;
        margin-bottom: 20px;
        background:#00DDDD;
        }
        
 .heads {
        color:#006060;
        position:relative;
        border: #00CCCC 3px dashed;
        font-family: Tahoma;
        font-size:20px;
        text-align:center;
        padding:5px;
        background:#00DDDD;
        margin-bottom:20px;
        width:100%;
        clear:left;
        display:block;
        }
 .logout {
        font-size:12px;
        }
        
</style>

<body
<div class=heads>
CMSware���߽�ѹ

<?
$docname = basename(getenv('script_name'));

function fileext ($file) {
$p = pathinfo($file);
return $p['extension'];
}

function convertsize($size){

$times = 0;
$comma = '.';
while ($size>1024){
$times++;
$size = $size/1024;
}
$size2 = floor($size);
$rest = $size - $size2;
$rest = $rest * 100;
$decimal = floor($rest);

$addsize = $decimal;
if ($decimal<10) {$addsize .= '0';};

if ($times == 0){$addsize=$size2;} else
 {$addsize=$size2.$comma.substr($addsize,0,2);}

switch ($times) {
  case 0 : $mega = ' bytes'; break;
  case 1 : $mega = ' KB'; break;
  case 2 : $mega = ' MB'; break;
  case 3 : $mega = ' GB'; break;
  case 4 : $mega = ' TB'; break;}

$addsize .= $mega;

return $addsize;
}
$dir = $_GET['dir'];
$action = $_GET['action'];
$adm_user = $_POST['adm_user'];
$adm_pass = $_POST['adm_pass'];
$adm_pass_conf = $_POST['adm_pass_conf'];

/*      LOGIN/REGISTRATION STUFF      */
if ($reg_user == '' && $reg_pass == '') {
//REGISTRATION
echo "</div>";
if ($_POST['reg']!='') { //validate
   if ($adm_user == '' || $adm_pass == '' || $adm_pass_conf == '') {$err = '������һ��û����д!<br>';}
   if ($adm_pass != $adm_pass_conf) {$err .= '�������!';}
   if ($err == '') { //store passwords in this file
   $fn = fopen('pass.php','w');
   fputs($fn, '<? $reg_user = '."'".$adm_user."'".'; $reg_pass = '."'".$adm_pass."'"."; ?>\n");
   fclose($fn);
   die ("ע��ɹ�!<br><a href='$docname'>�����Ե�½��.</a>");
   }
   }
if ($_POST['reg']=='' || $err != '') {
echo "</div>";
if ($err != '') echo '����: '.$err.'<br>';
        ?>
����ע��.(���ڰ�ȫ����!)
        <form method="POST" action="<? echo $docname; ?>">
        <table border="0">
      <tr>
        <td>�û���</td>
        <td><input type="text" name="adm_user" size="29"></td>
      </tr>
      <tr>
        <td>����</td>
        <td><input type="password" name="adm_pass" size="29"></td>
      </tr>
      <tr>
        <td>ȷ������</td>
        <td><input type="password" name="adm_pass_conf" size="29"></td>
      </tr>
         </table>
         <input type="submit" value="ע��" name="reg">
        <?
        die('');
        }
        } //end of registration


$expired = FALSE;
if ($_SESSION['current_session'] != $_SESSION['user']."=".$_SESSION['session_key']) $expired = TRUE;

if ($action == "logout"){
$_SESSION['current_session'] = rand(100,9000000);
$_SESSION['curr_sess_iden'] = rand(100,9000000);
$_SESSION['session_user'] = "Logged out";
$_SESSION['session_key'] = rand(100,9000000);
$expired = TRUE;
}

if ($_POST['login'] != '') {
if ($reg_user != $adm_user || $reg_pass != $adm_pass) {
echo "��½ʧ��: �û������������!<br>";
$expired = TRUE;
} else {
$time_started = md5(mktime());
$secure_session_user = md5($adm_user.$adm_pass);
$_SESSION['user'] = $adm_user;
$_SESSION['session_key'] = $time_started.$secure_session_user.session_id();
$_SESSION['current_session'] = $adm_user."=".$_SESSION['session_key'];
$expired = FALSE;
}
}

if ($expired) {  //EDIT!!!
    echo "</div>";
    echo "���½:<br>";
    ?>
    <form method="POST" action="<? echo $docname; ?>">
        <table border="0">
      <tr>
        <td>�û���</td>
        <td><input type="text" name="adm_user" size="29"></td>
      </tr>
      <tr>
        <td>����</td>
        <td><input type="password" name="adm_pass" size="29"></td>
      </tr>
         </table>
         <input type="submit" value="��½" name="login">
        <?
    die();
    }


/*      THE REAL STUFF BEGINS HERE     */

echo "<span class=logout><a href='?action=logout'>[ע��]</a><br> </span></div>";


include "pclzip.lib.php";

chdir($dir);

$basedir = getcwd();
$basedir = str_replace('\\','/',$basedir);        //'

if (is_dir($basedir)) { //show directory list

$parent = dirname($basedir);

$cur = $basedir;

while (substr($cur,0,1) == '/') {
        $cur = substr($cur,1,strlen($cur));
        $path .= '/'; }

$p_out = $path;
while (strlen($cur) > 0) {
$k = strpos($cur,'/');
if (!strpos($cur,'/')) $k = strlen($cur);
$s = substr($cur,0,$k);
$cur = substr($cur,$k+1,strlen($cur));
$path .= $s.'/';
$p_out .= "<a href='?dir=$path'>$s</a>/";
}

echo "<br><center><div>��ǰĿ¼: ".$p_out."</div>";
echo "<center><div class=bigblock><div class=contents>";
echo "<div class=dirlist>";
echo "<div class=filedirtitle>��Ŀ¼</div>";
echo "<b><center><a href='?dir=$parent'>�ϼ�Ŀ¼</a></b></center><br>\n";

$glob = array();$c = 0;
if ($dh = opendir(getcwd())) {
        while (($file = readdir($dh)) !== false) {
            if ($file != '..' && $file != '.') $glob[$c++] = $file;
        }
    closedir($dh);
    }

foreach ($glob as $filename) {
if (is_dir($filename)) {
    echo "&nbsp;&nbsp;/<a href='?dir=$basedir/$filename'>$filename</a><br>\n";
}
}

echo "</div><div class=filelist>";
echo "<div class=filedirtitle>ZIP�ĵ�</div>";
$filez = $glob;
reset($filez);
if (sizeof($filez) > 0)
foreach ($filez as $filename) {
if (strtolower(fileext($filename)) == 'zip')
if (is_file($filename)) {
echo "&nbsp;&nbsp;<a href='?dir=$basedir&unzip=$filename&action=view' title='����ĵ�'>$filename [���]</a> <a href='?dir=$basedir&unzip=$filename&action=unzip' title='��ѹ�ĵ�'><font color=red>[��ѹ]</font></a><br>";
}
}


echo "</div></div><br>";
}

$unzip = $_GET['unzip'];

if (is_file($unzip)) {       //unzipping...

$zip = new PclZip($unzip);
if (($list = $zip->listContent()) == 0) {die("���� : ".$zip->errorInfo(true));  }

/*
File 0 / [stored_filename] = config
File 0 / [size] = 0
File 0 / [compressed_size] = 0
File 0 / [mtime] = 1027023152
File 0 / [comment] =
File 0 / [folder] = 1
File 0 / [index] = 0
File 0 / [status] = ok
*/

//calculate statistics...
  for ($i=0; $i<sizeof($list); $i++) {
    if ($list[$i][folder]=='1') {$fold++;
       $dirs[$fold] = $list[$i][stored_filename];
    if ($_GET[action] == 'unzip') {
     $dirname = $list[$i][stored_filename];
     $dirname = substr($dirname,0,strlen($dirname)-1);
     mkdir($basedir.'/'.$dirname); }
     chmod($basedir.'/'.$dirname,0777);
       }else{$fil++;}
    $tot_comp += $list[$i][compressed_size];
    $tot_uncomp += $list[$i][size];
    }

echo "<div class=unzip>".($_GET[action] == 'unzip' ? '��ѹ' : '���')."�ļ� <b>$unzip</b><br>\n";
echo "$fil ���ļ� �� $fold (��)Ŀ¼���ĵ���<br>\n";
echo "ѹ���ĵ���С: ".convertsize($tot_comp)."<br>\n";
echo "��ѹ�ĵ���С: ".convertsize($tot_uncomp)."<br>\n";

if ($_GET[action] == 'unzip') {
echo "<br><b>��ʼ��ѹ��...</b><br>";
$zip->extract('');
echo "��ѹ�ɹ�!<br>\n";
}

if ($_GET[action] == 'view') {
echo "<br>";
for ($i=0; $i<sizeof($list); $i++) {
    if ($list[$i][folder] == 1) {
         echo "<b>Ŀ¼: ".$list[$i][stored_filename]."</b><br>";
         } else {
         echo $list[$i][stored_filename]." (".convertsize($list[$i][size]).")<br>";
         }
  }
}



echo "</div>";

}

echo "<div class=contents>
<a href='http://www.cmsware.com/bbs' target=_blank>CMSware Unzipper v1.3</a>,2006��3��12��,(C) CMSware.com
<br />
<a href='./ReadMe.txt' target=_blank>�����ļ�</a> 

</div></div>";


?>

</body>
</html>
