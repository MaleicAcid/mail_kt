
<?php
/**
 * Created by PhpStorm.
 * User: eddie
 * Date: 2018/3/5
 * Time: 10:37
 */


$PATH = "e:/mail-kt";
require $PATH.'/src/Exception.php';
require $PATH.'/src/PHPMailer.php';
require $PATH.'/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$SMTP_SERVER = 'smtp.163.com';
$MAIL_NAME = 'trans_ship_data@163.com';
$MAIL_PASS = 'OJWOOPOISYDDAEXB';

//$RECI_NAME = 'cs_maleicacid@163.com';
// $dispatch_head = "[dispatch]";
// $watch_path = "D:\phpspace\mail-ktdata\data";
$watch_path = "e:/NMEA";



function trans_byte($byte)
{

    $KB = 1024;

    $MB = 1024 * $KB;

    $GB = 1024 * $MB;

    $TB = 1024 * $GB;

    if ($byte < $KB) {

        return $byte . "B";

    } elseif ($byte < $MB) {

        return round($byte / $KB, 2) . "KB";

    } elseif ($byte < $GB) {

        return round($byte / $MB, 2) . "MB";

    } elseif ($byte < $TB) {

        return round($byte / $GB, 2) . "GB";

    } else {

        return round($byte / $TB, 2) . "TB";

    }
}

function check_file_time($filename) {

	$cur_hour = date('H');
	$filename_no_txt = substr($filename, 0, -4);
	$file_hour = end(explode(' ', $filename_no_txt));
	echo $cur_hour;
	echo $file_hour;
	if ($file_hour >= $cur_hour) {
		return false;
	}
	return true;
}
class FileWatch
{
 	protected $_mail = 0;

    protected $all = array();
 
    public function __construct($dir)
    {
    	$SMTP_SERVER = 'smtp.163.com';
		$MAIL_NAME = 'trans_ship_data@163.com';
		$MAIL_PASS = 'JCDTIENRFQKZOSRG';
        $dispatch_head = "[dispatch]";
        $to_1 = 'tianrui_z01@163.com';
        $to_2 = '1241514977@qq.com';
		$RECI_NAME = $MAIL_NAME;

    	$this->$_mail = new PHPMailer(true);
		$mail = $this->$_mail;
    	// $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
	    $mail->isSMTP();                                            // Send using SMTP
	    $mail->Host       = $SMTP_SERVER;                    // Set the SMTP server to send through
	    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
	    $mail->Username   = $MAIL_NAME;                     // SMTP username
	    $mail->Password   = $MAIL_PASS;                               // SMTP password
	    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
	    $mail->Port       = 25;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    	$mail->setFrom($MAIL_NAME, 'Mailer');
	    $mail->AddAddress($RECI_NAME, 'self');     // Add a recipient
        $mail->AddAddress($to_1, 'tianrui');     // Add a recipient
        $mail->AddAddress($to_2, 'tianxiao');     // Add a recipient
        $this->watch($dir);
    }
 
    //子类中重写这个方法
    public function run($file)
    {
    }
 
    protected function all_file($dir)
    {

        if (is_file($dir)) {
            $this->all[$dir] = md5_file($dir);
            return $this->all;
        }
        if (is_dir($dir)) {
            $open = opendir($dir);
            while (($file = readdir($open)) !== false) {
                if ($file != "." && $file != "..") {
                    $f = $dir . "/" . $file;
                    if (is_file($f)) {
                        $this->all[$f] = md5_file($f);
                    } elseif (is_dir($f)) {
                        $this->all_file($f);
                    }
 
                }
            }
        }
        return $this->all;
    }
 
    public function watch($dir)
    {
        $this->all = array();
        $old = $this->all_file($dir);
        while (true) {
            sleep(60*20);
            $this->all = array();
            $new = $this->all_file($dir);
            
            $re = array_diff($new, $old);
            $del = array_diff_key($old, $new);
            $re = array_merge($re, $del);
            //var_dump($re);
            if ($re) {
                $this->all = array();
                $old = $this->all_file($dir);
                $file = array_keys($re);
                // var_dump($file);
                $this->run($file[0]);
            }
        }
    }

    function sendMail($fullpath, $filename, $reason) {
		$reason_list = array( 
			'0' => '新增',
			'1' => '修改',
		);
		$reason = $reason_list[$reason];
        $size = filesize($fullpath);
		try {
		    // Attachments
		    $mail = $this->$_mail;
		    $mail->addAttachment($fullpath, $filename);         // Add attachments
		    // Content
		    $mail->Subject = "[dispatch] " . "filename:".$filename . "|size:".$size;
		    $mail->Body    = "filename:".$filename . "|size:".$size;
		    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

		    $mail->send();
		    echo 'Message has been sent';
		} catch (Exception $e) {
		    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
		}
}

}//endclass
//使用方法
class mywatch extends FileWatch
{
    public function run($file)
    {
        // var_dump($file);
        if(!empty($file)) {
            echo "new file or file has been changed with ".$file.PHP_EOL;
            //echo "please choose 1 or 0 to upload files or skip ".PHP_EOL;
            $fileName = end(explode('/', $file));
            // var_dump($fileName);
            $check_flag = check_file_time($fileName);
            print($check_flag);

            if (strlen($fileName) != 17) {
            	echo "ignored because of fileName".PHP_EOL;
 				return;
            }
 			if(!$check_flag) {
 				echo "ignored because of hour".PHP_EOL;
 				return;
 			}
            while (true) {
                // $a = trim(fgets(STDIN));
                if(true) { // ctype_digit($a)
                    // echo $a.PHP_EOL;
                    if(true) { //$a == 1
                        //上传文件
                        // echo "you choose upload files ".PHP_EOL;
                        // 判断系统
                        switch (PHP_OS) {
                            //linux 需要用scp 命令
                            case 'Linux':
                                // exec('scp '.$file.' root@192.168.1.21:/home/'.$file);
                                $this->sendMail($file, $fileName, $reason=1);
                                // break;
                            case 'WINNT':
                            	$this->sendMail($file, $fileName, $reason=1);
                                break;
                        }
                        break;
                    } else {
                        //不管 跳过
                        echo "you choose skip ".PHP_EOL;
                        break;
                    }
                } else {
                    echo 'please enter 0 or 1'.PHP_EOL;
                }
            }
        } else {
            echo "no files has created and no files has been changed".PHP_EOL;
        }
    }
}
echo 'Your System is '.PHP_OS.PHP_EOL;
echo "Welcome to use fileWatch System".PHP_EOL;
printf("pid=%d", getmypid());
echo PHP_EOL;
if (function_exists('cli_set_process_title')) {
	echo "can set process name!".PHP_EOL;;
    cli_set_process_title("php_ktdata_mailer");
} 

$watch = new mywatch($watch_path);