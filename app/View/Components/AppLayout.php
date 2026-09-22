<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Judul halaman web.
     */
    public ?string $title;

    /**
     * Header judul di topbar.
     */
    public ?string $header;

    public function __construct(?string $title = null, ?string $header = null)
    {
        $this->title = $title;
        $this->header = $header;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
