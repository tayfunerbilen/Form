# Form
Form işlemlerini daha kolay ve kontrollü yapmak için kullanabileceğiniz hafif, kolay kullanımlı bir form sınıfıdır.

# Örnek Form Oluşturmak
```php
<?php

$form = new Prototurk\Form;

$form->start('form.php', 'post');
  $form->label('Kullanıcı Adınız', 'username');
  $form->input('username', [
    'required' => true,
    'placeholder' => 'Kullanıcı Adı',
    'id' => 'username'
  ]);
  $form->label('Şifreniz', 'password');
  $form->input('password', [
    'required' => true,
    'placeholder' => 'Şifre',
    'type' => 'password',
    'id' => 'password'
  ]);
  $form->button('Giriş Yap');
$form->end();
```

# Oluşturulan Formu Göstermek
```php
<?php
echo $form->show(); // doğrudan yukarıda oluşturulan sıraya göre ekranda gösterir
?>
```

# Form için Özel Dosya Oluşturmak
```php
<?php
echo $form->show('form'); // form.template dosyasını okumaya çalışacak
?>
```

# Örnek Template Dosyası
Yukarıdaki örneği baz alarak
```
{form_start}

    <ul>
        <li>
            {label="Kullanıcı Adınız"}
            {input="username"}
        </li>
        <li>
            {label="Şifreniz"}
            {input="password"}
        </li>
        <li>
            {button="Giriş Yap"}
        </li>
    </ul>

{form_end}
```

# Dinamik Kontrol İçin İse
```php
if (isset($_POST['submit'])){
    if ($form->control()){
        print_r($form->values());
    } else {
        print_r($form->errors());
    }
}
```
