# MergeRequest

MergeRequest is a package for Laravel to merge rules between models and use them for request validations.

## Getting Started

Here is a basic example of how we can use the package:

### Prerequisites

Have a Laravel Proyect 5+ and composer

### Installing

1- First download the package using composer

```
composer require pyxeel/merge_rules
```

2- Second, now we can use

```php
MergeRules::merge([$rules, "prefix"], $moreRules)
```

### Example

1- Download the package

```
composer require pyxeel/merge_rules
```

2- Now, we are going to create two models for merge the rules

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelOne extends Model
{
    public static $rules = [
        'date' => 'date|required',
        'description' => 'string|nullable'
    ];
    
    public static function rules()
    {
        return self::$rules;
    }
}

```

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModelTwo extends Model
{
    public static $rules = [
        'name' => 'string|required',
        'doc' => 'numeric|required'
    ];
    
    public static function rules()
    {
        return self::$rules;
    }
}


```

3- Create a custom request

```
php artisan make:request CustomRequest
```

4- And now we can merge the rules like this:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomRequest extends FormRequest
{
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
        return MergeRules::merge(
            [
                ModelOne::rules(), 
                "ModelOne"
            ],
            ModelTwo::rules()
        )
    }
}

```

Output:

```php
array:5 [▼
  "ModelOne" => "required|array"
  "ModelOne.date" => "date|required"
  "ModelOne.description" => "string|nullable"
  "name" => "string|required"
  "doc" => "numeric|required"
]
```

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
