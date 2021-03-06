<?php
/**
 * This is a php class version of AdminLTE2 for quick integration
 * configuration done through file : config.json
 * @author jambonbill
 */
namespace LTE;

/**
* @brief Class providing adminlte2 skeleton
*/
class AdminLte2
{

    /**
     * Static path to assets
     * @var string
     */
    private $path='';// static path
    private $config=[];//admin config from json file
    private $title= 'title';// document title
    private $headHtml= '';
    private $lang= 'en';//$_SERVER['HTTP_ACCEPT_LANGUAGE']

    private $navbarCustomMenu='';//html
    private $userPanel='';//html
    private $DEBUG=false;

    /**
     * AdminLte Constructor
     * @param boolean $private [description]
     */
    public function __construct()
    {
        // get the config file. it must be located next to the class
        $configjson=__DIR__."/config.json";

        if(is_file($configjson)){
            $string = file_get_contents($configjson);
            $this->config=json_decode($string);
            $err=json_last_error();
            if($err){
                die("Error: Invalid config.json");
            }else{
                //find the correct path for assets
                $diff=count(explode("/",realpath('.')))-count(explode("/",realpath(__DIR__."/../../web")));
                //echo "diff=$diff\n";
                $this->path=str_repeat("../", $diff);
            }
        }else{
            throw new \Exception("Error : config.json file not found in ".realpath("."), 1);
        }


        $this->lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);// set language
        if($this->lang!='fr')$this->lang='en';
    }

    /**
     * Get/Set config
     * @param  array  $config [description]
     * @return [type]         [description]
     */
    public function config($config=[])
    {
        if($config){
            $this->config=$config;
        }
        return $this->config;
    }


    /**
     * Return detected language
     * @return [type] [description]
     */
    public function lang()
    {
        return $this->lang;
    }


    /**
     * Set page title
     * @param  string $title [description]
     * @return [type]        [description]
     */
    public function title($title = '')
    {
        $this->title = $title;
        return $this->title;
    }

    /**
     * return the admin html
     * @return string html
     */
    public function html()
    {
        $html=[];
        $html[]=$this->head();
        $html[]=$this->body();
        $html[]=$this->header();
        $html[]=$this->leftside();
        $html[]=$this->scripts();
        $html[]='<aside class="right-side">';
        return implode("",$html);
    }

    public function __toString()
    {
        return $this->html();
    }


    /**
     * GET/SET config meta's
     * @return [type] [description]
     */
    public function meta($meta=[])
    {
        if (isset($meta)&&is_array($meta)) {
            $this->config->meta=$meta;
        }
        return $this->config->meta;
    }


    /**
     * head
     * bring the headers, and initial assets
     * @return [type] [description]
     */
    public function head()
    {
        $HTML = [];
        $HTML[] = '<!DOCTYPE html>';
        $HTML[] = '<html lang="' . $this->lang() . '">';
        $HTML[] = '<head>';
        $HTML[] = '<meta charset="UTF-8">';
        $HTML[] = "<title>" . $this->title . "</title>";
        if(isset($this->config->apple_app_icon)){
            $HTML[] = '<link rel="apple-touch-icon" href="' . $this->path . $this->config->apple_app_icon . '">';
        }

        $HTML[] = "<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>";
        $HTML[] = '<meta name="apple-mobile-web-app-status-bar-style" content="black" />';
        $HTML[] = '<meta name="apple-mobile-web-app-capable" content="yes" />';

        if (isset($this->config->meta)&&is_array($this->config->meta)) {
            foreach($this->config->meta as $meta){
                $values=[];
                foreach($meta as $k=>$v){
                    $values[]=$k.'="'.$v.'"';
                }
                $HTML[]="<meta ".implode(' ',$values).">";
            }
        }

        if($this->config->favicon && is_file($this->path.$this->config->favicon)){
            $HTML[]='<link id="favicon" rel="shortcut icon" href="'.$this->path.$this->config->favicon.'">';
        }

        // Css
        if(isset($this->config->css)){
            foreach ($this->config->css as $v) {
                if (preg_match("/^http/i",$v)) {
                    $HTML[]='<link href="'.$v.'" rel="stylesheet" type="text/css" />';
                } else {
                    $HTML[]='<link href="'.$this->path.$v.'" rel="stylesheet" type="text/css" />';
                }
            }
        }

        // extra head //
        if ($this->headHtml) {
            $HTML[]=$this->headHtml;
        }

        $HTML[]="</head>";

        return implode("\n", $HTML);
    }


    /**
     * Get/Set extra html to <head> (apple links, tracking code, what you want)
     * @return [type] [description]
     */
    public function headHtml($htm='')
    {
        $this->headHtml=$htm;
        return $this->headHtml;
    }


    /**
     * body
     * bring the headers, and initial assets
     * @return [type] [description]
     */
    public function body()
    {
        $HTML=[];
        $class=[];
        //$class[]='skin-blue';
        if($this->config()->layout->skin)$class[]=$this->config()->layout->skin;
        else $class[]='skin-blue';

        if($this->config()->layout->fixed)$class[]='fixed';
        if($this->config()->layout->{'sidebar-collapse'})$class[]='sidebar-collapse';
        if($this->config()->layout->{'layout-boxed'})$class[]='layout-boxed';

        $HTML[]="<body class='".implode(" ",$class)."'>";
        $HTML[]='<div class="wrapper">';
        return implode("\n", $HTML);
    }


    /**
     * header
     * this is NOT the html header, but the ADMIN header (top bar)
     * @return [type] [description]
     */
    public function header()
    {
        $HTML=[];
        //$HTML[]=__FUNCTION__."()";

        // header logo: style can be found in header.less -->
        //$HTML[]='<header class="main-header">';


        $title="Admin";
        if (isset($this->config->title)) {
            $title=$this->config->title;
        }

        //$HTML[]='<a href="?" class="logo">';
        if(isset($this->config->homeurl))$homeurl=$this->path.$this->config->homeurl;
        else $homeurl='#';
        //$HTML[]="<a href='$homeurl'>$title</a>";
        //$HTML[]='<a class="navbar-brand" href="#">Navbar</a>';
        //$HTML[]='</a>';

        // Header Navbar: style can be found in header.less -->
        //$HTML[]='<nav class="navbar navbar-static-top" role="navigation">';

        // Sidebar toggle button //
        /*
        $HTML[]='<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">';
        $HTML[]='<span class="sr-only">Toggle navigation</span>';
        $HTML[]='</a>';
        */
        /*
        $HTML[]='<li class="nav-item" style="list-style: none;">';
        $HTML[]='<button class="nav-link navbar-toggler sidebar-toggle hidden-md-down" data-toggle="offcanvas" ></button>';
        $HTML[]='</li>';
        */
        /*
        $HTML[]='<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">';
        $HTML[]='<span class="navbar-toggler-icon"></span>';
        $HTML[]='</button>';
        */


        // Navbar right menu
        /*
        $HTML[]='<div class="navbar-custom-menu">';
        //$HTML[]='navbar-custom-menu';
        $HTML[]=$this->navbarCustomMenu;
        $HTML[]='</div>';
        */

        //$HTML[]='</nav>';

        $HTML[]=$this->newNav();
        //$HTML[]='</header>';

        return implode("\n", $HTML);
    }

    private $user=[];
    public function user($USR=[]){
        $this->user=$USR;
    }

    private function newNav(){

        if(isset($this->config->homeurl))$homeurl=$this->path.$this->config->homeurl;
        else $homeurl='#';

        $htm='<nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">';

        $htm.='<a class="navbar-brand" href="'.$homeurl.'" style="width:300px">'.$this->config()->title.'</a>';

          //<!--nav-link navbar-toggler sidebar-toggle-->
        $htm.='<button class="nav-link navbar-toggler sidebar-toggle" data-toggle="offcanvas" style="display:inline">';
        $htm.='    <span class="navbar-toggler-icon"></span>';
        $htm.='</button>';


        $htm.='<div class="collapse navbar-collapse" id="navbarsExampleDefault">';

        $htm.='<ul class="navbar-nav mr-auto">';
          $htm.='<li class="nav-item">';
            if($this->user)$htm.='<a class="nav-link disabled" href="#">'.$this->user['email'].'</a>';
          $htm.='</li>';

        $htm.='</ul>';

        $htm.='<ul class="navbar-nav flex-row ml-md-auto d-none d-md-flex">';

        if($this->user){
            $htm.='<li class="nav-item dropdown">';
            $htm.='<a class="nav-item nav-link dropdown-toggle mr-md-2" href="#" id="bd-versions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';

            //$htm.='User NAME';
            $htm.=$this->user['first_name'].' '.$this->user['last_name'];

            $htm.='</a>';
            $htm.='<div class="dropdown-menu dropdown-menu-right" aria-labelledby="bd-versions">';
                $htm.='<a class="dropdown-item" href="../home"><i class="fa fa-home"></i> Home</a>';
                $htm.='<a class="dropdown-item" href="../userinfo"><i class="fa fa-edit"></i> Profile</a>';
                $htm.='<a class="dropdown-item" href="../login/logout.php"><i class="fa fa-sign-out"></i> Log out</a>';
            $htm.='</div>';
            $htm.='</li>';
        }


        $htm.='</ul>';


        $htm.='</div>';
        $htm.='</nav>';
        return $htm;
    }




    /**
     * Set top navbar html
     * Usefull for user messages
     * @param  string $htm [description]
     * @return [type]      [description]
     */
    public function navbarCustomMenu($htm = '')
    {
        if($htm&&is_array($htm)){
            $htm=implode('',$htm);
        }

        if($htm)$this->navbarCustomMenu=$htm;
        return $this->navbarCustomMenu;
    }


    /**
     * Get/Set the menu user panel (first thing to appear in the left menu)
     * @param  string $htm [description]
     * @return [type]      [description]
     */
    public function userPanel($htm = '')
    {
        if($htm&&is_array($htm)){
            $htm=implode('',$htm);
        }

        if($htm)$this->userPanel=$htm;
        return $this->userPanel;
    }




    /**
     * left side
     */
    public function leftside()
    {
        $HTML=[];
        $HTML[]='<div class="wrapper row-offcanvas row-offcanvas-left">';
        //$HTML[]='<aside class="left-side sidebar-offcanvas">';//old
        $HTML[]='<aside class="main-sidebar">';//new
        // sidebar: style can be found in sidebar.less -->
        $HTML[]='<section class="sidebar">';

        // Sidebar user panel -->
        $HTML[]=$this->userPanel();


        // search field /
        if(isset($this->config->menusearch) && $this->config->menusearch){
            $HTML[]='<div class="sidebar-form input-group">';
            $HTML[]='<input type="text" id="q" name="q" class="form-control" placeholder="Search ...">';
            $HTML[]='<span class="input-group-btn">';
            $HTML[]='<button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>';
            $HTML[]='</span>';
            $HTML[]='</div>';
        }

        // sidebar menu: : style can be found in sidebar.less -->
        //$HTML[]= $this->menu();
        $HTML[]= $this->menu();

        $HTML[]='</section>';
        $HTML[]='</aside>';
        return implode("\n", $HTML);
    }


    /**
     * Return left menu
     * @return string html
     */
    public function menu($json = ''){

        $menu=$this->config->menu;

        if(!isset($menu)){
            //throw new \Exception("Error : $this->config->menu must be a object", 1);
            return '';
        }

        if(!is_object($this->config->menu))
        {

            if ($this->config->menu&&is_file(__DIR__.'/'.$this->config->menu)) {

                $content=file_get_contents(__DIR__.'/'.$this->config->menu);
                $this->config->menu=json_decode($content);

                $err=json_last_error();

                if($err){
                    die("error $err".json_last_error_msg()."<br>$content");
                    //throw new \Exception("JSON Error $err", 1);
                }

            } else {
                die($this->config->menu . " not found");
                return '';
            }
        }else{

        }

        $HTML=[];
        $HTML[]='<ul class="sidebar-menu">';

        foreach(@$this->config->menu as $name=>$o){

            $title='';
            $class='';

            if(!$o)continue;
            if(isset($o->class))$class='class="'.$o->class.'"';
            if(isset($o->title))$title='title="'.$o->title.'"';
            if(isset($o->sub))
            {
                $HTML[]='<li class="treeview" '.$title.'>';
                //if(!isset($o->url))$o->url='#';
                if(!isset($o->icon))$o->icon='';
                $HTML[]='<a href="'.@$o->url.'">';
                $HTML[]='<i class="'.$o->icon.'"></i> <span>'.$o->text.'</span>';
                $HTML[]='<i class="fa fa-angle-left pull-right"></i>';
                $HTML[]='</a>';
                $HTML[]='<ul class="treeview-menu">';
                foreach($o->sub as $obj){
                    $HTML[]='<li>';
                    if(isset($obj->url))$HTML[]="<a href='".$this->path.$obj->url."'>";
                    if(isset($obj->icon))$HTML[]="<i class='".$obj->icon."'></i> ";
                    $HTML[]='<span>'.$obj->text.'</span></a>';
                    $HTML[]='</li>';
                }
                $HTML[]='</ul>';
                $HTML[]='</li>';
            }
            else
            {
                $HTML[]='<li '.$class.' '.$title.'>';
                if(isset($o->url))$HTML[]='<a href="'.$this->path.$o->url.'">';
                if(isset($o->icon))$HTML[]='<i class="'.$o->icon.'"></i> ';
                $HTML[]='<span>'.@$o->text.'</span>';
                //$HTML[]='<small class="label pull-right bg-green">new</small>';//small
                if(isset($o->url))$HTML[]='</a>';
                $HTML[]='</li>';
            }
        }
        $HTML[]='</ul>';
        return implode('', $HTML);
    }

    /**
    * @brief the list of js scripts to be included
    * @returns html
    */
    public function scripts()
    {
        if(!isset($this->config->js)){
            return '';
        }

        $HTML=[];
        foreach ($this->config->js as $k => $js) {
            if (preg_match("/^http/", $js)) {
                $HTML[]='<script src="' . $js . '" type="text/javascript"></script>';
            } else {
                $HTML[]='<script src="' . $this->path . $js . '" type="text/javascript"></script>';
            }
        }
        return implode("\n", $HTML);
    }



    private $footer;

    /**
     * Define footer. The footer is displayed only when "end()" is called
     * @param  string $body [description]
     * @return [type]       [description]
     */
    public function footer($body=''){
        if ($body) {
            $HTML=[];
            $HTML[]='<footer class="main-footer">';
            //$HTML[]='<div class="pull-right hidden-xs">';
            //$HTML[]='<b>Version</b> 2.3.0';
            //$HTML[]='</div>';
            $HTML[]=$body;
            $HTML[]='</footer>';
            $this->footer=implode("\n",$HTML);
        }
        return $this->footer;
    }

    /**
     * Properly finish the html document and end the script
     * @return [type] [description]
     */
    public function end()
    {
        $HTML=[];
        $HTML[]="</aside>";// end aside class="right-side"
        $HTML[]=$this->footer();
        $HTML[]="</div>";
        $HTML[]="</body>";
        $HTML[]="</html>";
        echo implode("\n",$HTML);
        exit;
    }
}