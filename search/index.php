<?php
	/*
	DEVELOPER INFO
	Name: Novotny Ondrej
	Email: ondrej.novotny1410@gmail.com
	
	SCRIPT INFO
	Description: Search profile page
	*/
	#Script must not be required by other scripts
	if (count(debug_backtrace(null, 1)))
	{
		#Script required by other script => do not proceed with execution
		echo 'File '.__FILE__.' cannot be included.';
		return;
	}
	session_start();
	
	#Required scripts
	#Imports: $db, $executeSql
	require_once(__DIR__.'/../resources/db.php');
	#Imports: getValue, getInputInfo
	require_once(__DIR__.'/../resources/functions/general.php');
	
	#Header settings
	$headerSettings = [
		'includeSearchForm' => false
	];

	#Variables that will be used later in the script
	$getProfilesSql = 'SELECT id, username, email FROM users WHERE username LIKE :searchTerm OR email LIKE :searchTerm';
	$getProfilesCountSql = 'SELECT COUNT(id) AS profilesCount FROM users WHERE username LIKE :searchTerm OR email LIKE :searchTerm';

	$pageSize = 10;
	$searchTerm = null;
	$searchTermLength = 0;
	$page = 0;
	
	$reason = null;
	$_ = null;
	$profiles = [];
	$profilesCount = null;
	$pagesCount = null;
	
	#Checking if $_GET exists
	if ($_GET)
	{
		#Getting search term
		$searchTerm = getValue($_GET, 's');
		#Getting page
		$page = (int)getValue($_GET, 'page');
		
		#Getting search term length
		$searchTermLength = strlen($searchTerm);
		#Checking length
		if ($searchTermLength < 3)
		{
			#Search term is too short
		}
		else
		{
			#Getting profiles count
			list($_, $result, $_) = $executeSql($getProfilesCountSql, [':searchTerm' => '%'.$searchTerm.'%'], true);
			$profilesCount = getValue($result, 'profilesCount');
	
			#Getting pages count
			$pagesCount = ceil($profilesCount / $pageSize);
			#Checking if page is in range
			if ($page > $pagesCount - 1 and $pagesCount > 0)
			{
				#Page is too big
				$page = $pagesCount - 1;
			}
			elseif ($page < 0)
			{
				#Page is too low
				$page = 0;
			}
	
			#Adding limit to the profile search sql
			$getProfilesSql .= ' LIMIT '.$pageSize.' OFFSET '.$pageSize * $page;
			#Searching profiles
			list($_, $profiles, $_) = $executeSql($getProfilesSql, [':searchTerm' => '%'.$searchTerm.'%']);
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<!--Page title-->
		<title>
			<?= $searchTermLength >= 3 ? 'Search results for \''.$searchTerm.'\'' : 'Search profile' ?> in The great community
		</title>
		<!--Search page css-->
		<link rel="stylesheet" type="text/css" href="style.css">
		<!--General css-->
		<link rel="stylesheet" type="text/css" href="../style.css">
		<!--Google icons-->
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	</head>
	<body>
		<!--Page header-->
		<div id="header">
			<?php require('../header.php'); ?>
		</div>
		<!--Tip-->
		<div class="tip">
			<i class="material-icons">
				info
			</i>
			<b>
				Tip:
			</b>
			<span>
				Try searching "smith", it will search both by username and email
			</span>
		</div>
		<!--Page content-->
		<div id="content">
			<!--Title-->
			<h1>
				Search profile
			</h1>
			<!--Search form-->
			<form class="searchForm" action="." method="get">
				<!--Search input-->
				<div class="inputWrapper">
					<input id="searchTerm" type="text" name="s" placeholder="Search term" minlength="3" value="<?= $searchTerm; ?>">
				</div>
				<!--Search button with icon-->
				<div class="inputWrapper">
					<button>
						<i class="material-icons">
							search
						</i>
					</button>
				</div>
			</form>
			<!--Section wrapper-->
			<div id="profilesListWrapper">
				<!--Section title-->
				<h2>
					Search results
				</h2>
				<!--List of profiles-->
				<div id="profilesList">
					<?php if (empty($profiles)): ?>
						<!--There are no results to show-->
						<div class="searchResult">
							No results
						</div>
					<?php else: ?>
						<!--Loading profiles-->
						<?php foreach ($profiles as $profileNum => $profile): ?>
							<!--Resulting profile-->
							<div class="searchResult">
								<!--Username-->
								<div class="username">
									<?= getValue($profile, 'username'); ?>
								</div>
								<!--Email-->
								<div class="email">
									<?= getValue($profile, 'email'); ?>
								</div>
								<!--Profile link-->
								<a class="button orange profileUrl" href="../profile?id=<?= getValue($profile, 'id'); ?>&s=<?= $searchTerm; ?>">
									View profile
								</a>
							</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
			<!--Pagination-->
			<?php if (!empty($profiles)): ?>
				<div id="pagination">
					<!--Loading pages-->
					<?php for ($pageNum = 0; $pageNum < $pagesCount; $pageNum++): ?>
						<!--Checking if user is on this page-->
						<?php if ($pageNum == $page): ?>
							<!--Current page indicator-->
							<div class="currentPage">
								<?= $pageNum + 1; ?>
							</div>
						<?php else: ?>
							<!--Link to the page-->
							<a class="button orange" href="?s=<?= $searchTerm; ?>&page=<?= $pageNum; ?>">
								<?= $pageNum + 1 ?>
							</a>
						<?php endif; ?>
					<?php endfor; ?>
				</div>
			<?php endif; ?>
		</div>
		<!--Page footer-->
		<div id="footer">

		</div>
	</body>
</html>
<?php
	#Getting rid of all variables
	unset(
		$headerSettings,
		$getProfilesSql,
		$getProfilesCountSql,
		$pageSize,
		$searchTerm,
		$searchTermLength,
		$page,
		$reason,
		$_,
		$profiles,
		$profilesCount,
		$pagesCount
	);
?>