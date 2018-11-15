<footer>
	<div class="container-fluid">
		<div class="row is-flex">
			<!-- <div class="col-xs-12 col-md-1 col-md-push-9 footerNav">
				<h3>Navigation</h3>
				<ul>
					<li><a href="">Home</a></li>
					<li><a href="">Showcase</a></li>
					<li><a href="">Resume</a></li>
				</ul>
			</div> -->
			<div class="col-xs-12 col-md-5 col-md-offset-1 contactForm" id="contactForm">
				<h3>Let's Work Together</h3>

				<div id="form-messages"></div>
				<form id="ajax-contact" action="/php/footer_sendmail.php" method="POST">

					<div class="form-group col-xs-6 col-md-12 col-lg-6">
						<input type="text" class="form-control" name="inputName" id="inputName" placeholder="Name" required>
					</div>

					<div class="form-group col-xs-6 col-md-12 col-lg-6">
						<input type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="Email Address" required>
					</div>

					<div class="form-group col-xs-12">
						<textarea class="form-control" name="inputMessage" id="inputMessage" rows="5" placeholder="Tell me a bit about your project..." required></textarea>
					</div>



					<div class="form-group col-xs-12"> <!--  col-xs-6 col-md-4 col-lg-6 -->
						<button type="submit" class="btn btn-lg"><span>Send</span></button>
					</div>

				</form>

			</div>
			<div class="col-xs-12 col-md-5 about">
				<h3>Stephen Pierce</h3>
				<p>A Front End Web Developer based out of St. Petersburg, Florida with 5 years of experience. I aim to deliver quality, SEO-driven, responsive websites through HTML, CSS, and Javascript while maintaining data-integrity, and performance through PHP and MySQL.</p>
				<p><i class="fa fa-phone"></i><a href="tel:1-727-686-3108" alt="Call Stephen Pierce">+1 (727) 686-3108</a></p>
				<p><i class="fa fa-envelope"></i><a href="mailto:stephen@stephenpierce.io" alt="Email Stephen Pierce">stephen@stephenpierce.io</a></p>
			</div>
			<div class="col-xs-12 social">
				<h3>Let's Connect</h3>
				<ul>
					<li><a class="codepen" href="https://codepen.io/sppierce/" target="_blank" alt="Codepen"><i class="fa fa-codepen" aria-hidden="true"></i></a></li>
					<li><a class="github" href="https://github.com/sppierce" target="_blank" alt="GitHub"><i class="fa fa-github-alt" aria-hidden="true"></i></a></li>
					<li><a class="linkedin" href="https://www.linkedin.com/in/stephenpiercejr/" target="_blank" alt="LinkedIn"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
					<li><a class="facebook" href="https://www.facebook.com/stephenpierceio/" target="_blank" alt="Facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
					<li><a class="instagram" href="https://www.instagram.com/stephenpierceio/" target="_blank" alt="Instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
					<li><a class="twitter" href="https://twitter.com/stephenpierceio" target="_blank" alt="Twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
				</ul>
			</div>
			<div class="col-xs-12" id="copyright">
				<p>&copy; Copyright 2016 - <?php echo date('Y'); ?> &bull; Stephen Pierce &bull; Front End Web Developer &bull; St. Petersburg, FL &bull; All rights reserved <a href="https://www.stephenpierce.io/">www.stephenpierce.io</a></p>
			</div>
		</div>
	</div>
</footer>


<!-- Latest compiled and minified JavaScript -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous" defer></script>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script async src="assets/js/app.js" defer></script> <!-- load our javascript file related to AJAX form submission -->

<script type="text/javascript">

  $(document).ready(function () {

    // Mobile menu animation
    $('.hamburger-menu').on('click', function() {
      $('.bar').toggleClass('animate');
      $('.navList').toggleClass('slideInAnimate');
      $('nav').toggleClass('slideInAnimate');
    })

    $('ul.navList li').click(function(){
     $(this).addClass('current-page');
     $(this).siblings().removeClass('current-page');
    });


  });
</script>
</body>

</html>
