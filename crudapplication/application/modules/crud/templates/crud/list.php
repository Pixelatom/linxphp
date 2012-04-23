 <div class="row">
     <div class="span2" >
        <p><a class="btn" href="<?=Url::factory()->set_param('route', $controllername . '/add') ?>"> Agregar <?=(isset($label)) ? $label : $modelname ?> </a></p>
    </div>
    <!-- actual content -->
    <div class="span10">
        
        <!-- start filter form -->
        <form name="searchform" action="<?=Url::factory()->remove_param('q')?>" method="GET" enctype="multipart/form-data" class="well form-search">
            <input name="q" value="<?=(isset($_GET['q'])) ? $_GET['q'] : ''?>" type="text" class="input-medium search-query">
            <button type="submit" class="btn">Search</button>
        </form>
        
        <!-- end filter form -->
        

        <!--  start product-table ..................................................................................... -->
        <form class="form-inline" id="mainform"  method="post" action="<?=Url::factory()->set_param('route', $controllername . '/listitems') ?>">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th><input class="check-all" type="checkbox" /></th>

                        <? foreach ($headers as $header): ?>
                        <th ><?=$header ?></th>
                        <? endforeach; ?>
                        <th >Options</th>
                    </tr>
                </thead>
                <?foreach ($items as $row):?>
                <tbody>
                    <tr>
                        <td><input value="<?=$row->$id_property?>" name="list[]" type="checkbox" /></td>
                        <? foreach ($values[$row->$id_property] as $property): ?>
                        <td><?= $property ?></td>
                        <? endforeach; ?>
                        <td>
                            <!-- Icons -->
                             <a class="btn btn-mini"  href="<?=Url::factory()->set_param(Application::get_router_param(), "{$controllername}/edit/" . $row->$id_property) ?>" title="Edit">Edit</a>
                             <a class="btn btn-mini" href="<?=Url::factory()->set_param(Application::get_router_param(), "{$controllername}/delete/" . $row->$id_property) ?>" title="Delete">Detele</a>
                        </td>
                    </tr>
                </tbody>
                <? endforeach; ?>
            </table>
            
            <!-- bulk actions box -->
            <fieldset>
                
                
                        <select name="dropdown">
                            <option value="option1">Choose an action...</option>
                            <option value="option3">Delete</option>
                        </select>
                        <button type="submit" class="btn">Apply</button>
                
            </fieldset>
            
                
            
                
            
            <!-- bulk actions box -->

            <!-- pagination -->
            <div class="pagination">

                <li class="prev <?=($page-1<1)?'disabled':''?>">
                <a  href="<?=Url::factory()->set_param('page', $page - 1) ?>" title="Previous Page">&laquo; Previous</a>
                </li>
                <?php
                $i = 0;
                $page = Url::factory()->get_param('page',1);
                ?>

                <?for($i= $page - 2; $i<= $page + 2; $i++ ):?>
                <?if ($i>0 and $i<=$pagecount):?>
                <li class="<?=($page==$i)?'active':''?>">
                <a href="<?=Url::factory()->set_param('page', $i) ?>" title="<?=$i?>"><?=$i?></a>
                </li>
                <?endif;?>
                <?endfor;?>
                <li class="next <?=($page+1>$pagecount)?'disabled':''?>">
                <a  href="<?=Url::factory()->set_param('page', $page + 1) ?>" title="Next Page">Next &raquo;</a>
                </li>
                
            </div> <!-- End .pagination -->
            <!--  end content-table  -->
        </form>
        <div class="clear"></div>
    </div>
    
</div>



