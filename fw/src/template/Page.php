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
    private $complementos = [];

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
                    <div class='modal fade' data-keyboard='false' id='modal' tabindex='-1' role='dialog'>
                        <div class='modal-dialog modal-lg' role='document'>
                            <div class='modal-content' id='modaldiv'></div>
                        </div>
                    </div>
                    {$js}
                    <script>
                        chatrun.newmodeActive();
                    </script>
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

    public function noPermissionDialog() {
        echo <<<HTML
            <div class="p-3">
                <div class="alert alert-warning mb-0">
                    Você não tem permissão para acessar essa ferramenta.
                </div>
            </div>
        HTML;
        die();
    }

    public function addComplementMenu($comp) {
        $this->complementos[] = $comp;
    }

    public function generateMenu()
    {
        global $user, $db;
        $parts = [];
        $menus = json_decode(json_encode($this->mainmenu));
        if (is_object($menus)) {
            foreach ($menus as $menu => $item) {
                if (is_object($item)) {
                    $subparts = [];
                    $submenuscounter = 0;
                    $alertCounter = 0;

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

                            $alertItem = "";
                            if ($menu=='Apps') {

                                if ($sub=='Tickets') {
                                    $notificacao = (new \App\Brasiltec\Controller\TicketController())->getCount($user->id);
                                    $alertItem = "<div class='position-relative'>
                                            <div style='position: absolute; background: red; width: 16px; height: 16px; font-size: 10px; left: -18px; color: white; top: -19px;' class='rounded-circle d-flex'>
                                                <div class='m-auto'>{$notificacao}</div>
                                            </div>
                                        </div>";

                                    $alertCounter += $notificacao;
                                } elseif ($sub=='Comunidade') {
                                    $notificacao = $db->getResultSet("select count(q.id) as total from tb_community_question q
                                                                                    left join tb_community_answer a on a.tb_community_question_id = q.id
                                                                                    where a.id is null
                                                                                    and q.excluido = 0")['total'];
                                    $alertItem = "<div class='position-relative'>
                                            <div style='position: absolute; background: red; width: 16px; height: 16px; font-size: 10px; left: -18px; color: white; top: -19px;' class='rounded-circle d-flex'>
                                                <div class='m-auto'>{$notificacao}</div>
                                            </div>
                                        </div>";

                                    $alertCounter += $notificacao;
                                }

                            }

                            $subparts[] = "<a class='dropdown-item'  style='min-width: 220px' href='{$url}'>
                                                 {$sub}{$alertItem}
                                            </a>";
                        }
                        $submenuscounter++;
                    }
                    $subparts = implode("\n", $subparts);

                    $toggler = "dropdown-toggle";
                    $dropRig = "";
                    $alert = "";

                    if ($menu=='Apps') {
                        $menu = "<svg width='24' height='24' style='margin-top: -4px' viewBox='0 0 24 24' focusable='false' role='presentation'><path fill='currentColor' fill-rule='evenodd' d='M4 5.01C4 4.451 4.443 4 5.01 4h1.98C7.549 4 8 4.443 8 5.01v1.98C8 7.549 7.557 8 6.99 8H5.01C4.451 8 4 7.557 4 6.99V5.01zm0 6c0-.558.443-1.01 1.01-1.01h1.98c.558 0 1.01.443 1.01 1.01v1.98C8 13.549 7.557 14 6.99 14H5.01C4.451 14 4 13.557 4 12.99v-1.98zm6-6c0-.558.443-1.01 1.01-1.01h1.98c.558 0 1.01.443 1.01 1.01v1.98C14 7.549 13.557 8 12.99 8h-1.98C10.451 8 10 7.557 10 6.99V5.01zm0 6c0-.558.443-1.01 1.01-1.01h1.98c.558 0 1.01.443 1.01 1.01v1.98c0 .558-.443 1.01-1.01 1.01h-1.98c-.558 0-1.01-.443-1.01-1.01v-1.98zm6-6c0-.558.443-1.01 1.01-1.01h1.98c.558 0 1.01.443 1.01 1.01v1.98C20 7.549 19.557 8 18.99 8h-1.98C16.451 8 16 7.557 16 6.99V5.01zm0 6c0-.558.443-1.01 1.01-1.01h1.98c.558 0 1.01.443 1.01 1.01v1.98c0 .558-.443 1.01-1.01 1.01h-1.98c-.558 0-1.01-.443-1.01-1.01v-1.98zm-12 6c0-.558.443-1.01 1.01-1.01h1.98c.558 0 1.01.443 1.01 1.01v1.98C8 19.549 7.557 20 6.99 20H5.01C4.451 20 4 19.557 4 18.99v-1.98zm6 0c0-.558.443-1.01 1.01-1.01h1.98c.558 0 1.01.443 1.01 1.01v1.98c0 .558-.443 1.01-1.01 1.01h-1.98c-.558 0-1.01-.443-1.01-1.01v-1.98zm6 0c0-.558.443-1.01 1.01-1.01h1.98c.558 0 1.01.443 1.01 1.01v1.98c0 .558-.443 1.01-1.01 1.01h-1.98c-.558 0-1.01-.443-1.01-1.01v-1.98z'></path></svg>";
                        $toggler = '';
                        if ($alertCounter>0) {
                            $alert = "<div class='position-relative'>
                                        <div style='position: absolute; background: red; width: 16px; height: 16px; font-size: 10px; right: -5px; color: white; top: -27px;' class='rounded-circle d-flex'>
                                            <div class='m-auto'>{$alertCounter}</div>
                                        </div>
                                    </div>";
                        }
                    } else {
                        if ((new Strings($menu))->startsWith("<")) {
                            $toggler = '';
                            $dropRig = 'dropdown-menu-right';
                        }
                    }
                    $parts[] = "<li class='nav-item dropdown'>
                                <a class='nav-link {$toggler}' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                    {$menu}{$alert}
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


        if (!isset($user->preferencias->desabilitar_chat) || ($user->preferencias->desabilitar_chat==0)) {
            $parts[] = "<li class='nav-item'>
                            <a class='nav-link' href='javascript:void(0)' onclick='$(\".chatbox-toggle\").click()'>
                                <i class='fas fa-comment-alt'></i>
                                <div class='position-relative'>
                                    <div style='position: absolute; background: red; width: 16px; height: 16px; font-size: 10px; left: 8px; color: white; top: -27px;' class='rounded-circle d-flex'>
                                        <div class='m-auto chat-total-notificacoes'>0</div>
                                    </div>
                                </div>
                            </a>
                        </li>";
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
                            <select name='LANG' id='LANG' class='form-control form-control-sm bg-dark border-0 rounded-0 text-white'>
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