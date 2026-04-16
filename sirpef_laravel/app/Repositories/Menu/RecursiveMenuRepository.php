<?php
namespace App\Repositories\Menu;

class RecursiveMenuRepository
{
   /**
   * Return an array of recursive.
   *
   * @return Array
   */    
    static public function recursive(Array $menuIds = []): Array

        {
            $query = \App\Models\Menu::whereNull("menu_id");
    
            if ($menuIds) $query->whereIn("id", $menuIds);
            
            $menu = $query->with(
                "childrenMenus",                    
                    fn ($query) => $menuIds 
                        ? $query->whereIn("id", $menuIds)
                        : $query
            )->get();
            
            return json_decode($menu);
        } 
 
}
