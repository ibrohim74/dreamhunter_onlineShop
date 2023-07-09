<?php wp_footer() ?>
<!--Footer-->
<footer class="page-footer text-center text-md-left grey" id="contact">

    <!--Footer Links-->
    <div class="container ">

        <!--First row-->
        <div class="row ">

            <div class="col-md-10 col-lg-12 mt-5">




                <ul class="text-center list-unstyled d-flex justify-content-around">
                    <li>
                        <a href="https://goo.gl/maps/DMVkejSv2pgsfBp59">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                            <p>Toshkent shaxri, Yakkasaroy tumani, Jiydazor - 5</p>
                        </a>

                    </li>

                    <li><i class="fas fa-phone fa-2x"></i>
                        <p> <a href="tel:+998 55 151 11 11 "> +998 55 151 11 11 </a> <br>
                            <a href="tel:+998 90 880 99 99"> +998 90 880 99 99</a>
                        </p>
                    </li>
                </ul>
            </div>

            <!--First column-->
            <div class="col-md-12 my-4 wow fadeIn " data-wow-delay="0.3s ">

                <div class="footer-socials mt-3 mb-4 text-center ">

                    <!--Facebook-->
                    <a type="button " class="btn-floating btn-secondary "
                        href="https://instagram.com/dreamhunter.uz?igshid=YmMyMTA2M2Y="><i
                            class="fab fa-instagram "></i></a>
                    <!--Twitter-->
                    <a type="button " class="btn-floating btn-secondary "
                        href="https://www.facebook.com/profile.php?id=100089083041905"><i
                            class="fab fa-facebook-f "></i></a>
                    <!--Google +-->
                    <a type="button " class="btn-floating btn-secondary " href="https://t.me/dreamhunteruz"><i
                            class="fab fa-telegram "></i></a>

                </div>
            </div>
            <!--/First column-->
        </div>
        <!--/First row-->

    </div>
    <!--/Footer Links-->

    <!--Copyright-->
    <div class="footer-copyright py-3 text-center  ">
        <div class="container-fluid">
            © 2023 Copyright <br> <a href="https://t.me/farrux_u17/" target="_blank"> Farrux_u17 </a>
            <br><a href="https://t.me/khasanov_ibroxim/" target="_blank"> khasanov_ibroxim </a>
        </div>
    </div>
    <!--/Copyright-->

    <!-- Scrollspy -->
    <div class="dotted-scrollspy clearfix d-none d-sm-block ">
        <ul class="nav smooth-scroll flex-column ">
            <li class="nav-item "><a class="nav-link " href="#home"><span></span></a></li>
            <li class="nav-item "><a class="nav-link " href="#about"><span></span></a></li>
            <li class="nav-item "><a class="nav-link " href="#services"><span></span></a></li>
            <li class="nav-item "><a class="nav-link " href="#portfolio"><span></span></a></li>
            <li class="nav-item "><a class="nav-link " href="#contact"><span></span></a></li>
        </ul>
    </div>

</footer>

<!-- SCRIPTS -->

<!-- JQuery -->
<script type="text/javascript " src="<?php bloginfo('template_url') ?>/assets/js/jquery-3.3.1.min.js "></script>

<!-- Bootstrap tooltips -->
<script type="text/javascript " src="<?php bloginfo('template_url') ?>/assets/js/popper.min.js "></script>

<!-- Bootstrap core JavaScript -->
<script type="text/javascript " src="<?php bloginfo('template_url') ?>/assets/js/bootstrap.min.js "></script>

<!-- MDB core JavaScript -->
<script type="text/javascript " src="<?php bloginfo('template_url') ?>/assets/js/mdb.min.js "></script>

<script>
// initialize scrollspy
$('body').scrollspy({
    target: '.dotted-scrollspy'
});

// initialize lightbox
$(function() {
    $("#mdb-lightbox-ui ").load("mdb-addons/mdb-lightbox-ui.html ");
});

/* WOW.js init */
new WOW().init();

$('.navbar-collapse a').click(function() {
    $(".navbar-collapse ").collapse('hide');
});
</script>
<!--Footer-->
</body>

</html>