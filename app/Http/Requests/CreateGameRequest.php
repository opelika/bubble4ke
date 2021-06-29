<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateGameRequest extends Request {
	
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}
	
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'ip' => 'required|ip|validip',
			'port' => 'required|integer',
			'name' => 'required|max:58'
		];
	}

	public function messages()
	{
		return [
			'ip.required' => 'Please enter an IP address.',
			'ip.ip' => 'The IP address must be valid.',
			'ip.validip' => 'The IP address must be valid.',
			'port.integer' => 'The port must be a valid port.',
			'port.required' => 'Please enter a port.',
			'name.required' => 'Please enter a name for your server.',
		];
	}
}