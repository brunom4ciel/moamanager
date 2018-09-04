<?php
/**
 * @package    IEA.Application
 *
 * @copyright  Copyright (C) 2015 - 2017 Open Source CIn/UFPE, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
namespace moam\libraries\core\menu;

defined('_EXEC') or die();

class Menu
{

    private $menu = array();

    public function remove($index)
    {
        if (isset($this->menu[$index]))
            unset($this->menu[$index]);
    }

    public function clear()
    {
        $this->menu = array();
    }

    public function getMenu()
    {
        return $this->menu;
    }

    public function count()
    {
        return count($this->menu);
    }

    public function add($href = "", $label = "", $title = "", $target = "", $img = "")
    {
        $this->menu[] = (object) array(
            "image"=>$img,
            "href" => $href,
            "label" => $label,
            "title" => $title,
            "target" => $target
        );
    }

    public function addTab()
    {
        $this->menu[] = null;
    }
    
    
    public function toHTML()
    {
        $result = "\n<ul id=\"nav\">\n";
        $title = "";
        $target = "";

        foreach ($this->menu as $item) 
        {

            if($item == null)
            {
                $result .= "\t<li style='height:3px;border: 3px solid #ccc;'></li>\n";
            }
            else 
            {
                if (empty($item->title))
                    $title = " title=\"" . $item->title . "\"";
    
                if (empty($item->target))
                    $target = " target=\"" . $item->target . "\"";
    
                $result .= "\t<li>";
                $result .= "";

                if(!empty($item->image))
                {
                    //$result .= "<img width='32px' align='middle' src='templates/default/images/" . $item->image . "'/>";    
                    //style='background-image: url(\"templates/default/images/menu/" . $item->image . "\");background-position: left top;   background-repeat: no-repeat;padding-left:32px;' 
                }
                
                $result .= "<a " . $title . $target . " href=\"" . $item->href . "\">".$item->label . "</a></li>\n";
            }
        }

        $result .= "</ul>\n";

        return $result;
    }
}

?>