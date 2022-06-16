
<?php
session_start(); 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendmail_first($to, $subject, $fromname, $body, $attachment){

  $mail = new PHPMailer(); //实例化
  $mail->isSMTP(); // 启用SMTP
  //$mail->SMTPDebug = 1;
  $mail->SMTPAuth = true; //启用smtp认证

  $mail->SMTPSecure = 'tls';
  // $mail->Host = 'mail.geetaurzabooronline.com';//'smtp.gmail.com';
  // $mail->Port = 465;
  $mail->Port = 587;
  //or more succinctly:
  $mail->Host = 'tls://smtp.gmail.com:587';


  // $mail->Username = "admin@geetaurzabooronline.com";
  // $mail->Password = "a[JLvdgC=xOE"; //邮箱密码
  $mail->Username = "clarenceli419tech@gmail.com"; //你的邮箱名
  $mail->Password = "890419911123r"; //邮箱密码
  $mail->From = "admin@geetaurzabooronline.com"; //发件人地址（也就是你的邮箱地址）
  $mail->FromName = $fromname; //发件人姓名
  $mail->addAddress($to,"name");
  $mail->addReplyTo("admin@geetaurzabooronline.com", "a[JLvdgC=xOE"); //回复地址(可填可不填)
  $mail->WordWrap = 50; //设置每行字符长度
  $mail->addAttachment($attachment);
  //$mail->AddAttachment("images/01.jpg", "manu.jpg"); // 添加附件,并指定名称
  $mail->isHTML(true); // 是否HTML格式邮件
  //$mail->CharSet="utf-8"; //设置邮件编码
  $mail->Subject =$subject; //邮件主题
  $mail->Body = $body; //邮件内容
  $mail->AltBody = "This is the body in plain text for non-HTML mail clients"; //邮件正文不支持HTML的备用显示

  if(!$mail->send()) {
    echo "Message could not be sent. <p>";
    echo "Mailer Error: " . $mail->ErrorInfo;
    exit();
  } else {
    echo "Message has been sent";
  }
}
  //       $mail=new phpmailer();
  //       $mail->attachment=$attachment;
  //       $mail->setfrom=$from. $fromname;
  //       $mail->addaddress=$to;
  //       $mail->subject=$subject;
  //       $mail->body=$message;
  //       $mail->isHtml= false;

  //       return $mail->send;
  //     }

 

  if(isset($_POST['submit'])){
      $to = $_SESSION['email'];
             //echo "enter the correct email";
      if ((!filter_var($to, FILTER_VALIDATE_EMAIL))||(!($_POST['subject']))) {
        header("Location: users_page.php?email=". $_SESSION['email']);     
        exit; 
         // echo "enter the correct email";
      }
      else{
         
              // if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
              //     echo "enter the correct email";
              // }
              //  else{
          $subject=$_POST['subject'];
          $fname=$_POST['fname'];
          // $lname=$_POST['lname'];
          $message=$fname."\n".$_POST['message'];
          $message=wordwrap($message, 70);
          // $header="From:". $from;


                     //Get the uploaded file information
                     //  $name_of_uploaded_file = basename($_FILES['attachment']['name']);
                     // //copy the temp. uploaded file to uploads folder
                     //  $path_of_uploaded_file = $upload_folder . $name_of_uploaded_file;
                     //  $tmp_path = $_FILES["attachment"]["tmp_name"];
                     //  echo $tmp_path;
                     //  if(is_uploaded_file($tmp_path))
                     //  {
                     //    if(!copy($tmp_path,$path_of_uploaded_file))
                     //    {
                     //      $errors .= '\n error while copying the uploaded file';
                     //    }
                     //  }

                    /* //Get the uploaded file information
                      $name_of_uploaded_file = basename($_FILES['attachment']['name']);
                      //get the file extension of the file
                      $type_of_uploaded_file = substr($name_of_uploaded_file,strrpos($name_of_uploaded_file, '.') + 1);
                      $size_of_uploaded_file = $_FILES["uploaded_file"]["size"]/1024;//size in KBs
                      //Settings
                      $max_allowed_file_size = 100; // size in KB
                      $allowed_extensions = array("jpg", "jpeg", "gif", "bmp");
                      //Validations
                      if($size_of_uploaded_file > $max_allowed_file_size )
                      {
                        $errors .= "\n Size of file should be less than $max_allowed_file_size";
                      }
                      //------ Validate the file extension -----

                      $allowed_ext = false;
                      for($i=0; $i<sizeof($allowed_extensions); $i++)
                      {
                        if(strcasecmp($allowed_extensions[$i],$type_of_uploaded_file) == 0)
                        {
                          $allowed_ext = true;
                        }
                      }
                      if(!$allowed_ext)
                      {
                        $errors .= "\n The uploaded file is not supported file type. ".
                        " Only the following file types are supported: ".implode(',',$allowed_extensions);
                      }
*/                    

                      $file = 'attachment/' . $_FILES["attach"]["name"];
                     // echo $file;

                     if(move_uploaded_file($_FILES["attach"]["tmp_name"], $file)){
                        sendmail_first($to, $subject, $fname, $message, $file);
                     }
                     else{
                      echo "pls check your attachment";
                     }
                     
                     // echo("email sended");
                     header("Location: emailsended.php");     
                      exit; 
      }
    }else{    //If the form button wasn't submitted go to the index page, or login page 
      header("Location: ../index.html");     
      exit; 
      //echo "not submitted";
    }
?>
