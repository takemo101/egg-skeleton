<?php

namespace Module\Latte\Request;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * フォームリクエスト
 */
abstract class FormRequest
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly Request $request,
    ) {
        $this->populate();

        if ($this->autoValidateRequest()) {
            $this->validate();
        }
    }

    /**
     * リクエスト
     *
     * @return Request
     */
    public function request(): Request
    {
        return $this->request;
    }

    /**
     * プロパティ
     *
     * @return mixed[]
     */
    protected function properties(): array
    {
        $posts = $this->request()->request->all();
        $files = $this->request()->files->all();

        return [
            ...$posts,
            ...$files,
        ];
    }

    /**
     * バリデーション
     *
     * @return void
     */
    public function validate(): void
    {
        $violations = $this->validations();

        if (count($violations)) {
            throw new ValidationErrorHttpException($violations);
        }
    }

    /**
     * バリデーション結果のリストを取得する
     *
     * @return ConstraintViolationListInterface
     */
    public function validations(): ConstraintViolationListInterface
    {
        return $this->validator->validate($this);
    }

    /**
     * プロパティに値を設定する
     *
     * @return void
     */
    protected function populate(): void
    {
        foreach ($this->properties() as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    /**
     * 自動でバリデーションを行うか
     *
     * @return boolean
     */
    protected function autoValidateRequest(): bool
    {
        return true;
    }
}
