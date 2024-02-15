<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/list-guides">List Guides</a> <span
                        class="divider"></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span9">
            <div class="box-header well" data-original-title>
                <h2><i class="icon-edit"></i>Edit Guide</h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-content">
                <?php
                    if(isset($error)){
                        echo '<div class="alert alert-error">' . $error . '</div>';
                    }
                    if(isset($success)){
                        echo '<div class="alert alert-success">' . $success . '</div>';
                    }
                ?>
                <form class="form-horizontal" method="post" action="">
                    <input type="hidden" name="edit_guide" value="1"/>
                    <fieldset>
                        <legend></legend>
                        <div class="control-group">
                            <label class="control-label" for="typeahead">Guide Title</label>

                            <div class="controls">
                                <input type="text" class="input-xlarge" name="title" id="title"
                                       value="<?php if(ctype_xdigit($guide['title'])){ echo hex2bin($guide['title']); } else {echo  $guide['title']; } ?>"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="news_lang">Language </label>

                            <div class="controls">
                                <select id="lang" name="lang">
                                    <?php
                                        $languages = $this->website->lang_list();
                                        krsort($languages);
                                        foreach($languages as $key => $lang):
                                            $selected = ($guide['lang'] == $key) ? 'selected="selected"' : '';
                                            echo '<option value="' . $key . '" ' . $selected . '>' . $lang . '</option>' . "\n";
                                        endforeach;
                                    ?>
                                </select>

                                <p class="help-block">Guide for which translation.</p>
                            </div>
                        </div>
                         <div class="control-group">
                            <label class="control-label">Category </label>

                            <div class="controls">
                                <select name="category">
                                    <?php
                                        $catConfig = $this->config->values('guides_category');
                                        $newCats = [];
                                        foreach($catConfig as $key => $cat){
                                            if(isset($cat['parent_id']) && $cat['parent_id'] != ''){
                                                $newCats[$cat['parent_id']][$key] = $key;
                                            }
                                           
                                        }
                                        
                                        foreach($catConfig as $key => $cat){
                                            if(isset($newCats[$key])){
                                               
                                                echo '<optgroup label="'.$cat['name'].'">';
                                                     foreach($newCats[$key] as $k => $c){
                                                           $selected = ($guide['category'] == $k) ? 'selected="selected"' : '';
                                                          echo '<option value="' . $k . '" ' . $selected . '>' . $catConfig[$k]['name'] . '</option>' . "\n";
                                                          unset($catConfig[$k]);
                                                     }
                                                echo '</optgroup>';
                                            } 
                                            else{  
                                                $selected = ($guide['category'] == $key) ? 'selected="selected"' : '';
                                                if(!isset($cat['parent_id']) || $cat['parent_id'] == ''){
                                                    echo '<option value="' . $key . '" ' . $selected . '>' . $cat['name'] . '</option>' . "\n";
                                                }
                                            }
                                        }
                                    ?>
                                </select>

                                <p class="help-block">Guide Category.</p>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="guide">Guide</label>

                            <div class="controls">
                                <textarea class="cleditor" id="guide" name="guide"
                                          rows="4"><?php if(ctype_xdigit($guide['text'])){ echo hex2bin($guide['text']); } else { echo $guide['text']; } ?></textarea>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
        <div class="box span3">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Guides Manager</h2>
            </div>
            <div class="box-content">
                <?php
                    $this->load->view('admincp' . DS . 'view.panel_guides_manager');
                ?>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            var editorName = $('.cleditor').attr('name');
            CKEDITOR.replace(editorName);
        });
    </script>
</div>