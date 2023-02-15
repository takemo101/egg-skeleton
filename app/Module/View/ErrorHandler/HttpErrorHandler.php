<?php

namespace App\Module\View\ErrorHandler;

use App\Module\View\Request\ValidationErrorHttpException;
use App\Module\View\Session\FlashErrorMessages;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Takemo101\Egg\Http\ErrorHandler\HttpErrorHandler as ErrorHandler;
use Takemo101\Egg\Http\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

final class HttpErrorHandler extends ErrorHandler
{
    /**
     * HttpExceptionをハンドリングする
     *
     * @param Request $request
     * @param HttpException $error
     * @return Response
     */
    protected function handleHttpException(Request $request, HttpException $error): Response
    {
        if ($error instanceof ValidationErrorHttpException) {

            /** @var FlashErrorMessages */
            $errors = $this->container->make(FlashErrorMessages::class);
            $errors->put($error->toMessages());

            return new RedirectResponse(
                $request->headers->get('referer'),
            );
        }

        return new Response(
            latte(
                'error/error.latte.html',
                [
                    'error' => $error,
                ],
            ),
            $error->getStatusCode(),
        );
    }
}
