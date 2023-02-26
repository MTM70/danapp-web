<script>
    const base_url = "<?= base_url(); ?>";
</script>

<!DOCTYPE html>
<html lang="en">

<?= get_view("header", $data) ?>

<body>

    <main>
        <div class="container">

            <div class="w-100 h-100 start-0 position-absolute">
                <div class="col secondary" style="height: 40vh;">
                    <div class="rounded-circle position-absolute bubble" style="width: 100px; height: 100px; left:100px; top:50px;"></div>
                    <div class="rounded-circle position-absolute bubble" style="width: 100px; height: 100px; right:100px; top:100px;"></div>
                    <div class="rounded-circle position-absolute bubble" style="width: 100px; height: 100px; left:450px; top:130px;"></div>
                </div>
                <div class="col bg-white" style="height: 60vh;">

                </div>
            </div>

            <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="card rounded-4 border mb-3">

                                <div class="card-body">

                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">
                                            <a class="logo">
                                                <span class="d-none d-lg-block">DanApp</span>
                                            </a>
                                        </h5>
                                        <p class="text-center small">Enter your username & password to login</p>
                                    </div>

                                    <form class="row g-3 needs-validation" novalidate method="POST" id="form-login">

                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="floatingInput" placeholder="@example" name="user">
                                            <label for="floatingInput"><i class="bi bi-person-circle mx-2"></i>User</label>
                                        </div>
                                        
                                        <div class="form-floating mb-3">
                                            <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
                                            <label for="floatingPassword"><i class="bi bi-key-fill mx-2"></i>Password</label>
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-success rounded-5 w-100" type="submit" id="login-btn" style="padding:11px;">
                                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="login-loading"></span>
                                                <span id="login-text">Login</span>
                                            </button>
                                        </div>
                                    </form>

                                </div>

                            </div>

                            <div class="mt-4" style="z-index:1;">
                                <img src="<?= media() ?>/img/logo.png" height="25" alt="">
                            </div>

                        </div>
                    </div>
                </div>

            </section>

        </div>
    </main><!-- End #main -->

    <?= get_view("footer", $data) ?>

</body>

</html>