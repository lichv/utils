<?php

namespace Utils\Provider;

use Symfony\Component\HttpFoundation\Request;

interface ProviderInterface
{
    /**
     * Redirect the user to the authentication page for the provider.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function send(Request $request);

}
