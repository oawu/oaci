<a href="<?php echo RestfulUrl::index ();?>">回列表</a>
<hr>

ID：<?php echo $article->id;?>
<br/>
標題：<?php echo $article->title;?>
<br/>
內容：<?php echo $article->content;?>
<hr/>
最後修改：<?php echo $article->updated_at;?>
<br/>
產生時間：<?php echo $article->created_at;?>