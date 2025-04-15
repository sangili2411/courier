<?php
session_start();
include 'dbConn.php';
 
#Standard email info
if(isset($_POST['to'])) {
    $to = $_POST['to'];
    $subject = $_POST['subject'];
    $msg = $_POST['message'];
    $ccMail = $_POST['cc'];
    $pdfName = $_POST['custName'];
    $file_name = $_FILES['file']['tmp_name'];
    $file_type = $_FILES['file']['type'];
    $invoiceId = $_POST['invoiceId'];
    $piDetail = $_POST['for_detail'];
    
    #This variable will be used when declaring the "boundaries"
    #for the different sections of the email
    $boundary = md5(date('r', time()));
    
    #Initial Headers 
    $headers = "MIME-Version: 1.0\r\n"; // <- the "\r\n" indicate a carriage return and newline, respectively
    $headers .= "From: Sunmac Enterprices<lightsnpoles@sunmac.co.in>\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=" . $boundary . "\r\n";
    if(!empty($ccMail)) {
        $headers .= "Cc: ". $ccMail . "\r\n";
    }
    
    # This is saying the there will be more than one (a "mix") of Content Types in this email.
    # The "boundary" value will indicate when each content type will start
    
    #First Content Type
    $message = "\r\n\r\n--" . $boundary . "\r\n";
    # This indicates that I'm going to start
    # declaring headers specific to this section of the email. 
    # MAKE SURE there's only ONE(1) "\r\n" between the above boundry and the first header below (Content-Type)
    $message .= "Content-type: text/plain; charset=\"iso-8859-1\"\r\n";
    # Here I'm saying this content should be plain text
    $message .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    
    # Body of the email for the headers I just declared
    $message .= $msg . "\r\n";
    $message .= "";
    
    #Second Content Type
    $message .= "\r\n\r\n--" . $boundary . "\r\n";
    # This idicates that I'm going to start 
    # declaring some more headers for the content beslow
    # MAKE SURE there's only ONE(1) "\r\n" between the above boundry and the first header below (Content-Type)
    
    $message .= "Content-type:" . $file_type . "\r\n"; # <- Here I'm saying that this Content Type is for a JPEG image
    $message .= "Content-Transfer-Encoding: base64\r\n"; # <- this is saying that this section's content will be base64 Encoded
    // $message .= "Content-Disposition: attachment; filename=" . $file_name . "\r\n";
    $message .= "Content-Disposition: attachment; filename=BKHINV_" . str_replace(' ', '_', $pdfName) . ".pdf" . "\r\n";
    # This is saying the content below should be an attachment and gives it a file name
    
    # The base64_encode below is necessary because this is a file. 
    $message .= base64_encode(file_get_contents($file_name));
    $message .= "\r\n\r\n--" . $boundary . "--";
    # This indicates the end of the boundries. Notice the additional "--" after the boundry's value.
    
    # Send the email using "mail()".
    $mail_sent = @mail($to, $subject, $message, $headers);
    if($piDetail == "spi") {
        mysqli_query($conn, "UPDATE sample_pi SET IS_SAMPLE_INV_SENT = 1 WHERE SAMPLE_PI_ID = $invoiceId");
        echo "<script>alert('Mail Sent!'); location.href = 'view_performa_invoice.php';</script>";
    } else {
        mysqli_query($conn, "UPDATE sample_pi SET IS_INV_SENT = 1 WHERE SAMPLE_PI_ID = $invoiceId");
        echo "<script>alert('Mail Sent!'); location.href = 'view_invoice.php';</script>";
    }
    
    // echo $mail_sent ? "<script>alert('Mail Sent')</script>" : "<script>alert('Mail Failed')</script>";
    
    // echo $mail_sent ? "<script>window.open('https://gmail.com')</script>" : "<script>window.open('https://google.com')</script>";
}

?>

<!DOCTYPE html>
<html lang="en" class="no-js">

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html">
    <title>Send Mail</title>

    <meta name="author" content="Dilip Agarwal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="demo for sending mail using php.. Send Mail using SMTP and PHP!">
    <meta name="email" content="contactdilipagarwal@gmail.com">
    <meta name="copyright" content="Dilip Agarwal 2018" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Send Mail" />
    <meta property="og:description" content="Send mail using SMTP and PHP" />
    <meta property="og:image" content="http://www.dilipagarwal.com/images/dilip.jpg">
    <meta property="og:url" content="https://dilipagarwal001.github.io/Send-Mail-Using-PHP/" />
    <meta property="og:site_name" content="Send Mail" />
    <meta name="twitter:card" content="website">
    <meta name="twitter:site" content="Send Mail">
    <meta name="twitter:title" content="Send Mail">
    <meta name="twitter:description" content="Send mail using SMTP and PHP">
    <meta name="twitter:creator" content="@dilipagarwal001">
    <meta name="twitter:image" content="http://www.dilipagarwal.com/images/dilip.jpg">
    <meta itemprop="name" content="Send Mail">
    <meta itemprop="description" content="Demo for sending mail using php.. Send Mail using SMTP and PHP!">
    <meta itemprop="image" content="http://www.dilipagarwal.com/images/dilip.jpg">

    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />


</head>

<body>
    <!-- <form action="#" method="post" enctype="multipart/form-data">
        <div class="container">
            <div class="row text-center" style="">
                <div class="col-lg-offset-3 col-lg-7" style="box-shadow: 0 3px 20px 0px rgba(0, 0, 0, 0.1);">
                    <h2 class="text-center" style="color:grey">Send mail using Php with attachment</h3>
                        <div class="row firstrow">
                            <div class="col-lg-5">
                                <input type="email" class="text-center" Placeholder="Enter Your Email" name="to" required />
                            </div>
                            <div class="col-lg-offset-2 col-lg-5 box">
                                <input type="text" class="text-center" placeholder="Enter Your Subject" name="subject" required />
                            </div>
                        </div>

                        <label for="file-upload" class="custom-file-upload">
                            <i class="fa fa-cloud-upload"></i> Upload Your File Here
                        </label>


                        <input id="file-upload" type="file" name="file" required />
                        <br><br><br>
                        <div class="row">
                            <textarea placeholder="Enter Your Message" rows="3" cols="60" name="message"></textarea>
                        </div>
                        <div class="submit">
                            <button type="submit" class="" name="submit">Send Mail</button>
                            <br>
                        </div>
                </div>
            </div>

        </div>
    </form> -->
</body>

</html>