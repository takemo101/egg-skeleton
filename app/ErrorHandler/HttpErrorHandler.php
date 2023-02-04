<?php

namespace App\ErrorHandler;

use Takemo101\Egg\Http\ErrorHandler\HttpErrorHandler as ErrorHandler;
use Takemo101\Egg\Http\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

final class HttpErrorHandler extends ErrorHandler
{
    /**
     * HttpExceptionをハンドリングする
     *
     * @param HttpException $error
     * @return Response
     */
    protected function handleHttpException(HttpException $error): Response
    {
        return latte('error/error.latte.html', [
            'error' => $error,
        ])->setStatusCode($error->getStatusCode());
    }
}
