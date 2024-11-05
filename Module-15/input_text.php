<?php

require_once 'autoload.php';
use Entities\TelegraphText;
use Entities\Storage;
use Entities\FileStorage;
use Entities\User;
use Entities\View;
use Interfaces\IRender;
use Core\Com;
use Core\Spl;
use Core\Swig;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function errorHandler($level, $msg, $line, $file)
{
    echo 'Что-то пошло не так, мы скоро все исправим';
    $dt = new DateTime();
    file_put_contents('admin.log', $dt->format('Y-m-d') . ' ' . $msg . ' in line ' . $line . ' in file ' . $file);
}
set_error_handler('errorHandler');

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if((strlen($_POST['text']) > 0 && strlen($_POST['text']) < 501) && ($_POST['author'] && $_POST['title'] && $_POST['text'])) {
        $FileStorageObject = new FileStorage();
        $FileStorageObject->create(new TelegraphText($_POST['title'], $_POST['text'], $_POST['author']));
        
        if($_POST['email']) {
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'ssl://smtp.yandex.ru';
                $mail->SMTPAuth = true;
                $mail->Username = 'cheradam2011@yandex.ru';
                $mail->Password = 'chBrawl11Adam';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    
                $mail->setFrom("cheradam2011@yandex.ru", "Adam Chereshniuk");
                $mail->addAddress($_POST['email'], $_POST['author']);
                $mail->Subject = "Adding your new text";
                $mail->msgHTML("
                    <html>
                        <body>
                            <p>${$_POST['text']}</p>
                        </body>
                    </html>
                ");
                // $mail->From = "cheradam2011@yandex.ru";
                // $mail->FromName = "Adam Chereshniuk";

                $mail->send();
            } catch(Exception $e) {
                echo "Возникла ошибка: ${$mail->ErrorInfo}";
            }
        }
        echo "<span style='background-color: green; color: #fff; padding: 15px; font-weight: 700; font-size: 16px; font-family: Montserrat, sans-serif;'>Текст успешно отправлен</span>";
    }
};
?>

<form name="submit" class="form" action="input_text.php" method="POST">
    <input name="author" class="form-input" placeholder="Автор*" type="text">
    <input name="title" class="form-input" placeholder="Заголовок*" type="text">
    <textarea name="text" class="form-textarea" placeholder="Текст*"></textarea>
    <input name="email" class="form-input" placeholder="Email" type="text">
    <button class="form-btn" type="submit">Отправить</button>
</form>

<?php
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(strlen($_POST['text']) < 0 || strlen($_POST['text']) > 501 || !$_POST['author'] || !$_POST['title'] || !$_POST['text']) {
            echo "<span style='background-color: red; color: #fff; padding: 15px; font-weight: 700; font-size: 16px; font-family: Montserrat, sans-serif;'>Допустимая длина текста: 1-500 символов</span>";
        }
    }
?>
