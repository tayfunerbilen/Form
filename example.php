<?php

require_once __DIR__ . '/form.class.php';

$form = new Prototurk\Form;

$form->start('index.php', 'post');
    $form->label('Kullanıcı Adınız', 'username');
    $form->input('username', [
        'placeholder' => 'Kullanıcı Adı',
        'required' => true,
        'id' => 'username',
        'class' => 'form-control'
    ]);
    $form->label('Şifreniz', 'password');
    $form->input('password', [
        'placeholder' => 'Şifreniz',
        'id' => 'password'
    ]);
    $form->label('Hakkında', 'about');
    $form->textarea('about', [
        'id' => 'about',
        'rows' => 5,
        'cols' => 5,
        'placeholder' => 'Hakkınızda bir şeyler yazın'
    ], 'tayfun erbilen');
    $form->label('Cinsiyetiniz', 'gender');
    $form->select('gender', [
        1 => 'Kadın',
        2 => 'Erkek'
    ], [
        'id' => 'gender',
        'multiple' => true
    ], 2);
    $form->button('Giriş Yap', 'login_btn');
$form->end();

if (isset($_POST['submit'])){
    if ($form->control()){
        print_r($form->values());
    } else {
        print_r($form->errors());
    }
}

echo $form->show('login');

//$form->dump();
