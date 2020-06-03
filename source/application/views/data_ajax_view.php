<?
if(isset($field)){
  $echo = $field.'&nbsp;';
  $echo .= '<button class=edit>отправить</button>';
  $echo .= '<button class=del>удалить</button>';
  $echo .= '<button class=cancel>отменить</button>';  
  $echo .= '<div class=error></div>';   
} 
echo $echo;

/**
 $echo  = '<form action="/data/edit" method="post" enctype="multipart/form-data" class="edit_data">';
  $echo .= $field.'&nbsp;';
  $echo .= '<input type=submit class=edit value="отправить" />';
  $echo .= '<button class=del>удалить</button>';
  $echo .= '<button class=cancel>отменить</button>'; 
  $echo .= '</form>';

*/