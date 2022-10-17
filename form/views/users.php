<!DOCTYPE html>
<html>
<head>
	<title>Form Builder | Users</title>
	<link rel="icon" href="images/rm.png">
	<?php include 'stylesheets.php'; ?>
	<?php include 'scripts.php'; ?>
	<script type="text/javascript">
		var loginFields = <?= json_encode($loginFields); ?>;
	</script>
	<script type="text/javascript" src="js/users.js"></script>
</head>
<body>

	<?php include 'header.php'; ?>

	<div class="panel">
		<div class="panel-header">
			<?php include 'navigation.php'; ?>

			<?php if (isset($statusMessage)): ?>
				<p class="bg-success"><?= htmlspecialchars($statusMessage); ?></p>
			<?php endif; ?>
		</div>
		<div class="panel-body">
			<h3>Users</h3>
			<br>
			<table class="table">
				<thead>
					<tr>
						<th>First Name</th>
						<th>Last Name</th>
						<th>E-mail</th>
						<th>Manage</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($users)): ?>
					<tr>
						<td colspan="4"><em>No users</em></td>
					</tr>
					<?php endif; ?>
					<?php foreach ($users as $user): ?>
					<tr>
						<td><?= htmlspecialchars($user->first_name); ?></td>
						<td><?= htmlspecialchars($user->last_name); ?></td>
						<td><?= htmlspecialchars($user->email); ?></td>
						<td class="text-center"><button data-login_id="<?= htmlspecialchars($user->login_id); ?>" class="btn btn-primary edit_user">Edit</button></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<br>
			<?php
			if($user->login_id == 1):
			?>
			<button class="btn btn-primary" id="add_user_button">Add User</button>
			<?php
			endif;
			?>
			
		</div>
		<?php include 'footer.php'; ?>
	</div>
	
	<div id="user_edit_dialog" title="Add User" style="display: none">
		<form id="user_edit_form" action="user_edit.php" method="post">
			<input type="hidden" id="action" name="action" value="edit_user" />
			<input type="hidden" id="login_id" name="login_id" value="" />
			<div class="form-group">
				<label>First name:</label>
				<input type="text" id="first_name" name="first_name" spellcheck="false" value="" />
			</div>
			<div class="form-group">
				<label>Last name:</label>
				<input type="text" id="last_name" name="last_name" spellcheck="false" value="" />
			</div>
			<div class="form-group">
				<label>E-mail address:</label>
				<input type="email" id="email" name="email" spellcheck="false" value="" />
			</div>
			<div class="form-group">
				<label>Password:</label>
				<input type="password" id="password" name="password" value="" />
			</div>
		</form>
	</div>

</body>
</html>
