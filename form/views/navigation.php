<ul class="nav">
	<li class="nav-item <?= (in_array(basename($_SERVER['SCRIPT_NAME']), ['index.php'])) ? 'active' : '' ?>">
		<a class="nav-link" href="index.php">HOME</a>
	</li>
	<li class="nav-item <?= (in_array(basename($_SERVER['SCRIPT_NAME']), ['user_edit.php', 'users.php'])) ? 'active' : '' ?>">
		<a class="nav-link" href="users.php">USERS</a>
	</li>
	<li class="nav-item <?= (in_array(basename($_SERVER['SCRIPT_NAME']), ['form_edit.php', 'forms.php'])) ? 'active' : '' ?>">
		<a class="nav-link" href="forms.php">FORMS</a>
	</li>
</ul>

<a class="btn btn-primary float-right" id="bntLogout" href="logout.php">Log out</a></a>
