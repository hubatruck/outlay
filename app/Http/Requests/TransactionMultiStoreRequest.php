<?php

namespace App\Http\Requests;

use App\Feedbacks\TransactionFeedback;
use App\Http\Validators\TransactionValidator;
use Illuminate\Foundation\Http\FormRequest;

class TransactionMultiStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return TransactionValidator::getRulesFor(TransactionValidator::EVERYTHING_WITH_ITEMS);
    }

    /**
     * As we actually store the data in the session, we want to load it from that,
     * instead of checking the request data.
     *
     * @param null $key
     * @param null $default
     * @return array
     */
    public function input($key = null, $default = null): array
    {
        return $this->session()->get('transaction') ?? [];
    }


    /**
     * Get error redirect URL based on error types.
     *
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        $errors = $this->validator->errors();
        $response = $errors->hasAny(['amount', 'scope'])
            ? TransactionFeedback::checkItemError()
            : TransactionFeedback::checkPaymentError();
        return $response->getTargetUrl();
    }
}
