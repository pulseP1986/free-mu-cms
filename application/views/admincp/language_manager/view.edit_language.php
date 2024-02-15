<div id="content" class="span10">
    <div>
        <ul class="breadcrumb">
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>">Home</a> <span class="divider">/</span></li>
            <li><a href="<?php echo $this->config->base_url . ACPURL; ?>/languages">List Languages</a></li>
        </ul>
    </div>
    <div class="row-fluid">
        <div class="box span9">
            <div class="box-header well" data-original-title>
                <h2>Add Language String</h2>

                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-content">
                <form role="form" name="new_language_string" id="new_language_string" method="POST" action="<?php echo $this->config->base_url . ACPURL; ?>/add-string">
					<input type="hidden" name="lang" id="lang" value="<?php echo $lang;?>" />
					<div class="box-body">
						<div class="form-group">
						  <label>Key</label>
						  <input type="text" class="form-control input-xxlarge" name="key" id="key" placeholder="Enter Key" required  />
						</div>
						<div class="form-group">
						  <label>Text</label>
						  <textarea class="form-control input-xxlarge" rows="3" name="text" id="text" placeholder="Enter text" required></textarea>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-primary">Submit</button>
					</div>
				</form>
            </div>
        </div>
        <div class="box span3">
            <div class="box-header well">
                <h2><i class="icon-edit"></i> Language Manager</h2>
            </div>
            <div class="box-content">
                <?php
                    $this->load->view('admincp' . DS . 'view.panel_language_manager');
                ?>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header well" data-original-title>
                <h2>Translate language strings</h2>
                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                </div>
            </div>
            <div class="box-content">
				<table id="language_strings" class="table table-bordered table-striped bootstrap-datatable">
				 <thead>
					 <tr>
						<th>Key</th>
						<th>Text</th>
					</tr>
					</thead>
					<tbody>
					<?php 

					foreach($newStrings as $key => $tr){ 
						$tkey = array_keys($tr);

					?>
					<tr>
						<td><?php echo htmlentities($tkey[0]); ?></td>
						<td data-order="<?php echo $key; ?>" data-search="<?php echo htmlentities($tr[$tkey[0]][0]); ?>">
							<?php if(strlen($tr[$tkey[0]][0]) > 150){ ?>
							<textarea class="form-control input-xxlarge" rows="3" id="langstring*<?php echo $key; ?>"><?php echo htmlentities($tr[$tkey[0]][0]); ?></textarea>
							<?php } else { ?>
							<input class="form-control input-xxlarge" type="text" id="langstring*<?php echo $key; ?>" value="<?php echo htmlentities($tr[$tkey[0]][0]); ?>" />
							<?php } ?>
						</td>
					</tr>
					<?php 
					} 
					?>
					</tbody>
					<tfoot>
					<tr>
						<th>Key</th>
						<th>Text</th>
					</tr>
					</tfoot>
				</table>
            </div>
        </div>
    </div>
	<script>
	  $(document).ready(function(){
		var table = $('#language_strings').DataTable({
				"dom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span12'i><'span12 center'p>>",
				"pagingType": "bootstrap",
				"pageLength": 25,
				"stateSave": true,
				"processing": true
			});

		$('#new_language_string').on('submit', function(e){
		  e.preventDefault();
		  $.ajax({
			type: 'POST',
			url: $(this).attr('action'),
			data: $(this).serialize(),
			dataType: "json",
			success: function(data){
			  if(data.error){
				noty($.parseJSON('{"text":"'+data.error+'","layout":"topRight","type":"error"}'));  
			  }
			  if(data.success){
				noty($.parseJSON('{"text":"'+data.success+'","layout":"topRight","type":"success"}'));    
				var text = $('#text').val();
				table.row.add( $('<tr><td>'+key+'</td><td class="col-lg-9" data-order="'+data.key+'" data-search="'+text+'"><input class="form-control" type="text" style="width: 100% !important;" id="langstring*'+data.key+'" value="'+text+'" /></td></tr>')[0]).draw();
			  }
			}
		  });
		});
		$(document).on("input",'input[id^="langstring*"]', function (){
		  var key = $(this).attr("id").split("*")[1],
			  text = $(this).val();
		  $.ajax({
			type: 'POST',
			url: '<?php echo $this->config->base_url . ACPURL; ?>/edit-string',
			data: {lang: '<?php echo $lang; ?>', key: key, text: text},
			dataType: "json",
			success: function(data){
			  if(data.error){
				noty($.parseJSON('{"text":"'+data.error+'","layout":"topRight","type":"error"}'));  
			  }
			  $('.bootstrap-datatable').DataTable().ajax.reload();
			}
		  });
		});
	  });
	</script>
</div>