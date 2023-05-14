<?php

namespace Module\Latte\ErrorHandler;

use Module\Latte\Request\ValidationErrorHttpException;
use Module\Latte\Session\ErrorMessages;
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
        // バリデーションエラーレスポンス
        if ($error instanceof ValidationErrorHttpException) {
            return $this->validationErrorResponse($request, $error);
        }

        return new Response(
            latte(
                'error.error',
                [
                    'error' => $error,
                ],
            ),
            $error->getStatusCode(),
        );
    }

    /**
     * バリデーションエラーレスポンス
     *
     * @param Request $request
     * @param ValidationErrorHttpException $error
     * @return Response
     */
    private function validationErrorResponse(
        Request $request,
        ValidationErrorHttpException $error
    ): Response {
        /** @var ErrorMessages */
        $errors = $this->container->make(ErrorMessages::class);
        $errors->put($error->toMessages());

        return new RedirectResponse(
            (string) $request->headers->get('referer'),
        );
    }
}
