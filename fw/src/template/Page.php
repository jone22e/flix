<?php

namespace Flix\Fw\Template;

use Flix\FW\Types\Strings;

class Page {

    private $appName = "";
    private $title = "";
    private $cssFiles = [];
    private $jsFiles = [];
    private $body;
    private $mainmenu;

    public function Render()
    {
        $css = [];
        $js = [];
        if (count($this->cssFiles) > 0) {
            foreach ($this->cssFiles as $cssFile) {
                $css[] = "<link rel='stylesheet' href='{$cssFile}'>";
            }
        }

        if (count($this->jsFiles) > 0) {
            foreach ($this->jsFiles as $jsFile) {
                $js[] = "<script src='{$jsFile}'></script>";
            }
        }

        $css = implode("\n", $css);
        $js = implode("\n", $js);

        echo <<<HTML
            <!DOCTYPE html>
            <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
                    <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'>
                    <title>{$this->title}</title>
                    {$css}
                    <link rel='icon' href='/favicon.png?v=1' type='image/x-icon'>
                </head>
                <body>
                    {$this->generateMenu()}
                    {$this->body}
                    {$js}
                </body>
            </html>
        HTML;
    }

    public function noPermission() {
        $this->body = <<<HTML
            <div class="p-3">
                <div class="alert alert-warning">
                    Você não tem permissão para acessar essa ferramenta.
                </div>
            </div>
        HTML;
        $this->Render();
        die();
    }

    public function generateMenu()
    {
        global $user;
        $parts = [];
        $menus = json_decode(json_encode($this->mainmenu));
        if (is_object($menus)) {
            foreach ($menus as $menu => $item) {
                if (is_object($item)) {
                    $subparts = [];
                    $submenuscounter = 0;
                    foreach ($item as $keysub=>$submenu) {
                        if($submenuscounter % 2 == 0) {
                            if ($submenuscounter!=0)
                                $subparts[] = "</div>";
                            $subparts[] = "<div class=' w-100' style='border-right: 1px solid #e9ecef;'>";
                        } else {
                            $subparts[] = "<div class='dropdown-divider'></div>";
                        }
                        $subparts[] = "<h6 class='dropdown-header'>{$keysub}</h6>";
                        foreach ($submenu as $sub => $url) {
                            $subparts[] = "<a class='dropdown-item'  style='min-width: 220px' href='{$url}'>{$sub}</a>";
                        }
                        $submenuscounter++;
                    }
                    $subparts = implode("\n", $subparts);

                    $toggler = "dropdown-toggle";
                    $dropRig = "";
                    if ((new Strings($menu))->startsWith("<")) {
                        $toggler = '';
                        $dropRig = 'dropdown-menu-right';
                    }
                    $parts[] = "<li class='nav-item dropdown'>
                                <a class='nav-link {$toggler}' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                    {$menu}
                                </a>
                                <div class='dropdown-menu {$dropRig}' style='width: auto' aria-labelledby='navbarDropdown'>
                                  <div class='d-lg-flex'>
                                      {$subparts}
                                  </div>
                                </div>
                            </li>";
                } else {
                    $parts[] = "<li class='nav-item'>
                                    <a class='nav-link' href='{$item}'>{$menu}</a>
                                </li>";
                }

            }
        }

        $parts = implode("\n", $parts);

        $menu = "
            <nav class='navbar navbar-expand-lg navbar-dark bg-dark'>
                <a class='navbar-brand' href='#'>{$this->appName}</a>
                <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarSupportedContent' aria-controls='navbarSupportedContent' aria-expanded='false' aria-label='Toggle navigation'>
                    <span class='navbar-toggler-icon'></span>
                </button>
                <div class='collapse navbar-collapse' id='navbarSupportedContent'>
                    <ul class='navbar-nav mr-auto'>
                      {$parts}
                    </ul>
                    <ul class='navbar-nav ml-auto'>
                    
                        <li class='nav-item avatar dropdown mr-2 lang-select-box' style=' padding-top: 5px'>
                            <select name='LANG' id='LANG' class='form-control form-control-sm   bg-dark border-0 rounded-0 text-white'>
                                <option value='BR' title='BR'>BR</option>
                                <option value='EN' title='EN'>EN</option>
                                <option value='CN' title='CN'>CN</option>
                            </select>
                        </li>
                        
                        <li class='nav-item dropdown'>
                            <a class='nav-link' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                <i class='fa fa-user-circle'></i>
                            </a>
                            <div class='dropdown-menu dropdown-menu-right' aria-labelledby='navbarDropdown'>
                                <a class='dropdown-item' href='javascript:void(0)'>{$user->nome}</a>
                                <a class='dropdown-item' href='/logout'>Saír do sistema</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        ";
        return $menu;
    }

    /**
     * @param string $appName
     * @return Page
     */
    public function setAppName(string $appName): Page
    {
        $this->appName = $appName;
        return $this;
    }


    /**
     * @param string $title
     * @return Page
     */
    public function setTitle(string $title): Page
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param array $cssFiles
     * @return Page
     */
    public function setCssFiles($cssFiles)
    {
        $this->cssFiles = $cssFiles;
        return $this;
    }

    /**
     * @param array $jsFiles
     * @return Page
     */
    public function setJsFiles($jsFiles)
    {
        $this->jsFiles = $jsFiles;
        return $this;
    }


    /**
     * @param mixed $body
     * @return Page
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @param array $mainmenu
     * @return Page
     */
    public function setMainmenu($mainmenu = [])
    {
        $this->mainmenu = $mainmenu;
        return $this;
    }


}