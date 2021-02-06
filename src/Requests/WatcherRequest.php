<?php

namespace hackerESQ\Watcher\Requests;

use hackerESQ\Watcher\Watcher;
use Illuminate\Foundation\Http\FormRequest;

class WatcherRequest extends FormRequest
{
    use Watcher;

    /**
     * This is a base FormRequest that allows you to quickly use Watcher. 
     * To use this, replace (Request $request) with (WatcherRequest $request)
     * in your controller.
     * 
     * Alternatively, you can create your own custom FormRequest and use the 
     * Watcher trait that comes with this package.
     */
    
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
        return [];
    }
}