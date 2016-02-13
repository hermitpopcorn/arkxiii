<?php

namespace Illuminate\Session;

use App\Semester;

class Store implements SessionInterface
{
    /**
     * Regenerate the CSRF token value.
     *
     * @return void
     */
    public function regenerateToken()
    {
        $this->put('_token', Str::random(40) . Semester::get_active_semester()->id);
    }
}
