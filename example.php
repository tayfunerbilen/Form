<?php

require_once __DIR__ . '/form.class.php';

$form = new Prototurk\Form;

$form->start('form.php', 'post', 'formClass');

    $form->label('Kullanıcı Adınız', 'test');
    $form->input('username', [
        'placeholder' => 'Kullanıcı Adı',
        'class' => 'active',
        'id' => 'test',
        'required' => true
    ]);
    $form->label('Hakkımda');
    $form->textarea('about_us', [
        'rows' => 5,
        'cols' => 10
    ], 'hello world!');
    $form->label('Kategoriler');
    $form->select('categories', [
        '1' => 'Blog',
        '2' => 'Web',
        '3' => 'PHP'
    ], [
        'multiple' => true,
        'size' => 2,
        'style' => 'width: 300px',
        'id' => 'categories'
    ], '2');
    $form->button('Gönder');

$form->end();

if (isset($_POST['submit']))
{
    if ($form->control()){
        print_r($form->values());
    } else {
        print_r($form->errors());
    }
}

echo $form->show('form');

//$form->dump();
