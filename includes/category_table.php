<?php
	include_once('connect_database.php'); 
	include_once('functions.php'); 
?>

<div id="content" class="container col-md-12">
	<?php 
		// create object of functions class
		$function = new functions;
		
		// create array variable to store data from database
		$data = array();
		
		if(isset($_GET['keyword'])){	
			// check value of keyword variable
			$keyword = $function->sanitize($_GET['keyword']);
			$bind_keyword = "%".$keyword."%";
		}else{
			$keyword = "";
			$bind_keyword = $keyword;
		}
			
		if(empty($keyword)){
			$sql_query = "SELECT cid, category_name, category_image
					FROM tbl_news_category
					ORDER BY cid DESC";
		}else{
			$sql_query = "SELECT cid, category_name, category_image
					FROM tbl_news_category
					WHERE category_name LIKE ? 
					ORDER BY cid DESC";
		}
		
		
		$stmt = $connect->stmt_init();
		if($stmt->prepare($sql_query)) {	
			// Bind your variables to replace the ?s
			if(!empty($keyword)){
				$stmt->bind_param('s', $bind_keyword);
			}
			// Execute query
			$stmt->execute();
			// store result 
			$stmt->store_result();
			$stmt->bind_result($data['cid'], 
					$data['category_name'],
					$data['category_image']
					);
			// get total records
			$total_records = $stmt->num_rows;
		}
			
		// check page parameter
		if(isset($_GET['page'])){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
						
		// number of data that will be display per page		
		$offset = 10;
						
		//lets calculate the LIMIT for SQL, and save it $from
		if ($page){
			$from 	= ($page * $offset) - $offset;
		}else{
			//if nothing was given in page request, lets load the first page
			$from = 0;	
		}	
		
		if(empty($keyword)){
			$sql_query = "SELECT cid, category_name , category_image
					FROM tbl_news_category
					ORDER BY cid DESC LIMIT ?, ?";
		}else{
			$sql_query = "SELECT cid, category_name, category_image
					FROM tbl_news_category
					WHERE category_name LIKE ? 
					ORDER BY cid DESC LIMIT ?, ?";
		}
		
		$stmt_paging = $connect->stmt_init();
		if($stmt_paging ->prepare($sql_query)) {
			// Bind your variables to replace the ?s
			if(empty($keyword)){
				$stmt_paging ->bind_param('ss', $from, $offset);
			}else{
				$stmt_paging ->bind_param('sss', $bind_keyword, $from, $offset);
			}
			// Execute query
			$stmt_paging ->execute();
			// store result 
			$stmt_paging ->store_result();
			$stmt_paging->bind_result($data['cid'], 
					$data['category_name'],
					$data['category_image']
					);
			// for paging purpose
			$total_records_paging = $total_records; 
		}

		// if no data on database show "No Reservation is Available"
		if($total_records_paging == 0){
	
	?>
	<h1>Category Not Available
		<a href="add-category.php">
			<button class="btn btn-danger">Add New Category</button>
		</a>
	</h1>
	<hr />
	<?php 
		// otherwise, show data
		}else{
			$row_number = $from + 1;
	?>

	<div class="col-md-12">
		<h1>
			Category List
			<a href="add-category.php">
				<button class="btn btn-danger">Add New Category</button>
			</a>
		</h1>
		<hr/>
	</div>
	<!-- search form -->
	<form class="list_header" method="get">
		<div class="col-md-12">
			<p class="pholder">Search by Name : </p>
		</div>

		<div class="col-md-3">
			<input type="text" class="form-control" name="keyword" value="<?php echo isset($_GET['keyword']) ? $_GET['keyword'] : "" ?>" />
		</div>

		<br>
		&nbsp;&nbsp;&nbsp;
		<input type="submit" class="btn btn-primary" name="btnSearch" value="Search" />
	</form>
	<!-- end of search form -->
	
	<br/>
	<div class="col-md-12">
	<table class="table table-hover">
		<tr>
			<th>Name</th>
			<th>Image</th>
			<th width="20%">Action</th>
		</tr>
	<?php 
		while ($stmt_paging->fetch()){ ?>
			<tr>
				<td><?php echo $data['category_name'];?></td>
				<td width="20%"><img src="upload/category/<?php echo $data['category_image']; ?>" width="50" height="50"/></td>
				<td>
					<a href="edit-category.php?id=<?php echo $data['cid'];?>">
					Edit
					</a>&nbsp;
					<a href="delete-category.php?id=<?php echo $data['cid'];?>">
					Delete
					</a>
				</td>
			</tr>
		<?php 
		} 
	}
?>
	</table>
	</div>

	
	<div class="col-md-12">
	<h4>
	<?php 
		// for pagination purpose
		$function->doPages($offset, 'category.php', '', $total_records, $keyword);?>
	</h4>
	</div>
	<div class="separator"> </div>
</div> 

<?php 
	$stmt->close();
	include_once('close_database.php'); ?>
					
				