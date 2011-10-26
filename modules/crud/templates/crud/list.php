 <div class="row">

    <!-- actual content -->
    <div class="span11">
        <div>
        <!-- start filter form -->
        <form action="<?=UrlFactory::remove_param('q')?>" method="GET" enctype="multipart/form-data" class="forms" name="form" >
            <fieldset>
                <div class="clearfix">
                    <label for="xlInput">Buscar</label>
                    <div class="input">
                        <input class="text-input small-input" type="text" value="<?=(isset($_GET['q'])) ? $_GET['q'] : ''?>"  name="q" />
                        <button type="submit" class="btn">Enviar</button>
                    </div>
                </div>
            </fieldset>
        
        </form>
        
        <!-- end filter form -->
        </div>

        <!--  start product-table ..................................................................................... -->
        <form id="mainform"  method="post" action="<?=UrlFactory::set_param('route', $controllername . '/listitems') ?>">
            <table class="zebra-striped">
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
                             <a  href="<?=UrlFactory::set_param(Application::get_router_param(), "{$controllername}/edit/" . $row->$id_property) ?>" title="Edit">Editar</a>
                             <a  href="<?=UrlFactory::set_param(Application::get_router_param(), "{$controllername}/delete/" . $row->$id_property) ?>" title="Delete">Borrar</a>
                        </td>
                    </tr>
                </tbody>
                <? endforeach; ?>
            </table>
            <!-- bulk actions box -->
            <fieldset>
                <div class="clearfix">
                    <div >
                        <select name="dropdown">
                            <option value="option1">Choose an action...</option>
                            <option value="option3">Delete</option>
                        </select>
                        <button type="submit" class="btn primary">Aplicar</button>
                    </div>
                </div>
            </fieldset>
                
                
            
            <!-- bulk actions box -->

            <!-- pagination -->
            <div class="pagination">

                <li class="prev <?=($page-1<1)?'disabled':''?>">
                <a  href="<?=UrlFactory::set_param('page', $page - 1) ?>" title="Previous Page">&laquo; Previous</a>
                </li>
                <?php
                $i = 0;
                $page = UrlFactory::get()->get_param('page',1);
                ?>

                <?for($i= $page - 2; $i<= $page + 2; $i++ ):?>
                <?if ($i>0 and $i<=$pagecount):?>
                <li class="<?=($page==$i)?'active':''?>">
                <a href="<?=UrlFactory::set_param('page', $i) ?>" title="<?=$i?>"><?=$i?></a>
                </li>
                <?endif;?>
                <?endfor;?>
                <li class="next <?=($page+1>$pagecount)?'disabled':''?>">
                <a  href="<?=UrlFactory::set_param('page', $page + 1) ?>" title="Next Page">Next &raquo;</a>
                </li>
                
            </div> <!-- End .pagination -->
            <!--  end content-table  -->
        </form>
        <div class="clear"></div>
    </div>
    <div class="span3">
        <p><a class="btn success" href="<?=UrlFactory::set_param('route', $controllername . '/add') ?>"> Agregar <?=(isset($label)) ? $label : $modelname ?> </a></p>
    </div>
</div>



