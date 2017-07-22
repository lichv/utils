<?php

namespace Utils\Contracts;

use Symfony\Component\HttpFoundation\Request;

interface Provider
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function send(Request $request);

}
