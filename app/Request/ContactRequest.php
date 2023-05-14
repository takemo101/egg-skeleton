<?php

namespace App\Request;

use Module\Latte\Request\FormRequest;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ContactRequest extends FormRequest
{
    #[NotBlank(message: 'メッセージを入力してください')]
    public string $message;
}
