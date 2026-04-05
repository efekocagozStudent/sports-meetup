<?php
declare(strict_types=1);

class PageController extends Controller
{
    public function privacy(): void
    {
        $this->render('privacy', ['pageTitle' => 'Privacy Policy']);
    }
}
