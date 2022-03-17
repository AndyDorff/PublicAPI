<?php

namespace Modules\PublicAPI\Http\Requests\Api\V1;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\PublicAPI\Http\ResponseStatuses\Api\V1\Error\InvalidCredentialsResponseStatus;

class AuthRequest extends FormRequest
{
    public function failedValidation(Validator $validator)
    {
    	throw new HttpResponseException(response()->error()->unprocessableEntity(new InvalidCredentialsResponseStatus(
    		$validator->errors()->getMessages()
	    )));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'app_key' => ['required', 'size:16'],
            'app_secret' => ['required', 'size:32']
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function credentials(): array
    {
        return [$this->get('app_key'), $this->get('app_secret')];
    }

    public function appKey(): string
    {
        return $this->get('app_key');
    }

    public function appSecret(): string
    {
        return $this->get('app_secret');
    }
}
