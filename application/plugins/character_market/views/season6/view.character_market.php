<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.header');
?>	
<div id="content">
	<div id="box1">
		<?php 
		if(isset($config_not_found)):
			echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$config_not_found.'</div></div></div>';
		else:
			if(isset($module_disabled)):
				echo '<div class="box-style1"><div class="entry"><div class="e_note">'.$module_disabled.'</div></div></div>';
			else:
		?>	
		<div class="title1">
			<h1><?php echo __($about['name']); ?></h1>
		</div>
		<div class="box-style1" style="margin-bottom: 20px;">
			<h2 class="title"><?php echo __($about['user_description']); ?></h2>
			<div class="entry" >
				<div style="float:right;">
					<a class="custom_button" href="<?php echo $this->config->base_url;?>character-market/sell-character"><?php echo __('Sell Character');?></a>
					<a class="custom_button" href="<?php echo $this->config->base_url;?>character-market/sale-history"><?php echo __('Sale History');?></a>
				</div>
				<div style="padding-top:40px;"></div>
				<div style="clear:left;"></div>
				<div style="padding-top:20px;"></div>
				<?php if(isset($chars) && !empty($chars)): ?>
				<table class="ranking-table">
					<thead>
					<tr class="main-tr">
						<td>#</td>
						<td><?php echo __('Character');?></td>
						<td><?php echo __('Merchant');?></td>
						<td><?php echo __('Price + Tax');?> (<?php echo $this->config->values('character_market', array($this->session->userdata(array('user'=>'server')), 'sale_tax'));?>%)</td>
						<td><?php echo __('Class');?></td>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($chars as $ch):
					?>
					<tr>
						<td><?php echo $ch['icon'];?></td>
						<td><a href="<?php echo $this->config->base_url;?>character-market/buy/<?php echo $ch['id'];?>"><?php echo $ch['name'];?></a></span></td>
						<td><?php echo htmlspecialchars($ch['seller']);?></td>
						<td><?php echo $ch['price'];?></td>
						<td><?php echo $ch['class'];?></td>
					</tr>
					<?php
					endforeach;
					?>
					</tbody>
				</table>
				<?php 
				if(isset($pagination)):
				?>	
				<table style="width: 100%;"><tr><td><?php echo $pagination; ?></td></tr></table>	
				<?php
				endif;
				?>
				<?php 
				else:
				?>
				<div class="w_note"><?php echo __('No Characters Found.');?></div>
				<?php
				endif;
				?>
			</div>
		</div>
		<?php
			endif;
		endif;
		?>
	</div>
</div>
<?php
	$this->load->view($this->config->config_entry('main|template').DS.'view.right_sidebar');
	$this->load->view($this->config->config_entry('main|template').DS.'view.footer');
?>
	