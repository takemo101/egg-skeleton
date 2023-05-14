<?php

namespace App\Controller;

use App\Request\ContactRequest;

class ContactController
{
    /**
     * contact store
     *
     * @return string
     */
    public function store(ContactRequest $form): string
    {
        return latte('page.contact', [
            'message' => $form->message,
        ]);
    }
}
