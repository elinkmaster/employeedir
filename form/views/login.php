<!DOCTYPE html>
<html>
<head>
	<title>Form Builder | Login</title>
	<link rel="icon" href="images/rm.png">
	<?php include 'stylesheets.php'; ?>
	<?php include 'scripts.php'; ?>
</head>
<body>
	<?php include 'header.php'; ?>

	<div class="panel">
		<div class="panel-header">
			<div class="panel-title">Log In</div>
		</div>
		<div class="panel-body">
			<div id="login-logo">
				<img src="images/eLink_logo.png">
			</div>
			<div id="login-form">
				<form method="post" action="login.php">
					<div class="form-group">
						<label for="email">E-mail Address:</label>
						<input type="email" class="float-right" id="email" name="email" spellcheck="false" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" required>
					</div>
					<div class="form-group">
						<label for="password">Password:</label>
						<input type="password" class="float-right" id="password" name="password" required>
					</div>
					<button class="btn btn-primary" id="btnLogin" name="btnLogin">Log In</button>
					<a class="recover-pass" href="#">Forgot Password</a>
				</form>
			</div>
		</div>
		<?php include 'footer.php'; ?>
	</div>

	<div id="dialog-message" title="Reset Password">
		<p class="text-danger">Are you sure you wanted to reset your password?</p>
		<div>&nbsp;</div>
	</div>

	<script type="text/javascript">
		$(function() {
			$('#btnLogin').button();

			<?php if (!empty($_POST['email'])): ?>
				$('#password').focus();
			<?php else: ?>
				$('#email').focus();
			<?php endif; ?>

			$("#dialog-message").hide();
		});

		$(".recover-pass").click(function(){
			var email = $("#email").val();
			console.log('email: ' + email);
			$( "#dialog-message" ).dialog({
				modal: true,
				buttons: {
					Reset: function() {
						window.location.href = "/reset.php?acc=" + email;
					},
					Cancel: function() {
						$( this ).dialog( "close" );
					}
				}
			});
			$(".ui-dialog-titlebar-close").hide();
		});
	</script>

</body>
</html>
