<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <title>Hello, world!</title>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand" href="#">ADL Ventures</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggle" aria-controls="navbarToggle" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarToggle">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Made by ADL</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Case Studies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Industries</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<img src="assets/Grey-Background.jpg" alt="Image" style="width: 100%; height: 200px">

<div class="container mb-5">
    <div class="row">
        <div class="col-md-6">
            <a href="#" class="mt-2">Back to listings</a>
            <form>
                <h2 class="mt-3"><b>Submit your team</b></h2>
                <label class="mt-2 mb-2"><b>Company/team name</b></label>
                <input type="text" class="form-control shadow-sm" name="company_name" id="" placeholder="Company/team name" value="">

                <label class="mt-2 mb-2"><b>Website</b></label>
                <input type="text" class="form-control shadow-sm" name="website" id="" placeholder="Website">

                <label class="mt-2 mb-2"><b>Representative name</b></label>
                <input type="text" class="form-control shadow-sm" name="representative_name" id="" placeholder="Representaitve name">

                <label class="mt-2 mb-2"><b>Email address</b></label>
                <input type="email" class="form-control shadow-sm" name="email_address" id="" placeholder="Email address">

                <label class="mt-2 mb-2" for="example1"><b>Chalenge area</b></label>
                <select class="form-select" name="chalenge_area" id="example1">
                    <option selected>Open this select menu</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>

                <label class="mt-2 mb-2"><b>Product description</b></label>
                <textarea class="form-control shadow-sm" name="product_description" id="" placeholder="Product description" value=""></textarea>

                <label class="mt-2 mb-2" for="example2"><b>Product category</b></label>
                <select class="form-select" id="example2" name="product_category">
                    <option selected>Open this select menu</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>

                <label class="mt-2 mb-2"><b>Ideal teaming partner description</b></label>
                <textarea class="form-control shadow-sm" name="ideal_partner" id="" placeholder="Ideal teaming partner description" value=""></textarea>
            </form>
            <button type="button" class="btn btn-primary shadow-sm mt-3">Submit form</button>
        </div>
        <div class="col-md-1">

        </div>
        <div class="col-md-5 mt-3">
            <h4>Next steps</h4>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Id labore minima velit! Asperiores consequatur doloremque eos fugiat in iusto molestiae possimus quaerat veritatis voluptates! A aliquam animi asperiores at beatae delectus, deleniti dicta doloremque et expedita in incidunt necessitatibus placeat quam quia quibusdam quod ratione reiciendis repellendus saepe suscipit tempore vitae? Ab aspernatur eum iure nulla pariatur quis repudiandae. Itaque laborum porro voluptatem. Adipisci asperiores commodi consequuntur cum cumque cupiditate, dolor dolores ea error esse ex fuga illum in, iste itaque iure laudantium magnam maxime necessitatibus nemo neque nostrum obcaecati officiis quam reiciendis repellat sint tempore tenetur vel veritatis, vero.</p>
        </div>
    </div>
</div>

<style>
    b{
        font-weight: 500;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>

</body>
</html>
