<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/list-drops">List Drops</a> <span
                        class="divider"></li>
        </ul>
    </div>
    <?php
        if(isset($error)){
            if(is_array($error)){
                echo '<div class="alert alert-error span9">';
                foreach($error as $err){
                    echo $err . '<br />';
                }
                echo '</div>';
            } else{
                echo '<div class="alert alert-error span9">' . $error . '</div>';
            }
        }
        if(isset($success)){
            echo '<div class="alert alert-success span9">' . $success . '</div>';
        }
    ?>
    <div class="row-fluid">
        <div class="box span9">
            <div class="box-header well" data-original-title>
                <h2>Drops List</h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-content">
                <?php
                    echo '<table class="table">
                      <thead>
                          <tr>
                              <th>Title</th>
							  <th>Category</th>
                              <th>Language</th>
                              <th>Crete Date</th>
                              <th>Action</th>
                          </tr>
                      </thead>
                      <tbody>';
                    foreach($guides as $key => $value){
                        echo '<tr>
                            <td>' . htmlspecialchars($value['title']) . '</td>
							<td>' . $this->website->get_drop_cat($value['cat']) . '</td>
                            <td>' . htmlspecialchars($value['lang']) . '</td>
                            <td class="center">' . date(DATE_FORMAT, strtotime($value['date'])) . '</td>
                            <td class="center">
                                <a class="btn btn-info" href="' . $this->config->base_url . ACPURL . '/edit-drop/' . $value['id'] . '">
                                    <i class="icon-edit icon-white"></i>
                                    Edit
                                </a>
                                <a class="btn btn-danger" href="' . $this->config->base_url . ACPURL . '/delete-drop/' . $value['id'] . '">
                                    <i class="icon-trash icon-white"></i>
                                    Delete
                                </a>
                            </td>
                          </tr>';
                    }
                    echo '</tbody></table>';
                ?>
            </div>
        </div>
        <div class="box span3">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Drops Manager</h2>
            </div>
            <div class="box-content">
                <?php
                    $this->load->view('admincp' . DS . 'view.panel_drops_manager');
                ?>
            </div>
        </div>
    </div>
</div>