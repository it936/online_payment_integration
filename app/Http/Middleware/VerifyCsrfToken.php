<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
  protected $except = [
    'payment/cancel', // Add your cancel route here
    'payment/response',
    'payment/redirect',
     'res',
// Add redirect URL here if necessary
];

}
