<?php

return [
    'common' => [
        'greeting' => '¡Hola, :name!',
        'expiration' => '{1} Este enlace caduca en :count minuto.|[2,*] Este enlace caduca en :count minutos.',
        'fallback' => 'Si el botón «:action» no funciona, copia y pega la siguiente dirección en el navegador:',
        'rights' => 'Todos los derechos reservados.',
    ],
    'verification' => [
        'subject' => 'Confirma tu dirección de correo electrónico',
        'preview' => 'Confirma tu correo para comenzar a utilizar :app.',
        'heading' => 'Confirma tu correo electrónico',
        'intro' => 'Gracias por crear tu cuenta en :app. Confirma tu dirección de correo electrónico para completar el registro.',
        'action' => 'Confirmar correo',
        'outro' => 'Si no creaste esta cuenta, puedes ignorar este correo con seguridad.',
    ],
    'password_reset' => [
        'subject' => 'Restablece tu contraseña',
        'preview' => 'Recibimos una solicitud para restablecer tu contraseña en :app.',
        'heading' => 'Restablece tu contraseña',
        'intro' => 'Recibimos una solicitud para restablecer la contraseña de tu cuenta en :app.',
        'action' => 'Restablecer contraseña',
        'outro' => 'Si no solicitaste una nueva contraseña, puedes ignorar este correo con seguridad.',
    ],
];
