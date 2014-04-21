<?php

class Pagination {
     
    private static $paginationData = array();
    
    protected static $config = array(
        'url'           => null,
        'current_page'  => 1,
        'per_page'      => 10,
        'total_items'   => 0
    );
    
    public static function create($config){
        
        //Merge passed in config with default config
        self::$config = array_merge(self::$config, $config);
        
        $totalPages     = ceil(self::$config['total_items'] / self::$config['per_page']);
        $currentPage    = self::$config['current_page'];
        $previousPage   = ($currentPage <= 1) ? null : $currentPage-1;
        $nextPage       = ($currentPage >= $totalPages) ? null : $currentPage+1; 
        
        self::$paginationData = array(            
            'total_pages'   => $totalPages,
            'current_page'  => $currentPage,
            'previous_page' => $previousPage,
            'next_page'     => $nextPage
        );   
        
        self::$paginationData['render'] = self::render();
        
        return self::$paginationData;
        
    }
    
    
    private static function render(){   
        
        $render = '<ul class="pagination">';
            $render .= '<li' . ((self::$paginationData['previous_page']) ? '' : ' class="disabled"') . '><a href="' . self::urlPage(self::$paginationData['previous_page']) .'">&laquo;</a></li>';
            
            //Loop through the pages
            for($page = 1; $page <= self::$paginationData['total_pages']; $page++){
                $render .= '<li' . ((self::$paginationData['current_page'] == $page) ? ' class="active"' : '') . '><a href="' . self::urlPage($page) .'">' . $page . '</a></li>';
            }            
            
            $render .= '<li' . ((self::$paginationData['next_page']) ? '' : ' class="disabled"' ) . '><a href="' . self::urlPage(self::$paginationData['next_page']) .'">&raquo;</a></li>';
        $render .= '</ul>';
        
        return $render;
        
    }
    
    private static function urlPage($page = 1){
        return str_replace("{page}", $page, self::$config['url']);
    }
    
    /*
     * <ul class="pagination">
        <li><a href="#">«</a></li>
        <li><a href="#">1</a></li>
        <li class="active"><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li><a href="#">»</a></li>
      </ul>
     */
    
    
}